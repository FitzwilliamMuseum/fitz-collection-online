@if(array_key_exists('publications', $record['_source']))
<h3 class="lead collection">
  References and bibliographic entries
</h3>
<ul>
  @foreach($record['_source']['publications'] as $pub)
    <li><a href="/id/publication/{{ $pub['admin']['id']}}">{{ $pub['summary_title'] }}</a>
    @if(array_key_exists('page', $pub['@link']))
    page(s): {{ $pub['@link']['page']}}
    @endif
    </li>
  @endforeach
</ul>
@endif
