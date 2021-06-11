@extends('layouts/layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('title','Objects and artworks')

@section('content')
<div class="row">
  @foreach($data as $record)
  <div class="col-md-4 mb-3">
    <div class="card card-body h-100">
      <div class="container h-100">
        @if(array_key_exists('multimedia', $record['_source']))
        <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="img-fluid" src="http://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
         loading="lazy"
        /></a>
        @else
        <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="img-fluid" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-large-crop"
        alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
        @endif
        <div class="contents-label mb-3">
          <h3 class="lead">
            {{ $record['_source']['identifier'][0]['accession_number'] }}: {{ ucfirst($record['_source']['summary_title']) }}
          </h3>
          <p>{{ $record['_source']['department']['value'] }}</p>
          @if(array_key_exists('description', $record['_source']))
          <p>{{ ucfirst($record['_source']['description'][0]['value']) }} </p>
          @endif

        </div>
      </div>
      <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}" class="btn btn-dark">Read more</a>
    </div>
  </div>
  @endforeach

</div>
  <nav aria-label="Page navigation">
    {{ $paginator->links() }}
  </nav>
@endsection
