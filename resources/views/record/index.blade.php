@extends('layouts/layout')


@section('content')
<div class="row">
  @foreach($data as $record)
  @if(array_key_exists('multimedia', $record['_source']))
  @section('hero_image_title', ucfirst($record['_source']['summary_title']))
  @section('hero_image','https://api.fitz.ms/mediaLib/' . $record['_source']['multimedia'][0]['processed']['original']['location'])
  @else
  @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
  @section('hero_image_title', "The inside of our Founder's entrance")
  @endif

  @if(array_key_exists('multimedia', $record['_source']))
  <div class="col-md-6 mb-3">
    <h2>Principal image</h2>
    <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
      <div class="card card-body h-100">

        @if(array_key_exists('multimedia', $record['_source']))
        <img class="img-fluid" src="https://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['original']['location'] }}"
        loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
        />
        @endif
      </div>
    </div>
    @endif

    @if(array_key_exists('multimedia', $record['_source']))
    @if(!empty(array_slice($record['_source']['multimedia'],1)))
    <h3>Alternative views</h3>
    <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
      <div class="row">
        @foreach(array_slice($record['_source']['multimedia'],1) as $media)
        <div class="col-md-4 mx-auto">
          <img class="img-fluid  mt-4" src="https://api.fitz.ms/mediaLib/{{ $media['processed']['preview']['location'] }}"
          loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
          />
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  @include('includes/structure/iiif')
  @endif

  @if(array_key_exists('multimedia', $record['_source']))
  <div class="col-md-6 mb-3">
  @else
  <div class="col-md-12 mb-3">
  @endif
    <h2>Object information</h2>
    <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
      <div class="container">
        @section('title', ucfirst($record['_source']['summary_title']))

        @if(array_key_exists('description', $record['_source']))
          <p>{{ ucfirst($record['_source']['description'][0]['value']) }} </p>
        @endif

        @if(array_key_exists('note', $record['_source']))
        <p>{{ ucfirst($record['_source']['note'][0]['value']) }} </p>
        @endif

        @if(array_key_exists('legal', $record['_source']))
        <h4>Legal notes</h4>
        <p>{{ ucfirst($record['_source']['legal']['credit_line']) }} </p>
        @endif

        @if(array_key_exists('lifecycle',$record['_source'] ))

        @if(array_key_exists('acquisition', $record['_source']['lifecycle']))
        <h4>Acquisition and important dates</h4>
        <ul>
          <li>Method of acquisition: {{ ucfirst($record['_source']['lifecycle']['acquisition'][0]['method']['value']) }}</li>
          @if(array_key_exists('date', $record['_source']['lifecycle']['acquisition'][0]))
          <li>Dates: {{ $record['_source']['lifecycle']['acquisition'][0]['date'][0]['value'] }}</li>
          @endif
        </ul>
        @endif

        @if(array_key_exists('creation', $record['_source']['lifecycle']))
        <h4>Dating</h4>
        <ul>
          @if(array_key_exists('periods', $record['_source']['lifecycle']['creation'][0]))
          @foreach($record['_source']['lifecycle']['creation'][0]['periods'] as $date)
            <li>{{ ucfirst($date['summary_title']) }}</li>
          @endforeach
          @endif

          @if(array_key_exists('date', $record['_source']['lifecycle']['creation'][0]))
            <li>Date: {{$record['_source']['lifecycle']['creation'][0]['date'][0]['value']}}</li>
          @endif
        </ul>

        @if(array_key_exists('maker', $record['_source']['lifecycle']['creation'][0]))
        <h4>Production</h4>
        <ul>
          <li>Made by: <a href="/id/agent/{{ $record['_source']['lifecycle']['creation'][0]['maker'][0]['admin']['id'] }}">{{ $record['_source']['lifecycle']['creation'][0]['maker'][0]['summary_title']}}</a></li>
        </ul>
        @endif
        @endif
        @endif

        @if(array_key_exists('measurements', $record['_source']))
        <h4>Measurements and weight</h4>
        <ul>
          @foreach($record['_source']['measurements']['dimensions'] as $dim)
          <li>{{ $dim['value'] }}</li>
          @endforeach
        </ul>
        @endif

        @if(array_key_exists('content',$record['_source']))
        @if(array_key_exists('agents', $record['_source']['content']))
        <h4>Agents depicted</h4>
        <ul>
        @foreach($record['_source']['content']['agents'] as $agent)
          <li><a href="/id/agent/{{ $agent['admin']['id']}}">{{ ucfirst($agent['summary_title'])}}</a></li>
        @endforeach
        </ul>
        @endif
        @if(array_key_exists('subjects', $record['_source']['content']))
        <h4>Subjects depicted</h4>
        <ul>
        @foreach($record['_source']['content']['subjects'] as $subject)
          <li><a href="/id/terminology/{{ $subject['admin']['id']}}">{{ ucfirst($subject['summary_title'])}}</a></li>
        @endforeach
        </ul>
        @endif
        @endif

        @if(array_key_exists('component', $record['_source']))
        <h4>Components of work</h4>
        <ul>
        @foreach($record['_source']['component'] as $component)
        <li>
          {{ $component['name']}}: <a href="/id/terminology/{{ $component['materials'][0]['reference']['admin']['id']}}">{{ $component['materials'][0]['reference']['summary_title']}}</a>
          @if(array_key_exists('measurements', $component))
          : dimensions -

          @foreach($component['measurements']['dimensions'] as $dims)
          {{ $dims['value']}}
          @endforeach
          @endif
        </li>

        @endforeach
        </ul>
        @endif

        @if(array_key_exists('medium', $record['_source']))
        <h4>Materials used in production</h4>
        <ul>
          @foreach($record['_source']['medium'] as $materials)
            @foreach($materials as $material)
            @if(array_key_exists('description', $material[0]))
            <li>{{ ucfirst($material[0]['description'][0]['value'])}}</li>
            @endif
            @if(array_key_exists('reference', $material[0]))
            <li><a href="/id/terminology/{{ $material[0]['reference']['admin']['id']}}">{{ ucfirst($material[0]['reference']['summary_title'])}}</a></li>
            @endif
          @endforeach
          @endforeach
        </ul>
        @endif

        @if(array_key_exists('techniques', $record['_source']))
        <h4>Techniques used in production</h4>
        <ul>
          @foreach($record['_source']['techniques'] as $techniques)
          @if(array_key_exists('reference', $techniques))
          <li><a href="/id/terminology/{{ $techniques['reference']['admin']['id']}}">{{ ucfirst($techniques['reference']['summary_title'])}}</a> @if(array_key_exists('description', $techniques))
          : {{ ucfirst($techniques['description'][0]['value'])}}
          @endif</li>
          @endif
          @endforeach
        </ul>
        @endif

          @if(array_key_exists('inscription', $record['_source']))
          <h4>Inscription or legend</h4>
          @foreach($record['_source']['inscription'] as $inscription)
          <ul>
            @if(array_key_exists('transcription', $inscription))
            <li>Text present: {{ $inscription['transcription'][0]['value'] }}</li>
            @endif
            @if(array_key_exists('location',$inscription ))
            <li>Located on object: {{ ucfirst($inscription['location']) }}</li>
            @endif
            @if(array_key_exists('method',$inscription))
            <li>Method of creation: {{ ucfirst($inscription['method']) }}</li>
            @endif
            @if(array_key_exists('type',$inscription))
            <li>Type: {{ ucfirst($inscription['type']) }}</li>
            @endif
          </ul>
          @endforeach
          @endif

          @if(array_key_exists('department', $record['_source']))
          <p>Associated department: {{ $record['_source']['department']['value'] }}</p>
          @endif

          @if(array_key_exists('publications', $record['_source']))
          <h4>References and bibliographic entries</h4>
          <ul>
            @foreach($record['_source']['publications'] as $pub)
              <li><a href="/id/publication/{{ $pub['admin']['id']}}">{{ $pub['summary_title'] }}</a>
              @if(array_key_exists('page', $pub['@link']))
              page(s): {{ $pub['@link']['page']}}
              @endif
              </li>
            @endforeach
          </ul>
          @endif

          <h4>Identification numbers</h4>
          <ul>
            @foreach($record['_source']['identifier'] as $id)
            <li>{{ ucfirst($id['type']) }}: {{ $id['value']}}</li>
            @endforeach
          </ul>
          <h4>Audit data</h4>
          <ul>
            <li>Created: {{ \Carbon\Carbon::createFromTimestamp($record['_source']['admin']['created']/ 1000)->format('d-m-Y') }}</li>
            <li>Updated: {{ \Carbon\Carbon::createFromTimestamp($record['_source']['admin']['modified']/ 1000)->format('d-m-Y') }}</li>
            <li>Last processed: {{ \Carbon\Carbon::createFromTimestamp($record['_source']['admin']['processed']/ 1000)->format('d-m-Y') }}</li>
            <li>Data source: {{ $record['_source']['admin']['source'] }}</li>
          </ul>
          <div class="share">
            <btn class="btn btn-wine m-1"><a href="{{ url()->current() }}/json">json</a></btn>
          </div>

        </div>
      </div>

    </div>
    @foreach($record['_source']['identifier'] as $id)
    @if($id['type'] == 'Online 3D model')
    @section('sketchfab')
    <div class="container">
      <h2>3D scan</h2>
      <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
        <div class="embed-responsive embed-responsive-1by1">
          <iframe title="A 3D model of {{ $record['_source']['summary_title'] }}" class="embed-responsive-item"
          src="https://sketchfab.com/models/{{ $id['value']}}/embed?"
          frameborder="0" allow="autoplay; fullscreen; vr" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
        </div>
      </div>
    </div>
    @endsection
    @endif
    @endforeach


    @endforeach
  </div>
@endsection
