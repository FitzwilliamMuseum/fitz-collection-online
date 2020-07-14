@if(array_key_exists('materials', $record['_source']))
<h4>Materials used in production</h4>
<ul>
  @foreach($record['_source']['materials'] as $material)
    @foreach($material as $mat)
        <li><a href="/id/terminology/{{ $mat['admin']['id']}}">{{ ucfirst($mat['summary_title'])}}</a></li>
    @endforeach

  @endforeach
</ul>
@endif
