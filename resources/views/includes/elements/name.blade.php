@if(array_key_exists('name', $record['_source']))
  <h4>Named entities</h4>
  <ul>
  @foreach ($record['_source']['name'] as $name)
    <li>
      <a href="{{ URL::to('/id/terminology/'. $name['reference']['admin']['id']) }}">{{ ucfirst($name['reference']['summary_title']) }}</a></li>
  @endforeach
  </ul>
@endif
