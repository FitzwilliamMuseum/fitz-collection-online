@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title', $exhibition['summary_title'])
@section('content')
  <div class="shadow-sm p-3 mx-auto mb-3 rounded">
    @if(array_key_exists('venues', $exhibition))
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
      @else
        <p>
          No details recorded.
        </p>
      @endif
    </div>

    @if(!empty($records))
      <h3>Results</h3>
      <div class="row">
        @foreach($records as $record)

        @php
        $pris = Arr::pluck($record['_source']['identifier'],'priref');
        $pris = array_filter($pris);
        $pris= Arr::flatten($pris);
        @endphp

        <div class="col-md-4 mb-3">
          <div class="card h-100">
            <div class="results_image">
            @if(array_key_exists('multimedia', $record['_source']))
              <a href="/id/object/{{ $pris[0] }}"><img class="results_image__thumbnail" src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
               loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
              /></a>
            @else
              <a href="/id/object/{{ $pris[0] }}"><img class="results_image__thumbnail" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-medium-crop"
              alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
            @endif
            </div>
            <div class="card-body ">

              <div class="contents-label mb-3">
                <h3>
                <a href="/id/object/{{ $pris[0] }}">{{ ucfirst($record['_source']['summary_title']) }}</a>
                </h3>
                <p>
                  @if(array_key_exists('department', $record['_source']))
                    Holding department: {{ $record['_source']['department']['value'] }}<br/>
                  @endif
                </p>
              </div>
            </div>
          </div>
        </div>
        @endforeach

    </div>
    @endif
  @endsection
