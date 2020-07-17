@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
@foreach($data as $term)
@section('title', ucfirst($term['_source']['summary_title']))

    <div class="shadow-sm p-3 mx-auto mb-3 rounded">
      @if(array_key_exists('description', $term['_source']))
      @foreach($term['_source']['description'] as $description)
      <h3>{{ ucfirst($description['type']) }}</h3>
      <p>
      {{ ucfirst($description['value']) }}
      </p>
      @endforeach
      @else
      <p>No supplementary data available</p>
      @endif
    </div>
@endforeach

<h3>Example connected records</h3>
    <div class="row">
    @foreach($use['hits'] as $record)
    <div class="col-md-4 mb-3">
      <div class="card card-body h-100">
        <div class="container h-100">
          @if(array_key_exists('multimedia', $record['_source']))
          <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="img-fluid" src="https://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
           loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
          /></a>
          @else
          <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="img-fluid" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-large-crop"
          alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
          @endif
          <div class="contents-label mb-3">
            <h3>
            <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}">{{ ucfirst($record['_source']['summary_title']) }}</a>
            </h3>
            <p>
              @if(array_key_exists('department', $record['_source']))
                Holding department: {{ $record['_source']['department']['value'] }}<br/>
              @endif
              Accession Number: {{ $record['_source']['identifier'][0]['accession_number'] }}
            </p>
          </div>
        </div>
        <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}" class="btn btn-dark">Read more</a>
      </div>
    </div>
    @endforeach

  </div>
