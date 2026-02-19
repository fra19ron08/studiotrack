@extends('layouts.app')

@section('content')


    <p style="margin-top:10px;">
        Studio: {{ $studio->id }} — {{ $studio->name }}
    </p>

    <p style="margin-top:10px;">
        Slots ricevuti: {{ $slots->count() }}
    </p>
@php
  $base = \Carbon\Carbon::today();
  $selected = request('date', $base->format('Y-m-d'));
@endphp

<div class="mt-6 flex gap-2 overflow-x-auto">
  @for ($i=0; $i<7; $i++)
    @php $d = $base->copy()->addDays($i); @endphp
    <a href="{{ route('client.studios.show', $studio) }}?date={{ $d->format('Y-m-d') }}"
       class="px-4 py-2 rounded-xl border {{ $selected === $d->format('Y-m-d') ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200' }}">
      {{ $d->format('D d/m') }}
    </a>
  @endfor
</div>

    <div style="margin-top:16px; display:flex; flex-direction:column; gap:10px;">
        @foreach($slots as $slot)
            <div style="padding:12px; border:1px solid #374151; border-radius:12px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    Slot #{{ $slot->id }} —
                    {{ \Carbon\Carbon::parse($slot->start_at)->format('d/m H:i') }}
                    →
                    {{ \Carbon\Carbon::parse($slot->end_at)->format('H:i') }}
                    —
                    €{{ number_format($slot->price_cents / 100, 2, ',', '.') }}
                </div>

                <form method="POST" action="{{ route('client.slots.book', $slot->id) }}">
                    @csrf
                    <button type="submit" style="padding:8px 12px; border-radius:10px; background:#10b981; color:#062; font-weight:700;">
                        PRENOTA
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
