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
        <p>
          No supplementary data available
        </p>
      @endif
      <p>This term has <strong>{{ number_format($count['count'])}}</strong> records attributed within our system.</p>

    </div>
  @endforeach
@endsection
@section('connected')
  <div class="container-fluid bg-grey">
    <div class="container">
      <h3 class="lead collection">Connected records</h3>
      <div class="row">
        @foreach($use['hits'] as $record)
          <div class="col-md-4 mb-3">
            <div class="card h-100 ">

              <div class="results_image">
                @if(array_key_exists('multimedia', $record['_source']))
                  <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="results_image__thumbnail" src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                    loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                    /></a>
                  @else
                    <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="results_image__thumbnail" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-medium-crop"
                      alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
                    @endif
                  </div>


                  <div class="card-body ">

                    <div class="contents-label mb-3">
                      <h3 class="lead">
                        @if(array_key_exists('title',$record['_source'] ))
                          <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}">{{ ucfirst($record['_source']['title'][0]['value']) }}</a>
                        @else
                          <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}">{{ ucfirst($record['_source']['summary_title']) }}</a>
                        @endif
                      </h3>
                      <p class="text-info">
                        Accession Number: {{ $record['_source']['identifier'][0]['accession_number'] }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach

          </div>
          <div class="container shadow-sm p-3 mx-auto mb-3 rounded">
            <a href="/search/results?query=**" class="btn btn-dark btn-block">Find all results</a>
          </div>
        </div>
      </div>

    @endsection
