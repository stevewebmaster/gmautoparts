<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmation;
use App\Mail\ReservationReceived;
use App\Models\Part;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function store(Request $request, Part $part): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
        ]);

        // Re-check inside a transaction with the row locked: two people can hit
        // Reserve on the same part within the same second.
        try {
            $reservation = DB::transaction(function () use ($part, $validated) {
                $fresh = Part::whereKey($part->id)->lockForUpdate()->first();

                if (! $fresh || ! $fresh->isReservable()) {
                    return null;
                }

                return Reservation::reserve($fresh, $validated);
            });
        } catch (\Throwable $e) {
            Log::error('Reservation failed', ['part_id' => $part->id, 'exception' => $e]);

            return back()->withErrors([
                'reserve' => 'Sorry, something went wrong reserving that part. Please call us on 07 849 8814.',
            ])->withInput();
        }

        if (! $reservation) {
            return back()->withErrors([
                'reserve' => 'Sorry, that part has just been taken. Please get in touch and we will look out for another.',
            ])->withInput();
        }

        $this->sendEmails($reservation);

        return redirect()->route('reservations.show', $reservation->reference);
    }

    public function show(string $reference): View
    {
        $reservation = Reservation::where('reference', $reference)->firstOrFail();

        return view('reservations.show', ['reservation' => $reservation]);
    }

    /**
     * Queued, so a slow or unreachable mail server never blocks the customer's
     * confirmation page. A failure here must not lose the reservation — it is
     * already committed.
     */
    protected function sendEmails(Reservation $reservation): void
    {
        $adminEmail = config('mail.from.address', env('ADMIN_EMAIL', 'admin@example.com'));

        try {
            Mail::to($reservation->email)->queue(new ReservationConfirmation($reservation));
            Mail::to($adminEmail)->queue(new ReservationReceived($reservation));
        } catch (\Throwable $e) {
            Log::error('Reservation emails could not be queued', [
                'reference' => $reservation->reference,
                'exception' => $e,
            ]);
        }
    }
}
