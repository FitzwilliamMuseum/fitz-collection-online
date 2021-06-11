@extends('layouts.layout')
@section('content')
  <div class="row">
    @foreach($data as $record)
      @if(array_key_exists('multimedia', $record['_source']))
        @if(array_key_exists('title', $record['_source']))
        @section('hero_image_title', ucfirst($record['_source']['title'][0]['value']))
        @else
          @section('hero_image_title', ucfirst($record['_source']['summary_title']))

        @endif
        @section('hero_image', env('APP_URL') . '/imagestore/' . $record['_source']['multimedia'][0]['processed']['mid']['location'])
      @else
        @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
        @section('hero_image_title', "The inside of our Founder's entrance")
      @endif
      <!-- multimedia section start -->
      @include('includes/elements/media')
      <!-- multimedia section end -->

      <div class="col-md-12 mb-3 object-info">
        <h2 class="sr-only">Object information</h2>
        <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">

          <div class="container">

            @include('includes/elements/descriptive')
            @include('includes/elements/legal')
            @include('includes/elements/name')

            @include('includes/elements/measurements')
            {{-- https://stackoverflow.com/a/44242233 --}}
            <div class="text-center">
              <button type="button" class="btn btn-dark btn-circle btn-xl mb-5" data-toggle="collapse" data-target="#expand-more" aria-expanded="false" aria-controls="expand-more">
                <span class="collapsed">
                  Read More<br />@fa('plus')
                </span>
                <span class="expanded">
                  Read Less<br />@fa('minus')
                </span>
              </button>
            </div>
            <div id="expand-more" class="collapse">
              @include('includes/elements/lifecycle')
              @include('includes/elements/agents-subjects')
              @include('includes/elements/medium')
              @include('includes/elements/component')
              @include('includes/elements/materials')
              @include('includes/elements/techniques')
              @include('includes/elements/inscriptions')
              @include('includes/elements/publications')
              @include('includes/elements/exhibitions')
              @include('includes/elements/identification')
            </div>
          </div>
        </div>
      </div>
    </div>

  @endsection

  @section('machine')
    <div class="container-fluid bg-grey">
      <div class="container">
        <h3 class="lead">
          Cite this record
        </h3>
        <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
          <div class="container">
            @include('includes/elements/citation')
          </div>
        </div>
        <h3 class="lead">
          Machine readable data
        </h3>
        <div class="shadow-sm p-3 mx-auto  mt-3 rounded">
          <div class="container">
            @include('includes/elements/formats')
          </div>
        </div>
      </div>

    </div>
  @endforeach


  @if(!empty($mlt))
    @section('mlt')
      <div class="container-fluid bg-grey">
        <div class="container">
          <h3 class="lead">
            More objects and works of art you might like
          </h3>
          <div class="row">
            @foreach($mlt as $record)
              <div class="col-md-3 mb-3">
                <div class="card  h-100">
                  <div class="card-body results_image">
                    @if(array_key_exists('multimedia', $record['_source']))
                      <a href="{{ route('record', $record['_source']['identifier'][1]['priref']) }}"><img class="img-fluid " src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}" loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"/></a>
                    @else
                      <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="results_image__thumbnail" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-large-crop"
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
          </div>
        </div>
      @endsection
    @endif
  @endsection
