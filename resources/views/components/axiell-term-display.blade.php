<div class="row">
    @if(!empty($biographicalNote))
        <div class="col-md-9">
            <h3 class="collection lead">
                Biographical note
            </h3>
            <p>
                @foreach($biographicalNote as $note)
                    {{ucfirst($note)}}
                    @if(!$loop->last)
                        <br>
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if(!empty($nameTypes))
        <div class="col-md-3">
            <h3 class="collection lead">
                Type of agent
            </h3>
            <p>
                @foreach($nameTypes as $name)
                    {{ucfirst($name)}}
                    @if(!$loop->last)
                        <br>
                    @endif
                @endforeach
            </p>
        </div>
    @endif
</div>

<div class="row">
    @if(!empty($termTypes))
        <div class="col-md-6">
            <h3 class="collection lead">Term type</h3>
            <p>
                @foreach($termTypes as  $value)
                    {{ $value }}
                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if($termNumber != '')
        <div class="col-md-6">
            <h3 class="collection lead">
                Getty AAT term number
            </h3>
            <p>
                {{ $termNumber }}
            </p>

            <x-aat-getty-lookup :aatID="$termNumber"></x-aat-getty-lookup>
        </div>
    @endif

    @if(!empty($identifiers) && !is_null($identifiers['aat_id']))
        <div class="col-md-6">
            <h3 class="collection lead">
                Getty AAT term number
            </h3>
            <p>
                {{ $identifiers['aat_id'] }}
            </p>
            <x-aat-getty-lookup :aatID="$identifiers['aat_id']"></x-aat-getty-lookup>
        </div>
    @endif

    @if(!empty($broaderTerms))
        <div class="col-md-6">
            <h3 class="collection lead">Broader Terms used</h3>
            <p>
                @foreach($broaderTerms as $key => $value)
                    <a href="{{ route('terminology', 'term-' . $value) }}">{{$key}}</a>
                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if(!empty($narrowerTerms))
        <div class="col-md-6">
            <h3 class="collection lead">Narrower Terms used</h3>
            <p>
                @foreach($narrowerTerms as $key => $value)
                    <a href="{{ route('terminology', 'term-' . $value) }}">{{$key}}</a>
                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if(!empty($usedFor))
        <div class="col-md-6">
            <h3 class="collection lead">Term used for</h3>
            <p>
                @foreach($usedFor as $key => $value)
                    <a href="{{ route('terminology', 'term-' . $value) }}">{{$key}}</a>
                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if(!empty($equivalentTerms))
        <div class="col-md-6">
            <h3 class="collection lead">Equivalent Terms used</h3>
            <p>
                @foreach($equivalentTerms as $key => $value)
                    <a href="{{ route('terminology', 'term-' . $value) }}">{{$key}}</a>
                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        </div>
    @endif

    @if(!empty($created))
        <div class="col-md-6">
            <h3 class="collection lead">Created</h3>
            <p>{{ Carbon\Carbon::parse($created)->shortRelativeDiffForHumans() }}</p>
        </div>
    @endif

</div>
