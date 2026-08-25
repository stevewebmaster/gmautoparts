# Google Shopping (Merchant Center) product feed

> **Outstanding tasks and client decisions:** [LAUNCH-CHECKLIST.md](LAUNCH-CHECKLIST.md)

The site publishes a Google Merchant Center product feed of the parts catalogue.

**Feed URL:** `https://<your-domain>/feeds/google.xml`

Format is RSS 2.0 with the `g:` namespace, per Google's product data
specification. Built by [`App\Services\GoogleProductFeed`](../app/Services/GoogleProductFeed.php).

---

## What is in the feed

A part is included only if **all** of these are true:

| Requirement | Why |
| --- | --- |
| `is_visible` is true | Hidden parts are not for sale. |
| status is `available` or `on_hold` | `sold` and `withdrawn` parts are dropped entirely — a sold wrecker part is not coming back. |
| has a price | Google rejects a null or zero price, so unpriced parts cannot be fed. **No price, no Google.** |
| has at least one image | `image_link` is a required attribute. |

`available` maps to `in_stock`; `on_hold` maps to `out_of_stock` (still listed,
but not purchasable right now).

### Attribute notes

- **`id`** is `part-{id}`, not the stock number. Stock numbers are not unique in
  the database, and feed IDs must be unique and stable.
- **`condition`** is always `used`.
- **`identifier_exists`** is `no`, and `brand`/`gtin`/`mpn` are omitted. Used
  parts carry no barcode. A stock number is an internal yard code, **not** a
  manufacturer part number, so submitting it as an MPN would be wrong.
- **`title`** is `{year} {make} {model} {title}`, but each bit is skipped if the
  title already contains it — otherwise "Toyota Hilux Headlight" would become
  "2010 Toyota Hilux Toyota Hilux Headlight". Capped at 150 characters.
- **`description`** falls back to a generated sentence when the part has none.
  Capped at 5000 characters. Characters that are illegal in XML are stripped —
  without that, a stray control byte pasted into a description would make the
  whole feed unparseable.
- Up to 10 `additional_image_link` entries per item.

---

## Caching

The feed is cached for one hour, and **saving or deleting any part clears that
cache** — so marking stock sold takes effect on Google's next fetch rather than
waiting out the TTL.

This depends on the cache actually working. If `config/cache.php` ever goes
missing, Laravel silently falls back to a null store and the feed would be
rebuilt on every request.

To clear it by hand:

```bash
php artisan cache:clear
```

---

## ⚠️ APP_URL must be correct

Every `link` and `image_link` in the feed is rooted at **`APP_URL`** from `.env`,
*not* the incoming request host. That is deliberate: the feed is cached, so
request-relative URLs would bake in whichever hostname happened to trigger the
rebuild.

The consequence is that **if `APP_URL` is wrong on the server, every URL in the
feed is wrong** and Google will reject the items. Check it before submitting:

```bash
cd /container/application
grep APP_URL .env                       # must be the real https:// domain
curl -s https://<your-domain>/feeds/google.xml | head -20
```

Note also that images are read from the `public` disk explicitly. `FILESYSTEM_DISK`
is `local`, and only the `public` disk defines a URL, so a bare `Storage::url()`
returns a relative path — fine for an `<img>` tag on the site, but invalid in a feed.

---

## The purchase path

Google requires a genuine purchase path before it will approve products — an
enquiry form does not count. Google accepts **"payment upon collection"**, so no
cart or payment gateway is needed. The site implements this as
**reserve for collection**:

1. An **available** part shows a Reserve form on its page.
2. Submitting it creates a `Reservation` with a reference (e.g. `GM-A4K7RT`),
   snapshots the part title and price, and sets the part to **on hold** — so it
   drops to `out_of_stock` in this feed straight away.
3. The customer lands on a **confirmation page** at `/reservations/{reference}`
   and is emailed a copy. Both emails are queued.
4. The part is held for **7 days** (`Reservation::HOLD_DAYS`). A scheduled
   command, `reservations:release-expired`, puts uncollected parts back on sale
   each morning at 06:00.
5. G&M marks it **Collected** (part becomes sold) or **Cancelled** (part goes
   back to available) in the Parts Loader app or in Filament.

Only `available` parts can be reserved — `Part::isReservable()`. This is
deliberately stricter than `isEnquirable()`, which allows on-hold parts, so the
same part cannot be reserved twice. The check is re-run inside a locking
transaction at submit time to close the race between two people clicking Reserve
at once.

### Policy pages

`/returns-policy` and `/shipping-policy` are published and editable in the admin
under Content → Pages. They are seeded by `PolicyPageSeeder`:

```bash
php artisan db:seed --class=PolicyPageSeeder
```

The seeder uses `firstOrCreate`, so re-running it will never overwrite edits
made in the admin.

> ⚠️ **The seeded policy text is a draft that G&M must review and approve**
> before the feed is submitted. It contains assumptions — a 30 day warranty on
> used mechanical parts, 14 days for change of mind, customer pays return
> freight, indicative freight costs — that need confirming. Google requires the
> published policy to be accurate. Note also that the NZ Consumer Guarantees Act
> applies to used goods sold by a trader and cannot be contracted out of for
> consumer sales.

Both policies must also be entered in Merchant Center itself, not just published
on the site.

Merchant Center's **Diagnostics** tab is the fastest way to see what Google still
wants — item level errors there are far more specific than the documentation.

---

## Product structured data (works without Merchant Center)

Part pages emit schema.org `Product` and `BreadcrumbList` JSON-LD via
`partials/part-structured-data.blade.php`. Google reads this from the page
alone — **no Merchant Center account, no feed, and no purchase path required** —
so it earns rich results in ordinary Search independently of everything above.

Google distinguishes two cases, and both are covered:

- **Merchant listings** — pages where the part can be bought. Priced,
  available parts carry a full `Offer`.
- **Product snippets** — the variant intended for pages you *cannot* buy from.
  Unpriced parts get `Product` markup with no `Offer`, which is still valid.

Availability maps `available` → `InStock`, `on_hold` → `OutOfStock`,
`sold` → `SoldOut`.

Values come from `App\Services\PartPresenter`, the same source the Merchant
feed uses. That is deliberate: **Google matches the landing page markup against
the feed item**, so `sku` equals the feed `id`, and name, url, image and
availability all agree. If you change one, change it in `PartPresenter` so both
follow.

Validate with [Google's Rich Results Test](https://search.google.com/test/rich-results)
against a live part URL after deploying.

---

## Setting it up in Merchant Center

1. Create/verify the Merchant Center account and claim the website domain.
2. **Products → Data sources → Add product source → scheduled fetch.**
3. Paste the feed URL, set currency **NZD** and country **New Zealand**, and a
   daily fetch schedule.
4. Check **Diagnostics** after the first fetch.
