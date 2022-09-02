<script>
window.addEventListener('LaravelMaps:MapInitialized', function (event) {
    let element = event.detail.element;
    let map = event.detail.map;
    @if(array_key_exists('department', $data))
      @if($data['department']['value'] === 'Antiquities')
        let dareLayer = L.tileLayer('https://dh.gu.se/tiles/imperium/{z}/{x}/{y}.png', {
            attribution: 'Tiles: <a href="https://imperium.ahlfeldt.se/">DARE 2014</a>'
        });

        map.addLayer(dareLayer);
      @endif
  @endif
  map.scrollWheelZoom.disable();
});
</script>
