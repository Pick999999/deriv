<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" > 

  <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
<style>
table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            cursor: pointer;
        }
        th {
            background-color: #f4f4f4;
        }
        .highlight {
            background-color: yellow;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
 
.sarabun-regular {
  font-family: "Sarabun", sans-serif;
  font-weight: 400;
  font-style: normal;
}

</style>
<script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>


 </head>
 <body class='sarabun-regular'>
  
  <h1>Chart </h1>


   <div class="container mt-5">
    <div id="price-chart" class="bordergray flex" style='width:100%;height:350px'>
         
    </div>
	<button type='button' id='' class='mBtn' onclick="SetData()">Add Data</button>

	<button type='button' id='' class='mBtn' onclick="RemoveData()">Remove Data</button>
        
    </div>

<textarea id="data" rows="" cols="">

</textarea>


  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

   <!-- Required JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    

<script>
let chart ;
let rsiSeries ;

function SetData() {

//let aa = chart ; 
rsiSeries = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                priceLineVisible: false,
            });

rsiData = getData();
rsiSeries.setData(rsiData);
chart.timeScale().fitContent();


} // end func

function RemoveData() {

//let aa = chart ;

chart.removeSeries(rsiSeries);


} // end func



function getData() {

	    let candleData = [
            { time: '2021-01-01',  value: 103 },
            { time: '2021-01-02',  value: 108 },
            { time: '2021-01-03',  value: 106 },
            { time: '2021-01-04',  value: 107 },
            { time: '2021-01-05',  value: 110 },
            { time: '2021-01-06',  value: 112 },
            { time: '2021-01-07',  value: 116 },
            { time: '2021-01-08',  value: 119 },
            { time: '2021-01-09', value: 123 },
            { time: '2021-01-10',  value: 125 },
            { time: '2021-01-11',  value: 124 },
            { time: '2021-01-12', value: 121 },
            { time: '2021-01-13', value: 119 },
            { time: '2021-01-14',  value: 116 },
            { time: '2021-01-15',  value: 115 },
            { time: '2021-01-16',  value: 118 },
            { time: '2021-01-17',  value: 121 },
            { time: '2021-01-18',  value: 124 },
            { time: '2021-01-19', value: 127 },
            { time: '2021-01-20',  value: 130 }
        ];


		return candleData ;


} // end func


        $(document).ready(function() {
           const priceChartContainer = document.getElementById('price-chart');
		   chart = LightweightCharts.createChart(priceChartContainer, {
                height: priceChartContainer.offsetHeight,
                layout: {
                    background: { color: '#ffffff' },
                    textColor: '#333',
                },
                grid: {
                    vertLines: { color: '#f0f0f0' },
                    horzLines: { color: '#f0f0f0' },
                },
                timeScale: {
                    borderColor: '#d1d1d1',
                    timeVisible: true,
                },
                rightPriceScale: {
                    borderColor: '#d1d1d1',
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.1,
                    },
                },
           });
			rsiSeries = chart.addLineSeries({
                color: '#2962FF',
                lineWidth: 2,
                priceLineVisible: false,
            });
/*
            const rsiData = getData();
			rsiSeries.setData(rsiData);
            chart.timeScale().fitContent();
			//alert('Finished');
*/

             
        }); // end docready
    </script>



  


 </body>
</html>
