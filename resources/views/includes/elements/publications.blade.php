@if(array_key_exists('publications', $data))
    <h3 class="lead collection">
        References and bibliographic entries
    </h3>
    <ul>
        @foreach($data['publications'] as $pub)
            <li><a href="{{ route('publication.record',$pub['admin']['id'])}}">
                    {{ $pub['summary_title'] }}
                </a>
                @if(array_key_exists('page', $pub['@link']))
                    page(s): {{ $pub['@link']['page']}}
                @endif
            </li>
        @endforeach
    </ul>
@endif
