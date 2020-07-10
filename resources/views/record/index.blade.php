@extends('layouts/layout')


@section('content')
<div class="row">
  @foreach($data as $record)
  @if(array_key_exists('multimedia', $record['_source']))
  @section('hero_image_title', ucfirst($record['_source']['summary_title']))

  @section('hero_image','http://api.fitz.ms/mediaLib/' . $record['_source']['multimedia'][0]['processed']['original']['location'])
  @else
  @section('hero_image','https://fitz-cms-images.s3.eu-west-2.amazonaws.com/img_20190105_153947.jpg')
  @section('hero_image_title', "The inside of our Founder's entrance")

  @endif

  @if(array_key_exists('multimedia', $record['_source']))
  <div class="col mb-3">
    <div class="card card-body h-100">
      <div class="container h-100">
        @if(array_key_exists('multimedia', $record['_source']))
        <img class="img-fluid" src="http://api.fitz.ms/mediaLib/{{ $record['_source']['multimedia'][0]['processed']['original']['location'] }}"
          loading="lazy" alt="An image of {{ ucfirst($record['_source']['summary_title']) }}"
          />
          @endif
        </div>
      </div>
    </div>
    @endif

    <!-- <pre>@php(var_dump($record['_source']))</pre> -->
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
                <li>Method of acquisition: {{ $record['_source']['lifecycle']['acquisition'][0]['method']['value'] }} {{ $record['_source']['lifecycle']['acquisition'][0]['date'][0]['value'] }}</li>
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

          @if(array_key_exists('department, '$record['_source']))
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
