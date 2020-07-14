@if(array_key_exists('measurements', $record['_source']))
<h4>Measurements and weight</h4>
<ul>
  @foreach($record['_source']['measurements']['dimensions'] as $dim)
  <li>{{ $dim['value'] }}</li>
  @endforeach
</ul>
@endif
