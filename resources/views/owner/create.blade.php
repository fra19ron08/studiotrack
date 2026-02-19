@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Crea studio</h1>
      <p class="mt-1 text-slate-600 dark:text-slate-300">Inserisci i dati e carica una cover.</p>
    </div>
  </div>

  <form method="POST"
        action="{{ route('owner.studios.store') }}"
        enctype="multipart/form-data"
        class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 p-6">

    @csrf

    @include('owner._form', ['studio' => null])

    <div class="mt-6 flex gap-2">
      <button class="rounded-xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">
        Salva
      </button>

      <a href="{{ route('owner.studios.index') }}"
         class="rounded-xl border border-slate-200 dark:border-slate-800 px-6 py-3 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
        Annulla
      </a>
    </div>
  </form>
</div>
@endsection
