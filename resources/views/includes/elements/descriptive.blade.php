@section('title', ucfirst($record['_source']['summary_title']))

@if(array_key_exists('title', $record['_source']))

  <h4>
    Titles
  </h4>
  <ul>
  @foreach($record['_source']['title'] as $titles)
  <li>{{ $titles['value'] }} </li>
  @endforeach
  </ul>
@endif

@if(array_key_exists('description', $record['_source']))
  @foreach($record['_source']['description'] as $description)
    <p>{!! ucfirst(nl2br($description['value'])) !!} </p>
  @endforeach
@endif

@if(array_key_exists('note', $record['_source']))
  <p>
    {{ ucfirst($record['_source']['note'][0]['value']) }}
  </p>
@endif
