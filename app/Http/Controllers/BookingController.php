<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\StudioSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
public function store(Request $request, int $slotId)
{
    $userId = $request->user()->id;

    $booking = DB::transaction(function () use ($slotId, $userId) {
        $slot = StudioSlot::whereKey($slotId)->lockForUpdate()->firstOrFail();

        if ($slot->status !== 'available') {
            abort(409, 'Slot non disponibile');
        }

        $booking = Booking::create([
            'user_id' => $userId,
            'studio_id' => $slot->studio_id,
            'slot_id' => $slot->id,
            'status' => 'confirmed',
            'total_cents' => $slot->price_cents,
        ]);

        $slot->update(['status' => 'reserved']);

        return $booking;
    });

    return redirect()
        ->route('client.bookings.show', $booking->id)
        ->with('status', 'Prenotazione confermata!');
}

}
