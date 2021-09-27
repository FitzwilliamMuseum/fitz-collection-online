@if(!empty($research))
<div class="container-fluid bg-grey p-3">
  <div class="container">
    <h4 class="lead">Suggested Scholarly Content</h4>
    <div class="row ">
      @foreach($research as $record)
        <div class="col-md-3 mb-3">
          <div class=" h-100">
            @if(!is_null($record['thumbnail']))
              <div class="results_image">
                <a href="{{ $record['url'][0] }}">
                  <img class="results_image__thumbnail img-fluid" src="{{ $record['thumbnail'][0]}}"
                  alt="Featured image for the project: {{ $record['title'][0] }}"
                  loading="lazy"/>
                </a>
                </div>
              @else
                <div class="results_image">
                  <a href="{{ $record['url'][0] }}">
                    <img class="results_image__thumbnail img-fluid" src="https://content.fitz.ms/fitz-website/assets/gallery3_roof.jpg?key=directus-large-crop"
                    alt="The Fitzwilliam Museum's gallery 3 roof" loading="lazy"/>
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


                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
@endif
