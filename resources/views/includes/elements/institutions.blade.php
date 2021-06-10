@if(array_key_exists('institutions', $record['_source']))
<h3 class="lead collection">
  Associated institutions
</h3>
<p>
  @foreach($record['_source']['institutions'] as $institution)
  <a href="/id/agent/{{ $institution['admin']['id']}}">{{ $institution['summary_title'] }}</a><br/>
  @endforeach
</p>
@endif
