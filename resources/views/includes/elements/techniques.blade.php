@if(array_key_exists('techniques', $data))
    <h3 class="lead collection">
        Techniques used in production
    </h3>
    <p>
        @foreach($data['techniques'] as $techniques)
            @if(array_key_exists('reference', $techniques))
                <a href="{{ route('terminology',$techniques['reference']['admin']['id'])}}">
                    {{ ucfirst($techniques['reference']['summary_title'])}}
                </a>
                @if(array_key_exists('description', $techniques))
                    : {{ ucfirst($techniques['description'][0]['value'])}}
                @endif
                @if(!$loop->last)
                    <br/>
                @endif
            @endif
        @endforeach
    </p>
@endif
