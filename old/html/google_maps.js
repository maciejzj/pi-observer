/**
 * Extracts location data in form of arrays extracted from JSON sent by PHP.
 *
 * @param data JSON with location data.
 * @returns Array of arrays containing log times, latitudes and longitudes.
 */
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

/**
 * Creates Google Maps embedded on the website. Calls processes of data
 * extraction and initialisation of map.
 *
 * @param data JSON with location data.
 */
function makeGoogleMaps(locLogData) {
	[timeData, latData, lngData] = extractLocation(locLogData);
	initialize(timeData, latData, lngData);
}

/**
 * Initialises Google Maps using Google's libraries.
 *
 * @param timeData Array with timestamps.
 * @param latData Array with latitude data.
 * @param lngData Array with longitude data.
 */
function initialize(timeData, latData, lngData) {
	/* Extract last logged lat and lng, they will be used for the initial
	 * center point of the map.
	 */
	var mapLat = latData[latData.length - 1]
	var mapLng = lngData[lngData.length - 1]

	/* Set initial zoom and center point form last logged position */
	var mapOptions = {
		zoom: 15,
		center: {lat: mapLat, lng: mapLng}
	};
	/* Get the map div on the html site */
	map = new google.maps.Map(document.getElementById('map'),
			mapOptions);
	
	/* Create markers for all rows (log entries) */
	for (i = 0; i < latData.length; i++) {
		var markerLat = latData[i];
		var markerLng = lngData[i];
		
		var marker = new google.maps.Marker({
			position: {lat: markerLat, lng: markerLng},
			map: map
		});

		/* Create timestamp string for marker label */
		var content = "<b>" + timeData[i] + "</b>" + " (" + markerLat + ", " + markerLng + ")";
		var infowindow = new google.maps.InfoWindow()
		/* Add lister for maker clikc, it will invoke marker label with
		 * the timestamp.
		 */
		google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
			return function() {
				infowindow.setContent(content);
				infowindow.open(map,marker);
			};
		})(marker,content,infowindow));  
	}
	/* Set trigger event for resizing */
	google.maps.event.trigger(map, 'resize');
}
