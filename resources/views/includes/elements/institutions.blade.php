@if(array_key_exists('institutions', $record['_source']))
    <h3 class="lead collection">
        Associated departments & institutions
    </h3>
    <p>
        @foreach($record['_source']['institutions'] as $institution)
            Owner or interested party: <a
                href="/id/agent/{{ $institution['admin']['id']}}">{{ $institution['summary_title'] }}</a><br/>
        @endforeach
        @if(array_key_exists('department', $record['_source']))
            Associated department: <a
                href="/id/departments/{{ urlencode($record['_source']['department']['value'])}}">{{ $record['_source']['department']['value'] }}</a>
        @endif
    </p>
@endif
