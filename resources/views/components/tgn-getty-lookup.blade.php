@if(!empty($scopeNote))
    <div class="row">
        @if(!is_null($scopeNote))
            <div class="col">
                <h3 class="collection lead">Getty TGN Information</h3>
                @markdown($scopeNote)
            </div>
        @endif
    </div>
@endif
