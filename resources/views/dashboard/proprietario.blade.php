@extends('layouts.app')
@section('title', 'Dashboard Proprietario')

@section('content')
<div class="p-8 bg-darkbg min-h-screen">
    <h1 class="text-3xl font-bold text-gold mb-8">I Miei Studi</h1>
    <div class="grid gap-6">
        @foreach(auth()->user()->studios as $studio)
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl text-primary">{{ $studio->name }}</h2>
            <p>{{ $studio->address }}</p>
            <p class="text-gold text-xl">€{{ $studio->price_per_hour }}/h</p>
            <p>{{ $studio->description }}</p>
        </div>
        @endforeach
        <a href="{{ route('owner.studios.create') }}" class="bg-accent text-white px-6 py-3 rounded-lg hover:bg-red-700">
            + Nuovo Studio
        </a>
    </div>
</div>
@endsection
