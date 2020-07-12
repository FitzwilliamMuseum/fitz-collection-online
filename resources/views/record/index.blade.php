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
  <div class="col mb-3">
    <div class="card card-body h-100">
      <div class="container">
        @if(array_key_exists('multimedia', $record['_source']))
        <img class="img-fluid" src="https://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['original']['location'] }}"
          loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
          />
        @section('iiif')
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/mootools/1.6.0/mootools.min.js"></script>
        <script type="text/javascript" src="/js/iipmooviewer-2.0-min.js"></script>

        <script type="text/javascript">

            // IIPMooViewer options: See documentation at http://iipimage.sourceforge.net for more details

            // The *full* image path on the server. This path does *not* need to be in the web
            // server root directory. On Windows, use Unix style forward slash paths without
            // the "c:" prefix
            var image = '/{{ str_replace(".jpg", ".ptif", $record["_source"]["multimedia"][0]["processed"]["original"]["location"])}}';

            // Copyright or information message
            var credit = 'fitzwilliam Museum';

            // Create our iipmooviewer object
            new IIPMooViewer( "viewer", {
        	image: image,
        	credit: credit,
          server: 'https://api.fitz.ms/iipsrv/iipsrv.fcgi',
        	viewport: {resolution:3}
            });

          </script>
        @endsection
        <div id="viewer"></div>


        @endif
        </div>
        @if(array_key_exists('multimedia', $record['_source']))

        <div class="row">
        @foreach(array_slice($record['_source']['multimedia'],1) as $media)
        <div class="col-md-4 mx-auto">
        <img class="img-fluid img-thumbnail mt-4" src="https://api.fitz.ms/mediaLib/{{ $media['processed']['preview']['location'] }}"
          loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
          />
        </div>
        @endforeach
        </div>
        @endif
      </div>
    </div>
    @endif

    <div class="col mb-3">
      <div class="card card-body h-100">
        <div class="container h-100">
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
                <li>Method of acquisition: {{ $record['_source']['lifecycle']['acquisition'][0]['method']['value'] }}</li>
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
                <li>{{ $date['summary_title']}}</li>
              @endforeach
              @endif
              @if(array_key_exists('date', $record['_source']['lifecycle']['creation'][0]))
                <li>Date: {{$record['_source']['lifecycle']['creation'][0]['date'][0]['value']}}</li>
              @endif
            </ul>

              @if(array_key_exists('maker', $record['_source']['lifecycle']['creation'][0]))
              <h4>Production</h4>
              <ul>
                <li>Made by: {{ $record['_source']['lifecycle']['creation'][0]['maker'][0]['summary_title']}}</li>
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

          @if(array_key_exists('department', $record['_source']))
            <p>Associated department: {{ $record['_source']['department']['value'] }}</p>
          @endif

          @if(array_key_exists('publications', $record['_source']))
          <h4>References and bibliographic entries</h4>
            <ul>
              @foreach($record['_source']['publications'] as $pub)
                <li>{{ $pub['summary_title'] }}</li>
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
