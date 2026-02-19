<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class ClientBookingController extends Controller
{
    public function show(Request $request, Booking $booking)
    {
        // sicurezza: un cliente vede solo le sue prenotazioni
        abort_unless($booking->user_id === $request->user()->id, 403);

        return view('client.bookings.show', compact('booking'));
    }
}
