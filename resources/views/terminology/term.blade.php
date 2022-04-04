@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title', ucfirst($data['summary_title']))

@section('content')
@if(array_key_exists('description', $data))
    @foreach($data['description'] as $description)
        <h3 class="lead">{{ ucfirst($description['type']) }}</h3>
        <p>
            {{ ucfirst($description['value']) }}
        </p>
    @endforeach
@else
    <p>
        No scope notes or supplementary data available at the moment.
    </p>
@endif
<p>This term has <strong>{{ number_format($count['count'])}}</strong> records attributed within our system.</p>
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

@section('pagination')
    @if($connected->total() > 24)
        <div class="container-fluid bg-white mb-5 p-4 text-center">
            <nav aria-label="Page navigation">
                {{ $connected->appends(request()->except('page'))->links() }}
            </nav>
        </div>
    @endif
@endsection
