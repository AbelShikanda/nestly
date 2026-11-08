@extends('layouts.app')

@section('content')
    @include('layouts.partials.header')

    <div id="fh5co-property-single">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="property-single-heading">
                        <h2>{{ $property->name }}</h2>
                        <span class="price">{{ $property->price }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <img src="{{ asset('1n-west/images/property-1.jpg') }}" class="img-fluid" alt="{{ $property->name }}">
                </div>
                <div class="col-md-7">
                    <ul class="list-details">
                        <li>Rooms: {{ $property->rooms }}</li>
                        <li>Baths: {{ $property->baths }}</li>
                        <li>Living Rooms: {{ $property->living_rooms }}</li>
                        <li>Kitchens: {{ $property->kitchens }}</li>
                        <li>Country: {{ $property->country }}</li>
                        <li>County: {{ $property->county }}</li>
                        <li>Town: {{ $property->town }}</li>
                    </ul>
                    <p><strong>Description:</strong> {{ $property->meta_description }}</p>
                    <p><strong>Meta Keywords:</strong> {{ $property->meta_keywords }}</p>
                    <a href="{{ route('properties') }}" class="btn btn-primary">Back to Properties</a>
                </div>
            </div>
        </div>
    </div>
@endsection