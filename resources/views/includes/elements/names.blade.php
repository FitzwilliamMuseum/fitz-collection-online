@if(array_key_exists('name', $data))
    <h3 class="lead collection">
        Entities
    </h3>

    <ul>
        @foreach($data['name'] as $name)
            @if(array_key_exists('reference', $name))
                <li>
                    @if(array_key_exists('type', $name['reference']))
                        {{ ucfirst($name['reference']['type'])}}:
                    @endif
                    <a href="{{route('terminology',[$name['reference']['admin']['id']])}}">
                        {{ ucfirst($name['reference']['summary_title']) }}
                    </a>
                </li>
            @elseif(array_key_exists('type', $name))
                <li>{{ ucfirst($name['type']) }}: {{ $name['value'] }}</li>
            @else(!array_key_exists('reference', $name))
                <li>{{$name['value']}}</li>
            @endif
        @endforeach
    </ul>
@endif
