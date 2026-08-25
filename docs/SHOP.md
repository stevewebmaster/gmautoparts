# Online shop — cart, checkout and Stripe

The site sells parts online: multi-item cart, card payment via Stripe hosted
Checkout, and either free collection from Te Awamutu or couriered delivery.

---

## What can be sold online

A part is purchasable only if **all** of these hold (`Part::isPurchasable()`):

| Requirement | Why |
| --- | --- |
| visible, status `available` | Obvious, and stops double-selling |
| has a price | Nothing to charge otherwise |
| has a **shipping band** that is not `quote_only` | Freight cannot be priced without it |

Anything failing the last two falls back to the existing **reserve for
collection** flow, so nothing is lost — it just cannot be bought outright.

### Shipping bands

Set per part, in the Parts Loader app when loading, or in Filament (including a
**bulk "Set shipping band" action** for working through existing stock).

| Band | Meaning |
| --- | --- |
| Small | Fits a courier bag |
| Medium | Boxed, one person lifts it |
| Large | Bulky, still couriable |
| Quote only | Engines, doors, panels — **not sold online** |

> **Existing parts have no band and are therefore not sold online until banded.**
> That is deliberate: the alternative was defaulting everything to a guessed band
> and silently under-charging freight on every large item. Use the Filament
> filter "Not set — not sold online" to work through the backlog.

Rates live in [`config/shipping.php`](../config/shipping.php) — **review them
against real courier invoices**, they are estimates.

### Freight is charged once, at the highest band

A cart with a small, a medium and a large part is charged the **large** rate
once, not the sum of all three. Several parts normally travel as one
consignment, so summing would overcharge badly. Rural adds a flat surcharge.

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
