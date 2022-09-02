@if(array_key_exists('department', $record['_source']))
    <p>
        Associated department: <a href="{{ route('department',urlencode($record['_source']['department']['value'])) }}">
            {{ $record['_source']['department']['value'] }}
        </a>
    </p>
@endif
