@if(array_key_exists('materials', $record['_source']))
<h4>Materials used in production</h4>
<ul>
  @foreach($record['_source']['materials'] as $material)
    <li>
    @if(array_key_exists('note', $material))
      @foreach ($material['note'] as $note)
        {{ $note['value'] }}
      @endforeach
    @endif
    @foreach($material as $mat)
      @if(array_key_exists('admin', $mat))
        <a href="/id/terminology/{{ $mat['admin']['id']}}">{{ ucfirst($mat['summary_title'])}}</a>
      </li>
      @endif
    @endforeach
  @endforeach
</ul>
@endif
