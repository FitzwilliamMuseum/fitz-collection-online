@if(property_exists($location->adlibJSON,'recordList'))
    @if(is_array($location->adlibJSON->recordList->record))
        @foreach($location->adlibJSON->recordList->record as $record)
            @if(property_exists($record, "current_location"))
                <div class="mb-2 text-center">
                    @if(property_exists($record, "current_location.type"))
                        @if($record->{"current_location.type"}[0] != 'display')
                            <span class="badge bg-warning">
                                Current Location: In storage
                            </span>
                        @else
                            <span class="badge bg-dark">
                                Current Location: {{$record->{"current_location.description"}[0]}}
                            </span>
                        @endif
                    @else
                        <span class="badge bg-info">Awaiting location update</span>
                    @endif
                </div>
            @else
                <div class="mb-2 text-center">
                    <span class="badge bg-info">Awaiting location update</span>
                </div>
            @endif
        @endforeach
    @endif
@endif
