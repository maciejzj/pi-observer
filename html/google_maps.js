<script>
function initialize() {
	var mapOptions = {
		zoom: 8,
		center: {lat: -34.397, lng: 150.644}
	};
	map = new google.maps.Map(document.getElementById('map'),
			mapOptions);
		
	var val = <?php echo json_encode($locStr) ?>;
	window.alert(val);
	var marker = new google.maps.Marker({
		position: {lat: -34.397, lng: 150.644},
		map: map
	});

	var infowindow = new google.maps.InfoWindow({
		content: '<p>Marker Location:' + marker.getPosition() + '</p>'
	});

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map, marker);
	});
}
</script>