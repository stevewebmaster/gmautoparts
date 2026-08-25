# Shop launch checklist

Where the online shop stands, and what is left to do. **Work through the phases in
order** — the live domain has to land before Stripe and Google Merchant Center, or
both get set up twice.

Shareable version (tickable, same content):
<https://claude.ai/code/artifact/e51d4cd8-d5ef-441b-8297-92effb1762a7>

Tasks are tagged **[Steve]** (server / code) or **[G&M]** (a decision or job only
the client can do).

---

## State as at 2026-08-07

| | |
| --- | --- |
| ✅ | Deployed to SiteHost and all migrations run |
| ✅ | Cron installed and verified — all four scheduled jobs firing |
| ✅ | Cart, checkout, Google feed, structured data, reserve flow all live |
| ⛔ | **No Stripe keys** — checkout refuses cleanly, nothing can be bought |
| ⛔ | **Only 4 parts live**, and the shipping bands have not been applied yet |
| ⛔ | **Still on `gm.websitemaster.co.nz`** — not the live domain |

---

## ⚠️ Do the domain first

`APP_URL` is written into every link in the Google feed and the product markup on
each part page. Stripe's webhook and return URLs are tied to the domain, and
Merchant Center makes you verify *and claim* the domain before it will accept a
feed. Setting Stripe and Google up on the current host means redoing both.

The exception: a Stripe **test-mode** purchase is worth doing at any point, just to
prove the chain works. Test webhooks take a minute to recreate.

---

## Phase 1 — Point the live domain at the site

- [ ] **[G&M]** Confirm the domain, and who controls its DNS
- [ ] **[Steve]** DNS A record → SiteHost container IP; wait for propagation
- [ ] **[Steve]** Add the domain to `server_name` in
      `~/container/config/nginx/sites-available/default`, then restart the container.
      Leave `root` as `/container/application/public`.
- [ ] **[Steve]** Issue the SSL certificate via the SiteHost panel. Stripe and
      Merchant Center both require HTTPS.
- [ ] **[Steve]** Update `APP_URL` and rebuild config:
      ```bash
      cd ~/container/application
      nano .env          # APP_URL=https://yourdomain.co.nz
      php artisan config:cache
      php artisan cache:clear
      ```
- [ ] **[Steve]** Verify the feed picked it up — every `link` and `image_link` must be
      the live https domain, not `/storage/…` and not the old host:
      ```bash
      curl -s https://yourdomain.co.nz/feeds/google.xml | head -20
      ```

## Phase 2 — Finish the stock setup

- [ ] **[Steve]** Put cron logging back to `/dev/null` in
      `~/container/crontabs/crontab` and delete `storage/logs/cron.log`
      (it grows ~1 MB/day otherwise)
- [ ] **[Steve]** Apply the shipping bands — safe to re-run, only touches parts with
      no band, never overwrites a correction:
      ```bash
      cd ~/container/application
      php artisan parts:guess-shipping-bands
      php artisan cache:clear
      ```
- [ ] **[G&M]** Review the bands in **Admin → Catalogue → Parts** (filter by shipping
      band). They were guessed from the part name. A wrong band does not error — it
      quietly loses money on every sale.
- [ ] **[G&M]** Load and price the rest of the stock. Only 4 parts are live, and a part
      with no price cannot be sold online *or* listed on Google. Parts loaded from
      here get a shipping size in the Parts Loader app, so this backlog is a one-off.

## Phase 3 — Turn on card payments

- [ ] **[G&M]** Open the Stripe account — business details, GST number, bank account
      for payouts. Roughly 2.7% + 30c on domestic cards.
- [ ] **[Steve]** Add test keys (`STRIPE_KEY`, `STRIPE_SECRET`), then `config:cache`
- [ ] **[Steve]** ⚠️ **Create the webhook** — `https://yourdomain.co.nz/stripe/webhook`,
      events `checkout.session.completed` and `checkout.session.expired`. Signing
      secret into `STRIPE_WEBHOOK_SECRET`, then `config:cache`.
      Without it, a customer who closes the tab after paying leaves an order stuck on
      "awaiting payment" and the sweep hands their parts back to the shop.
- [ ] **[Steve]** Test purchase with `4242 4242 4242 4242`, any future expiry/CVC.
      Check all four: order shows **Paid**; part flips to **Sold**; customer receipt
      arrives; pick-and-pack email arrives.
      *Receipt missing but order paid → the queue. Order stuck unpaid → the webhook.*
- [ ] **[Steve]** Switch to live keys — and recreate the webhook in live mode, it has
      its own signing secret
- [ ] **[G&M]** Decide who checks orders daily. Paid orders arrive by email and show
      under **Orders to Pack** in the Parts Loader app; a customer has already paid
      and is waiting.

## Phase 4 — Get onto Google Shopping

- [ ] **[G&M]** ⚠️ **Approve the returns and shipping policy text** — see the decisions
      table below. Blocks Google approval, and customers agree to it at checkout.
- [ ] **[Steve]** Create Merchant Center, verify and claim the domain
- [ ] **[Steve]** Add the feed as a scheduled fetch — **Products → Data sources → Add**.
      Currency **NZD**, country **New Zealand**, daily.
      `https://yourdomain.co.nz/feeds/google.xml`
- [ ] **[Steve]** Work the **Diagnostics** tab. Expect disapprovals on the first fetch;
      item-level errors there are more specific than any documentation.
- [ ] **[Steve]** Run a live part URL through the
      [Rich Results Test](https://search.google.com/test/rich-results) — works
      independently of Merchant Center
- [ ] **[G&M]** Free listings only, or Shopping ads too? Free listings need no ad
      spend. Start free.

## Phase 5 — Worth doing once live

- [ ] **[Steve/G&M]** Check the freight rates against real courier invoices — the
      values in `config/shipping.php` are estimates, not quotes
- [ ] **[Steve]** Add a `sitemap.xml` and `robots.txt` — neither exists. Cheapest
      remaining SEO gap; a sitemap of part pages helps Google find stock as it loads.
- [ ] **[G&M]** Consider Google **free local listings** — G&M has a physical yard, so
      parts can show in Search and Maps as in-store stock. Separate feed, no checkout
      required, no ad spend.
- [ ] **[Steve]** Delete the two unused header images —
      `public/images/page-headers/secondhand-parts.jpg` (1.1 MB) and
      `vehicle-wrecking-yard.webp`, both untracked and referenced nowhere

---

## Decisions only G&M can make

The policy pages are live with these assumptions baked in. Each is a commercial
choice. None of this is legal advice, and under the NZ **Consumer Guarantees Act** a
trader cannot contract out of the guarantees on used goods sold to consumers.

| Decision | Currently says | Needs |
| --- | --- | --- |
| Warranty on used mechanical parts | 30 days | Confirm or change |
| Change-of-mind returns window | 14 days | Confirm, or drop entirely |
| Return freight — change of mind | Customer pays | Confirm |
| Return freight — faulty part | G&M pays | Confirm |
| Refund turnaround | 10 working days | Confirm |
| Small freight — North / South | $12 / $18 | Check vs invoices |
| Medium freight — North / South | $20 / $30 | Check vs invoices |
| Large freight — North / South | $75 / $110 | Check vs invoices |
| Rural delivery surcharge | +$6.50 | Check vs invoices |
| Free-freight threshold | Off | Decide if wanted |

### How freight is charged

A basket is charged the **largest band in it, once** — not the sum of every part.
Several parts normally travel as one consignment, so summing would overcharge badly.
A cart with a headlight, an alternator and a door pays the door's rate and no more.

Parts too big to price this way — engines, gearboxes, nosecuts, panels, seats — are
banded **quote only**. They are not sold online and not sent to Google; their pages
offer *Reserve for collection* instead, so nothing is lost.

---

## Reference — what runs on the server

| Scheduled job | Runs | Does |
| --- | --- | --- |
| `queue:work --stop-when-empty` | every minute | Sends order and reservation emails |
| `orders:release-stale` | every 10 min | Frees parts from abandoned checkouts |
| `images:optimize` | every 5 min | Resizes uploaded photos |
| `reservations:release-expired` | daily 06:00 | Puts uncollected reservations back on sale |

All four are driven by **one** cron entry in `~/container/crontabs/crontab`, which
SiteHost runs as `www-data` — **not** your SSH user. See
[DEPLOY-SITEHOST.md](DEPLOY-SITEHOST.md).

Deeper detail: [SHOP.md](SHOP.md) for cart/checkout/Stripe,
[GOOGLE-SHOPPING.md](GOOGLE-SHOPPING.md) for the feed and structured data.
