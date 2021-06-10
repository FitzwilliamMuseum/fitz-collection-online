@if(array_key_exists('medium', $record['_source']))
<h3 class="lead collection">
  Materials used in production
</h3>
<p>
  @foreach($record['_source']['medium'] as $materials)

    @foreach($materials as $material)
      @foreach($material as $fabric)
        @if(array_key_exists('reference', $fabric))
          <a href="/id/terminology/{{ $fabric['reference']['admin']['id']}}">{{ ucfirst($fabric['reference']['summary_title'])}}</a>
              @if(array_key_exists('description', $fabric))
                @foreach($fabric['description'] as $desc)
                : {{ ucfirst($desc['value'])}}
                @endforeach
              @endif
              <br />
        @endif
      @endforeach
    @endforeach
  @endforeach
</p>
@endif
