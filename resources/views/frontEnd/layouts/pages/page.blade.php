@extends('frontEnd.layouts.master')
@section('title', $page->title ?? 'Page')
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

<section class="createpage-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-content">
                    <div class="page-title mb-2">
                        <h5>{{$page->title}}</h5>
                    </div>
                    <div class="page-description">
                        {!! $page->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    section.createpage-section {
        min-height: 50vh;
        margin-bottom: 0 !important;
        padding: 40px 0;
    }
    .copyright p {
        margin-bottom: 0 !important;
    }
    .page-description {
        font-size: 15px;
        line-height: 1.7;
        color: #333;
    }
    .page-description img {
        max-width: 100% !important;
        height: auto !important;
    }
    .page-description table {
        width: 100% !important;
        border-collapse: collapse;
        margin: 15px 0;
    }
    .page-description table th,
    .page-description table td {
        padding: 10px 14px;
        border: 1px solid #ddd;
    }
    .page-description iframe {
        max-width: 100%;
    }
    .page-title{
        text-align: center;
        text-transform: uppercase;
        margin-bottom: 10px !important;
    }
</style>
@endsection
