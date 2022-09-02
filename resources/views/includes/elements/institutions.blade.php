@if(array_key_exists('institutions', $data))
    <h3 class="lead collection">
        Associated departments & institutions
    </h3>
    <p>
        @foreach($data['institutions'] as $institution)
            Owner or interested party: <a
                href="{{ route('agent',$institution['admin']['id']) }}">
                {{ $institution['summary_title'] }}
            </a>
            @if($loop->last)
                <br/>
            @endif
        @endforeach
        @if(array_key_exists('department', $data))
            Associated department: <a href="{{ route('department', urlencode($data['department']['value']))}}">
                {{ $data['department']['value'] }}
            </a>
        @endif
    </p>
@endif
