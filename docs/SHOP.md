# Online shop — cart, checkout and Stripe

> **Outstanding tasks and client decisions:** [LAUNCH-CHECKLIST.md](LAUNCH-CHECKLIST.md)

The site sells parts online: multi-item cart, card payment via Stripe hosted
Checkout, and either free collection from Te Awamutu or couriered delivery.

---

## What can be sold online

A part is purchasable only if **all** of these hold (`Part::isPurchasable()`):

| Requirement | Why |
| --- | --- |
| visible, status `available` | Obvious, and stops double-selling |
| has a price | Nothing to charge otherwise |
| has a **freight size** that is not truck freight | Freight cannot be priced without it |

Anything failing the last two falls back to the existing **reserve for
collection** flow, so nothing is lost — it just cannot be bought outright.

### Freight sizes

Every part is given a **freight size**: the NZ Couriers satchel or box it goes in,
picked from the same list the yard uses in eDespatchIT when printing a label. The
price online is what that label costs, so the yard is not out of pocket and the
customer is not overcharged.

| Size | Use |
| --- | --- |
| E20 satchel | Door handles, sensors, small parts — one price anywhere |
| E60 satchel, up to 10 kg | Small boxed parts up to about 0.009 m³ (G&M tried boxes in one) |
| E60 satchel, 10–15 kg | The same bag. Separate because a part over 10 kg cannot go North as a 0.015 m³ box |
| 0.015 – 0.125 m³ box | Boxed parts, by the box's volume. 0.015 takes up to 10 kg, 0.025 up to 15 kg, 0.03 up to 20 kg, the rest up to 25 kg |
| Truck / quote | Bumpers, doors, gearboxes, engines — **not sold online**, reserve instead |

Set when loading in the Parts Loader app — which has a **measure the box** helper:
type L × W × H in cm and the weight, and it picks the smallest E60 or box that fits
(the E20 is picked by eye: nobody has measured one) — or
for existing stock under **Manage Stock → Needs freight size**. Also in Filament,
including a bulk **Set freight size** action.

> **A part with no freight size is not sold online and not sent to Google.**
> That is deliberate: guessing box sizes is what put headlights at $20 freight.

### How the price is worked out

[`config/shipping.php`](../config/shipping.php) holds the courier's **Cost**
column from eDespatchIT, as the screen shows it, for each box to each island:

- **North Island** is Paraparaumu (rated *long haul*) — the dearest North Island
  urban rate, so nowhere on the North Island is under-charged
- **South Island** is Christchurch (rated *inter-island*)

The customer pays cost **+ the variable adjustment** (18.4%, the fuel-linked
figure under the eDespatchIT total) **+ GST**, rounded up to the dollar.
Satchels are a flat price anywhere, entered before GST: their fuel charge is
already in the price, so only GST is added (E20 $12 → $14, E60 $28 → $33).

When the adjustment changes, set `NZC_VARIABLE_ADJUSTMENT` in `.env` and run
`php artisan config:cache` — checkout and Google both follow. No deploy needed.
When the box rates themselves change, update `courier_rates` and deploy.

**A part is charged the cheapest way to send it, from its own size upward.** A
part fits in anything bigger that takes its weight, and the courier rates are not
in size order: an E60 part goes North as a 0.015 m³ box ($17) but South in the
satchel ($33, against $38 for the box). That is the yard's own rule of thumb,
applied automatically. Weight is respected because under-ticketing is penalised:
a 10–15 kg E60 part skips the 10 kg box and goes North as a 0.03 m³ box instead.
The yard's order email and **Online Orders** in the Parts Loader app say what to
send each part as, so the label matches what the customer paid.

> The eDespatchIT screen has the North Island 0.03 m³ box ($16.77) cheaper than
> the 0.025 ($19.56). G&M think that is NZ Couriers' mistake, but it is the rate
> they are charged, so 0.025 m³ parts go North as 0.03. If NZ Couriers correct
> it, update `courier_rates` — the yard would otherwise be ticketing at a rate
> that no longer exists.

To print the current table:

```bash
php artisan tinker --execute='foreach (App\Enums\ShippingBand::cases() as $s) echo $s->shortLabel(), "  ", $s->isShippable() ? $s->ratesSummary() : "not sold online", PHP_EOL;'
```

### Each part is charged its own freight

NZ Couriers charge per item, even several items on one consignment, so a cart is
charged the **sum** of each part's freight. (This used to charge only the
biggest part, which lost the full freight on every other box — a pair of
headlights to Christchurch is two boxes.) When the yard can pack parts together
for less, G&M refund the difference by hand in Stripe; checkout and the shipping
policy say so.

Rural adds `rural_surcharge` **per part**, for the same reason: NZ Couriers need
a rural ticket on every item.

### The shipping policy quotes the same rates

The shipping policy page is edited in the admin, but its freight table is not
text: a `[freight-rates]` placeholder in the page is replaced with a table drawn
from `config/shipping.php`, so the policy always matches checkout and the Google
feed. Removing the placeholder in the admin removes the table.

### Sizing stock in bulk

```bash
php artisan parts:guess-shipping-bands --dry-run   # preview, saves nothing
php artisan parts:guess-shipping-bands             # apply
```

Only sizes what is safe to size from a part's name: small parts as an **E20
satchel**, and truck freight (bumpers, doors, bonnets, engines, gearboxes,
nosecuts…) as **Truck / quote**. Anything boxed is left for the yard to measure.
Keywords are matched against the subcategory and title, smallest first, so a
specific component beats a general assembly word ("Gearbox ECU" is a satchel,
not a gearbox), and anchored at a word start so "ecu" does not match the middle
of "nosecut".

**It only touches parts with no size at all**, so it is safe to re-run and will
never overwrite a correction made in the admin. `--force` re-sizes everything,
including corrections.

### Prices include GST

Every price on the site — parts, freight, the checkout total, the receipt — is
what the customer pays, **GST included**. That is what NZ consumer pricing expects,
and Google Shopping requires it for New Zealand. The receipt shows the GST inside
the total (3/23 of it).

G&M's first parts were loaded without GST. Once they confirm, add it in one go:

```bash
php artisan parts:add-gst --before=YYYY-MM-DD --dry-run   # preview
php artisan parts:add-gst --before=YYYY-MM-DD             # apply
```

`--before` is the day G&M started entering GST-inclusive prices; only parts
created before it are touched. Prices are rounded up to the dollar ($85 → $98).
It records every old and new price in `storage/app/parts-gst-added.json` and
refuses to run again while that file exists — running it twice would add GST
twice.

---

## Checkout flow

1. **Add to cart** — session-based, storing only part ids. Price, availability
   and band are re-read from the database on every access, so a cart left open
   overnight cannot check out at yesterday's price.
2. **Checkout** — customer details, pickup or delivery, address and island.
   Freight updates live on the page; the server recalculates it from the same
   table at submit and **never trusts the browser's number**.
3. **Order created** as `pending`, parts set to `on_hold`, inside a locking
   transaction that re-checks every part — closing the race where two people
   check out the same part at once.
4. **Stripe hosted Checkout** — the customer is redirected to Stripe. Card
   details never touch this site (PCI scope stays at SAQ A).
5. **Payment confirmed** two ways, whichever arrives first:
   - the **webhook** (`POST /stripe/webhook`) — the authority, and works even if
     the customer closes the tab
   - the **success page**, so it is truthful immediately

   `Order::markPaid()` is idempotent, so both firing is harmless.
6. Parts are marked **sold**; receipt to the customer and a pick-and-pack email
   to the yard, both queued.

Unpaid orders hold their parts for **one hour** (`Order::PENDING_HOLD_MINUTES`),
then `orders:release-stale` (scheduled every 10 minutes) puts them back on sale.
Releasing only touches a part still at `on_hold`, so a part sold in the yard
meanwhile is never resurrected.

---

## Configuration

```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Without `STRIPE_SECRET`, checkout refuses cleanly and tells the customer to
phone — it does not create orphan orders.

### Setting up the webhook (required)

The webhook is what actually marks orders paid. Without it, a customer who
closes the tab before returning leaves a paid order sitting at "awaiting
payment", and the sweep will release their parts.

1. Stripe Dashboard → **Developers → Webhooks → Add endpoint**
2. URL: `https://<your-domain>/stripe/webhook`
3. Events: `checkout.session.completed` and `checkout.session.expired`
4. Copy the signing secret into `STRIPE_WEBHOOK_SECRET`, then `php artisan config:cache`

The endpoint is CSRF-exempt (Stripe has no session) and instead verifies the
Stripe signature. An unsigned or forged request gets a 400.

### Testing before going live

Use test keys (`pk_test_`/`sk_test_`) and Stripe's test card `4242 4242 4242 4242`
with any future expiry and any CVC. Run `stripe listen --forward-to
localhost:8000/stripe/webhook` to exercise the webhook locally.

---

## Where orders are managed

- **Parts Loader app → Online Orders** — the yard view: what to pick, pack and
  send, with the delivery address and a Dispatched / Collected button.
- **Filament → Shop → Orders** — full detail, status, and the Stripe payment
  reference for issuing refunds (refunds are done in the Stripe dashboard, not
  here).

---

## Relationship to the Google feed

Only purchasable parts go in the Merchant feed — Google requires a working
checkout, so advertising a quote-only part would fail that. Quote-only and
unbanded parts stay on the site with the reserve flow, they are just not fed.
See [GOOGLE-SHOPPING.md](GOOGLE-SHOPPING.md).
