@if(array_key_exists('name', $record['_source']))
  <h3 class="lead collection">
    Named entities
  </h3>
  <ul class="entities">
  @foreach ($record['_source']['name'] as $name)
    @if(array_key_exists('reference', $name))
    <li>
      <a class="btn btn-sm btn-outline-dark " href="{{ URL::to('/id/terminology/' . $name['reference']['admin']['id']) }}">{{ ucfirst($name['reference']['summary_title']) }}</a>
    </li>
    @else
      <li>
        <a class="btn btn-sm btn-outline-dark " href="#">{{ ucfirst($name['value']) }}</a></li>
      </li>
    @endif
  @endforeach
  </ul>
@endif
