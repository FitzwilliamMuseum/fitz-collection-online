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
        <h4>Visual results</h4>
        <div class="form-group form-check ">
          <input type="checkbox" class="form-check-input" id="images" name="images"
          @if(request()->has('images'))
            checked
          @endif
          >
          <label class="form-check-label" for="images">Only with images?</label>
        </div>
      </div>
      <div class="col">
        <h4>Operand for your search</h4>
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
        <h4>Sort by last update</h4>
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

  @if(!empty($facets))
  <h3>Filters</h3>
  <div class="filters row">
    @if(array_key_exists('department', $facets) && !empty($facets['department']['buckets']) )

    <!-- Departments -->
    <div class="col-md-3 mb-3">
      <div class="card h-100">
        <div class="card-body ">

          <div class="contents-label mb-3">
            <h5>
            Department
          </h5>

              @if(array_key_exists('department', $facets))
              <ul>
              @foreach ($facets['department']['buckets'] as $bucket)
                <li>
                  <a href="{{ Request::fullUrl() }}&department={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
                </li>
              @endforeach
              </ul>
              @endif

          </div>
        </div>
      </div>
    </div>
    @endif
    <!-- End of departments -->


    @if(array_key_exists('department', $facets) && !empty($facets['department']['buckets']) )

    <!-- Makers -->
    <div class="col-md-3 mb-3">
      <div class="card h-100">
        <div class="card-body ">

          <div class="contents-label mb-3">
            <h5>
            Maker
          </h5>

              @if(array_key_exists('maker', $facets))
              <ul>
              @foreach ($facets['maker']['buckets'] as $bucket)
                <li>
                  <a href="{{ Request::fullUrl() }}&maker={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
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
      <div class="col-md-3 mb-3">
        <div class="card h-100">
          <div class="card-body ">

            <div class="contents-label mb-3">
              <h5>
                Material
              </h5>

              @if(array_key_exists('material', $facets))
                <ul>
                  @foreach ($facets['material']['buckets'] as $bucket)
                    <li>
                      <a href="{{ Request::fullUrl() }}&material={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count'])}}</a>
                    </li>
                  @endforeach
                </ul>
              @endif

            </div>
          </div>
        </div>
      </div>
    @endif
    <!-- End of materials -->

    <!-- Periods -->
    @if(array_key_exists('period', $facets) && !empty($facets['period']['buckets']) )

    <div class="col-md-3 mb-3">
      <div class="card h-100">
        <div class="card-body ">

          <div class="contents-label mb-3">
            <h5>
              Period
            </h5>

              @if(array_key_exists('period', $facets))
              <ul>
              @foreach ($facets['period']['buckets'] as $bucket)
                <li>
                  <a href="{{ Request::fullUrl() }}&period={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ number_format($bucket['doc_count']) }}</a>
                </li>
              @endforeach
              </ul>
              @endif

          </div>
        </div>
      </div>
    </div>
    @endif
    <!-- End of periods -->
    {{-- <!-- object_type -->
    @if(array_key_exists('object_type', $facets) && !empty($facets['object_type']['buckets']) )

    <div class="col-md-4 mb-3">
      <div class="card h-100">
        <div class="card-body ">

          <div class="contents-label mb-3">
            <h5>
            Object type
            </h5>

              @if(array_key_exists('object_type', $facets))
              <ul>
              @foreach ($facets['object_type']['buckets'] as $bucket)
                <li>
                  <a href="{{ Request::fullUrl() }}&object_type={{  $bucket['key'] }}">{{  ucfirst($bucket['key']) }}: {{ $bucket['doc_count'] }}</a>
                </li>
              @endforeach
              </ul>
              @endif

          </div>
        </div>
      </div>
    </div>
  @endif --}}
    <!-- End of object_type -->

  </div>
  @endif

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

  <nav aria-label="Page navigation">
    {{ $paginate->appends(request()->except('page'))->links() }}
  </nav>
@endif
@endsection
