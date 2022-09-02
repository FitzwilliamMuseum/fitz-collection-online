@extends('layouts.layout')
@section('title', 'Details for ' . urldecode(ucwords($name)))
@section('description', 'Details and description for objects connected to ' . urldecode(ucwords($name)))')

@section('content')
    <div class="shadow-sm mx-auto mb-3 rounded">
        <p>
            This department has <strong>{{ number_format($connected->total())}}</strong> records attributed within
            our system.
        </p>
    </div>
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
    @include('includes.elements.machine-cite-department')
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
