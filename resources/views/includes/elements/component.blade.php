@if(array_key_exists('component', $data))
    <h3 class="lead collection">
        Components of the work
    </h3>
    <p>
        @foreach($data['component'] as $component)
            {{ $component['name'] }}

            @if(array_key_exists('materials', $component))
                composed of
                @foreach($component['materials'] as $material)
                    <a href="{{ route('terminology',$material['reference']['admin']['id']) }}">
                        {{ $material['reference']['summary_title'] }}
                    </a>
                    @if(array_key_exists('note', $material))
                        @foreach ($material['note'] as $note)
                            ( {{ $note['value'] }})
                        @endforeach
                    @endif
                @endforeach
            @endif
            @if(array_key_exists('measurements', $component))
                @foreach($component['measurements']['dimensions'] as $dim)
                    @if(array_key_exists('dimension',$dim) && array_key_exists('units',$dim))
                        {{$dim['dimension']}} {{$dim['value']}} {{$dim['units']}}
                    @endif
                @endforeach
            @endif
            @if(!$loop->last)
                <br/>
            @endif
        @endforeach
    </p>
@endif
