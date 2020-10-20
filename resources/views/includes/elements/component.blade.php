@if(array_key_exists('component', $record['_source']))
  <h4>Components of the work</h4>
  <ul>
  @foreach($record['_source']['component'] as $component)
    <li>
      {{ $component['name'] }}

    @if(array_key_exists('materials', $component))
      composed of:

          @foreach($component['materials'] as $material)
            <a href="/id/terminology/{{ $material['reference']['admin']['id'] }}">{{ $material['reference']['summary_title'] }}</a>,
          @endforeach

    @endif
    @if(array_key_exists('measurements', $component))
       measuring:
       {{ implode(' by ', Arr::flatten($component['measurements']['dimensions'])) }}
    @endif

  </li>
  @endforeach
</ul>
@endif
