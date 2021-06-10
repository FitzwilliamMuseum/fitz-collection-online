
  @if(array_key_exists('agents', $record['_source']))
  <h3 class="lead collection">
    Agents depicted
  </h4>
  <ul class="entities">
  @foreach($record['_source']['agents'] as $agent)
    @if(array_key_exists('admin', $agent))
      <li>
        <a class="btn btn-sm btn-outline-dark mb-1" href="/id/agent/{{ $agent['admin']['id']}}">{{ ucfirst($agent['summary_title'])}}</a>
      </li>
    @else
      <li>
        {{ ucfirst($agent['summary_title'])}}
      </li>
    @endif
  @endforeach
  </ul>
  @endif

  @if(array_key_exists('subjects', $record['_source']))
  <h3 class="lead collection">
    Subjects depicted
  </h3>
  <ul class="entities">
  @foreach($record['_source']['subjects'] as $subject)
    @if(array_key_exists('admin', $subject))
      <li>
        <a class="btn btn-sm btn-outline-dark mb-1" href="/id/terminology/{{ $subject['admin']['id']}}">{{ ucfirst($subject['summary_title'])}}</a>
      </li>
    @endif
  @endforeach
  </ul>
  @endif
