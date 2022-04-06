@if(!empty($spoliation))
    @if(!Carbon\Carbon::parse($spoliation[0]['expiry_date'])->isPast())
        <div class="container">
            <div class="alert alert-info text-dark text-center w-50 mx-auto">
                @foreach($spoliation as $message)
                    <p>{{ $message['text']  }}</p>
                    <a class="btn btn-dark" href="{{ env("MAIN_URL") }}/news/{{ $message['news_slug'] }}"
                       title="Full details for this case">Read the details for this case</a>
                @endforeach
            </div>
        </div>
    @endif
@endif
