<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the yard: this one is paid and needs picking and packing. */
class OrderReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PAID online order ' . $this->order->reference . ' — $' . number_format((float) $this->order->total, 2),
            replyTo: [$this->order->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-received',
            with: ['url' => route('app.orders')],
        );
    }
}
