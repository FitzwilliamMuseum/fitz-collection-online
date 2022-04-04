@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@foreach($data as $term)
    @section('title', ucfirst($term['_source']['summary_title']))
@endforeach

@section('content')
    @foreach($data as $term)
        <div class="shadow-sm p-3 mx-auto mb-3 rounded">
            @if(array_key_exists('description', $term['_source']))
                @foreach($term['_source']['description'] as $description)
                    <h3 class="lead">
                        {{ ucfirst($description['type']) }}
                    </h3>
                    <p>
                        {{ ucfirst($description['value']) }}
                    </p>
                @endforeach
            @else
                <p>
                    No supplementary data available.
                </p>
            @endif
        </div>
    @endforeach
@endsection

@section('connected')
    <div class="container-fluid bg-white">
        <div class="container">
            <h3 class="lead collection">Connected records with images</h3>
            <div class="row">
                @foreach($connected as $record)
                   <x-search-result :record="$record">
                @endforeach
            </div>

        </div>
    </div>

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
