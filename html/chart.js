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

function makeChart(chartID, chartLabel, log_data) {
  [xData, yData] = extractData(log_data);
  drawChart(chartID, chartLabel, xData, yData);
}

function makeDoubleChart(chartID,
                        chartLabel1,
                        chartLabel2,
                        log_data1,
                        log_data2) {

  [xData1, yData1] = extractData(log_data1);
  [xData2, yData2] = extractData(log_data2);
  
  try {
    if (xData1.length != xData2.length) throw "Unequal number of datastamps"
    drawDoubleChart(chartID, chartLabel1, chartLabel2, xData1, yData1, yData2);
  }
  catch(err) {
      alert(err);
    }
}

function drawChart(chartID, chartLabel, xData, yData) {
  
  const myChart = document.getElementById(chartID).getContext('2d');

  let chart1 = new Chart(myChart, {
    type:'line', //bar, horizontalBar, pie, line, doughnut, radar, polarArea
    data:{
      labels:xData,
      datasets:[
        {
        label: chartLabel,
        fill:false,
        borderColor:'428bca',
        lineTension:0.2,
        data: yData}
      ],
    },
    options:{
      title:{
        display:true,
        text:(chartLabel + " log"),
        fontSize:25,
        fontColor:'#000'
      },
      elements: {
        point:{
          radius: 0
        }
      },
      legend:{
        display:true,
        position:'top'
      },
      layout:{
        padding:{
          left:50,
          right:50,
          bottom:50,
          top:50
        }
      }
    }
  });
}

function drawDoubleChart(chartID, chartLabel1, chartLabel2, xData, yData1, yData2) {
  
  const myChart = document.getElementById(chartID).getContext('2d');

  let chart1 = new Chart(myChart, {
    type:'line', //bar, horizontalBar, pie, line, doughnut, radar, polarArea
    data:{
      labels:xData,
      datasets:[
        {
        label: chartLabel1,
        fill:false,
        borderColor:'428bca',
        lineTension:0.2,
        data: yData1},
        {
        label: chartLabel2,
        fill:false,
        borderColor:'cc2536',
        lineTension:0.2,
        data: yData2}
      ],
      
    },
    options:{
      title:{
        display:true,
        text:(chartLabel1 + " and " + chartLabel2 + " log"),
        fontSize:25,
        fontColor:'#000'
      },
      elements: {
        point:{
          radius: 0
        }
      },
      legend:{
        display:true,
        position:'top'
      },
      layout:{
        padding:{
          left:50,
          right:50,
          bottom:50,
          top:50
        }
      }
    }
  });
}