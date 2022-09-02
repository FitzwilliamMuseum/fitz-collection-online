@if(array_key_exists('medium', $data))
    <h3 class="lead collection">
        Materials used in production
    </h3>
    <p>
        @foreach($data['medium'] as $materials)
            @foreach($materials as $material)
                @foreach($material as $fabric)
                    @if(array_key_exists('reference', $fabric))
                        <a href="{{ route('terminology',$fabric['reference']['admin']['id'])}}">{{ ucfirst($fabric['reference']['summary_title'])}}</a>
                        @if(array_key_exists('description', $fabric))
                            @foreach($fabric['description'] as $desc)
                                : {{ ucfirst($desc['value'])}}
                            @endforeach
                        @endif
                        @if(!$loop->last)
                            <br/>
                        @endif
                    @endif
                @endforeach
            @endforeach
        @endforeach
    </p>
@endif
