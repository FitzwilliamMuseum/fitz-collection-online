@extends('layouts.layout')
@section('title','Search results')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('description', 'Search results from our highlights')
@section('keywords', 'search,results,collection,highlights,fitzwilliam')
@section('content')

<h2>Search results</h2>
<div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
  <p>
    Your search for <strong>{{ $queryString }}</strong> returned <strong>{{ $number }}</strong> results.
  </p>
</div>



  <div class="row">
    @foreach($records as $record)
    <div class="col-md-4 mb-3">
      <div class="card card-body h-100">
        <div class="container h-100">
          @if(array_key_exists('multimedia', $record['_source']))
          <a href="/object/id/{{ $record['_source']['identifier'][1]['priref']}}"><img class="img-fluid" src="http://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
           loading="lazy"
          /></a>
        @endif
          <div class="contents-label mb-3">
            <h3>
            {{ ucfirst($record['_source']['summary_title']) }}
            </h3>
            <p>
              @if(array_key_exists('department', $record['_source']))
                Holding department: {{ $record['_source']['department']['value'] }}<br/>
              @endif
              Accession Number: {{ $record['_source']['identifier'][0]['accession_number'] }}
            </p>
          </div>
        </div>
        <a href="/object/id/{{ $record['_source']['identifier'][1]['priref']}}" class="btn btn-dark">Read more</a>
      </div>
    </div>
    @endforeach

</div>
  <nav aria-label="Page navigation">
    {{ $paginate->links() }}
  </nav>
@endsection
