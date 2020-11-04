@if(array_key_exists('exhibitions', $record['_source']))
  <h4>
    Related exhibitions
  </h4>
  <ul>
  @foreach ($record['_source']['exhibitions'] as $exhibition)
      <li>{{ $exhibition['summary_title'] }}</li>
  @endforeach
  </ul>
@endif
