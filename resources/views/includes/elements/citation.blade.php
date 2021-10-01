<div class="mb-2">
  <h3 class="lead">Citation for print</h3>
  <p>
    This record can be cited in the Harvard Bibliographic style using the text below:
  </p>

  <code>
    The Fitzwilliam Museum (2020) "{{ ucfirst($record['_source']['summary_title']) }}"
    Web page available at: {{ url()->current() }} Accessed: {{ \Carbon\Carbon::now()->toDateTimeString() }}
  </code>
</div>

<div class="mb-2">
  <h3 class="lead">Citation for Wikipedia</h3>

  <p>
    To cite this record on Wikipedia you can use this code snippet:
  </p>

  <code>
     &#123;&#123;cite web|url={{ url()->current() }}|title={{ ucfirst($record['_source']['summary_title']) }}|author=The Fitzwilliam Museum|accessdate={{ \Carbon\Carbon::now()->toDateTimeString() }}|publisher=The University of Cambridge&#125;&#125;
  </code>
</div>

@if(array_key_exists('multimedia', $record['_source'] ))
@if(array_key_exists('processed', $record['_source']['multimedia'][0]))

<h3 class="lead">Bootstrap HTML code for reuse</h3>
@php
$image = env('APP_URL')  . '/imagestore/' . $record['_source']['multimedia'][0]['processed']['large']['location'];
  if(array_key_exists('title', $record['_source'])){
    $caption = $record['_source']['title'][0]['value'];
  } else {
    $caption = $record['_source']['summary_title'];
  }
@endphp
  <code>
    &lt;div class=&quot;text-center my-3&quot;&gt;
      &lt;figure class=&quot;figure&quot;&gt;
        &lt;img src=&quot;{{ $image }}&quot; alt=&quot;{{ $caption }}&quot; class=&quot;img-fluid&quot; /&gt;
        &lt;figcaption class=&quot;figure-caption text-info&quot;&gt;{{ $caption }}&lt;/figcaption&gt;
      &lt;/figure&gt;
    &lt;/div&gt;
  </code>
@endif
@endif
