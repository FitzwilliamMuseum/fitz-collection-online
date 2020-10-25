<h2>Full size image</h2>
<div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
  <div>
    <img class="img-fluid mx-auto d-block" src="https://api.fitz.ms/mediaLib/{{ $record['processed']['original']['location'] }}"
    loading="lazy" alt="An image of "
    /></a>
  </div>
  <div class="text-center mb-2 mt-2">
    <span class="btn btn-wine m-1 p-2 share">
      <a href="/imagestore/{{ $object['multimedia'][0]['processed']['original']['location'] }}" target="_blank"
      download="{{ basename($object['multimedia'][0]['processed']['original']['location'] ) }}"><i class="fas fa-download mr-2"></i>  Download this image</a>
    </span>
    <span class="btn-wine btn m-1 p-2 share">
      <a href="/id/object/{{ $object['identifier'][1]['priref']}}">Back to record</a>
    </span>
  </div>
</div>

@section('hero_image', 'https://api.fitz.ms/mediaLib/' . $record['processed']['original']['location'])
