//  TODO:
//  Frontend:
//      Mobile Responsiveness
//  Backend:
//  Like a Poyo, Feel a Poyo, Be a Poyo

window.onload = function() {

    var baseLayer = L.tileLayer(
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
        maxZoom: 18
      }
    );

    var cfg = {
      "radius": 2,
      "maxOpacity": .8,
      "scaleRadius": true,
      "useLocalExtrema": true,
      latField: 'lat',
      lngField: 'lng',
      valueField: 'carbon'
    };


    var heatmapLayer = new HeatmapOverlay(cfg);

    var map = new L.Map('map-canvas', {
      center: new L.LatLng(25.6586, -80.3568),
      zoom: 4,
      layers: [baseLayer, heatmapLayer]
    });

    fetch("https://ipwho.is/", {
        method: 'GET',
      })
      .then(response  => response.json())
      .then(data => {
        map.panTo(new L.LatLng(data["latitude"], data["longitude"]));
      })
      .catch(rejected => {
          console.log(rejected);
      })

    let last_map_update = 0
    const throttle_ms = 500
    map.on("move", function () {
      if ((Date.now() - last_map_update) > throttle_ms) {
        update_map_data()
        last_map_update = Date.now()
      }
    });

    function update_map_data() {
      let map_bounds = map.getBounds();
      let northEast = map_bounds['_northEast']
      let southWest = map_bounds['_southWest']
      console.log(northEast);
      console.log(southWest);
      console.log("=========");

      fetch('api/data.php?'+ new URLSearchParams({
        'lowlat': southWest['lat'],
        'lowlong': southWest['lng'],
        'toplat': northEast['lat'],
        'toplong': northEast['lng'],
      }), {
        method: 'GET',
      })
      .then(response  => response.text())
      .then(data => {
        console.log(data);
        heatmapLayer.setData(JSON.parse(data));
      })
      .catch(rejected => {
          console.log(rejected);
      })
    }



};
