<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;

class ClientStudioController extends Controller
{
    // Pagina con la barra di ricerca (senza risultati)
    public function searchForm(Request $request)
    {
        // se vuoi precompilare i campi nel form:
        $q    = trim($request->query('q', ''));
        $city = trim($request->query('city', ''));
        $date = trim($request->query('date', ''));
        $min  = $request->query('min');
        $max  = $request->query('max');

        return view('client.studios.search', compact('q', 'city', 'date', 'min', 'max'));
    }

    // Redirect dal form (opzionale se il form punta già alla route results)
    public function search(Request $request)
    {
        return redirect()->route('client.studios.results', $request->only(['q', 'city', 'date', 'min', 'max']));
    }

    // Pagina risultati
    public function results(Request $request)
    {
        $q    = trim($request->query('q', ''));
        $city = trim($request->query('city', ''));
        $date = trim($request->query('date', ''));

        $min = $request->query('min');
        $max = $request->query('max');
        $min = is_numeric($min) ? (int) $min : null;
        $max = is_numeric($max) ? (int) $max : null;

        $studios = Studio::query()
            ->where('is_active', 1)
            ->when($city !== '', fn ($qr) => $qr->where('city', 'like', "%{$city}%"))
            ->when($q !== '', fn ($qr) => $qr->where('name', 'like', "%{$q}%"))
            ->when($min !== null && $max !== null, fn ($qr) => $qr->whereBetween('price_per_hour', [$min, $max]))
            ->when($min !== null && $max === null, fn ($qr) => $qr->where('price_per_hour', '>=', $min))
            ->when($max !== null && $min === null, fn ($qr) => $qr->where('price_per_hour', '<=', $max))
            ->when($date !== '', function ($qr) use ($date) {
                try {
                    $start = now()->parse($date)->startOfDay();
                } catch (\Throwable $e) {
                    return;
                }
                $end = (clone $start)->endOfDay();

                $qr->whereHas('slots', function ($s) use ($start, $end) {
                    $s->where('status', 'available')
                      ->whereBetween('start_at', [$start, $end]);
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('client.studios.results', compact('studios', 'q', 'city', 'min', 'max', 'date'));
    }

    public function show(Studio $studio, Request $request)
    {
        $date = trim($request->query('date', ''));

        try {
            $dayStart = $date !== '' ? now()->parse($date)->startOfDay() : now()->startOfDay();
        } catch (\Throwable $e) {
            $dayStart = now()->startOfDay();
            $date = '';
        }

        $dayEnd = (clone $dayStart)->endOfDay();

        $slots = $studio->slots()
            ->where('status', 'available')
            ->whereBetween('start_at', [$dayStart, $dayEnd])
            ->orderBy('start_at')
            ->get();

        return view('client.studios.show', compact('studio', 'slots', 'date'));
    }
    public function index(Request $request)
{
    $q    = trim($request->query('q', ''));
    $city = trim($request->query('city', ''));
    $date = trim($request->query('date', ''));
    $min  = $request->query('min');
    $max  = $request->query('max');

    return view('client.studios.index', compact('q','city','date','min','max'));
}

}
