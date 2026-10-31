<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The customer's receipt. */
class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        // "Reply to this email" has to reach someone: mail goes out as noreply@.
        return new Envelope(
            subject: 'Order confirmed — ' . $this->order->reference,
            replyTo: [config('mail.contact_address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmation',
            with: ['url' => route('checkout.success', $this->order->reference)],
        );
    }
}
