@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">Se non sai</h1>
      <p class="mt-1 text-slate-600 dark:text-slate-300">Cerca per zona, data e budget. Prenoti in pochi click.</p>
    </div>

    <a href="{{ route('dashboard') }}"
       class="hidden sm:inline-flex rounded-xl border border-slate-200 dark:border-slate-800 px-4 py-2 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
      Dashboard
    </a>
  </div>

  {{-- Search bar --}}
  <form method="GET" action="{{ route('client.studios.results') }}"
        class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 p-4 shadow-sm">

    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      {{-- Dove --}}
      <div class="md:col-span-5">
        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Dove</label>
        <input name="city" value="{{ request('city') }}"
               placeholder="Milano, Roma, zona, indirizzo…"
               class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      </div>

      {{-- Quando --}}
      <div class="md:col-span-3">
        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Quando</label>
        <input type="date" name="date" value="{{ request('date') }}"
               class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      </div>

      {{-- Prezzo --}}
      <div class="md:col-span-2">
        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Min €/h</label>
        <input type="number" name="min" value="{{ request('min') }}" min="0" step="1"
               class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      </div>

      <div class="md:col-span-2">
        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Max €/h</label>
        <input type="number" name="max" value="{{ request('max') }}" min="0" step="1"
               class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      </div>

      {{-- CTA --}}
      <div class="md:col-span-12 flex flex-col sm:flex-row gap-2">
        <button class="rounded-xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">
          Cerca
        </button>

        <a href="{{ route('client.studios.index') }}"
           class="rounded-xl border border-slate-200 dark:border-slate-800 px-6 py-3 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition text-center">
          Reset
        </a>
      </div>
    </div>

    {{-- Chips filtri attivi --}}
    @php
      $chips = [];
      if (request('city')) $chips[] = request('city');
      if (request('date')) $chips[] = 'Data ' . request('date');
      if (request('min'))  $chips[] = 'Min €' . request('min') . '/h';
      if (request('max'))  $chips[] = 'Max €' . request('max') . '/h';
    @endphp

    @if(count($chips))
      <div class="mt-3 flex flex-wrap gap-2">
        @foreach($chips as $c)
          <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-800 px-3 py-1 text-sm text-slate-700 dark:text-slate-200">
            {{ $c }}
          </span>
        @endforeach
      </div>
    @endif
  </form>

  {{-- Map (solo “preview” sulla pagina ricerca) --}}
  <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-white dark:bg-slate-950">
    <div id="map" class="w-full h-80"></div>
  </div>

  {{-- Hint --}}
  <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-300">
    Inserisci i filtri e premi <span class="font-semibold text-slate-900 dark:text-white">Cerca</span> per vedere i risultati nella pagina dedicata.
  </div>
</div>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const map = L.map('map').setView([45.4642, 9.19], 12);

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endsection
