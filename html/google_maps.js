function extractLocation(data) {
	var timeArray = [];
	var latArray = [];
	var lngArray = [];
	
	for (var i = 0; i < data.length; i++) {
		if(JSON.stringify(data[i].status).replace(/['"]+/g, '') == 'Correct') {
			timeArray.push(JSON.stringify(data[i].log_time).replace(/['"]+/g, ''));
			latArray.push(JSON.parse(data[i].latitude));
			lngArray.push(JSON.parse(data[i].longitude));
		}
	}
	return [timeArray, latArray, lngArray];
}

function makeGoogleMaps(locLogData) {
	[timeData, latData, lngData] = extractLocation(locLogData);
	initialize(timeData, latData, lngData);
}

function initialize(timeData, latData, lngData) {
	var mapLat = latData[latData.length - 1]
	var mapLng = lngData[lngData.length - 1]

	var mapOptions = {
		zoom: 15,
		center: {lat: mapLat, lng: mapLng}
	};
	map = new google.maps.Map(document.getElementById('map'),
			mapOptions);
	
	// TODO: try{ if(latData.length != lngData.length) throw expception; }
	for (i = 0; i < latData.length; i++) {
		var markerLat = latData[i];
		var markerLng = lngData[i];
		
		var marker = new google.maps.Marker({
			position: {lat: markerLat, lng: markerLng},
			map: map
		});

		var content = "<b>" + timeData[i] + "</b>" + " (" + markerLat + ", " + markerLng + ")";
		var infowindow = new google.maps.InfoWindow()
		google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
			return function() {
				infowindow.setContent(content);
				infowindow.open(map,marker);
			};
		})(marker,content,infowindow));  
	}
	google.maps.event.trigger(map, 'resize');
}
