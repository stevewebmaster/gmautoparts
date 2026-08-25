<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the yard so someone knows to put the part aside. */
class ReservationReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New reservation ' . $this->reservation->reference . ' — ' . $this->reservation->part_title,
            replyTo: [$this->reservation->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation-received',
            with: [
                'url' => route('app.reservations'),
            ],
        );
    }
}
