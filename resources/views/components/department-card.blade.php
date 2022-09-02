<div class="col-md-4 mb-3">
    <div class="card card-fitz h-100">
        <a href="{{ route('department', urlencode($department['key'])) }}">
            <img class="card-img-top"
                 src="{{$image}}"
                 alt="An image representative of the department of {{$department['key']}}"
                 width="416"
                 height="416"
                 loading="lazy"
            />
        </a>
        <div class="card-body h-100">
            <div class="contents-label mb-3">
                <h3>
                    <a href="{{ route('department', urlencode($department['key'])) }}" class="stretched-link">
                        {{ $department['key'] }}
                    </a>
                </h3>
                <p class="text-info">Records available: {{ number_format($department['doc_count']) }}</p>
            </div>
        </div>
    </div>
</div>
