<h2 class="lead ">Image attached to {{ $object['identifier'][0]['accession_number'] }}</h2>
<div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
  <div>
    <img class="img-fluid mx-auto d-block" src="{{ env('APP_URL')}}/imagestore/{{ $record['processed']['large']['location'] }}"
    loading="lazy" alt="An image of {{ $exif->getCaption() }}"
    /></a>
  </div>
  <div class="text-center mb-2 mt-2">
    <a class="btn btn-sm btn-sm btn-dark m-1" href="#download-message" data-bs-toggle="collapse" aria-expanded="false" aria-controls="download-message"
    ><i class="fas fa-download mr-2"></i> Use this image</a>
      <a class="btn btn-sm btn-sm btn-dark m-1" href="{{ route('record', $object['identifier'][1]['priref']) }}">Back to record</a>
    @if(Arr::has($filtered[0]['processed'], 'zoom'))
      <a class="btn btn-sm btn-dark m-1" href="{{ route('image.iiif', $object['multimedia'][0]['admin']['id']) }}" ><img src="/images/logos/iiif.svg" width="20px" />  IIIF view</a>
    @endif
  </div>
  <div class="bg-grey col-md-6 mt-2 mx-auto collapse p-3" id="download-message">
    <x-terms-of-use :path="$record['processed']['large']['location']" />
  </div>
</div>

@section('hero_image', env('APP_URL'). '/imagestore/' . $record['processed']['large']['location'])
