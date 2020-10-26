
  @if(array_key_exists('agents', $record['_source']))
  <h4>Agents depicted</h4>
  <ul>
  @foreach($record['_source']['agents'] as $agent)
    @if(array_key_exists('admin', $agent))
      <li><a href="/id/agent/{{ $agent['admin']['id']}}">{{ ucfirst($agent['summary_title'])}}</a></li>
    @else
      <li>{{ ucfirst($agent['summary_title'])}}</li>
    @endif
  @endforeach
  </ul>
  @endif

  @if(array_key_exists('subjects', $record['_source']))
  <h4>Subjects depicted</h4>
  <ul>
  @foreach($record['_source']['subjects'] as $subject)
    @if(array_key_exists('admin', $subject))
      <li><a href="/id/terminology/{{ $subject['admin']['id']}}">{{ ucfirst($subject['summary_title'])}}</a></li>
    @else
      <li>{{ ucfirst($subject['summary_title'])}}</li>
    @endif
  @endforeach
  </ul>
  @endif
