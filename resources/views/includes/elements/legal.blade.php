@if(array_key_exists('legal', $record['_source']))
<h3 class="lead collectiom">
  Legal notes
</h4>
<p class="text-info">
  {{ ucfirst($record['_source']['legal']['credit_line']) }}
</p>
@endif
