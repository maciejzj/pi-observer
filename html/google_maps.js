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
	alert(timeArray);
	return [timeArray, latArray, lngArray];
}

function makeGoogleMaps(locLogData) {
	[timeData, latData, lngData] = extractLocation(locLogData);	
}