@extends('layouts.app')

@section('title', 'Mapa de Restaurantes')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
            <div id="vue-app">
                <restaurant-map 
                    :restaurants='@json($restaurantsData)'
                ></restaurant-map>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/app.js') }}"></script>
<script>
console.log('Restaurantes passados para Vue:', @json($restaurantsData));
console.log('Elemento vue-app:', document.getElementById('vue-app'));
</script>
@endpush