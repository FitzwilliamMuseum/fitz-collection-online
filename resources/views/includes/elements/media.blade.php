@if(array_key_exists('multimedia', $record['_source']))
<div class="col-md-12 mb-3">
  <h2>Principal image</h2>
  <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
    <div>

      @if(array_key_exists('multimedia', $record['_source']))
      <img class="img-fluid mx-auto d-block" src="https://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['original']['location'] }}"
      loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
      />
      @endif
    </div>
  </div>
  @endif

  @if(array_key_exists('multimedia', $record['_source']))
  @if(!empty(array_slice($record['_source']['multimedia'],1)))
  <h3>Alternative views</h3>
  <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
    <div class="row">
      @foreach(array_slice($record['_source']['multimedia'],1) as $media)
      <div class="col-md-4 mx-auto">
        <img class="img-fluid  mt-4" src="https://api.fitz.ms/mediaLib/{{ $media['processed']['preview']['location'] }}"
        loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
        />
      </div>
      @endforeach
    </div>
  </div>
  @endif
</div>

@include('includes/structure/iiif')
@endif
