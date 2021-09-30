@extends('layouts.layout')
@section('title','Search results')
@section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
@section('hero_image_title', "The inside of our Founder's entrance")
@section('description', 'Search results from our highlights')
@section('keywords', 'search,results,collection,highlights,fitzwilliam')

@php
$base = Request::query();
if(array_key_exists('page', $base)){
  unset($base['page']);
}
$query = http_build_query($base);
@endphp
@section('content')
@endsection

@section('search-box')
  <div class="container-fluid ">
    <div class="container">
      <div class="col-12 p-3 mx-auto mb-3 rounded">
        <p>
          Your search for <strong>{{ $queryString }}</strong> returned <strong>{{ $number }}</strong> results.
        </p>

        @if($number > 9999)
          <p>You may want to refine your search for better results</p>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('search-results')
<div class="container-fluid bg-gbdo">
  <div class="container p-3">
  <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
    {{ \Form::open(['url' => url('/search/results'),'method' => 'GET']) }}
    <div class="row">
      <div class="form-group col-md-12">
        <input type="text" id="query" name="query"  class="form-control input-lg mr-4"
        placeholder="Search our collection" required value="{{ $queryString }}">
      </div>
    </div>

    <div class="row">
      <div class="col">
        <h3 class="lead collection">Visual results</h4>
          <div class="form-group form-check ">
            <input type="checkbox" class="form-check-input" id="images" name="images"
            @if(request()->has('images'))
              checked
            @endif
            >
            <label class="form-check-label" for="images">Only with images?</label>
          </div>
          <div class="form-group form-check ">
            <input type="checkbox" class="form-check-input" id="iiif" name="iiif"
            @if(request()->has('iiif'))
              checked
            @endif
            >
            <label class="form-check-label" for="images">Deep zoom enabled?</label>
          </div>
        </div>
        <div class="col">
          <h3 class="lead collection">Operand for your search</h4>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="operator" id="operator" value="AND" checked>
              <label class="form-check-label" for="operator">
                AND
              </label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="operator" id="operator" value="OR" >
              <label class="form-check-label" for="operator">
                OR
              </label>
            </div>
          </div>
          <div class="col">
            <h3 class="collection lead">Sort by last update</h3>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="sort" id="sort" value="desc" checked>
              <label class="form-check-label" for="sort">
                Descending
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="sort" id="sort" value="asc" >
              <label class="form-check-label" for="sort">
                Ascending
              </label>
            </div>
          </div>

        </div>
        <div class="row">
          <div class="form-group col-md-12">
            <button type="submit" class="btn btn-dark">Submit</button>
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#exampleModal2">
              Filter your search
            </button>
          </div>
        </div>
        @if(count($errors))
          <div class="form-group">
            <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif
        {!! Form::close() !!}
      </div>
    </div>
  </div>
@endsection

@section('connected')
<div class="container-fluid bg-white p-3">
  <div class="container">
    @if(!empty($records))
      <h3 class="lead">
        Your Search Results
      </h3>
      <div class="row">
        @foreach($records as $record)
        @php
        $pris = Arr::pluck($record['_source']['identifier'],'priref');
        $pris = array_filter($pris);
        $pris= Arr::flatten($pris);
        @endphp

        <div class="col-md-4 mb-3">
          <div class="card h-100">
            <div class="mx-auto">
              @if(array_key_exists('multimedia', $record['_source']))
                <a href="/id/object/{{ $pris[0] }}"><img class="card-image-top" src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['preview']['location'] }}"
                  loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                  /></a>
                @else
                  <a href="/id/object/{{ $pris[0] }}"><img class="card-image-top" src="https://content.fitz.ms/fitz-website/assets/no-image-available.png?key=directus-medium-crop"
                    alt="A stand in image for {{ ucfirst($record['_source']['summary_title']) }}}"/></a>
                  @endif
                </div>
                <div class="card-body ">
                  <div class="contents-label mb-3">
                    <h3 class="lead ">
                      @if(array_key_exists('title',$record['_source'] ))
                        <a href="/id/object/{{ $pris[0] }}">{{ ucfirst($record['_source']['title'][0]['value']) }}</a>
                      @else
                        <a href="/id/object/{{ $pris[0] }}">{{ ucfirst($record['_source']['summary_title']) }}</a>
                      @endif
                    </h3>
                    @if(array_key_exists('accession_number', $record['_source']['identifier'][0]))
                      <p class="text-info">
                        {{ $record['_source']['identifier'][0]['accession_number'] }}
                      </p>
                    @endif
                      @include('includes.elements.makers')

                    </div>
                  </div>
                </div>
              </div>
          @endforeach
        </div>

      @endif
      @section('pagination')
        @if($paginate->total() > 24)
        <div class="container-fluid bg-grey mb-5 p-4 text-center">
          <nav aria-label="Page navigation" >
            {{ $paginate->appends(request()->except('page'))->links() }}
          </nav>
        </div>
        @endif
      @endsection
      <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-slideout" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Filter your search</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              @if(!empty($facets))
                <div class="filters">
                  @if(array_key_exists('department', $facets) && !empty($facets['department']['buckets']) )

                    <!-- Departments -->
                    <div class="col mb-3" >

                      <div class="contents-label mb-3">
                        <h5 class="lead">
                          <a data-toggle="collapse" href="#department">Department</a>
                        </h5>

                        @if(array_key_exists('department', $facets))
                          <ul  class="collapse" id="department">
                            @foreach ($facets['department']['buckets'] as $bucket)
                              <li>
                                <a href="/search/results?{{ $query  }}&department={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
                              </li>
                            @endforeach
                          </ul>
                        @endif

                      </div>
                    </div>
                  @endif
                  <!-- End of departments -->


                  @if(array_key_exists('department', $facets) && !empty($facets['department']['buckets']) )

                    <!-- Makers -->
                    <div class="col mb-3">
                      <div >
                        <div >

                          <div class="contents-label mb-3">
                            <h5 class="lead">
                              <a data-toggle="collapse" href="#maker">Maker</a>
                            </h5>

                            @if(array_key_exists('maker', $facets))
                              <ul  class="collapse" id="maker">
                                @foreach ($facets['maker']['buckets'] as $bucket)
                                  <li>
                                    <a href="/search/results?{{ $query  }}&maker={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
                                  </li>
                                @endforeach
                              </ul>
                            @endif

                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                  <!-- Meterials -->
                  @if(array_key_exists('material', $facets) && !empty($facets['material']['buckets']) )
                    <div class="col mb-3">

                      <div class="contents-label mb-3">
                        <h5 class="lead">
                          <a data-toggle="collapse" href="#material">Material</a>
                        </h5>

                        @if(array_key_exists('material', $facets))
                          <ul  class="collapse" id="material">
                            @foreach ($facets['material']['buckets'] as $bucket)
                              <li>
                                <a href="/search/results?{{ $query  }}&material={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count'])}}</a>
                              </li>
                            @endforeach
                          </ul>
                        @endif

                      </div>

                    </div>
                  @endif
                  <!-- End of materials -->

                  <!-- Periods -->
                  @if(array_key_exists('period', $facets) && !empty($facets['period']['buckets']) )

                    <div class="col mb-3">

                      <div class="contents-label mb-3">
                        <h5 class="lead">
                          <a data-toggle="collapse" href="#period">Period</a>
                        </h5>

                        @if(array_key_exists('period', $facets))
                          <ul class="collapse" id="period">
                            @foreach ($facets['period']['buckets'] as $bucket)
                              <li>
                                <a href="/search/results?{{ $query  }}&period={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
                              </li>
                            @endforeach
                          </ul>
                        @endif

                      </div>

                    </div>
                  @endif
                  <!-- End of periods -->
                  <!-- object_type -->
                  @if(array_key_exists('object_type', $facets) && !empty($facets['object_type']['buckets']) )

                    <div class="col mb-3">

                      <div class="contents-label mb-3">
                        <h5 class="lead">
                          <a data-toggle="collapse" href="#object_type">Object Type</a>
                        </h5>

                        @if(array_key_exists('object_type', $facets))
                          <ul class="collapse" id="object_type">
                            @foreach ($facets['object_type']['buckets'] as $bucket)
                              <li>
                                <a href="/search/results?{{ $query  }}&object_type={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
                              </li>
                            @endforeach
                          </ul>
                        @endif

                      </div>

                    </div>
                  @endif
                  <!-- End of object_type -->

                </div>
              @endif
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
