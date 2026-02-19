@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Modifica studio</h1>
      <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $studio->name }}</p>
    </div>

    <a href="{{ route('owner.studios.index') }}"
       class="hidden sm:inline-flex rounded-xl border border-slate-200 dark:border-slate-800 px-4 py-2 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
      Torna alla lista
    </a>
  </div>

  <form method="POST"
        action="{{ route('owner.studios.update', $studio) }}"
        enctype="multipart/form-data"
        class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 p-6">

    @csrf
    @method('PUT')

    @include('owner._form', ['studio' => $studio])

    <div class="mt-6 flex gap-2">
      <button class="rounded-xl bg-slate-900 px-6 py-3 text-white font-semibold hover:bg-slate-800 transition">
        Salva modifiche
      </button>

      <a href="{{ route('owner.studios.index') }}"
         class="rounded-xl border border-slate-200 dark:border-slate-800 px-6 py-3 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
        Annulla
      </a>
    </div>
  </form>
</div>
@endsection
