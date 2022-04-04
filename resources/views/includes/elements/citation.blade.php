<div class="mb-2">
    <h3 class="lead">Citation for print</h3>
    <p>
        This record can be cited in the Harvard Bibliographic style using the text below:
    </p>
    <div class="bg-grey p-3 rounded">
        <button class="btn btn-dark m-1 float-right" id='harvardCopy'>@fa('copy')</button>

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
        <button class="btn btn-dark m-1 float-right" id='wikiCopy'>@fa('copy')</button>

        <code id="wikiData">
            &#123;&#123;cite web|url={{ url()->current() }}|title={{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}|author=The Fitzwilliam Museum|accessdate={{ \Carbon\Carbon::now()->toDateTimeString() }}|publisher=The University of Cambridge&#125;&#125;
        </code>

    </div>
</div>

@if(array_key_exists('multimedia', $record['_source'] ))
    @if(array_key_exists('processed', $record['_source']['multimedia'][0]))

        <h3 class="lead">Bootstrap HTML code for reuse</h3>
        <p>To use this as a simple code embed, copy this string:</p>
        <div class="bg-grey p-3 rounded">
            <button class="btn btn-dark m-1 float-right" id='bootstrapCopy'>@fa('copy')</button>

            <pre id="bootstrapCode">
&lt;div class=&quot;text-center my-3&quot;&gt;
    &lt;figure class=&quot;figure&quot;&gt;
        &lt;img src="{{ env('APP_URL')  . '/imagestore/' . $record['_source']['multimedia'][0]['processed']['large']['location'] }}"
        alt="{{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}" class="img-fluid" /&gt;
        &lt;figcaption class="figure-caption text-info">{{ $record['_source']['title'][0]['value'] ?? ucfirst($record['_source']['summary_title']) }}&lt/figcaption&gt&gt;
    &lt;/figure&gt;
&lt;/div&gt;
</pre>
        </div>
    @endif
@endif

