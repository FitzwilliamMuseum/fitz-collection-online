@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
  @section('title', $name)
  <div class="shadow-sm p-3 mx-auto mb-3 rounded">
    <p>This department has <strong>{{ number_format($use['count'])}}</strong> records attributed within our system.</p>
  </div>
  <h3>Example connected records</h3>
  <div class="row">
    @foreach($data as $record)
      <div class="col-md-4 mb-3 ">
        <div class="card h-100">
          <div class="results_image">
            @if(array_key_exists('multimedia', $record['_source']))
              <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="results_image__thumbnail" src="/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                /></a>
              @else
                <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="results_image__thumbnail" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-medium-crop"
                  alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
                @endif
              </div>
              <div class="card-body">
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
            </div>
          </div>
        @endforeach

      </div>
    @endsection
