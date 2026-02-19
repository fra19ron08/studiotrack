@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-4">
  <div class="col-span-12">
    <div class="flex items-start justify-between gap-4">
      <div>
        <p class="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-800 px-3 py-1 text-sm text-slate-600 dark:text-slate-300">
          StudioTrack • booking studi
        </p>
        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
          Ciao {{ auth()->user()->name }}, trova lo studio giusto in pochi secondi.
        </h1>
        <p class="mt-2 text-slate-600 dark:text-slate-300">
          Cerca, confronta e prenota studi verificati con prezzi chiari e disponibilità reale.
        </p>
      </div>

      <div class="hidden md:flex gap-2">
        <a href="{{ route('client.studios.index') }}"
           class="rounded-xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">
          Cerca studi
        </a>
        <a href="{{ route('profile.edit') }}"
           class="rounded-xl border border-slate-200 dark:border-slate-800 px-5 py-3 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
          Profilo
        </a>
      </div>
    </div>
  </div>

  <div class="col-span-12 md:col-span-4 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950">
    <div class="text-sm text-slate-500 dark:text-slate-400">Studi verificati</div>
    <div class="mt-2 text-slate-700 dark:text-slate-200">Solo studi professionali con attrezzatura e info complete.</div>
  </div>

  <div class="col-span-12 md:col-span-4 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950">
    <div class="text-sm text-slate-500 dark:text-slate-400">Prezzi chiari</div>
    <div class="mt-2 text-slate-700 dark:text-slate-200">Confronto immediato: niente sorprese a checkout.</div>
  </div>

  <div class="col-span-12 md:col-span-4 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950">
    <div class="text-sm text-slate-500 dark:text-slate-400">Prenotazione sicura</div>
    <div class="mt-2 text-slate-700 dark:text-slate-200">Pagamenti protetti e gestione cancellazioni semplice.</div>
  </div>

  <div class="col-span-12 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950">
    <div class="flex items-center justify-between gap-4">
      <div>
        <div class="text-lg font-semibold text-slate-900 dark:text-white">Suggerimento</div>
        <div class="mt-1 text-slate-600 dark:text-slate-300">Imposta le tue preferenze (genere, budget, distanza, disponibilità).</div>
      </div>
      <a href="{{ route('client.studios.index') }}"
         class="rounded-xl border border-slate-200 dark:border-slate-800 px-4 py-2 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
        Imposta ora
      </a>
    </div>
  </div>
</div>
@endsection
