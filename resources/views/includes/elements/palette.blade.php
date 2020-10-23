<h4>Colours in this image</h4>
<div <div class="col-12 shadow-sm p-3 mx-auto mb-3 rounded">
  <div class="swatches">
    @foreach ($palette as $color)
      <div class="swatch mr-2" style="background-color: rgb({{ $color[0] }}, {{ $color[1] }}, {{ $color[2] }})"></div>
    @endforeach
  </div>
</div>
