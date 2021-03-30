@if(array_key_exists('multimedia', $record['_source']))
  <div class="col-md-12 mb-3">
    <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
      <div>
        @if(array_key_exists('multimedia', $record['_source']))
          <a href="/id/image/{{ $record['_source']['multimedia'][0]['admin']['id']}}"><img class="img-fluid mx-auto d-block" src="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['large']['location'] }}"
            loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
            /></a>
          @endif
          <div class="text-center mt-2">
            <span class="btn btn-wine m-1 p-2 share">
              <a href="/id/image/{{ $record['_source']['multimedia'][0]['admin']['id']}}" ><i class="fas fa-search mr-2"></i> View image details</a>
            </span>
            @if(array_key_exists('source',$record['_source']['multimedia'][0]['admin']))
            <span class="btn btn-wine m-1 p-2 share">
              <a href="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['large']['location'] }}" target="_blank"
              download="{{ basename($record['_source']['multimedia'][0]['processed']['large']['location'] ) }}"><i class="fas fa-download mr-2"></i> Download this image</a>
            </span>
          @else
            <span class="btn btn-wine m-1 p-2 share">
              <a href="{{ env('APP_URL')}}/imagestore/{{ $record['_source']['multimedia'][0]['processed']['large']['location'] }}" target="_blank"
              download="{{ basename($record['_source']['multimedia'][0]['processed']['large']['location'] ) }}"><i class="fas fa-download mr-2"></i> Download this image</a>
            </span>
            @endif
            @if(array_key_exists('multimedia', $record['_source']))
              @php
              $con = array();
              foreach ($record['_source']['multimedia'] as $image ){
              $con[] = array('zoom',Arr::has($image, 'processed.zoom'));
              }
              @endphp

              @if($discount = Arr::get($con, 'zoom', true))
                <span class="btn btn-wine m-1 p-2 share">
                  <a href="/id/image/iiif/{{ $record['_source']['multimedia'][0]['admin']['id']}}" ><img src="/images/logos/iiif.svg" width="20px" />  IIIF view</a>
                </span>
                <span class="btn btn-wine m-1 p-2 share">
                  <a href="https://api.fitz.ms/data-distributor/iiif/{{ $record['_source']['admin']['id']}}/manifest" ><img src="/images/logos/iiif.svg" width="20px" />  IIIF Manifest</a>
                </span>
              @endif
            @endif
          </div>
        </div>
      </div>
    @endif

    @if(array_key_exists('multimedia', $record['_source']))
      @if(!empty(array_slice($record['_source']['multimedia'],1)))
        @php
        $images = [];
        $ports = array();
        foreach (array_slice($record['_source']['multimedia'],1,9) as $image ){
        $ports[] = array('portfolio', Arr::has($image, 'admin.source'));
        // dump($ports);
        if(Arr::get($ports, 'portfolio', true)){
          unset($image['processed']);
        }
        $images[] = $image;
        }
        // dump($images);
        @endphp
        @if(Arr::has($images,'processed'))
        <h3>Alternative views</h3>
        <div class="row ">
          @foreach($images as $media)
            <div class="col-md-4 mt-3">
              <div class="card card-body h-100">
                <a href="/id/image/{{ $media['admin']['id']}}"><img class="img-fluid mx-auto d-block" src="{{ env('APP_URL')}}/imagestore/{{ $media['processed']['preview']['location'] }}"
                  loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
                  /></a>


                 @if($media['admin']['source'] != 'portfolio')
                    <span class="btn btn-wine m-1 mt-3 mb-3 p-2 share">
                      <a href="{{ env('APP_URL')}}/imagestore/{{ $media['processed']['large']['location'] }}" target="_blank"
                      download="{{ basename($media['processed']['large']['location'] ) }}"><i class="fas fa-download mr-2"></i>  Download this image</a>
                    </span>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
          @php
            $records = count($record['_source']['multimedia']);
          @endphp
          @if($records > 9)
            <span class="btn btn-wine m-1 mt-3 mb-3 p-2 share d-block">
              <a href="{{ route('images.multiple', [$record['_source']['identifier'][1]['value']]) }}"
              ><i class="fas fa-eye mr-2"></i>  View all {{ $records }} images attached </a>
            </span>
          @endif
        @endif
      </div>
      @endif
      {{-- @include('includes/structure/iiif') --}}
    @endif
