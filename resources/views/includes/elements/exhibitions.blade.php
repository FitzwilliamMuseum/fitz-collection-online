@if(array_key_exists('exhibitions', $record['_source']))
  <h3 class="lead collection">
    Related exhibitions
  </h4>
  <ul>
  @foreach ($record['_source']['exhibitions'] as $exhibition)
      <li>
        <a href="{{ route('exhibition.record', [$exhibition['admin']['id']]) }}">{{ $exhibition['summary_title'] }}</a>
      </li>
  @endforeach
  </ul>
@endif
