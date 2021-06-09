@extends('layouts.layout')
@section('content')
  <div class="row">
    @foreach($data as $record)
      @if(array_key_exists('multimedia', $record['_source']))
        @section('hero_image_title', ucfirst($record['_source']['summary_title']))
        @section('hero_image', env('APP_URL') . '/imagestore/' . $record['_source']['multimedia'][0]['processed']['mid']['location'])
      @else
        @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
        @section('hero_image_title', "The inside of our Founder's entrance")
      @endif
      <!-- multimedia section start -->
      @include('includes/elements/media')
      <!-- multimedia section end -->

      <div class="col-md-12 mb-3 object-info">
        <h2>Object information</h2>
        <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
          <div class="container">

            @include('includes/elements/descriptive')

            @include('includes/elements/name')

            @include('includes/elements/legal')

            @include('includes/elements/lifecycle')

            @include('includes/elements/measurements')

            @include('includes/elements/agents-subjects')

            @include('includes/elements/medium')

            @include('includes/elements/component')

            @include('includes/elements/materials')

            @include('includes/elements/techniques')

            @include('includes/elements/inscriptions')

            @include('includes/elements/department')

            @include('includes/elements/publications')

            @include('includes/elements/exhibitions')

            @include('includes/elements/identification')

            @include('includes/elements/institutions')
          </div>
        </div>
        <h4>How to cite this record</h4>
        <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
          <div class="container">
            @include('includes/elements/citation')
          </div>
        </div>
        <h4>Machine readable data</h4>

        <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
          <div class="container">
            @include('includes/elements/formats')
          </div>
        </div>
      </div>
    </div>

  @endsection

  @include('includes/elements/sketchfab')

  @if(!empty($mlt))
    @section('mlt')

      <div class="container">
        <h3>More objects and works of art you might like</h3>
        <div class="row">
          @foreach($mlt as $record)
            <div class="col-md-4 mb-3">
              <div class="card  h-100">
                <div class="results_image">
                  @if(array_key_exists('multimedia', $record['_source']))
                    <a href="{{ route('record', $record['_source']['identifier'][1]['priref']) }}"><img class="results_image__thumbnail" src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                      loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                      /></a>
                    @else
                      <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="results_image__thumbnail" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-large-crop"
                        alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
                      @endif
                    </div>
                    <div class="card-body ">
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
          </div>
        @endsection
      @endif
    @endforeach
