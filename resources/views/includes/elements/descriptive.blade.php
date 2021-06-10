@section('title', ucfirst($record['_source']['summary_title'])  . ': ' . $data[0]['_source']['identifier'][0]['accession_number'])
@section('description', 'A record for a Fitzwilliam Museum object: ' . ucfirst($record['_source']['summary_title']) . ' ' . $data[0]['_source']['identifier'][0]['accession_number'])
@if(array_key_exists('title', $record['_source']))
  <h3 class="lead collection">
    Titles
  </h3>
  <p>
    @foreach($record['_source']['title'] as $titles)
      {{ $titles['value'] }}
    @if(array_key_exists('translation',$titles))
      <br/><span class="text-info">Translated as: {{ $titles['translation'][0]['value'] }}</span>
    @endif
    </br>
    @endforeach
  </p>
@endif

@if(array_key_exists('description', $record['_source']))
  <h3 class="lead collection">
    Description
  </h3>
  @foreach($record['_source']['description'] as $description)
    <p>{!! ucfirst(nl2br($description['value'])) !!} </p>
  @endforeach
@endif

@if(array_key_exists('note', $record['_source']))
  <h3 class="lead collection">
    Notes
  </h3>
  @foreach ($record['_source']['note'] as $note)
  <p>
    <strong>{{ ucfirst($note['type']) }}:</strong> {{ ucfirst($note['value']) }}
  </p>
  @endforeach
@endif
