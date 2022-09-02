<div class="row">
    @if(!empty($scopeNote))
        <div class="col-md-6">
            <h3 class="collection lead">Getty scope note</h3>
            <p>
                {{ $scopeNote }}
            </p>
        </div>
    @endif
    @if(!empty($altNames))
        <div class="col-md-6">
            <h3 class="collection lead">Alternative names</h3>
            <p>
                @foreach($altNames as $altName)

                    {{ $altName }}
                    @if(!$loop->last)
                        <br/>
                    @endif
                @endforeach
            </p>
        </div>
    @endif
</div>
