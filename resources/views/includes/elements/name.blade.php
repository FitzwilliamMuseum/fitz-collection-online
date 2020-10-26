@if(array_key_exists('name', $record['_source']))
  <h4>Named entities</h4>
  <ul>
  @foreach ($record['_source']['name'] as $name)
    @if(array_key_exists('reference', $name))
    <li>
      <a href="{{ URL::to('/id/terminology/' . $name['reference']['admin']['id']) }}">{{ ucfirst($name['reference']['summary_title']) }}</a>
    </li>
    @else
      <li>
        {{ ucfirst($name['value']) }}
      </li>
    @endif
  @endforeach
  </ul>
@endif
