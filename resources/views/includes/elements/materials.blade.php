@if(array_key_exists('materials', $record['_source']))
<h3 class="lead collection">
  Materials used in production
</h3>
<p>
  @foreach($record['_source']['materials'] as $material)
    @if(array_key_exists('note', $material))
      @foreach ($material['note'] as $note)
        {{ $note['value'] }}
      @endforeach
    @endif
    @foreach($material as $mat)
      @if(array_key_exists('admin', $mat))
        <a href="/id/terminology/{{ $mat['admin']['id']}}">{{ ucfirst($mat['summary_title'])}}</a>

      @endif
    @endforeach
    <br />
  @endforeach
</p>
@endif
