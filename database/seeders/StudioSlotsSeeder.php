<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StudioSlotsSeeder extends Seeder
{
    public function run(): void
    {
        $slotMinutes = 60;
        $days = 30;

        $startHour = 10;
        $endHour = 22; // ultimo start: 21:00 se slot 60 min

        $studios = DB::table('studios')->select('id', 'price_per_hour')->get();

        foreach ($studios as $studio) {
            for ($d = 0; $d < $days; $d++) {
                $day = Carbon::today()->addDays($d);

                for ($h = $startHour; $h <= ($endHour - ($slotMinutes / 60)); $h++) {
                    $start = $day->copy()->setTime($h, 0);
                    $end = $start->copy()->addMinutes($slotMinutes);

                    DB::table('studio_slots')->updateOrInsert(
                        [
                            'studio_id' => $studio->id,
                            'start_at' => $start,
                        ],
                        [
                            'end_at' => $end,
                            'price_cents' => (int) round(((float) $studio->price_per_hour) * 100),
                            'status' => 'available',
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
