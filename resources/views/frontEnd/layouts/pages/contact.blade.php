@extends('frontEnd.layouts.master')
@section('title', 'Contact Us')
@section('content')

<section class="comn_sec">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="cmn_menu">
                    <ul>
                        @foreach($cmnmenu as $key=>$value)
                        <li>
                            <a href="{{route('page',$value->slug)}}">{{$value->name}}</a>
                        </li>
                        @endforeach
                        <li>
                            <a href="{{route('contact')}}">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">

        <div class="row">
            <div class="col-sm-6">
                <div class="cont_item">
                 <a href="tel:{{$contact->hotline}}">
                  <i data-feather="phone"></i>
                  {{$contact->hotline}}
                 </a>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="cont_item">
                 <a href="mailto:{{$contact->email}}">
                  <i data-feather="mail"></i>
                  {{$contact->email}}
                 </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-sm-10">
                <div class="contact-form">
                    <h5 class="account-title">Or Send Us a Message</h5>
                    <form action="{{route('contact')}}" method="POST" class="row" data-parsley-validate="">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @csrf
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" required placeholder="Enter your full name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col-end -->
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label for="phone">Phone Number *</label>
                                <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{old('phone')}}" required placeholder="Enter your phone number">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col-end -->
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{old('email')}}" required placeholder="Enter your email address">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col-end -->
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="subject">Subject *</label>
                                <input type="text" id="subject" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{old('subject')}}" required placeholder="Enter message subject">
                                @error('subject')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col-end -->
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="message">Message *</label>
                                <textarea id="message" rows="5" class="form-control @error('message') is-invalid @enderror" name="message" required placeholder="Write your message here...">{{old('message')}}</textarea>
                                @error('message')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col-end -->
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <button type="submit" class="submit-btn">Send Message</button>
                            </div>
                        </div>
                        <!-- col-end -->
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@push('script')
<script src="{{asset('frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('frontEnd/')}}/js/form-validation.init.js"></script>
@endpush