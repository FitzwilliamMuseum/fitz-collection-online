<h3 class="lead collection">
    Identification numbers
</h3>
<p>
    @foreach($data['identifier'] as $id)
        @if(array_key_exists('type', $id))
            @if($id['type'] === 'uri')
                <span class="visually-hidden"><a href="{{ $id['value']}}">Stable URI</a><br/></span>
            @elseif($id['type'] === 'priref')
                Primary reference Number: <a href="{{ route('record',$id['value'])}}">{{ $id['value']}}</a><br/>
            @elseif($id['type'] === 'Online 3D model')
                <span class="visually-hidden"><a
                        href="https://sketchfab.com/3d-models/{{ $id['value']}}">Sketchfab model</a><br/></span>
            @elseif($id['type'] === 'Wikidata')
                Wikidata: <a href="https://www.wikidata.org/wiki/{{ $id['value'] }}">{{ $id['value'] }}</a><br/>
            @else
                {{ ucfirst($id['type']) }}: {{ $id['value']}}<br/>
            @endif
        @else
            {{ $id['value']}}<br/>
        @endif
    @endforeach
</p>
<h3 class="lead collection">
    Audit data
</h3>
<span class="badge  bg-dark mb-2 mr-1">Created: {{ \Carbon\Carbon::createFromTimestamp($data['admin']['created']/ 1000)->format('l j F Y') }}</span>
<span class="badge  bg-dark mb-2 mr-1">Updated: {{ \Carbon\Carbon::createFromTimestamp($data['admin']['modified']/ 1000)->format('l j F Y') }}</span>
<span class="visually-hidden">Last processed: {{ \Carbon\Carbon::createFromTimestamp($data['admin']['processed']/ 1000)->format('l j F Y') }}</span>

@include('includes.elements.institutions')
