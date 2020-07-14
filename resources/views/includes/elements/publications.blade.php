@if(array_key_exists('publications', $record['_source']))
<h4>References and bibliographic entries</h4>
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
