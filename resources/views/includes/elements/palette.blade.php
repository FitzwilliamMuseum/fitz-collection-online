<h3 class="lead collection">
    Colours in this image
</h3>
<div class="col-12  p-3 mx-auto mb-3 rounded ">
    <div class="swatches text-center">
        @foreach ($palette as $color)
            <div class="swatch mr-2"
                 style="background-color: rgb({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }})"></div>
        @endforeach
    </div>
    <button class="btn btn-dark m-1" id='colorCopy'>@svg('fas-copy', ['width' => 15]) Copy RGB palette strings to clipboard</button>
    <p class="visually-hidden" id="colorsToCopy">@foreach ($palette as $color)rgb({{ implode(',',$color) }})@if(!$loop->last),@endif @endforeach</p>
</div>
