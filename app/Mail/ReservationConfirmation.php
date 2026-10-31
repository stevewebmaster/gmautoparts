<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the customer. This is their proof of order, so it carries the
 * reference, the price held and the collection deadline.
 */
class ReservationConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function envelope(): Envelope
    {
        // "Reply to this email" has to reach someone: mail goes out as noreply@.
        return new Envelope(
            subject: 'Your part is reserved — ' . $this->reservation->reference,
            replyTo: [config('mail.contact_address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation-confirmation',
            with: [
                'url' => route('reservations.show', $this->reservation->reference),
            ],
        );
    }
}
