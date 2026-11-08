@extends('layouts.app')

@section('content')
    @include('layouts.partials.aside')

    @include('layouts.partials.searchProperties')

    <div id="fh5co-about">
        <div class="container">
            <div class="row">
                <!-- --------------------------------------------------- -->
                <div class="col-sm-4 text-center animate-box" data-animate-effect="fadeIn">
                    <div class="fh5co-staff">
                        <img class="img-responsive" src="{{ asset('1n-west/images/user-1.jpg') }}"
                            alt="Free HTML5 Templates by freeHTML5.co">
                        <h3>Jean Smith</h3>
                        <span class="list-prop"></span>
                        <p>The Caretaker</p>
                        <p class="fh5co-social-icons">
                            <a href="#"><i class="icon-twitter2"></i></a>
                            <a href="#"><i class="icon-facebook2"></i></a>
                            <a href="#"><i class="icon-instagram"></i></a>
                            <!-- <a href="#"><i class="icon-dribbble2"></i></a> -->
                            <!-- <a href="#"><i class="icon-youtube"></i></a> -->
                        </p>
                        <!-- <p><a class="btn btn-primary btn-outline" href="#">View Properties</a></p> -->
                    </div>
                </div>
                <!-- --------------------------------------------------- -->
            </div>
        </div>
    </div>
    
@endsection
