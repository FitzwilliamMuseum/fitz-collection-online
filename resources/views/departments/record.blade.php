@extends('layouts.layout')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('content')
  @section('title', $name)
  <div class="shadow-sm p-3 mx-auto mb-3 rounded">
    <p>This department has <strong>{{ number_format($use['count'])}}</strong> records attributed within our system.</p>
  </div>
@endsection
@section('connected')
  <div class="container-fluid bg-white">
    <div class="container">
      <h3 class="lead collection">Connected records</h3>
      <div class="row">
        @foreach($records as $record)
          <div class="col-md-4 mb-3">
            <div class="card h-100">
                @if(array_key_exists('multimedia', $record['_source']))
                  <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}">
                    <img class="card-img-top" src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                    loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                    /></a>
                  @else
                    <a href="/id/object/{{ $record['_source']['identifier'][1]['priref']}}"><img class="card-img-top" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-medium-crop"
                      alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
                    @endif

                  <div class="card-body ">
                    <div class="contents-label">
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
    @section('pagination')
      @if($paginate->total() > 24)
      <div class="container-fluid bg-white mb-5 p-4 text-center">
        <nav aria-label="Page navigation" >
          {{ $paginate->appends(request()->except('page'))->links() }}
        </nav>
      </div>
      @endif
    @endsection
