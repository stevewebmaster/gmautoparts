<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Stripe hosted Checkout.
 *
 * Card details never touch this site — the customer is redirected to a
 * Stripe-hosted page and comes back. That keeps PCI scope to the minimum
 * (SAQ A) and means no card data is ever stored or logged here.
 */
class StripeCheckout
{
    public function configured(): bool
    {
        return filled(config('services.stripe.secret'));
    }

    protected function client(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Creates the Checkout session for an order and returns the URL to send the
     * customer to. Amounts are sent in cents, taken from what the order already
     * recorded — never recalculated here, so what is charged is exactly what the
     * customer was shown.
     */
    public function createSession(Order $order): Session
    {
        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'quantity' => $item->quantity,
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'unit_amount' => (int) round((float) $item->price * 100),
                    'product_data' => [
                        'name' => $item->title,
                    ],
                ],
            ];
        }

        if ((float) $order->shipping > 0) {
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'unit_amount' => (int) round((float) $order->shipping * 100),
                    'product_data' => [
                        'name' => 'Freight' . ($order->is_rural ? ' (rural delivery)' : ''),
                    ],
                ],
            ];
        }

        return $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'customer_email' => $order->email,
            'client_reference_id' => $order->reference,
            'metadata' => [
                'order_reference' => $order->reference,
                'order_id' => (string) $order->id,
            ],
            'success_url' => route('checkout.success', $order->reference) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel', $order->reference),
            // Stripe expires the session too, so an abandoned checkout cannot be
            // paid long after we have released the parts back on sale.
            'expires_at' => now()->addMinutes(Order::PENDING_HOLD_MINUTES)->timestamp,
        ]);
    }

    public function retrieveSession(string $sessionId): Session
    {
        return $this->client()->checkout->sessions->retrieve($sessionId);
    }

    /**
     * Verifies a webhook came from Stripe. Without this anyone could POST a
     * "payment succeeded" event and get free parts.
     */
    public function verifyWebhook(string $payload, ?string $signature): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            (string) $signature,
            (string) config('services.stripe.webhook_secret'),
        );
    }
}
