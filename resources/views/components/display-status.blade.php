@if(property_exists($location->adlibJSON,'recordList'))
    @if(is_array($location->adlibJSON->recordList->record))
    @foreach($location->adlibJSON->recordList->record as $record)
        @if(property_exists($record, "current_location"))
            <div class="mb-2 text-center">
        <span class="btn-outline-dark btn">
            @if($record->{"current_location.type"}[0] != 'display')
                Current Location: In storage
            @else
                Current Location: {{$record->{"current_location.description"}[0]}}
            @endif
        </span>
            </div>
        @else
            <div class="mb-2 text-center">
                <span class="btn-outline-dark btn">Awaiting location update</span>
            </div>
        @endif
    @endforeach
@endif
@endif
