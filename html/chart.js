//const myChart = document.getElementById('myChart').getContext('2d');

function extractData(data) {
  var dataArray = [];
  var timeArray = [];

  for (var i = 0; i < data.length; i++) {
    dataArray.push(JSON.parse(data[i].log_val));
    timeArray.push(JSON.stringify(data[i].log_time).replace(/['"]+/g, ''));
  }

  return [timeArray, dataArray];
}

function makeChart(chartName, log_data) {
  [xData, yData] = extractData(log_data);
}