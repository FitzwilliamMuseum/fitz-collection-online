@foreach(array_slice($labels,0,10) as $label)
    {{ $label }}
    @if(!$loop->last)
        <br/>
    @endif
@endforeach
