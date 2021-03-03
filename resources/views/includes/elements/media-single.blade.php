<h2>Full size image</h2>
<div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
  <div>
    <img class="img-fluid mx-auto d-block" src="{{ env('APP_URL')}}/imagestore/{{ $record['processed']['original']['location'] }}"
    loading="lazy" alt="An image of "
    /></a>
  </div>
  <div class="text-center mb-2 mt-2">
    <span class="btn btn-wine m-1 p-2 share">
      <a href="{{ env('APP_URL')}}/imagestore/{{ $record['processed']['original']['location'] }}" target="_blank"
      download="{{ $record['processed']['original']['location'] }}"><i class="fas fa-download mr-2"></i>  Download this image</a>
    </span>
    <span class="btn-wine btn m-1 p-2 share">
      <a href="/id/object/{{ $object['identifier'][1]['priref']}}">Back to record</a>
    </span>
    @if(array_key_exists('multimedia', $object))
      @if(array_key_exists('zoom', $object['multimedia'][0]['processed']))
        <span class="btn btn-wine m-1 p-2 share">
          <a href="/id/image/iiif/{{ $object['multimedia'][0]['admin']['id']}}" ><img src="/images/logos/iiif.svg" width="20px" />  IIIF view</a>
        </span>
      @endif
    @endif
  </div>
</div>

@section('hero_image', env('APP_URL'). '/imagestore/' . $record['processed']['original']['location'])
