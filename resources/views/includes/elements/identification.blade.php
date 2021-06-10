<h3 class="lead collection">
  Identification numbers
</h3>
<p>
  @foreach($record['_source']['identifier'] as $id)
    @if(array_key_exists('type', $id))
      @if($id['type'] === 'uri')
        <span class="sr-only"><a href="{{ $id['value']}}">Stable URI</a><br/></span>
      @elseif($id['type'] === 'priref')
        Primary reference Number: <a href="/id/object/{{ $id['value']}}">{{ $id['value']}}</a><br />
      @elseif($id['type'] === 'Online 3D model')
        <span class="sr-only"><a href="https://sketchfab.com/3d-models/{{ $id['value']}}">Sketchfab model</a><br/></span>
      @else
        {{ ucfirst($id['type']) }}: {{ $id['value']}}<br />
      @endif
    @else
      {{ $id['value']}}<br/>
    @endif
  @endforeach
</p>
<h3 class="lead collection">
  Audit data
</h4>
<ul class="entities">
  <li class="btn btn-sm btn-outline-dark mb-1 mr-1">Created: {{ \Carbon\Carbon::createFromTimestamp($record['_source']['admin']['created']/ 1000)->format('l j F Y') }}</li>
  <li class="btn btn-sm btn-outline-dark mb-1 mr-1">Updated: {{ \Carbon\Carbon::createFromTimestamp($record['_source']['admin']['modified']/ 1000)->format('l j F Y') }}</li>
  <li class="sr-only">Last processed: {{ \Carbon\Carbon::createFromTimestamp($record['_source']['admin']['processed']/ 1000)->format('l j F Y') }}</li>
</ul>
@include('includes/elements/institutions')
