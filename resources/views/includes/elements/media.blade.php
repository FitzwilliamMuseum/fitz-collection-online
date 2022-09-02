@if(array_key_exists('multimedia', $data))
    @section('hero_image', env('APP_URL') . '/imagestore/' .  $data['multimedia'][0]['processed']['mid']['location'])
    @section('media-files')
        @if(array_key_exists('large', $data['multimedia'][0]['processed']))
            <div class="container-fluid bg-white">
                <div class="shadow-sm p-3 mx-auto mb-3">
                    <div>
                        @php
                            $dim = $data['multimedia'][0]['processed']['large']['measurements']['dimensions'][1]['value'];
                            if($dim > 1000){
                              $width = '100%';
                            } else {
                              $width = '';
                            }
                        @endphp
                        <a href="{{ route('image.single', $data['multimedia'][0]['admin']['id']) }}"><img
                                width="{{ $width }}" class="img-fluid mx-auto d-block main-image rounded"
                                src="{{ env('APP_URL')}}/imagestore/{{ $data['multimedia'][0]['processed']['large']['location'] }}"
                                loading="lazy" alt="An image of {{ ucfirst($data['summary_title']) }}"
                            /></a>
                    </div>
                    <div class="text-center mt-2">
                        <x-exif-details-media :data="$data"></x-exif-details-media>
                    </div>
                    <div class="text-center mt-2">
                        <a class="btn btn-sm btn-sm btn-dark m-1"
                           href="{{ route('image.single', $data['multimedia'][0]['admin']['id']) }}">
                            @svg('fas-search',['width' => 15]) View image details
                        </a>


                        @php
                            $con = array();
                            foreach ($data['multimedia'] as $image ){
                              if(Arr::has($image, 'processed.zoom')) {
                                $con[] = array(
                                  'zoom' => Arr::has($image, 'processed.zoom'),
                                  'image' => $image['admin']['id']
                                );
                              }
                            }
                        @endphp

                        @if(!empty($con))
                            @if(Arr::get($con, 'zoom', true))
                                @php
                                    $slow = Arr::pluck($con, 'image');
                                @endphp
                                {{-- Check for IIIF --}}
                                <a class="btn btn-sm btn-sm btn-dark m-1"
                                   href="{{ route('image.iiif', $data['multimedia'][0]['admin']['id']) }}">
                                    <img src="{{ asset( '/images/logos/iiif.svg') }}" alt="IIIF icon - view image" width="20px"/> Universal Viewer Deep zoom
                                </a>
                                <a class="btn btn-sm btn-sm btn-dark m-1" href="{{ route('image.mirador', $data['multimedia'][0]['admin']['id']) }}">
                                    <img src="{{ asset( '/images/icons/Mirador.svg')}}" alt="Mirador icon - view image" width="20px"/> Mirador deep zoom
                                </a>
                                <a class="btn btn-sm btn-sm btn-dark m-1" href="https://api.fitz.ms/data-distributor/iiif/{{ $data['admin']['id']}}/manifest">
                                    <img src="{{ asset('/images/logos/iiif.svg') }}" alt="IIIF Manifest" width="20px"/> IIIF Manifest
                                </a>
                                <a class="btn btn-sm btn-sm btn-dark m-1" href="{{ route('slow.iiif', ['image' => $slow[0]]) }}">
                                    @svg('fas-eye', ['width' => 15]) Slow looking
                                </a>
                            @endif
                        @endif
                        @php
                            $three = [];
                        @endphp
                        @foreach($data['identifier'] as $key => $rep)
                            @if(array_key_exists('type',$rep))
                                @if($rep['type'] ===  'Online 3D model')
                                    @php
                                        $three[] = true;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                        @if(!empty($three))
                            <a class="btn btn-sm btn-sm btn-dark m-1 "
                               href="{{ route('sketchfab', $data['identifier'][1]['priref']) }}"><img
                                    src="{{ asset( "/images/logos/sketchfab-logo.svg") }}"
                                    width="15"
                                    height="15"
                                    alt="Sketchfab logo"
                                /> 3D view</a>
                        @endif
                        <a class="btn btn-sm btn-sm btn-dark m-1" href="#download-message" data-bs-toggle="collapse"
                           aria-expanded="false" aria-controls="download-message"
                        >@svg('fas-download',['width' => 15]) Use this image</a>

                    </div>
                    <div class="bg-grey col-md-6 mt-2 mx-auto collapse p-3" id="download-message">
                        <x-terms-of-use :path="$data['multimedia'][0]['processed']['large']['location']"></x-terms-of-use>
                    </div>
                    <x-color-thief-object-details :data="$data"></x-color-thief-object-details>
                </div>
            </div>
        @endif

        @if(array_key_exists('multimedia', $data))
            @if(!empty(array_slice($data['multimedia'],1)))
                @php
                    $images = [];
                    foreach (array_slice($data['multimedia'],1,5) as $image ){
                        $images[] = array(
                          'admin' => $image['admin'],
                          'processed' => $image['processed']
                        );
                    }
                @endphp

                @if(sizeof($images) > 0)
                    <div class="container-fluid bg-gbdo p-3 ">
                        <div class="container text-center">
                            <h3 class="lead collection mx-auto">
                                Alternative views
                            </h3>
                            <div class="row">
                                @foreach($images as $media)
                                    <div class="col-md-3 mt-3 mx-auto ">
                                        <div class="h-100">
                                            <a href="{{ route('image.single', $media['admin']['id']) }}">
                                                <img class="img-fluid mx-auto d-block rounded"
                                                     src="{{ env('APP_URL')}}/imagestore/{{ $media['processed']['preview']['location'] }}"
                                                     loading="lazy"
                                                     alt="An image of {{ ucfirst($data['summary_title']) }}"/>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                $records = count($data['multimedia'])
                            @endphp
                            @if($records > 4)
                                <div class="container">
                                    <a class="btn btn-sm btn-sm btn-dark m-1 mt-2"
                                       href="{{ route('images.multiple', [$data['identifier'][1]['value']]) }}"
                                    >@svg('fas-images', ['width' => 15]) View all {{ $records }} images attached </a>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif
            @endif
        @endif
    @endsection

@endif
