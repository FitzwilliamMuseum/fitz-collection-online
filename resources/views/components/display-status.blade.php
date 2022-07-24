@if(is_array($location->adlibJSON->recordList->record))
    <div class="mb-2 text-center">
        <span class="btn-outline-dark btn">
        @foreach($location->adlibJSON->recordList->record as $record)
                @if($record->{"current_location.type"}[0] === 'storage')
                    Current Location: {{$record->{"current_location.type"}[0]}}
                @else
                    Current Location: {{$record->{"current_location.description"}[0]}}
                @endif
            @endforeach
    </span>
    </div>
@endif

