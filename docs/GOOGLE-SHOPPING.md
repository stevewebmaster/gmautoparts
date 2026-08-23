# Google Shopping (Merchant Center) product feed

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

## Before Google will approve the feed

The feed being valid is **not** sufficient. Google requires a genuine purchase
path, and an enquiry form does not count — products will be disapproved.

Google does accept **"payment upon collection"** as a payment method, so a
full ecommerce checkout is not needed. What is needed:

1. A **reserve-for-collection flow** that behaves like a checkout: select the
   part, confirm, receive an order confirmation page and email, pay on pickup.
   (This is the next planned piece of work.)
2. A published **returns policy** page, also entered in Merchant Center.
3. A published **shipping policy** page.

Until those exist, the feed can still be submitted — Merchant Center's
Diagnostics tab is the fastest way to see exactly what Google wants, and item
level errors there are more specific than any documentation.

## Setting it up in Merchant Center

1. Create/verify the Merchant Center account and claim the website domain.
2. **Products → Data sources → Add product source → scheduled fetch.**
3. Paste the feed URL, set currency **NZD** and country **New Zealand**, and a
   daily fetch schedule.
4. Check **Diagnostics** after the first fetch.
