@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title', $exhibition['summary_title'])
@section('content')
  <div class="shadow-sm p-3 mx-auto mb-3 rounded">
    @foreach($exhibition['venues'] as $venue)
      <p>Held: {{ $venue['summary_title'] }}</p>
      @if(array_key_exists('@link', $venue))
        @if(array_key_exists('date', $venue['@link']))
          @foreach ($venue['@link']['date'] as $date)
            @if(array_key_exists('from', $date))
              From: {{ $date['from']['value'] }}
            @endif
            @if(array_key_exists('to', $date))
              To: {{ $date['to']['value'] }}
            @endif
          @endforeach
        @endif
      @endif
      <ul>
    @endforeach
  </div>
@endsection
