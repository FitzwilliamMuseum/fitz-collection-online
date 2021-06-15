@if(array_key_exists('lifecycle',$record['_source'] ))

  @if(array_key_exists('acquisition', $record['_source']['lifecycle']))
    <h3 class="lead collection">
      Acquisition and important dates
    </h4>
    <p>
      @foreach($record['_source']['lifecycle']['acquisition'] as $acquistion)
        @if(array_key_exists('method', $acquistion))
            Method of acquisition: {{ ucfirst($acquistion['method']['value']) }}
        @endif
        @if(array_key_exists('date', $acquistion))
          ({{ $acquistion['date'][0]['value'] }})
        @endif
        @if(array_key_exists('agents',$acquistion))
          by
          @foreach ($acquistion['agents'] as $agent)
             <a href="{{ route('agent', [$agent['admin']['id']]) }}">{{ $agent['summary_title'] }}</a>
          @endforeach
        @endif
      <br/>
      @endforeach
    </p>
  @endif

  @if(array_key_exists('creation', $record['_source']['lifecycle']))
    @if(array_key_exists('date', $record['_source']['lifecycle']['creation'][0]))
      <h3 class="lead collection">
        Dating
      </h3>

      {{-- @if(array_key_exists('note',$record['_source']['lifecycle']['creation'][0]))
        @foreach($record['_source']['lifecycle']['creation'][0]['note'] as $note)
          <p>{{ $note['value'] }}</p>
        @endforeach
      @endif --}}

      <p>
        @if(array_key_exists('periods', $record['_source']['lifecycle']['creation'][0]))
          @foreach($record['_source']['lifecycle']['creation'][0]['periods'] as $date)
          <a href="/id/terminology/{{ $date['admin']['id']}}">{{ ucfirst($date['summary_title']) }}</a><br />
          @endforeach
        @endif
        @if(!empty($record['_source']['lifecycle']['creation'][0]['date']))
          @foreach ($record['_source']['lifecycle']['creation'][0]['date'] as $dating)
            @if(array_key_exists('range', $dating))
              @if($dating['range'])
                @if(array_key_exists('from', $dating))
                  @if(array_key_exists('precision', $dating['from']))
                    {{ ucfirst($dating['from']['precision']) }}
                  @endif
                  {{ $dating['from']['earliest'] }}
                  @if(array_key_exists('era', $dating['from']))
                    @foreach($dating['from']['era'] as $era)
                      {{ $era }}
                    @endforeach
                  @endif
                  -
                  @if(array_key_exists('to', $dating))
                    @if(array_key_exists('precision', $dating['to']))
                      {{ ucfirst($dating['to']['precision']) }}
                    @endif
                    {{ $dating['to']['earliest'] }}
                    @if(array_key_exists('era', $dating['to']))
                      @foreach($dating['to']['era'] as $era)
                        {{ $era }}
                      @endforeach
                    @endif
                  @endif
                @endif
              @endif
            @endif
          @endforeach
        @endif

        @if(array_key_exists('value', $record['_source']['lifecycle']['creation'][0]['date'][0]))
          Production date:
            @if(isset($record['_source']['lifecycle']['creation'][0]['date'][0]['precision']))
              {{ $record['_source']['lifecycle']['creation'][0]['date'][0]['precision'] }}
            @endif

            @php
            if(array_key_exists('value', $record['_source']['lifecycle']['creation'][0]['date'][0])) {
              $dateTime = $record['_source']['lifecycle']['creation'][0]['date'][0]['value'];
              if($dateTime < 0){
                $suffix = ' BCE';
                $string = abs($dateTime) . '' . $suffix;
              } else {
                $suffix = 'CE ';
                $string = $suffix . '' . $dateTime;
              }
            }
            @endphp
            {{ $string }}
            @if(array_key_exists('note', $record['_source']['lifecycle']['creation'][0]['date'][0]))
              : {{ $record['_source']['lifecycle']['creation'][0]['date'][0]['note'][0]['value']}}
            @endif
        @endif
      </p>
    @endif

  @endif

  @if(array_key_exists('creation', $record['_source']['lifecycle']))
    @if(array_key_exists('maker',$record['_source']['lifecycle']['creation'][0]))

    @if(array_key_exists('note', $record['_source']['lifecycle']['creation'][0]))
      @php
      $notes = $record['_source']['lifecycle']['creation'][0]['note'];
      @endphp
      <h3 class="lead collection">
        Note
      </h3>
      @foreach($notes as $note)
        <p>
          {{ ucfirst($note['value']) }}
        </p>
      @endforeach
    @endif
  @endif


  @if(array_key_exists('places', $record['_source']['lifecycle']['creation'][0]))
    <h3 class="lead collection">
      Place(s) associated
    </h4>
    <ul class="entities">
      @php
        $coord =  array();
      @endphp
      @foreach($record['_source']['lifecycle']['creation'][0]['places'] as $place)
        <li>
          {{ preg_replace('@\x{FFFD}@u', 'î', $place['summary_title']) }}
          @php
            $geo = new \App\LookupPlace();
            $geo->setPlace($place['summary_title']);
            $geodata = $geo->lookup()->first()->getCoordinates();
            $lat = $geodata->getLatitude();
            $lon = $geodata->getLongitude();
            $coord[] = array('lat' => $lat, 'lng' => $lon);
          @endphp

          @if(array_key_exists('hierarchies', $place))
            @foreach ($place['hierarchies'] as $hierarchies)
              @php
              $hierarchies = array_reverse($hierarchies, true);
              @endphp
              @foreach($hierarchies as $hierarchy)
                @if(array_key_exists('summary_title', $hierarchy))
                  &Sc; {{ $hierarchy['summary_title'] ?? ''}}
                @endif
              @endforeach
            @endforeach
          @endif
        </li>
      @endforeach
    </ul>
    @section('map')
      {{-- @dd($coord); --}}
    @map([
      'lat' => $coord[0]['lat'],
      'lng' => $coord[0]['lng'],
      'zoom' => 6,
      'markers' => [
        ['title' => 'Place associated',
        'lat' => $coord[0]['lat'],
        'lng' => $coord[0]['lng'],
        'popup' => 'Place associated'],
      ]
    ])
    @endsection
  @endif
  @if(array_key_exists('collection', $record['_source']['lifecycle']))
    @if(array_key_exists('places', $record['_source']['lifecycle']['collection'][0]))
      <h3 class="lead collection">
        Find spot
      </h3>
      <ul class="entities">
        @foreach($record['_source']['lifecycle']['collection'][0]['places'] as $place)
          <li>
            {{ preg_replace('@\x{FFFD}@u', 'î', $place['summary_title']) }}
            @php
              $geo = new \App\LookupPlace();
              $geo->setPlace($place['summary_title']);
              $geodata = $geo->lookup()->first()->getCoordinates();
              $lat = $geodata->getLatitude();
              $lon = $geodata->getLongitude();

            @endphp
            @section('map')
            @map([
              'lat' => $lat,
              'lng' => $lon,
              'zoom' => 6,
              'markers' => [
                ['title' => 'Place of origin',
                'lat' => $lat,
                'lng' => $lon,
                'popup' => 'Place of origin'],
              ]
            ])
            @endsection
            @if(array_key_exists('hierarchies', $place))
              @foreach ($place['hierarchies'] as $hierarchies)
                @php
                $hierarchies = array_reverse($hierarchies, true);
                @endphp
                @foreach ($hierarchies as $hierarchy)
                  @if(array_key_exists('summary_title', $hierarchy))
                    &Sc; {{ $hierarchy['summary_title'] ?? ''}}
                  @endif
                @endforeach
              @endforeach
            @endif
          </li>
        @endforeach
      </ul>
    @endif
  @endif
@endif



@endif

@if(array_key_exists('school_or_style', $record['_source']))
<h3 class="lead collection">
  School or Style
</h3>
<p>
@foreach($record['_source']['school_or_style'] as $school)
  <a href="{{  route('terminology',[$school['admin']['id']]) }}">{{ $school['summary_title'] }}</a>
@endforeach
</p>
@endif
