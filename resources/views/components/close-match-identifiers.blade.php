<div class="my-2">
    <h3 class="collection lead">Closely matched 3rd party resources</h3>
    @foreach($combined as $key => $value)
        <a href="{{ $value }}">{{ $value }}</a>
        @if(!$loop->last)
            <br/>
        @endif
    @endforeach
</div>
