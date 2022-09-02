@if(array_key_exists('exhibitions', $data))
    <h3 class="lead collection">
        Related exhibitions
    </h3>
    <ul>
        @foreach ($data['exhibitions'] as $exhibition)
            <li>
                <a href="{{ route('exhibition.record', [$exhibition['admin']['id']]) }}">{{ $exhibition['summary_title'] }}</a>
            </li>
        @endforeach
    </ul>
@endif
