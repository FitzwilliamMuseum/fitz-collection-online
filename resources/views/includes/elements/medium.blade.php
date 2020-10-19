@if(array_key_exists('medium', $record['_source']))
<h4>Materials used in production</h4>
<ul>
  @foreach($record['_source']['medium'] as $materials)

    @foreach($materials as $material)
      @foreach($material as $fabric)
        @if(array_key_exists('reference', $fabric))
          <li><a href="/id/terminology/{{ $fabric['reference']['admin']['id']}}">{{ ucfirst($fabric['reference']['summary_title'])}}</a>
              @if(array_key_exists('description', $fabric))
                : {{ ucfirst($fabric['description']['value'])}}
              @endif
              </li>
        @endif
      @endforeach
    @endforeach
  @endforeach
</ul>
@endif
