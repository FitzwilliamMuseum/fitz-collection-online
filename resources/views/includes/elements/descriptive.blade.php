@section('description', 'A record for a Fitzwilliam Museum object: ' . ucfirst($data['summary_title']) . ' ' . $data['identifier'][0]['accession_number'])
@if(array_key_exists('accession_number', $data['identifier'][0]))
    @if(array_key_exists('title',$data))
        @section('title', ucfirst($data['title'][0]['value'])  . ': ' . $data['identifier'][0]['accession_number'])
    @else
        @section('title', ucfirst($data['summary_title'])  . ': ' . $data['identifier'][0]['accession_number'])
    @endif
@endif

@if(array_key_exists('title', $data))
    <h3 class="lead collection">
        Titles
    </h3>
    <p>
        @foreach($data['title'] as $titles)
            {{ $titles['value'] }}
            @if(array_key_exists('translation',$titles))
                <br/>
                <span class="text-info">Translated as: {{ $titles['translation'][0]['value'] }}</span>
                @endif
                </br>
                @endforeach
    </p>
@endif

@include('includes.elements.makers')

@include('includes.elements.names')

@include('includes.elements.categories')

@if(array_key_exists('description', $data))
    <h3 class="lead collection">
        Description
    </h3>
    @foreach($data['description'] as $description)
        <p>{!! ucfirst(nl2br($description['value'])) !!} </p>
    @endforeach
@endif

@if(array_key_exists('note', $data))
    <h3 class="lead collection">
        Notes
    </h3>
    @foreach ($data['note'] as $note)
        <p>
            <strong>{{ ucfirst($note['type']) }}:</strong> {{ ucfirst($note['value']) }}
        </p>
    @endforeach
@endif
