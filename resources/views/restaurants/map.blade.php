@extends('layouts.app')

@section('title', 'Mapa de Restaurantes')

@section('content')
<div class="container-fluid px-0">
    <div class="restaurant-map-container">
        <restaurant-map 
            :restaurants='@json($restaurants)'
            initial-lat="{{ $initialLat ?? -23.5505 }}"
            initial-lng="{{ $initialLng ?? -46.6333 }}"
            initial-zoom="{{ $initialZoom ?? 12 }}"
        ></restaurant-map>
    </div>
</div>
@endsection

@push('styles')
<style>
.restaurant-map-container {
    min-height: calc(100vh - 200px);
    background: var(--black);
}
</style>
@endpush