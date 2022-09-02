@if(!empty($scopeNote))
    <div class="row">
        @if(!is_null($scopeNote))
            <div class="col-md-6">
                <h3 class="collection lead">
                    Getty ULAN Biography
                </h3>
                @markdown($scopeNote)
            </div>
        @endif
        @if(is_array($axiell->{'name.type'}))
            <div class="col-md-3">
                <h3 class="collection lead">
                    Assigned role(s)
                </h3>
                <p>
                    Role: {{ ucfirst(strtolower($axiell->{'name.type'}[0]->value[0])) }}
                </p>
            </div>
        @endif
        @if(is_array($altNames))
            <div class="col-md-3">
                <h3 class="collection lead">
                    Alternative name(s)
                </h3>
                <p>
                    @foreach(array_slice($altNames,0,10) as $altName)
                        {{($altName->getValue())}}
                        @if(!$loop->last)
                            <br/>
                        @endif
                    @endforeach
                </p>
            </div>
        @endif
    </div>
@endif
