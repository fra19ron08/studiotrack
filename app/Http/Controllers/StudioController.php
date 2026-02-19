<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StudioController extends Controller
{
public function index()
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $studios = Auth::user()->studios;
    return view('dashboard.proprietario', compact('studios'));
}



    public function create()
    {
        return view('owner.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'price_per_hour' => 'required|numeric|min:10|max:500',
            'description' => 'nullable|string',

            // textarea semplice, NON json
            'equipments' => 'nullable|string',

            'is_active' => 'sometimes|boolean',
            'cover_image_path' => 'nullable|image|max:2048',
        ]);

        // equipments: una riga = un elemento
        $equipments = collect(preg_split("/\r\n|\n|\r/", $request->input('equipments', '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();

        // se la colonna è JSON/TEXT, salviamo JSON string
        $validated['equipments'] = json_encode($equipments);

        // geocoding (per lat/lng NOT NULL)
        $geo = Http::withHeaders([
                'User-Agent' => 'StudioTrack/1.0 (dev)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $validated['address'] . ', ' . ($validated['city'] ?? ''),
                'format' => 'json',
                'limit' => 1
            ])->json(0) ?? null;

        $validated['user_id'] = Auth::id();
        $validated['lat'] = isset($geo['lat']) ? (float) $geo['lat'] : 45.4642;
        $validated['lng'] = isset($geo['lon']) ? (float) $geo['lon'] : 9.1900;
// available_slots: se non lo gestisci ancora con StudioSlot, metti default vuoto
$validated['available_slots'] = json_encode([]);

        if ($request->hasFile('cover_image_path')) {
            $validated['cover_image_path'] = $request->file('cover_image_path')
                ->store('studios/covers', 'public');
        }

        Studio::create($validated);

        return redirect()->route('owner.studios.index')->with('success', 'Studio creato!');
    }

    public function show(Studio $studio)
    {
        $this->authorize('owner', $studio);
        return view('owner.studios.show', compact('studio'));
    }

    public function edit(Studio $studio)
    {
        $this->authorize('owner', $studio);
        return view('owner.studios.edit', compact('studio'));
    }

    public function update(Request $request, Studio $studio)
    {
        $this->authorize('owner', $studio);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'price_per_hour' => 'required|numeric|min:10|max:500',
            'description' => 'nullable|string',
            'equipments' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'cover_image_path' => 'nullable|image|max:2048',
        ]);

        $equipments = collect(preg_split("/\r\n|\n|\r/", $request->input('equipments', '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();

        $validated['equipments'] = json_encode($equipments);

        if ($request->hasFile('cover_image_path')) {
            if ($studio->cover_image_path) {
                Storage::disk('public')->delete($studio->cover_image_path);
            }

            $validated['cover_image_path'] = $request->file('cover_image_path')
                ->store('studios/covers', 'public');
        }

        $studio->update($validated);

        return redirect()->route('owner.studios.index')->with('success', 'Studio aggiornato!');
    }

    public function destroy(Studio $studio)
    {
        $this->authorize('owner', $studio);

        if ($studio->cover_image_path) {
            Storage::disk('public')->delete($studio->cover_image_path);
        }

        $studio->delete();

        return redirect()->route('owner.studios.index')->with('success', 'Studio eliminato!');
    }
}
