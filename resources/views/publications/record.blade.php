@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
@foreach($data as $publication)
@section('title', $publication['_source']['summary_title'])


    <div class="shadow-sm p-3 mx-auto mb-3 rounded">
      @if(array_key_exists('lifecycle', $publication['_source']))
        @if(array_key_exists('publication', $publication['_source']['lifecycle']))
        <h3>Publication Date</h3>
        @foreach($publication['_source']['lifecycle']['publication'][0]['date'] as $date)
          <p>
              {{ $date['value'] }}
          </p>
        @endforeach
        @endif
      @endif
    </div>

  <h3>Connected records</h3>
    <div class="shadow-sm p-3 mx-auto mb-3 rounded">

    </div>
  @endforeach
@endsection
