@if(array_key_exists('materials', $data))
    <h3 class="lead collection">
        Materials used in production
    </h3>
    <p>
        @foreach($data['materials'] as $material)
            @if(array_key_exists('note', $material))
                @foreach ($material['note'] as $note)
                    {{ $note['value'] }}
                @endforeach
            @endif
            @foreach($material as $mat)
                @if(array_key_exists('admin', $mat))
                    <a href="{{ route('terminology',$mat['admin']['id'])}}">
                        {{ ucfirst($mat['summary_title'])}}
                    </a>
                @endif
            @endforeach
            @if(!$loop->last)
                <br/>
            @endif
        @endforeach
    </p>
@endif
