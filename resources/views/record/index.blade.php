@extends('layouts/layout')

@section('hero_image_title', "The inside of our Founder's entrance")
@section('title','Objects and artworks')

@section('content')
<div class="row">
  @foreach($data as $record)
  @if(array_key_exists('multimedia', $record['_source']))
  @section('hero_image','http://api.fitz.ms/mediaLib/' . $record['_source']['multimedia'][0]['processed']['original']['location'])
  @else
  @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
  @endif

  @if(array_key_exists('multimedia', $record['_source']))
  <div class="col mb-3">
    <div class="card card-body h-100">
      <div class="container h-100">
        @if(array_key_exists('multimedia', $record['_source']))
        <a href="/objects-and-artworks/highlights/"><img class="img-fluid" src="http://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['original']['location'] }}"
          loading="lazy"
          /></a>
          @endif

        </div>
      </div>
    </div>
    @endif
    
    <!-- <pre>@php(var_dump($record['_source']))</pre> -->
    <div class="col mb-3">
      <div class="card card-body h-100">
        <div class="container h-100">
          <h3>
            {{ ucfirst($record['_source']['summary_title']) }}
          </h3>

          @if(array_key_exists('description', $record['_source']))
          <p>{{ ucfirst($record['_source']['description'][0]['value']) }} </p>
          @endif

          @if(array_key_exists('creation', $record['_source']['lifecycle']))
          <h4>Dating</h4>
          <ul>
            @foreach($record['_source']['lifecycle']['creation'][0]['periods'] as $date)
            <li>{{ $date['summary_title']}}</li>
            @endforeach
          </ul>
          @endif

          @if(array_key_exists('measurements', $record['_source']))
          <h4>Measurements and weight</h4>
          <ul>
            @foreach($record['_source']['measurements']['dimensions'] as $dim)
            <li>{{ $dim['value'] }}</li>
            @endforeach
          </ul>
          @endif

          <p>Associated department: {{ $record['_source']['department']['value'] }}</p>

          @if(array_key_exists('acquisition', $record['_source']['lifecycle']))
          <h4>Acquisition and important dates</h4>
          <ul>
            <li>Acquired: {{ $record['_source']['lifecycle']['acquisition'][0]['method']['value'] }} {{ $record['_source']['lifecycle']['acquisition'][0]['date'][0]['value'] }}</li>
          </ul>
          @endif

          @if(array_key_exists('publications', $record['_source']))

          <h4>References and bibliographic entries</h4>
          <ul>
            @foreach($record['_source']['publications'] as $pub)
            <li>{{ $pub['summary_title'] }}</li>
            @endforeach
          </ul>
          @endif

          <h4>Identification numbers</h4>
          <ul>
            <li>Accession number:   {{ $record['_source']['identifier'][0]['accession_number'] }}</li>
            <li>Primary reference:   {{ $record['_source']['identifier'][1]['priref'] }}</li>
          </ul>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endsection
