@extends('layouts.layout')
@section('title', 'Details for a publication entitled: ' . $data['summary_title'])
@section('description', 'Details for a publication entitled: ' . $data['summary_title'] . ' connected to museum objects')
@section('content')
    <x-publication-details :publication="$data" :count="$connected->total()"></x-publication-details>
@endsection

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

@section('machine')
    @include('includes.elements.machine-cite-publications')
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
