@if(array_key_exists('component', $record['_source']))
  <h3 class="lead collection">
    Components of the work
  </h3>
  <p>
  @foreach($record['_source']['component'] as $component)
      {{ $component['name'] }}:

    @if(array_key_exists('materials', $component))
      composed of
          @foreach($component['materials'] as $material)
            <a href="/id/terminology/{{ $material['reference']['admin']['id'] }}">{{ $material['reference']['summary_title'] }}</a>
            @if(array_key_exists('note', $material))
              @foreach ($material['note'] as $note)
                ( {{ $note['value'] }})
              @endforeach
            @endif
          @endforeach
    @endif

    @if(array_key_exists('measurements', $component))
       @foreach($component['measurements']['dimensions'] as $dim)
         {{$dim['dimension']}} {{$dim['value']}} {{$dim['units']}}
       @endforeach
       <br/>
    @endif

  @endforeach
</p>
@endif
