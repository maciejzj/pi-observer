<?php
$locStr = "";
$myfile = fopen("./sensor_logs/google_maps_log", "r") or die("Unable to fetch loactions file!");
// Output one line until end-of-file
while(!feof($myfile)) {
	$locStr = $locStr . fgets($myfile);
}
fclose($myfile);
?>

<script>
function initialize() {
	var locStr = <?php echo json_encode($locStr) ?>;
	locStr = locStr.split(/\r?\n/);
	locStr.pop();
	
	var locRecords = new Array(locStr.length)
	for (i = 0; i < locStr.length; i++) {
		locRecords[i] = new Array(3);
		locRecords[i] = locStr[i].split(",");
	}
	
	var mapLat = parseFloat(locRecords[locRecords.length - 1][1]);
	var mapLng = parseFloat(locRecords[locRecords.length - 1][2]);

	var mapOptions = {
		zoom: 12,
		center: {lat: mapLat, lng: mapLng}
	};
	map = new google.maps.Map(document.getElementById('map'),
			mapOptions);		
	for (i = 0; i < locRecords.length; i++) {
		record = locRecords[i];
		var markerLat = parseFloat(record[1]);
		var markerLng = parseFloat(record[2]);
		
		var marker = new google.maps.Marker({
			position: {lat: markerLat, lng: markerLng},
			map: map
		});

		var content = "<b>" + record[0] + "</b>" + " (" + markerLat + ", " + markerLng + ")";
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
</script>