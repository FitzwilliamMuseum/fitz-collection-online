@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title', $exhibition['summary_title'])

@section('content')
    <x-exhibition-details :exhibition="$exhibition"/>
@endsection

@php
if(!isset($startDate)){
    $startDate = Carbon\Carbon::now();
}
@endphp

@if(Carbon\Carbon::parse($startDate)->isPast())
@section('connected')
    <div class="container-fluid bg-white">
        <div class="container">
            <h3 class="lead collection">Connected records</h3>
            <div class="row">
                @foreach($connected as $record)
                    <x-search-result :record="$record"/>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@endif

@section('pagination')
    @if($connected->total() > 24)
        <div class="container-fluid bg-white mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $connected->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
