<h3 class="lead">Bootstrap HTML code for reuse</h3>
<p>To use this as a simple code embed, copy this string:</p>
<div class="bg-grey p-3 rounded">
    <button class="btn btn-dark m-1 float-end" id='bootstrapCopy'>@svg('fas-copy',['width' => '15'])</button>
    <pre id="bootstrapCode">
&lt;div class=&quot;text-center">
    &lt;figure class=&quot;figure&quot;&gt;
        &lt;img src="{{ env('APP_URL')  . '/imagestore/' . $data['multimedia'][0]['processed']['large']['location'] }}"
        alt="{{ $data['title'][0]['value'] ?? ucfirst($data['summary_title']) }}"
        class="img-fluid" /&gt;
        &lt;figcaption class="figure-caption text-info">{{ $data['title'][0]['value'] ?? ucfirst($data['summary_title']) }}&lt/figcaption&gt
    &lt;/figure&gt;
&lt;/div&gt;
    </pre>
</div>
