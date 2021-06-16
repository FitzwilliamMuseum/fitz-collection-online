
@if(array_key_exists('agents', $record['_source']))
  <h3 class="lead collection">
    People, subjects and objects depicted
  </h4>
  <ul class="entities">

    @if(array_key_exists('name', $record['_source']))

      @foreach ($record['_source']['name'] as $name)
        @if(array_key_exists('reference', $name))
          <li>
            <a class="btn btn-sm btn-outline-dark " href="{{ URL::to('/id/terminology/' . $name['reference']['admin']['id']) }}">{{ ucfirst($name['reference']['summary_title']) }}</a>
          </li>
        @else
          <li>
            <a class="btn btn-sm btn-outline-dark " href="#">{{ ucfirst($name['value']) }}</a></li>
          </li>
        @endif
      @endforeach

    @endif
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
    @if(array_key_exists('subjects', $record['_source']))
      @foreach($record['_source']['subjects'] as $subject)
        @if(array_key_exists('admin', $subject))
          <li>
            <a class="btn btn-sm btn-outline-dark mb-1" href="/id/terminology/{{ $subject['admin']['id']}}">{{ ucfirst($subject['summary_title'])}}</a>
          </li>
        @endif
      @endforeach
    @endif
  </ul>
@endif
