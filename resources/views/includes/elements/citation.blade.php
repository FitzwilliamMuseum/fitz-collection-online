<div class="mb-2">
    <h3 class="lead">Citation for print</h3>
    <p>
        This record can be cited in the Harvard Bibliographic style using the text below:
    </p>
    <div class="bg-grey p-3 rounded">
        <button class="btn btn-dark m-1 float-end" id='harvardCopy'>@svg('fas-copy',['width' => '15'])</button>
        <code id="harvardData">
            The Fitzwilliam Museum ({{ now()->year }})
            "{{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}"
            Web page available at: {{ url()->current() }} Accessed: {{ \Carbon\Carbon::now()->toDateTimeString() }}
        </code>

    </div>
</div>

<div class="mb-3">
    <h3 class="lead">Citation for Wikipedia</h3>
    <p>
        To cite this record on Wikipedia you can use this code snippet:
    </p>
    <div class="bg-grey p-3 rounded">
        <button class="btn btn-dark m-1 float-end" id='wikiCopy'>
            @svg('fas-copy',['width' => '15'])
        </button>
        <code id="wikiData">
            &#123;&#123;cite web|url={{ url()->current() }}
            |title={{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}
            |author=The Fitzwilliam Museum|accessdate={{ \Carbon\Carbon::now()->toDateTimeString() }}|publisher=The
            University of Cambridge&#125;&#125;
        </code>

    </div>
</div>

<div class="mb-3">
    <h3 class="lead">API call for this record</h3>
    <p>
        To call these data via our API (remember this needs to be authenticated) you can use this code snippet:
    </p>
    <div class="bg-grey p-3 rounded">
        <button class="btn btn-dark m-1 float-end" id='apiCopy'>
            @svg('fas-copy',['width' => '15'])
        </button>
        <code id="apiCode">{{ route('api.objects.show',$record['_source']['admin']['id']) }}</code>

    </div>
</div>


@if(array_key_exists('multimedia', $record['_source'] ))
    @if(array_key_exists('processed', $record['_source']['multimedia'][0]))
        <h3 class="lead">Bootstrap HTML code for reuse</h3>
        <p>To use this as a simple code embed, copy this string:</p>
        <div class="bg-grey p-3 rounded">
            <button class="btn btn-dark m-1 float-end" id='bootstrapCopy'>@svg('fas-copy',['width' => '15'])</button>
            <pre id="bootstrapCode">
                &lt;div class=&quot;text-center my-3&quot;&gt;
                    &lt;figure class=&quot;figure&quot;&gt;
                        &lt;img src="{{ env('APP_URL')  . '/imagestore/' . $record['_source']['multimedia'][0]['processed']['large']['location'] }}"
                        alt="{{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}"
                        class="img-fluid" /&gt;
                        &lt;figcaption class="figure-caption text-info">{{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}&lt/figcaption&gt
                    &lt;/figure&gt;
                &lt;/div&gt;
            </pre>
        </div>
    @endif
@endif

