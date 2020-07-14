@if(array_key_exists('department', $record['_source']))
<p>Associated department: {{ $record['_source']['department']['value'] }}</p>
@endif
