@if(array_key_exists('inscription', $record['_source']))
<h4>Inscription or legend</h4>
@foreach($record['_source']['inscription'] as $inscription)
<h5>Inscription present</h5>

@if(array_key_exists('description', $inscription))
<p>{{ $inscription['description'][0]['value'] }}</p>
@endif
<ul>
  @if(array_key_exists('transcription', $inscription))
  <li>Text present: {{ $inscription['transcription'][0]['value'] }}</li>
  @endif
  @if(array_key_exists('location',$inscription ))
  <li>Located on object: {{ ucfirst($inscription['location']) }}</li>
  @endif
  @if(array_key_exists('method',$inscription))
  <li>Method of creation: {{ ucfirst($inscription['method']) }}</li>
  @endif
  @if(array_key_exists('type',$inscription))
  <li>Type: {{ ucfirst($inscription['type']) }}</li>
  @endif
</ul>
@endforeach
@endif
