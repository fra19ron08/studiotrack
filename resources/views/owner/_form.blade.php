@php
  /** @var \App\Models\Studio|null $studio */
  $studio = $studio ?? null;

  // Equipments (array) -> render in textarea come righe
  $equipmentsOld = old('equipments');
  $equipmentsVal = $equipmentsOld !== null
      ? $equipmentsOld
      : ($studio?->equipments ?? []);

  if (is_string($equipmentsVal)) {
      // se arriva già come stringa, lo lasciamo
  } else {
      $equipmentsVal = implode("\n", array_filter($equipmentsVal));
  }

  // Available slots (array) -> textarea JSON
  $slotsOld = old('available_slots');
  $slotsVal = $slotsOld !== null ? $slotsOld : ($studio?->available_slots ?? []);

  if (!is_string($slotsVal)) {
      $slotsVal = json_encode($slotsVal, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  }

  // is_active checkbox default true in create
  $isActive = old('is_active', $studio?->is_active ?? true);
@endphp

<div class="space-y-5">
  {{-- Nome --}}
  <div>
    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nome studio</label>
    <input name="name"
           value="{{ old('name', $studio->name ?? '') }}"
           class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
    @error('name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
  </div>

  {{-- Città + Indirizzo --}}
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    <div class="md:col-span-4">
      <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Città</label>
      <input name="city"
             value="{{ old('city', $studio->city ?? '') }}"
             placeholder="es. milano"
             class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      @error('city') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-8">
      <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Indirizzo</label>
      <input name="address"
             value="{{ old('address', $studio->address ?? '') }}"
             placeholder="Via..., CAP, città"
             class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      @error('address') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
  </div>

  {{-- Lat/Lng --}}
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    <div class="md:col-span-6">
      <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Latitudine</label>
      <input name="lat" inputmode="decimal"
             value="{{ old('lat', $studio->lat ?? '') }}"
             placeholder="es. 45.4642"
             class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      @error('lat') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-6">
      <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Longitudine</label>
      <input name="lng" inputmode="decimal"
             value="{{ old('lng', $studio->lng ?? '') }}"
             placeholder="es. 9.1900"
             class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
      @error('lng') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
    </div>
  </div>

  {{-- Prezzo --}}
  <div>
    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Prezzo per ora (€)</label>
    <input type="number" name="price_per_hour" min="0" step="1"
           value="{{ old('price_per_hour', $studio->price_per_hour ?? '') }}"
           class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400" />
    @error('price_per_hour') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
  </div>

  {{-- Cover image --}}
  <div>
    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Foto copertina</label>

    @if(!empty($studio?->cover_image_path))
      <img src="{{ asset('storage/'.$studio->cover_image_path) }}"
           alt="Cover {{ $studio->name }}"
           class="mt-2 h-44 w-full rounded-xl object-cover border border-slate-200 dark:border-slate-800" />
    @endif

    <input type="file" name="cover_image" accept="image/*"
           class="mt-2 block w-full text-sm text-slate-700 dark:text-slate-200" />
    @error('cover_image') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror

    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
      Suggerito: JPG/PNG, max ~4MB.
    </p>
  </div>

  {{-- Equipments: modalità semplice (una riga per elemento) --}}
  <div>
    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Equipments (1 per riga)</label>
    <textarea name="equipments" rows="5"
              class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400"
              placeholder="Neumann U87&#10;SSL Console&#10;Batteria Yamaha">{{ $equipmentsVal }}</textarea>
    @error('equipments') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror

    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
      (Nel controller converti le righe in array con explode/newline.)
    </p>
  </div>

  {{-- Available slots: JSON --}}
  <div>
    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Available slots (JSON)</label>
    <textarea name="available_slots" rows="8"
              class="mt-1 w-full font-mono text-sm rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400"
              placeholder='[{"start":"2026-02-18 10:00","end":"12:00"}]'>{{ $slotsVal }}</textarea>
    @error('available_slots') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
  </div>

  {{-- Descrizione --}}
  <div>
    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Descrizione</label>
    <textarea name="description" rows="5"
              class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-slate-400"
              placeholder="Scrivi una descrizione...">{{ old('description', $studio->description ?? '') }}</textarea>
    @error('description') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
  </div>

  {{-- Attivo --}}
  <div class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}
           class="rounded border-slate-300 dark:border-slate-700" />
    <label class="text-sm text-slate-700 dark:text-slate-200">Studio attivo (visibile ai clienti)</label>
  </div>
</div>
