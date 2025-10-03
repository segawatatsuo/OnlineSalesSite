@extends('layouts.app')

@section('title', 'ccmedico shop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush



@section('content')

    <main class="main">
        <div class="line-up">
            <a href="{{ asset('products/wax') }}"><img src="{{ asset('images/shop-top/bikyaku.jpg') }}" alt=""
                    class="pic"></a>
            <a href="{{ asset('products/airstocking') }}"><img src="{{ asset('images/shop-top/daimond.jpg') }}"
                    alt="" class="pic"></a>
            <a href="{{ asset('products/gelnail') }}"><img src="{{ asset('images/shop-top/3in1Lineup1560-600.jpg') }}"
                    alt="" class="pic"></a>
        </div>


    </main>

@endsection
