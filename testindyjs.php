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



 </head>
 <body class='sarabun-regular'>
  
 <h1>ทดสอบ Class Trade</h1>
<input type="text" id="candleData" value='
[
    {
        "close": 1642.97,
        "epoch": 1739085540,
        "high": 1643.48,
        "low": 1641.71,
        "open": 1642.61
    },
    {
        "close": 1647.9,
        "epoch": 1739085600,
        "high": 1647.9,
        "low": 1642.99,
        "open": 1643.36
    },
    {
        "close": 1646.45,
        "epoch": 1739085660,
        "high": 1648.51,
        "low": 1645.61,
        "open": 1647.78
    },
    {
        "close": 1644.75,
        "epoch": 1739085720,
        "high": 1646.5,
        "low": 1643.89,
        "open": 1646.5
    },
    {
        "close": 1644.76,
        "epoch": 1739085780,
        "high": 1645.29,
        "low": 1642.87,
        "open": 1645.29
    },
    {
        "close": 1644.2,
        "epoch": 1739085840,
        "high": 1644.95,
        "low": 1641.17,
        "open": 1644.28
    },
    {
        "close": 1643.04,
        "epoch": 1739085900,
        "high": 1645.82,
        "low": 1643.04,
        "open": 1644.24
    },
    {
        "close": 1645.21,
        "epoch": 1739085960,
        "high": 1645.92,
        "low": 1641.38,
        "open": 1642.75
    },
    {
        "close": 1645.45,
        "epoch": 1739086020,
        "high": 1646.08,
        "low": 1644.27,
        "open": 1645.27
    },
    {
        "close": 1646.45,
        "epoch": 1739086080,
        "high": 1648.59,
        "low": 1645.23,
        "open": 1645.23
    },
    {
        "close": 1644.7,
        "epoch": 1739086140,
        "high": 1646.93,
        "low": 1643.99,
        "open": 1646.45
    },
    {
        "close": 1645.42,
        "epoch": 1739086200,
        "high": 1646.23,
        "low": 1644.36,
        "open": 1644.96
    },
    {
        "close": 1646.13,
        "epoch": 1739086260,
        "high": 1646.13,
        "low": 1643.43,
        "open": 1645.07
    },
    {
        "close": 1642.55,
        "epoch": 1739086320,
        "high": 1645.64,
        "low": 1642.38,
        "open": 1645.23
    },
    {
        "close": 1640.28,
        "epoch": 1739086380,
        "high": 1643.38,
        "low": 1639.65,
        "open": 1643.38
    },
    {
        "close": 1639.1,
        "epoch": 1739086440,
        "high": 1641.86,
        "low": 1639.1,
        "open": 1640.67
    },
    {
        "close": 1639.33,
        "epoch": 1739086500,
        "high": 1640.2,
        "low": 1638.39,
        "open": 1639.97
    },
    {
        "close": 1640.8,
        "epoch": 1739086560,
        "high": 1641.61,
        "low": 1638.31,
        "open": 1639.01
    },
    {
        "close": 1641.62,
        "epoch": 1739086620,
        "high": 1641.67,
        "low": 1639.54,
        "open": 1640.32
    },
    {
        "close": 1640.35,
        "epoch": 1739086680,
        "high": 1642.64,
        "low": 1640.35,
        "open": 1641.75
    },
    {
        "close": 1639.5,
        "epoch": 1739086740,
        "high": 1641.99,
        "low": 1639.5,
        "open": 1640
    },
    {
        "close": 1639.2,
        "epoch": 1739086800,
        "high": 1641.6,
        "low": 1639.08,
        "open": 1639.15
    },
    {
        "close": 1640.55,
        "epoch": 1739086860,
        "high": 1642,
        "low": 1639.15,
        "open": 1639.24
    },
    {
        "close": 1643.04,
        "epoch": 1739086920,
        "high": 1643.04,
        "low": 1640.61,
        "open": 1641.14
    },
    {
        "close": 1641.35,
        "epoch": 1739086980,
        "high": 1643,
        "low": 1640.61,
        "open": 1642.63
    },
    {
        "close": 1640.6,
        "epoch": 1739087040,
        "high": 1642.54,
        "low": 1640.27,
        "open": 1640.79
    },
    {
        "close": 1641.12,
        "epoch": 1739087100,
        "high": 1641.65,
        "low": 1639.13,
        "open": 1640.75
    },
    {
        "close": 1644.52,
        "epoch": 1739087160,
        "high": 1644.76,
        "low": 1640.31,
        "open": 1640.76
    },
    {
        "close": 1642.74,
        "epoch": 1739087220,
        "high": 1644.59,
        "low": 1642.36,
        "open": 1644.31
    },
    {
        "close": 1644.97,
        "epoch": 1739087280,
        "high": 1645.24,
        "low": 1641.57,
        "open": 1642.01
    },
    {
        "close": 1645.61,
        "epoch": 1739087340,
        "high": 1646.74,
        "low": 1644.22,
        "open": 1645.8
    },
    {
        "close": 1646.11,
        "epoch": 1739087400,
        "high": 1646.73,
        "low": 1644.24,
        "open": 1645.72
    },
    {
        "close": 1643.39,
        "epoch": 1739087460,
        "high": 1645.53,
        "low": 1642.97,
        "open": 1645.53
    },
    {
        "close": 1644.36,
        "epoch": 1739087520,
        "high": 1645.13,
        "low": 1643.08,
        "open": 1643.08
    },
    {
        "close": 1645.89,
        "epoch": 1739087580,
        "high": 1646.56,
        "low": 1644.35,
        "open": 1644.35
    },
    {
        "close": 1644.58,
        "epoch": 1739087640,
        "high": 1647.06,
        "low": 1643.74,
        "open": 1646.53
    },
    {
        "close": 1646.85,
        "epoch": 1739087700,
        "high": 1646.87,
        "low": 1644.54,
        "open": 1645.61
    },
    {
        "close": 1648.22,
        "epoch": 1739087760,
        "high": 1648.86,
        "low": 1646.93,
        "open": 1647.02
    },
    {
        "close": 1648.71,
        "epoch": 1739087820,
        "high": 1649.18,
        "low": 1647.1,
        "open": 1647.1
    },
    {
        "close": 1646.71,
        "epoch": 1739087880,
        "high": 1650.16,
        "low": 1646.05,
        "open": 1649.11
    },
    {
        "close": 1646.53,
        "epoch": 1739087940,
        "high": 1648.38,
        "low": 1646.53,
        "open": 1647
    },
    {
        "close": 1647.21,
        "epoch": 1739088000,
        "high": 1648.67,
        "low": 1646.64,
        "open": 1647.08
    },
    {
        "close": 1647.25,
        "epoch": 1739088060,
        "high": 1648.93,
        "low": 1645.9,
        "open": 1646.78
    },
    {
        "close": 1647.09,
        "epoch": 1739088120,
        "high": 1648.46,
        "low": 1646.27,
        "open": 1647.13
    },
    {
        "close": 1648.26,
        "epoch": 1739088180,
        "high": 1649.53,
        "low": 1646.23,
        "open": 1646.78
    },
    {
        "close": 1650.16,
        "epoch": 1739088240,
        "high": 1650.98,
        "low": 1648.49,
        "open": 1648.49
    },
    {
        "close": 1649.85,
        "epoch": 1739088300,
        "high": 1651.46,
        "low": 1649.85,
        "open": 1650.82
    },
    {
        "close": 1649.98,
        "epoch": 1739088360,
        "high": 1651.16,
        "low": 1648.92,
        "open": 1649.58
    },
    {
        "close": 1648.98,
        "epoch": 1739088420,
        "high": 1651.37,
        "low": 1648.98,
        "open": 1650.27
    },
    {
        "close": 1650.09,
        "epoch": 1739088480,
        "high": 1650.99,
        "low": 1648.5,
        "open": 1648.5
    },
    {
        "close": 1646.03,
        "epoch": 1739088540,
        "high": 1649.39,
        "low": 1645.51,
        "open": 1649.39
    },
    {
        "close": 1646.38,
        "epoch": 1739088600,
        "high": 1647.3,
        "low": 1644.74,
        "open": 1646.14
    },
    {
        "close": 1642.83,
        "epoch": 1739088660,
        "high": 1646.96,
        "low": 1642.66,
        "open": 1646.57
    },
    {
        "close": 1647.15,
        "epoch": 1739088720,
        "high": 1647.68,
        "low": 1642.11,
        "open": 1642.11
    },
    {
        "close": 1648,
        "epoch": 1739088780,
        "high": 1649.12,
        "low": 1646.56,
        "open": 1647.11
    },
    {
        "close": 1648.9,
        "epoch": 1739088840,
        "high": 1649.5,
        "low": 1647.7,
        "open": 1648.11
    },
    {
        "close": 1645.55,
        "epoch": 1739088900,
        "high": 1650.42,
        "low": 1645.55,
        "open": 1649.49
    },
    {
        "close": 1644.31,
        "epoch": 1739088960,
        "high": 1646.52,
        "low": 1644.31,
        "open": 1646.11
    },
    {
        "close": 1641.72,
        "epoch": 1739089020,
        "high": 1644.16,
        "low": 1641.72,
        "open": 1644.16
    },
    {
        "close": 1641.49,
        "epoch": 1739089080,
        "high": 1642.21,
        "low": 1641.2,
        "open": 1641.32
    }
]'>
<button type='button' id='' class='mBtn' onclick="doAjaxCalClassTrade()">Test Class Trade</button>
<div id="resultClassTrade" class="bordergray flex" style='border:2px solid red'>
     
</div>
<hr>



<div class="container mt-5">
        <h3>เลือกวันที่ (วว/ดด/ปป)</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker" placeholder="เลือกวันที่"/>
                    <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
</div>
<div id="resultFromPHP" class="bordergray flex">
     
</div>
TreeView
<div id="tree-view" class="tree-view"></div>

<div id="result" class="bordergray flex">
     
</div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

   <!-- Required JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
	<style>
        .tree-view {
            font-family: Arial, sans-serif;
        }
        .tree-view ul {
            list-style-type: none;
            padding-left: 20px;
        }
        .tree-view li {
            margin: 5px 0;
        }
        .tree-view .node {
            cursor: pointer;
        }
        .tree-view .node:hover {
            color: blue;
        }
        .tree-view .node.collapsed::before {
            content: "+ ";
        }
        .tree-view .node.expanded::before {
            content: "- ";
        }
    </style>

    <script>
        $(document).ready(function() {
            // Set moment locale to Thai
            moment.locale('th');
            
            // Initialize datepicker
            $('#datetimepicker').datetimepicker({
                format: 'L',
                locale: 'th',
                icons: {
                    time: 'fas fa-clock',
                    date: 'fas fa-calendar',
                    up: 'fas fa-arrow-up',
                    down: 'fas fa-arrow-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'fas fa-calendar-check',
                    clear: 'fas fa-trash',
                    close: 'fas fa-times'
                },
                buttons: {
                    showToday: true,
                    showClear: true,
                    showClose: true
                },
                tooltips: {
                    today: 'วันนี้',
                    clear: 'ล้าง',
                    close: 'ปิด',
                    selectMonth: 'เลือกเดือน',
                    prevMonth: 'เดือนก่อนหน้า',
                    nextMonth: 'เดือนถัดไป',
                    selectYear: 'เลือกปี',
                    prevYear: 'ปีก่อนหน้า',
                    nextYear: 'ปีถัดไป',
                    selectDecade: 'เลือกทศวรรษ',
                    prevDecade: 'ทศวรรษก่อนหน้า',
                    nextDecade: 'ทศวรรษถัดไป',
                    prevCentury: 'ศตวรรษก่อนหน้า',
                    nextCentury: 'ศตวรรษถัดไป'
                }
            });
        });
    </script>

	<script>
	function createTreeView(element, data) {
            if (typeof data === 'object' && data !== null) {
                const ul = document.createElement('ul');
                for (const key in data) {
                    const li = document.createElement('li');
                    const node = document.createElement('span');
                    node.classList.add('node');
                    node.textContent = key;

                    if (typeof data[key] === 'object' && data[key] !== null) {
                        node.classList.add('collapsed');
                        node.addEventListener('click', function() {
                            if (this.classList.contains('collapsed')) {
                                this.classList.remove('collapsed');
                                this.classList.add('expanded');
                            } else {
                                this.classList.remove('expanded');
                                this.classList.add('collapsed');
                            }
                        });

                        li.appendChild(node);
                        createTreeView(li, data[key]);
                    } else {
                        node.textContent += `: ${data[key]}`;
                        li.appendChild(node);
                    }

                    ul.appendChild(li);
                }
                element.appendChild(ul);
            }
        }

	</script>
	 


  
  
  
   <script src="api/jsCandlestickIndy.js"></script>
  

   <script>
    $(document).ready(function () {
      
	   //const clsIndy = new Dog("Buddy");
	   const clsIndy = new Indy("Buddy");
	   clsIndy.showCurPair();
	   candleRawData = document.getElementById("candleData").value ;
	   clsIndy.setRawdata(candleRawData) ;
	   let result = clsIndy.mainCalIndy() ;
       console.log('Result',result);
	   const analyzer = new AdvancedIndicators();
       let enrichedData = analyzer.calculateAdvancedIndicators(result);

	   const analyzer2 = new AdvancedIndicatorsStep2;
       enrichedData = analyzer2.FinalStep(enrichedData) ;
	   doAjaxCreateIndyByPHP(candleRawData);

	   /* แสดงผลเป็น treeview*/
       return;
  	   const treeViewElement = document.getElementById('tree-view');
	   const data = {
	 	 name: "Root",
		 children: [ enrichedData  ]
	   }
	   createTreeView(treeViewElement, data );
	   
	   
    });


async function doAjaxCreateIndyByPHP(dataRaw) {

   
    let result ;
    let ajaxurl = 'phpMainIndy.php';
    let data = {
	 "Mode": 'createIndy' ,
     "aggregrate" : "5m",
     "dataRaw" :dataRaw
    } ;
    data2 = JSON.stringify(data);
	//console.log('Data Raw', data2);
	//alert(data2);
    try {
        result =  await $.ajax({
            url: ajaxurl,
            type: 'POST',
			contentType: 'application/json',
	        //dataType: "json",
            data: data2,
	    success: function(data, textStatus, jqXHR){
              console.log(textStatus + ": " + jqXHR.status);
              // do something with data
            },
            error: function(jqXHR, textStatus, errorThrown){
			  alert(textStatus + ": " + jqXHR.status + " " + errorThrown);	 
              console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
            }
        });
        //alert(result);
		document.getElementById("resultFromPHP").innerHTML = JSON.stringify(result) ;
        return result;
    } catch (error) {
        console.error(error);
    }
}

async function doAjaxCalClassTrade() {

   
    let result ;
    let ajaxurl = 'phpMainIndy.php';
    let data = { "Mode": 'testClassTrade'
    } ;
    data2 = JSON.stringify(data);
	//console.log('Data Raw', data2);
	//alert(data2);
    try {
        result =  await $.ajax({
            url: ajaxurl,
            type: 'POST',
			contentType: 'application/json',
	        //dataType: "json",
            data: data2,
	    success: function(data, textStatus, jqXHR){
              console.log(textStatus + ": " + jqXHR.status);
              // do something with data
            },
            error: function(jqXHR, textStatus, errorThrown){
			  alert(textStatus + ": " + jqXHR.status + " " + errorThrown);	 
              console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
            }
        });
        //alert(result);
		document.getElementById("resultClassTrade").innerHTML = JSON.stringify(result) ;
		
        return result;
    } catch (error) {
        console.error(error);
    }
}
     
    
</script>
    
   
  


 </body>
</html>
