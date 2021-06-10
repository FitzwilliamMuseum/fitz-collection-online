@if(array_key_exists('techniques', $record['_source']))
<h3 class="lead collection">
  Techniques used in production
</h4>
<p>
  @foreach($record['_source']['techniques'] as $techniques)
  @if(array_key_exists('reference', $techniques))
  <a  href="/id/terminology/{{ $techniques['reference']['admin']['id']}}">{{ ucfirst($techniques['reference']['summary_title'])}}</a> @if(array_key_exists('description', $techniques))
  : {{ ucfirst($techniques['description'][0]['value'])}}<br/>
  @endif</li>
  @endif
  @endforeach
</p>
@endif
