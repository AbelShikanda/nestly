@extends('layouts.app')

@section('content')
    @include('layouts.partials.aside')

    <div id="fh5co-contact">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
                    <h3>Contact Information</h3>
                    <p>
                        Need assistance with buying or renting?
                    </p>
                </div>
            </div>
<form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="row animate-box">
                        <div class="col-md-6">
                            <h3 class="section-title">Our Address</h3>
                            <p>
                                Speak to an available agent to
                                provide the guidance you need to make an informed real estate decision.
                                we are only a message away 24/7.
                            </p>
                            <ul class="contact-info">
                                <li><i class="icon-location-pin"></i>Telgat street, Suite 721 Kakamega KE 7905-00200</li>
                                <li><i class="icon-phone2"></i><a href="tel:+254722729567">+254 722 729 567</a></li>
                                <li><i class="icon-mail"></i><a href="mailto:info@1n-west.com">info@1n-west.com</a></li>
                                <li><i class="icon-globe2"></i><a href="https://1n-west.com" target="_blank">www.1n-west.com</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Subject">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <textarea name="message" class="form-control" id="" cols="30" rows="7" placeholder="Message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
        </div>
    </div>
    <div id="map" class="fh5co-map"></div>
@endsection
