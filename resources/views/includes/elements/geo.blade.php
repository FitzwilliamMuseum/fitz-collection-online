@if(array_key_exists('lifecycle',$data ))
  @if(array_key_exists('creation',$data['lifecycle']))
  @if(array_key_exists('places', $data['lifecycle']['creation'][0]))
    <h3 class="lead collection">
      Place(s) associated
    </h3>
    <ul class="entities">
      @php
      $coord =  array();
      $placeName = '';
      @endphp
      @foreach($data['lifecycle']['creation'][0]['places'] as $place)
        @php
        $placeName .= $place['summary_title'];
        @endphp
        <li>
          {{ preg_replace('@\x{FFFD}@u', 'î', $place['summary_title']) }}
          @if(array_key_exists('hierarchies', $place))
            @foreach ($place['hierarchies'] as $hierarchies)
              @php
              $hierarchies = array_reverse($hierarchies, true);
              @endphp
              @foreach($hierarchies as $hierarchy)
                @if(array_key_exists('summary_title', $hierarchy))
                  &Sc; {{ $hierarchy['summary_title'] ?? ''}}
                  @php
                  $placeName .= ', ';
                  $placeName .= $hierarchy['summary_title'] ?? '';
                  @endphp
                @endif
              @endforeach
            @endforeach
          @endif
        </li>
        @php
        $geo = new \App\LookupPlace();
        $geo->setPlace($placeName);
        $gd = $geo->lookup();
        if(!$gd->isEmpty()){
          $geodata = $gd->first()->getCoordinates();
          $lat = $geodata->getLatitude();
          $lon = $geodata->getLongitude();
          $coord[] = array('lat' => $lat, 'lng' => $lon);
        }
        @endphp

      @endforeach
    </ul>
    @if(!empty($coord))
      {{-- @section('map')
        @map([
          'lat' => $coord[0]['lat'],
          'lng' => $coord[0]['lng'],
          'zoom' => 6,
          'markers' => [
            ['title' => 'Place associated',
            'lat' => $coord[0]['lat'],
            'lng' => $coord[0]['lng'],
            'popup' => 'Place associated'],
          ]
        ])
      @endsection --}}
    @endif
  @endif
  @if(array_key_exists('collection', $data['lifecycle']))
    @if(array_key_exists('places', $data['lifecycle']['collection'][0]))
      <h3 class="lead collection">
        Find spot
      </h3>
      <ul class="entities">
        @foreach($data['lifecycle']['collection'][0]['places'] as $place)
          <li>
              <a href="{{route('terminology',$place['admin']['id'])}}">{{ preg_replace('@\x{FFFD}@u', 'î', $place['summary_title']) }}</a>
            @php
            if($place['summary_title'] === 'Thebes (Egypt)'){
              $lookup = 'Luxor Egypt';
            } elseif($place['summary_title'] === 'Abydos Egypt'){
              $lookup = 'Sohag, Egypt';
            } else  {
              $lookup = $place['summary_title'];
            }
            @endphp
            @php
            $geo = new \App\LookupPlace();
            $geo->setPlace($lookup);
            $gd = $geo->lookup();
            if(!$gd->isEmpty()){
              $geodata = $geo->lookup()->first()->getCoordinates();
              $lat = $geodata->getLatitude();
              $lon = $geodata->getLongitude();
            }
            @endphp

            @isset($lat)
              @section('map')
                @map([
                  'lat' => $lat,
                  'lng' => $lon,
                  'zoom' => 6,
                  'markers' => [
                    ['title' => 'Place of origin',
                    'lat' => $lat,
                    'lng' => $lon,
                    'popup' => 'Place of origin'],
                  ]
                ])
              @endsection
            @endisset
            @if(array_key_exists('hierarchies', $place))
              @foreach ($place['hierarchies'] as $hierarchies)
                @php
                $hierarchies = array_reverse($hierarchies, true);
                @endphp
                @foreach ($hierarchies as $hierarchy)
                  @if(array_key_exists('summary_title', $hierarchy))
                      &Sc; <a href="{{ route('terminology',$hierarchy['admin']['id']) }}">
                          {{ $hierarchy['summary_title'] ?? ''}}
                      </a>
                  @endif
                @endforeach
              @endforeach
            @endif
          </li>
        @endforeach
      </ul>
    @endif
  @endif
@endif
@endif
