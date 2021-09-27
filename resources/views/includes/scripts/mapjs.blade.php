<script>
window.addEventListener('LaravelMaps:MapInitialized', function (event) {
  var element = event.detail.element;
  var map = event.detail.map;
  @if(array_key_exists('department', $data[0]['_source']))
  @if($record['_source']['department']['value'] === 'Antiquities')
  var dareLayer = L.tileLayer('https://dh.gu.se/tiles/imperium/{z}/{x}/{y}.png', {
    attribution: 'Tiles: <a href="http://imperium.ahlfeldt.se/">DARE 2014</a>'
  }),
  awmcLayer = L.tileLayer('https://dh.gu.se/tiles/imperium/{z}/{x}/{y}.png', {
    attribution: 'Iliad Points &copy; Recogito/Pelagios Tiles &copy; <a href="http://mapbox.com/" target="_blank">MapBox</a> | ' +
    'Data &copy; <a href="https://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> and contributors, CC-BY-SA | '+
    'Tiles and Data &copy; 2013 <a href="http://www.awmc.unc.edu" target="_blank">AWMC</a> ' +
    '<a href="http://creativecommons.org/licenses/by-nc/3.0/deed.en_US" target="_blank">CC-BY-NC 3.0</a>'
  });
  map.addLayer(dareLayer);
  @endif
  @endif
  map.scrollWheelZoom.disable();

});

</script>
