/**
 * Extracts logs data in form of arrays extracted from JSON sent by PHP.
 *
 * @param data JSON with logs data.
 * @returns Array of arrays containing log times, and log values.
 */
function extractData(data) {
	var dataArray = [];
	var timeArray = [];

	for (var i = 0; i < data.length; i++) {
		dataArray.push(JSON.parse(data[i].log_val));
		timeArray.push(JSON.stringify(data[i].log_time).replace(/['"]+/g, ''));
	}
	return [timeArray, dataArray];
}

/**
 * Creates chart embedded on the website. Calls processes of data
 * extraction and drawing of the chart.
 *
 * @param chartID Identifier of the div in which the chart will be placed.
 * @param chartLabel String that will describe the chart in title.
 * @param log_data JSON with log data.
 */
function makeChart(chartID, chartLabel, log_data) {
	[xData, yData] = extractData(log_data);
	drawChart(chartID, chartLabel, xData, yData);
}

/**
 * Creates chart embedded on the website. Calls processes of data
 * extraction and drawing of the chart. This function makes chart with two
 * datasets.
 *
 * @param chartID Identifier of the div in which the chart will be placed.
 * @param chartLabel1 String that describes the chart in title for dataset 1.
 * @param log_data1 JSON with log data for dataset 1.
 * @param chartLabel2 String that describes the chart in title for dataset 2.
 * @param log_data2 JSON with log data for dataset 2.
 */
function makeDoubleChart(chartID,
                         chartLabel1,
                         chartLabel2,
                         log_data1,
                         log_data2) {

	/* Extract data from JSON to arrays */
	[xData1, yData1] = extractData(log_data1);
	[xData2, yData2] = extractData(log_data2);

	try {
		if (xData1.length != xData2.length) throw "Unequal number of datastamps"
		drawDoubleChart(chartID, chartLabel1, chartLabel2,
		                xData1, yData1, yData2);
	}
	catch(err) {
		alert(err);
	}
}

/**
 * Draws chart on the website from dataset in given arrays.
 *
 * @param chartID Identifier of the div in which the chart will be placed.
 * @param chartLabel String that will describe the chart in title.
 * @param xData Array with data on x axis.
 * @param yData Array with data on y axis.
 */
function drawChart(chartID, chartLabel, xData, yData) {

	/* Get the div to draw chart in */
	const myChart = document.getElementById(chartID).getContext('2d');

	let chart1 = new Chart(myChart, {
		type:'line', //bar, horizontalBar, pie, line, doughnut, radar, polarArea
		data:{
			labels:xData,
			datasets:[
				{
				label: chartLabel,
				fill:true,
				borderColor:'#E2228C',
				lineTension:0.2,
				data: yData}
			],
		},
		options:{
			responsive:true,
			maintainAspectRatio: false,
			title:{
				display:true,
				text:(chartLabel + " log"),
				fontSize:25,
				fontColor:'#000'
			},
			elements: {
				point:{
					radius: 2
				}
			},
			legend:{
				display:true,
				position:'top'
			},
			layout:{
				padding:{
					left:10,
					right:10,
					bottom:10,
					top:10
				}
			}
		}
	});
}

/**
 * Draws chart on the website from dataset in given arrays, for two datasets.
 *
 * @param chartID Identifier of the div in which the chart will be placed.
 * @param chartLabel1 String that describes the chart in legend, for dataset 1.
 * @param chartLabel1 String that describes the chart in legend, for dataset 2.
 * @param xData1 Array with data on x axis for dataset 1.
 * @param yData1 Array with data on y axis for dataset 1.
 * @param xData1 Array with data on x axis for dataset 2.
 * @param yData1 Array with data on y axis for dataset 2.
 */
function drawDoubleChart(chartID,
                         chartLabel1,
                         chartLabel2, 
                         xData,
                         yData1,
                         yData2) {

	const myChart = document.getElementById(chartID).getContext('2d');

	let chart1 = new Chart(myChart, {
		type:'line', //bar, horizontalBar, pie, line, doughnut, radar, polarArea
		data:{
			labels:xData,
			datasets:[
				{
				label: chartLabel1,
				fill:false,
				borderColor:'#E2228C',
				lineTension:0.2,
				data: yData1},
				{
				label: chartLabel2,
				fill:false,
				borderColor:'#656DFF',
				lineTension:0.2,
				data: yData2}
			],

		},
		options:{
			responsive:true,
			maintainAspectRatio: false,
			title:{
				display:true,
				text:(chartLabel1 + " and " + chartLabel2 + " log"),
				fontSize:25,
				fontColor:'#000'
			},
			elements: {
				point:{
					radius: 2
				}
			},
			legend:{
				display:true,
				position:'top'
			},
			layout:{
				padding:{
					left:10,
					right:10,
					bottom:10,
					top:10
				}
			}
		}
	});
}
