@if(!empty($shopify))
    <div class="container-fluid bg-gbdo  p-3">
        <div class="container">
            <h4 class="lead">Suggested products from Curating Cambridge</h4>
            <div class="row ">
                @foreach($shopify as $record)
                    <div class="col-md-3 mb-3">
                        <div class=" h-100">
                            @if(!is_null($record['thumbnail']))
                                <div class="results_image">
                                    <a href="{{ $record['url'][0] }}">
                                        <img class="results_image__thumbnail img-fluid"
                                             src="{{ str_replace(array('.jpg','.png'),array('_200x200.jpg','_200x200.png'),$record['thumbnail'][0])}}"
                                             alt="Featured image for the project: {{ $record['title'][0] }}"
                                             loading="lazy"/>
                                    </a>
                                </div>
                            @else
                                <div class="results_image">
                                    <a href="{{ $record['url'][0] }}">
                                        <img class="results_image__thumbnail img-fluid"
                                             src="https://content.fitz.ms/fitz-website/assets/gallery3_roof.jpg?key=directus-large-crop"
                                             alt="The Fitzwilliam Museum's gallery 3 roof"
                                             loading="lazy"/>
                                    </a>
                                </div>
                            @endif
                            <div class="card-body h-100">
                                <div class="contents-label mb-3">
                                    <h3 class="lead">
                                        <a href="{{ $record['url'][0] }}" class="stretched-link">
                                            {{ $record['title'][0] }}
                                        </a>
                                    </h3>
                                    <p>
                                        £{{ number_format((float)$record['price'][0], 2, '.', '') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
