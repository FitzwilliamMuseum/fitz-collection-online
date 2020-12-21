<div class="mb-2">
  <p>
    This record can be cited in the Harvard Bibliographic style using the text below:
  </p>

  <code>
    The Fitzwilliam Museum (2020) "{{ ucfirst($record['_source']['summary_title']) }}"
    Web page available at: {{ url()->current() }} Accessed: {{ \Carbon\Carbon::now()->toDateTimeString() }}
  </code>
</div>

<div class="mb-2">

  <p>
    To cite this record on Wikipedia you can use this code snippet:
  </p>

  <code>
     &#123;&#123;cite web|url={{ url()->current() }}|title={{ ucfirst($record['_source']['summary_title']) }}|author=The Fitzwilliam Museum|accessdate={{ \Carbon\Carbon::now()->toDateTimeString() }}|publisher=The University of Cambridge&#125;&#125;
  </code>
</div>
