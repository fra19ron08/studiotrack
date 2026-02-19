@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">Studi trovati</h1>
      <p class="mt-1 text-slate-600 dark:text-slate-300">
        Risultati: <span class="font-semibold text-slate-900 dark:text-white">{{ $studios->total() }}</span>
        — Pagina {{ $studios->currentPage() }} / {{ $studios->lastPage() }}
      </p>
    </div>

    <a href="{{ route('client.studios.index') }}"
       class="rounded-xl border border-slate-200 dark:border-slate-800 px-4 py-2 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
      Modifica ricerca
    </a>
  </div>

  {{-- Cards --}}
  <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($studios as $studio)
    
      <a href="{{ route('client.studios.show', $studio) }}"
         class="group rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950 hover:border-slate-300 dark:hover:border-slate-700 transition">
         @php
  $img = $studio->cover_image_path
      ? asset('storage/' . ltrim($studio->cover_image_path, '/'))
      : null;
@endphp

<div class="mb-4 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 aspect-[16/9]">
  @if($img)
    <img src="{{ $img }}" alt="{{ $studio->name }}" class="h-full w-full object-cover">
  @else
    <div class="h-full w-full flex items-center justify-center text-sm text-slate-500 dark:text-slate-400">
      cover spacchiosa
    </div>
  @endif
</div>

        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="text-lg font-semibold text-slate-900 dark:text-white truncate">
              {{ $studio->name }}
            </div>
            <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
              <span class="line-clamp-2">{{ $studio->address }}</span>
            </div>
          </div>

          <div class="shrink-0 text-right">
            <div class="text-sm text-slate-500 dark:text-slate-400">da</div>
            <div class="text-lg font-semibold text-slate-900 dark:text-white">
              €{{ number_format((float)($studio->price_per_hour ?? 0), 0) }}/h
            </div>
          </div>
        </div>

        <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
          <span class="px-2 py-1 rounded-full border border-slate-200 dark:border-slate-800">Verificato</span>
          <span class="px-2 py-1 rounded-full border border-slate-200 dark:border-slate-800">100mq</span>
          @if(!empty($studio->city))
            <span class="ml-auto">{{ $studio->city }}</span>
          @endif
        </div>

        <div class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:underline">
          Vedi disponibilità →
        </div>
      </a>
    @empty
      <div class="col-span-full rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-300">
        Nessun risultato con questi filtri.
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="mt-8">
    {{ $studios->links() }}
  </div>
</div>
@endsection
