@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
        Prenotazione confermata
    </h1>

    <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-950">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Booking #{{ $booking->id }}
        </div>

        <div class="mt-3 grid gap-2">
            <div class="flex items-center justify-between">
                <span class="text-slate-600 dark:text-slate-300">Stato</span>
                <span class="font-semibold text-slate-900 dark:text-white">{{ $booking->status }}</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-600 dark:text-slate-300">Totale</span>
                <span class="font-semibold text-slate-900 dark:text-white">
                    €{{ number_format($booking->total_cents / 100, 2, ',', '.') }}
                </span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-600 dark:text-slate-300">Creato</span>
                <span class="text-slate-900 dark:text-white">
                    {{ optional($booking->created_at)->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('client.studios.index') }}"
           class="inline-flex items-center justify-center rounded-xl bg-slate-900 text-white px-5 py-3 font-semibold hover:bg-slate-800 transition">
            Prenota un altro studio
        </a>

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 px-5 py-3 font-semibold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-900 transition">
            Vai alla dashboard
        </a>
    </div>
</div>
@endsection
