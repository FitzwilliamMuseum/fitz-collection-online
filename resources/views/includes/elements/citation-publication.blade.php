<div class="mb-2">
    <h3 class="lead">
        Citation for print
    </h3>
    <p>
        This page can be cited in the Harvard Bibliographic style using the text below:
    </p>
    <div class="bg-grey p-3 rounded">
        <button class="btn btn-dark m-1 float-end" id='harvardCopy'>@svg('fas-copy',['width' => '15'])</button>
        <code id="harvardData">
            The Fitzwilliam Museum ({{ now()->year }}) "Publications details for {{ $data['summary_title'] }}" Web page available at: {{ url()->current() }} Accessed: {{ \Carbon\Carbon::now()->toDateTimeString() }}
        </code>

    </div>
</div>

<div class="mb-3">

    <h3 class="lead">
        Citation for Wikipedia
    </h3>
    <p>
        To cite this page on Wikipedia you can use this code snippet:
    </p>
    <div class="bg-grey p-3 rounded">
        <button class="btn btn-dark m-1 float-end" id='wikiCopy'>
            @svg('fas-copy',['width' => '15'])
        </button>
        <code id="wikiData">
            &#123;&#123;cite web|url={{ url()->current() }}|title=Publications details for {{ $data['summary_title'] }}|author=The Fitzwilliam Museum|accessdate={{ \Carbon\Carbon::now()->toDateTimeString() }}|publisher=The University of Cambridge&#125;&#125;
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
        <code id="apiCode">{{ route('api.publications.show',$data['admin']['id']) }}</code>
    </div>
</div>


