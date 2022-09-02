@extends('layouts.layout')
@section('title', 'Details for an exhibition entitled: ' . $exhibition['summary_title'])
@section('description', 'Details for an exhibition the museum contribute objects to: ' . $exhibition['summary_title'])

@section('content')
    <x-exhibition-details :exhibition="$exhibition"></x-exhibition-details>
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
                        <x-search-result :record="$record"></x-search-result>
                    @endforeach
                </div>
            </div>
        </div>
    @endsection
@endif

@section('machine')
    @include('includes.elements.machine-cite-exhibition')
@endsection

@section('pagination')
    @if($connected->total() > 24)
        <div class="container-fluid bg-white mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $connected->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
