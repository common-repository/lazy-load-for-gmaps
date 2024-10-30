	window.addEventListener('scroll', showVisible);
	showVisible();

	function showVisible() {
		var elem = document.getElementsByClassName("lazy-load-for-gmaps-wrap");

		for (i = 0; i < elem.length; i++) {

			if (isVisible(elem[i])) {
				if (document.getElementById("dynamic_gmap") == null){
					webamatorDynamicLoadGoogleMap();
				}
			}

		}

	}

    function isVisible(elem) {

      let coords = elem.getBoundingClientRect();

      let windowHeight = document.documentElement.clientHeight;

      let topVisible = coords.top > 0 && coords.top < windowHeight;
      let bottomVisible = coords.bottom < windowHeight && coords.bottom > 0;

      return topVisible || bottomVisible;
    }

	function initMap() {

		mapsarr.forEach(function(item,i) {

			var uluru = { lat: item[0] , lng: item[1] };
			var map_canvas = new google.maps.Map(document.getElementById('ll_gmap_canvas_id_'+i), {zoom: item[2],center: uluru});
			var marker = new google.maps.Marker({position: uluru,map: map_canvas});

		});

	}
