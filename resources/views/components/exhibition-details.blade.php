@if(array_key_exists('venues', $exhibition))
    @foreach($exhibition['venues'] as $venue)
        <p>Held: {{ $venue['summary_title'] }}</p>
        @if(array_key_exists('@link', $venue))
            @if(array_key_exists('date', $venue['@link']))
                @foreach ($venue['@link']['date'] as $date)
                    @if(array_key_exists('from', $date))
                        From: {{ $date['from']['value'] }}
                        @php
                            $startDate = $date['from']['value'];
                        @endphp
                    @endif
                    @if(array_key_exists('to', $date))
                        To: {{ $date['to']['value'] }}
                    @endif
                @endforeach
            @endif
        @endif
    @endforeach
@else
    <p>
        No details recorded.
    </p>
@endif
