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

  @if(!empty($records))
  @foreach($records as $result)
    <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded search-results">
      @if(isset($result['searchImage']))
          <img src="{{$result['searchImage'][0]}}" class="rounded rounded-circle float-right" height="150" width="150"
          alt="Highlight image for {{ $result['title'][0] }}"/>
        @else
          <img src="https://fitz-cms-images.s3.eu-west-2.amazonaws.com/fvlogo.jpg" class="rounded float-right" width="200"
          alt="No image was provided"/>
      @endif
      <h3>{{ $result['_source']['summary_title']}}</h3>
      <p>
        @if(!empty($result['_source']['description'][0]['value']))
          {{ $result['_source']['description'][0]['value'] }}...
        @endif
      </p>

      </div>
  @endforeach
  <nav aria-label="Page navigation">
    {{ $paginate->links() }}
  </nav>

@else
  <p>No results to display.</p>
@endif
</div>
</div>
@endsection
