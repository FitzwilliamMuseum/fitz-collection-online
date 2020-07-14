@if(array_key_exists('lifecycle',$record['_source'] ))

  @if(array_key_exists('acquisition', $record['_source']['lifecycle']))
  <h4>Acquisition and important dates</h4>
  <ul>
    <li>Method of acquisition: {{ ucfirst($record['_source']['lifecycle']['acquisition'][0]['method']['value']) }}</li>
    @if(array_key_exists('date', $record['_source']['lifecycle']['acquisition'][0]))
    <li>Dates: {{ $record['_source']['lifecycle']['acquisition'][0]['date'][0]['value'] }}</li>
    @endif
  </ul>
  @endif

  @if(array_key_exists('creation', $record['_source']['lifecycle']))
  <h4>Dating</h4>

  @if(array_key_exists('note',$record['_source']['lifecycle']['creation'][0]))
    <p>{{ $record['_source']['lifecycle']['creation'][0]['note'][0]['value'] }}</p>
  @endif
  <ul>
    @if(array_key_exists('periods', $record['_source']['lifecycle']['creation'][0]))
    @foreach($record['_source']['lifecycle']['creation'][0]['periods'] as $date)
      <li>{{ ucfirst($date['summary_title']) }}</li>
    @endforeach
    @endif

    @if(array_key_exists('date', $record['_source']['lifecycle']['creation'][0]))
      <li>Date:
        @if(isset($record['_source']['lifecycle']['creation'][0]['date'][0]['precision']))
        {{ $record['_source']['lifecycle']['creation'][0]['date'][0]['precision'] }}
        @endif
        {{$record['_source']['lifecycle']['creation'][0]['date'][0]['value']}}
      </li>
    @endif
  </ul>
  @endif

  @if(array_key_exists('maker',$record['_source']['lifecycle']['creation'][0]))
  <h4>Maker(s)</h4>
  <ul>
  @foreach($record['_source']['lifecycle']['creation'][0]['maker'] as $maker)
  @if(array_key_exists('@link', $maker))
      @if(array_key_exists('role', $maker['@link']))
      <li>
      @foreach($maker['@link']['role'] as $role)
        {{ ucfirst($role['value'])}}:
      @endforeach
      @endif
      @if(array_key_exists('admin', $maker))
      <a href="/id/agent/{{ $maker['admin']['id']}}">{{ $maker['summary_title']}}</a>
      @else
      <li>{{ $maker['summary_title']}}
      @endif
    @endif
    </li>
  @endforeach
  </ul>
  @endif

  @if(array_key_exists('places', $record['_source']['lifecycle']['creation'][0]))
  <h4>Place(s) associated</h4>
  <ul>
  @foreach($record['_source']['lifecycle']['creation'][0]['places'] as $place)
  <li>{{ $place['summary_title'] }}</li>
  @endforeach
  </ul>
  @endif



@endif
