@if(array_key_exists('inscription', $record['_source']))
<h3 class="lead collection">
  Inscription or legends present
</h3>
@foreach($record['_source']['inscription'] as $inscription)
@if(array_key_exists('description', $inscription))
<p><strong>Inscription present:</strong> {{ $inscription['description'][0]['value'] }}</p>
@endif
<ul class="entities">
  @if(array_key_exists('transcription', $inscription))
  <li><em>Text:</em> <span class="text-info">{{ $inscription['transcription'][0]['value'] }}</span></li>
  @endif
  @if(array_key_exists('location',$inscription ))
  <li><em>Location:</em> {{ ucfirst($inscription['location']) }}</li>
  @endif
  @if(array_key_exists('method',$inscription))
  <li><em>Method of creation:</em> {{ ucfirst($inscription['method']) }}</li>
  @endif
  @if(array_key_exists('type',$inscription))
  <li><em<Type:</em> {{ ucfirst($inscription['type']) }}</li>
  @endif
</ul>
@endforeach
@endif
