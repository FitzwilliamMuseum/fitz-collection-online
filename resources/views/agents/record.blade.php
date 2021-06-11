@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
  @foreach($data as $agent)
    @section('title', ucfirst($agent['_source']['summary_title']))

    <div class="shadow-sm p-3 mx-auto mb-3 rounded">
      <p>Fullname: {{ $agent['_source']['name'][0]['value']}}</p>
      <p>
        This agent has been used <strong>{{ $use['total']['value'] }}</strong> times within our collections systems.
      </p>
    </div>
  @endforeach
@endsection
@section('connected')
  <div class="bg-grey container-fluid">
    <div class="container">
      <h3 class="mb-3 lead collection">Example connected records</h3>
      <div class="row">
        @foreach($use['hits'] as $record)
          <div class="col-md-4 mb-3">
            <div class="card  h-100">
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
                  <div class="card-body">
                    <div class="contents-label mb-3">
                      <h3 class="lead">
                        <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}">{{ ucfirst($record['_source']['summary_title']) }}</a>
                      </h3>
                      <p>
                        Accession Number: {{ $record['_source']['identifier'][0]['accession_number'] }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach

          </div>
        </div>
      </div>
    @endsection
