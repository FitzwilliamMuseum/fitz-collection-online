@if(array_key_exists('venues', $exhibition))
    @foreach($exhibition['venues'] as $venue)
        <p>
            Held: <a href="{{ route('terminology', [ $venue['admin']['id']]) }}">{{ $venue['summary_title'] }}</a>
        @if(array_key_exists('@link', $venue))
            @if(array_key_exists('date', $venue['@link']))
                @foreach ($venue['@link']['date'] as $date)
                    @if(array_key_exists('from', $date))
                        <br/>From: {{ $date['from']['value'] }}
                        @php
                            $startDate = $date['from']['value'];
                        @endphp
                    @endif
                    @if(array_key_exists('to', $date))
                            - {{ $date['to']['value'] }}
                    @endif
                @endforeach
            @endif
        @endif
        </p>
    @endforeach
@else
    <p>
        No details recorded.
    </p>
@endif
