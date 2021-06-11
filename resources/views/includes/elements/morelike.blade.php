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
