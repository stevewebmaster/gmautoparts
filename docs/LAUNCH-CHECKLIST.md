# Shop launch checklist

Where the online shop stands, and what is left to do. **Work through the phases in
order** — the live domain has to land before Stripe and Google Merchant Center, or
both get set up twice.

Shareable version (tickable, same content):
<https://claude.ai/code/artifact/e51d4cd8-d5ef-441b-8297-92effb1762a7>

Tasks are tagged **[Steve]** (server / code) or **[G&M]** (a decision or job only
the client can do).

---

## State as at 2026-10-29

| | |
| --- | --- |
| ✅ | Live on **gmautospares.co.nz** with SSL; cron and all four scheduled jobs running |
| ✅ | Freight priced by NZ Couriers box size — checkout, Google feed and Shipping Policy all agree |
| ✅ | Merchant Center verified and fetching the feed: 15 parts in |
| ✅ | All 19 listed parts sized: 15 buyable online, 4 engine conversions by truck (enquiry) |
| ⛔ | **No Stripe yet** — G&M are verifying the account; checkout asks people to phone |
| ✅ | One address: www, plain http and `gm.websitemaster.co.nz` all 301 to `https://gmautospares.co.nz/` |
| ✅ | `robots.txt` and `sitemap.xml` live; Google gets each part's real condition (all 15 listed are new stock) |

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

- [x] **[G&M]** Confirm the domain, and who controls its DNS
- [x] **[Steve]** DNS A record → SiteHost container IP; wait for propagation
- [x] **[Steve]** Add the domain to `server_name` in
      `~/container/config/nginx/sites-available/default`, then restart the container.
      Leave `root` as `/container/application/public`.
- [x] **[Steve]** Issue the SSL certificate via the SiteHost panel. Stripe and
      Merchant Center both require HTTPS.
- [x] **[Steve]** Update `APP_URL` and rebuild config:
      ```bash
      cd ~/container/application
      nano .env          # APP_URL=https://yourdomain.co.nz
      php artisan config:cache
      php artisan cache:clear
      ```
- [x] **[Steve]** Verify the feed picked it up — every `link` and `image_link` must be
      the live https domain, not `/storage/…` and not the old host:
      ```bash
      curl -s https://yourdomain.co.nz/feeds/google.xml | head -20
      ```
- [x] **[Steve]** ⚠️ **Send every other address to one.** www, plain http and the old
      host all serve the full site — four copies of every page to Google, and a
      checkout page that loads over plain http. Every page now carries a canonical
      tag; to redirect as well, add to `.env` and rebuild config:
      ```bash
      REDIRECT_HOSTS=www.gmautospares.co.nz,gm.websitemaster.co.nz
      php artisan config:cache
      ```
      Then check: `curl -sI https://www.gmautospares.co.nz/` and
      `curl -sI http://gmautospares.co.nz/` should both answer `301` with
      `location: https://gmautospares.co.nz/`. If the http one still answers `200`,
      SiteHost's proxy is not sending `X-Forwarded-Proto` — the host redirects still
      work; http → https then needs doing in nginx. To undo, remove the line and
      `config:cache`. Merchant Center's website is set to the **www** address: if it
      complains once www redirects, change it there to `https://gmautospares.co.nz/`.
      *Live 2026-10-29: SiteHost's proxy does send `X-Forwarded-Proto`, so http → https
      works from the app.*
- [x] **[Steve]** Before deploying the `sales@` change: in SiteHost, forward
      `sales@gmautospares.co.nz` to G&M's inbox, then send a test from a Gmail address
      and check it arrives (and not in spam). The site shows `sales@` and customer
      emails reply to it, so it must work first. Leave `ADMIN_EMAIL` pointing straight
      at the inbox they read — orders should not depend on a forward
      *Forwarder set up 2026-10-31.*
- [x] **[Steve]** Now that http redirects, make the session cookie https-only: add
      `SESSION_SECURE_COOKIE=true` to `.env`, then `php artisan config:cache`

## Phase 2 — Finish the stock setup

- [ ] **[Steve]** Put cron logging back to `/dev/null` in
      `~/container/crontabs/crontab` and delete `storage/logs/cron.log`
      (it grows ~1 MB/day otherwise)
- [x] **[Steve]** Deploy the freight-size change and run `php artisan migrate --force`.
      Small parts become E20 satchels; **Medium and Large parts lose their band and
      drop out of the shop and Google until measured** — there is no honest way to
      convert them. The same migrate rewrites the Shipping Policy to match checkout,
      unless it was edited in the admin (then it logs a warning — check the page shows
      the freight table).
- [x] **[Steve]** Size the obvious ones — satchel parts and truck freight. Safe to
      re-run, only touches parts with no size, never overwrites a correction:
      ```bash
      cd ~/container/application
      php artisan parts:guess-shipping-bands
      php artisan cache:clear
      ```
- [x] **[G&M]** Measure the rest: Parts Loader app → **Manage Stock → Needs freight
      size**. Tap **Freight** on a part, type the box's L × W × H and weight, and it
      picks the box. A size that is too small does not error — it quietly loses money
      on every sale.
- [ ] **[G&M]** Load and price the rest of the stock. Only 4 parts are live, and a part
      with no price cannot be sold online *or* listed on Google. Parts loaded from
      here get a freight size in the Parts Loader app, so this backlog is a one-off.
- [ ] **[G&M]** Enter prices **including GST** from now on — the site, receipt and
      Google all treat a price as what the customer pays.
- [ ] **[Steve]** Once G&M say go, add GST to the prices loaded without it (rounded
      up to the dollar). `--before` is the day they switched. **Probably not needed:**
      on 2026-10-29, 11 of the 19 live prices were already a round figure plus GST —
      check the dry run before applying anything:
      ```bash
      php artisan parts:add-gst --before=YYYY-MM-DD --dry-run
      php artisan parts:add-gst --before=YYYY-MM-DD
      ```

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
      The shipping policy was rewritten to match checkout on 2026-10-29; the returns
      policy decisions are still open.
- [x] **[Steve]** Create Merchant Center, verify and claim the domain
- [x] **[Steve]** Add the feed as a scheduled fetch — **Products → Data sources → Add**.
      Currency **NZD**, country **New Zealand**, daily.
      `https://yourdomain.co.nz/feeds/google.xml`
- [ ] **[Steve]** Work the **Diagnostics** tab. Expect disapprovals on the first fetch;
      item-level errors there are more specific than any documentation.
- [ ] **[Steve]** Run a live part URL through the
      [Rich Results Test](https://search.google.com/test/rich-results) — works
      independently of Merchant Center. The markup itself was checked on 2026-10-29:
      name, price, availability and condition are there; it leaves out the optional
      shipping and returns details, so expect warnings for those
- [ ] **[G&M]** Free listings only, or Shopping ads too? Free listings need no ad
      spend. Start free.

## Phase 5 — Worth doing once live

- [ ] **[Steve/G&M]** Check the first few orders' freight against the courier
      invoices. Rates are from G&M's own eDespatchIT screen (October 2026), so they
      should match. When the variable adjustment moves, set `NZC_VARIABLE_ADJUSTMENT`
      in `.env` and `config:cache`
- [ ] **[Steve]** Consider GoSweetSpot (NZ Couriers' integration API): create the
      consignment and label straight from a paid order, and optionally quote the
      exact address at checkout. Needs every part's box size, which this freight-size
      work provides. Google still needs the fixed table.
- [x] **[Steve]** Add a `sitemap.xml` and `robots.txt`. Both are generated:
      robots.txt keeps crawlers out of the Parts Loader, admin, cart and checkout, and
      the sitemap lists every part still for sale, the vehicles and the main pages.
- [ ] **[Steve]** Submit `https://gmautospares.co.nz/sitemap.xml` in Google Search
      Console (**Sitemaps**), so new stock is found as it loads
- [ ] **[G&M]** Consider Google **free local listings** — G&M has a physical yard, so
      parts can show in Search and Maps as in-store stock. Separate feed, no checkout
      required, no ad spend.
- [x] **[Steve]** Turn on reCAPTCHA: create a v3 key (see DEPLOY-SITEHOST.md for
      the domains), set `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY`, then
      `config:cache`. Send the contact form once and check it arrives; check
      `laravel.log` for `reCAPTCHA` lines over the first week
      *Live 2026-10-31: on all five forms, and a post without a token is turned away.*
- [x] **[Steve]** ⚠️ Change the database password. A real-looking one was committed in
      `.env.sitehost.example` (and the deploy zip) and is still in git history.
      Set a new one in the SiteHost panel, then straight away update `DB_PASSWORD` in
      `.env` and `php artisan config:cache` — the site cannot reach the database in
      between, so do it at a quiet time. Then load a part page and `/admin` to check
      *Changed 2026-10-31.*
- [ ] **[Steve]** Check `MINIAPP_PIN` on the server is at least 6 digits
- [x] **[Steve]** Deploy the Laravel 13 upgrade. The code is done and tested: Laravel
      13.39, Filament 3.3.57, Livewire 3.8.11, and `composer audit` is clean — on
      Laravel 10, which no longer gets security fixes, it listed ~50 advisories.
      Laravel 13 needs PHP 8.3+. Every package changes, so put the site in maintenance
      mode for the minute it takes:
      ```bash
      cd ~/container/application
      php -v                          # must say 8.3 or higher: stop here if not
      php artisan down
      git pull origin main
      composer install --no-dev -o
      php artisan optimize:clear
      php artisan config:cache
      php artisan view:cache
      php artisan up
      ```
      If `composer install` fails, go back to Laravel 10: `git reset --hard 1ab9d95`,
      then the same `composer install`, cache and `up` lines. Afterwards log in to
      `/admin` once (Laravel re-hashes the password to its stronger setting by
      itself) and send a test contact form
      *Live 2026-10-31.*
- [x] **[Steve]** Check no admin password is stored as plain text. From 10 June until
      `1ab9d95`, `gm:create-admin` saved passwords as typed, because Laravel 10 never
      applied the model's hashing. On the server:
      ```bash
      php artisan tinker --execute='foreach (App\Models\User::all() as $u) echo $u->email, " ", str_starts_with($u->password, "$2y$") ? "ok" : "PLAIN TEXT", PHP_EOL;'
      ```
      A `PLAIN TEXT` account cannot log in, and its password was readable in the
      database: delete it and re-create it with `php artisan gm:create-admin`
      *Checked 2026-10-31: both admin accounts are hashed.*
- [x] **[Steve]** Once Laravel 13 is live, rename the CSRF middleware the app extends
      from `VerifyCsrfToken` to `PreventRequestForgery`, Laravel 13's name (the old one
      is a deprecated alias). Left until then so the upgrade deploy works on both versions
      *Done 2026-10-31; goes live with the next deploy, nothing to do on the server.*
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
| Rural delivery surcharge | +$6.50 per part — per part confirmed, **amount is a placeholder** | The figure from the booking page |
| Freight to the far ends | Paraparaumu and Christchurch rates used everywhere | Check one box to e.g. Kaitaia, Gisborne and Invercargill isn't dearer |
| Existing part prices | Loaded without GST | Go-ahead to add GST to all of them |
| Free-freight threshold | Off | Decide if wanted |

Settled with G&M, 2026-10-29: satchels are $12 / $28 **plus GST** with fuel included
($14 / $33 to the customer); an E60 takes about 0.009 m³ and 15 kg, so it sits below
the 0.015 m³ box; parts packed together for less get the difference refunded; rural
is per item; truck freight is always quoted before the customer pays. The 0.025 vs
0.03 m³ North Island rate looks like NZ Couriers' mistake — G&M will follow it up
gently, and it is used as charged until then.

### How freight is charged

Each part has a **freight size** — the NZ Couriers satchel or box it goes in, the
same list as eDespatchIT. The price is the courier's cost from G&M's own eDespatchIT
screen, plus the variable adjustment, plus GST, rounded up to the dollar. North
Island is priced to Paraparaumu (long haul), South Island to Christchurch.

| Example | North Island | South Island |
| --- | ---: | ---: |
| Door handle, sensor (E20 satchel) | $14 | $14 |
| Vacuum pump (E60 — North goes as a 0.015 m³ box) | $17 | $33 |
| 0.05 m³ box | $38 | $112 |
| Big headlight (0.125 m³ box) | $94 | $280 |

A basket pays **each part's freight**, because NZ Couriers charge per item even on
one consignment. If the yard can pack parts together for less, it refunds the
difference.

Bumpers, doors, gearboxes, engines, nosecuts and panels go by **truck** and are
sized *Truck / quote*. They are not sold online and not sent to Google; their pages
offer *Reserve for collection*, and point anyone wanting delivery to the enquiry
form, so G&M can quote the freight before they pay.

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
