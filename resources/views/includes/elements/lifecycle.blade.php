@if(array_key_exists('lifecycle',$data ))

    @if(array_key_exists('acquisition', $data['lifecycle']))
        <h3 class="lead collection">
            Acquisition and important dates
        </h3>
        <p>
            @foreach($data['lifecycle']['acquisition'] as $acquisition)
                @if(array_key_exists('method', $acquisition))
                    Method of acquisition: {{ ucfirst($acquisition['method']['value']) }}
                @endif
                @if(array_key_exists('date', $acquisition))
                    ({{ $acquisition['date'][0]['value'] }})
                @endif
                @if(array_key_exists('agents',$acquisition))
                    by
                    @foreach ($acquisition['agents'] as $agent)
                        <a href="{{ route('agent', [$agent['admin']['id']]) }}">{{ $agent['summary_title'] }}</a>
                    @endforeach
                @endif
                @if(!$loop->last)
                    <br/>
                @endif
            @endforeach
        </p>
    @endif

    @if(array_key_exists('creation', $data['lifecycle']))
        @if(array_key_exists('date', $data['lifecycle']['creation'][0]))
            <h3 class="lead collection">
                Dating
            </h3>

            <p>
                @if(array_key_exists('periods', $data['lifecycle']['creation'][0]))
                    @foreach($data['lifecycle']['creation'][0]['periods'] as $date)
                        <a href="{{route('terminology',[$date['admin']['id']]) }}">{{ ucfirst($date['summary_title']) }}</a>
                        <br/>
                    @endforeach
                @endif
                @if(!empty($data['lifecycle']['creation'][0]['date']))
                    @foreach ($data['lifecycle']['creation'][0]['date'] as $dating)
                        @if(array_key_exists('range', $dating))
                            @if($dating['range'])
                                @if(array_key_exists('from', $dating))
                                    @if(array_key_exists('precision', $dating['from']))
                                        {{ ucfirst($dating['from']['precision']) }}
                                    @endif
                                    @if(array_key_exists('value', $dating['from']))
                                        @if(!is_string($dating['from']['value']))
                                            {{ ltrim(abs($dating['from']['value']),'0') }}
                                        @else
                                            {{  ltrim($dating['from']['value'],'0') }}
                                        @endif
                                    @endif
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
                                        @if(array_key_exists('value', $dating['to']))
                                            @if(!is_string($dating['to']['value']))
                                                {{ abs($dating['to']['value']) }}
                                            @else
                                                {{ ltrim($dating['to']['value'],'0') }}
                                            @endif
                                        @endif
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

                @if(array_key_exists('value', $data['lifecycle']['creation'][0]['date'][0]))
                    Production date:
                    @if(isset($data['lifecycle']['creation'][0]['date'][0]['precision']))
                        {{ $data['lifecycle']['creation'][0]['date'][0]['precision'] }}
                    @endif

                    @php
                        if(array_key_exists('value', $data['lifecycle']['creation'][0]['date'][0])) {
                          $dateTime = $data['lifecycle']['creation'][0]['date'][0]['value'];
                          if($dateTime < 0){
                            $suffix = ' BC';
                            $string = abs($dateTime) . $suffix;
                          } else {
                            $suffix = 'AD ';
                            $string = $suffix . $dateTime;
                          }
                        }
                    @endphp
                    {{ $string }}
                    @if(array_key_exists('note', $data['lifecycle']['creation'][0]['date'][0]))
                        : {{ $data['lifecycle']['creation'][0]['date'][0]['note'][0]['value']}}
                    @endif
                @endif
            </p>
        @endif

    @endif

    @if(array_key_exists('creation', $data['lifecycle']))
        @if(array_key_exists('maker',$data['lifecycle']['creation'][0]))

            @if(array_key_exists('note', $data['lifecycle']['creation'][0]))
                @php
                    $notes = $data['lifecycle']['creation'][0]['note']
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
    @endif
@endif

@if(array_key_exists('school_or_style', $data))
    <h3 class="lead collection">
        School or Style
    </h3>
    <p>
        @foreach($data['school_or_style'] as $school)
            <a href="{{  route('terminology',[$school['admin']['id']]) }}">{{ $school['summary_title'] }}</a>
            @if(!$loop->last)
                <br/>
            @endif
        @endforeach
    </p>
@endif
