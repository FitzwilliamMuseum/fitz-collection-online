@if(array_key_exists('lifecycle', $publication))
    <p>
        This publication has been used <strong>{{ $count }}</strong> times in our system.
    </p>
    @if(array_key_exists('publication', $publication['lifecycle'] ))
        @foreach($publication['lifecycle']['publication'][0]['date'] as $date)
            <p>
                Publication Date: {{ $date['value'] }}
            </p>
        @endforeach
    @endif
@endif
