<h2 class="lead ">Image attached to {{ $object['identifier'][0]['accession_number'] }}</h2>
<div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
  <div>
    <img class="img-fluid mx-auto d-block" src="{{ env('APP_URL')}}/imagestore/{{ $record['processed']['large']['location'] }}"
    loading="lazy" alt="An image of {{ $exif->getCaption() }}"
    />
  </div>
  <div class="text-center mb-2 mt-2">
    <a class="btn btn-sm btn-sm btn-dark m-1" href="#download-message" data-bs-toggle="collapse" aria-expanded="false" aria-controls="download-message"
    >@svg('fas-download',['class' => 'mr-2','width' => 15]) Use this image</a>
      <a class="btn btn-sm btn-sm btn-dark m-1" href="{{ route('record', $object['identifier'][1]['priref']) }}">@svg('fas-backward',['class' => 'mr-2','width' => 15]) Back to record</a>
  </div>
  <div class="bg-grey col-md-6 mt-2 mx-auto collapse p-3" id="download-message">
    <x-terms-of-use :path="$record['processed']['large']['location']"></x-terms-of-use>
  </div>
</div>

@section('hero_image', env('APP_URL'). '/imagestore/' . $record['processed']['large']['location'])
