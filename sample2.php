<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">

  <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/9235/9235967.png" type="image/png">


  <title>Trade Deriv</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  
  
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped&family=Playfair+Display:ital@1&family=Sarabun:wght@200&display=swap" rel="stylesheet">
  
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <style>
   
  body,* {
    font-family: 'Kanit', sans-serif;*/
    font-family: 'Sarabun', sans-serif;
    font-family: 'Noto Sans Thai', sans-serif;
  }
  .pink { color: #ff0080 }
  .gray { color: gray }
  
   .bordergray   { border:1px solid gray ; padding:8px }
   td { border:1px solid lightgray; padding:5px }
   input[type="text"] {
    text-align: center;
   }
   .sarabun {
     font-family : 'Sarabun', sans-serif;
   }

  </style>

   <style>
        .switch-container {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        /* Hover effect */
        .slider:hover {
            background-color: #aaa;
        }

        input:checked + .slider:hover {
            background-color: #0d84e3;
        }
    </style>

  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  
  
  <script src="candleAnalysis.js"></script>  
  <script src="class/util.js"></script>  

  <script src="netmeter.js"></script>  

  
  <script type="module" src="main2.js?id=<?=time();?>"></script>
  <!-- 
  <script src="class/eval.js"></script>  
   -->
  
  

  
 </head>
 <body class='sarabun'>
  
  <button type='button' id='btnConnect' class='mBtn' onclick='getAssets()' >Connect</button>
  <button type='button' id='btngetAllAssets' class='mBtn' onclick='requestActiveSymbols()' >Get All Asset</button>

  <button type='button' id='' class='mBtn' onclick="MainAnaly()">Analysis Data</button>

<div id="status" class="bordergray flex">
     
</div>
 
 <div class="container mt-5">
    <div class="row">
      <div class="time-display">
       Server Time: <span id="serverTime">Loading...</span>	   
     </div>
	 <div class="switch-container">
        <label class="switch">
            <input type="checkbox" id='startReadCandle' checked>
            <span class="slider"></span>
        </label>
        <span>เริ่ม อ่านแท่งเทียน </span>
    </div>
	 <label>เลือก Timeframe Candle :</label>
                <div class="radio-group">
                    <label><input type="radio" name="timeframe" value="1" checked> 1 นาที</label>
                    <label><input type="radio" name="timeframe" value="5"> 5 นาที</label>
                    <label><input type="radio" name="timeframe" value="10"> 10 นาที</label>
                    <label><input type="radio" name="timeframe" value="15"> 15 นาที</label>
                    <label><input type="radio" name="timeframe" value="30"> 30 นาที</label>
                </div>

	</div>
    <div class="row">
	   <div id="" class="bordergray flex col-md-4">	       	
	     <label class="block text-sm font-medium text-gray-700">Symbol</label>
         <select id="symbolSelect" onclick='displaySymBol()' class="mt-1 block w-full p-2 border rounded">
		 </select>
		 <button type='button' id='' class='mBtn' onclick="getlocalAsset()">Get Local Asset</button>
		  <div id="symbolStatus" class="bordergray flex">
	      </div>
       </div>  
	   <div id="" class="bordergray flex col-md-4">	       	
	      <label class="block text-sm font-medium text-gray-700">จำนวนการเทรด </label>         
		  <div id="resultTotalTrade" class="bordergray flex">
	      </div>
		  <label class="block text-sm font-medium text-gray-700">Closed Trade</label>         
		  <div id="resultClosedTrade" class="bordergray flex">0
	      </div>

       </div>  
	    <div id="" class="bordergray flex col-md-4">	       	
	      <label class="block text-sm font-medium text-gray-700">Balance </label>         
		  <div id="resultBalance" class="bordergray flex">
	      </div>
		  <label class="block text-sm font-medium text-gray-700">Grand Profit(Bath) </label>         
		  <div id="resultGrandProfit" class="bordergray flex">0.0
	      </div>

       </div>  

	   <div id="result" class="bordergray flex" style='font-size:24px;color:red'>
	        
	   </div>
	  

	   <div id="" class="bordergray flex col-md-8">	       	
         <label class="block text-sm font-medium text-gray-700 pink ">Auto Trade</label>
		 <input type="checkbox" id="autoTrade" schecked>&nbsp;&nbsp;
	     <label class="block text-sm font-medium text-gray-700">Trade Type</label>
         <select id="contractType" class="mt-1 block w-full p-2 border rounded">		  
			<option value="" selected>Multiply
			<option value="">Rise-Fall
		   </select>
		 </select>
		 <label class="block text-sm font-medium text-gray-700">Time Duration</label>
		 <input type="text" id="timeDuration" style='width:80px' 
		 onfocus='this.select()'
		 value=3>         
		 Duration(M,H,D)
		 <select id="durationTrade" style='height:30px;width:80px'>
			<option value="m" selected>Minute
			<option value="h">Hour
			<option value="d">Day

		 </select>

		 <label class="block text-sm font-medium text-gray-700">Money Trade</label>
		 <input type="text" id="moneyTrade" style='width:80px' value=10>         
         <span id='resultSuggestTrade' style='color:red;font-weight:bold'></span>
		 <button type='button' id='' class='mBtn' onclick="placeOrder('CALL')">CALL</button>
		 <button type='button' id='' class='mBtn' onclick="placeOrder('PUT')">PUT</button>
		 <span id='signalSuggest' style='color:red;font-weight:bold'></span>

		 <button type='button' id='' class='mBtn' onclick="placeOrderTwin('CALLPUT')">CALL+PUT</button>
		 <span id='signalSuggest' style='color:red;font-weight:bold'></span>

		 <button type='button' id='' class='mBtn' onclick="getProposal()">Proposal</button>

		 <button type='button' id='' class='mBtn' onclick="buyContract9999()">Test Rise/Fall</button>

		 <a href='https://app.deriv.com/reports/positions?lang=TH' target=_blank><button type='button' id='' class='mBtn' onclick="fff()">หน้า กราฟ Deriv</button></a>

		 <a href='https://app.deriv.com/reports/positions?lang=TH' target=_blank><button type='button' id='' class='mBtn' onclick="fff()">หน้ารายงาน Deriv</button></a>
       </div> 
	   <div id="" class="bordergray flex col-md-4">	       	
	     <input type="checkbox" id="useTakeProfit" checked>
		   <span style='color:#ff0080;font-weight:bold'>
		    Use TakeProfit ?
		  </span>
		 <select id="profitPercent" onclick='CalTakeProfitMoney()'>
		   <?php
		     for ($i=0;$i<=10;$i++) { ?>
		         <option value="<?=$i*10?>"><?=$i*10;?>&nbsp;%    
		     <?php }
		   ?>			
		 </select>
		 <hr>
		 Take Profit Money::<input type="text" id="takeprofitmoney" onblur='savetakeprofitmoney()' value=1>
       </div> 

	</div>

	 
	 <div id="status" class="bordergray flex">
	      
	 </div>
     <span style='color:#ff0080'>Profit This Session :: </span> <input type="text" id = 'profitThissession' value =0> &nbsp;บาท

	 <span style='color:#ff0080'>Profit This Trade :: </span> <input type="text" id = 'profitThisTrade' value =0> &nbsp;บาท
	 <span style='color:#ff0080'>Loss This Trade :: </span> <input type="text" id = 'lossThisTrade' value =0> &nbsp;บาท
	 <span style='color:#ff0080'>Balance This Trade :: </span> <input type="text" id = 'lossThisTrade' value =0> &nbsp;บาท


	 <br>
	 
	      
	 <table id="profitTable">	 
		 <tr>
			<td>ครั้งที่ </td>
			<td>contract id</td>
			<td>contract_type</td>
			<td>Cost</td>

			<td>เวลาที่ซื้อ</td>
			<td>ซื้อที่ราคา</td>
			<td>ขายที่ราคา</td>
            
			<td>Profit</td>
		 </tr>
		  

	 </table>
	 <input type="checkbox" id="showTableProfit">&nbsp;&nbsp;
	 <span style='color:red;font-weight:bold'>แสดงตาราง Profit ??</span> 
	 
	 <div id="profitTableShow" class="bordergray flex">
	 </div>

	 <div id="closetradestatus" class="bordergray flex" style='height:100px;overflow:scroll;margin-top:15px'>
	      
	 </div>


	 


  
  <hr>
<div id="" class="bordergray flex" style='display:none'>
     

  sample2.getActionForTrade[js]()->thepapers.iqlab/AjaxTradeView.getActionForTrade[php]($data)->thepapers.deriv/api/clsCandlestickIndy->CreateAnalyisData($candleDataList)->
  clsCandlestickIndy.InsertData_AnalyEMATmp($dataTxt)->thepapers.iqlab/clsTrade.getActionFromIDVer2
</div>
  
  <?php
    Tab();
  ?>


<?php
function Tab() {  ?>
  
 HTML

 
 <div class="container" style='margin-bottom:300px'>
    <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Graph</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Table Candle Data</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Raw Data</button>
          </li>

		  <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab2" data-bs-toggle="tab" data-bs-target="#contact2" type="button" role="tab" aria-controls="contact2" aria-selected="false">Analysis Data</button>
          </li>
		  <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab3" data-bs-toggle="tab" data-bs-target="#contact3" type="button" role="tab" aria-controls="contact3" aria-selected="false">Eval Code</button>
          </li>

		  <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab4" data-bs-toggle="tab" data-bs-target="#contact4" type="button" role="tab" aria-controls="contact4" aria-selected="false">Trade Table</button>
          </li>

		  <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab5" data-bs-toggle="tab" data-bs-target="#contact5" type="button" role="tab" aria-controls="contact5" aria-selected="false">แผนการ Trade แบบต่างๆ</button>
          </li>

        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <p>This is the Home tab.</p>
          </div>
          
          
          <?php RawDataTab(); ?>
		  <?php AnalysisTab(); ?>
          <?php TableRawData(); ?>
          <?php EvalTabCode(); ?>
          <?php TradeTableTabCode();?>
		  <?php TabPlanTrade(); ?>

		  
        </div>
      </div>
    </div>
  </div>
     

 
     
  
<?php 

} // end function

function TableRawData() { ?>

         <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
             <p>This is the Table Candle Raw Data tab.</p>
			 <div class="candle-data">
               <h2>Latest Candle Data Table</h2>
                <table id="candleTable">			
                  <thead>
                    <tr>
                        <th>Time</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th class="ema3">EMA3</th>
                        <th class="ema5">EMA5</th>
                    </tr>
                  </thead>
                <tbody></tbody>
               </table>
             </div>
          </div>


<?php
} // end function

function RawDataTab() {  ?>
<div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            
			<h2>Result Raw Candle Data Json </h2>
             <button type='button' id='' class='mBtn' onclick="MainAnaly()">
			 สร้างข้อมูล วิเคราะห์ </button>
             <div id="resultRawData" class="bordergray flex" style='border:1px solid gray;padding:10px'>  
             </div>
          </div>

<?php
} // end function


function AnalysisTab() {  ?>

<div class="tab-pane fade" id="contact2" role="tabpanel" aria-labelledby="contact-tab2">
            <p>This is the Analysis Data tab-----.</p>
			<h2>Result Analysis  Data </h2>
             <button type='button' id='' class='mBtn' onclick="getActionForTrade999()">Get Action From Eval</button>
             <div id="resultAnalysis" class="bordergray flex" style='border:1px solid gray;padding:10px;  word-wrap: break-word;'>  
             </div>
			 <textarea id="dataTest" rows="" cols="">
			  
				

			 </textarea>
          </div>

<?php
} // end function

function EvalTabCode() {  ?>
<div class="tab-pane fade" id="contact3" role="tabpanel" aria-labelledby="contact-tab">
            
			<h2>Eval Code</h2>
             <button type='button' id='' class='mBtn' onclick="getActionForTrade()">Get Eval Code</button>

			 <button type='button' id='' class='mBtn' onclick="getActionForTrade()">Get Action </button>
			 <textarea id="evalCode" style='width:100%;height:300px;padding:10px'></textarea>

             <div id="resultEval" class="bordergray flex" style='border:1px solid gray;padding:10px'>  
             </div>
			 
          </div>

<?php
} // end function

function TradeTableTabCode() {  ?>
<style>
 th {
 border:1px solid gray; padding:8px;
 font-family: 'Sarabun', sans-serif;
 }
</style>

<div class="tab-pane fade" id="contact4" role="tabpanel" aria-labelledby="contact-tab">
            
			<h2>Trade Table </h2>
			  Server Time: <span id="serverTime2">Loading...</span>	   
			  <input type="checkbox" id="confirmCloseOrder">
			  <span style='color:#ff0080;font-weight:bold'>
			  Confirm ก่อนปิด Order ???
			  </span>
             
			 <button type='button' id='' class='mBtn' onclick="getPortfolio()">แสดงรายการ Order </button>
			 Price Direction <span id= 'priceDirection' style='color:red;font-weight:bold'></span>

             <div id="resultPortfolio" class="bordergray flex" style='border:1px solid gray;padding:10px'>  
             
			 <table id="trades-table" class='sarabun'>
				<thead>
					<tr class='sarabun'>
						<th>Contract ID</th>
						<th class='sarabun'>ประเภท</th>
						<th>สินทรัพย์</th>
						<th>ราคาซื้อ</th>
						<th>ราคาปัจจุบัน</th>
						<th>กำไร/ขาดทุน</th>
						<th>เวลาเริ่ม</th>
						<th>หมดเวลา</th>
						<th>การดำเนินการ</th>
					</tr>
				</thead>
				<tbody id="trades-body"></tbody>
             </table>
                                    ยอดขาดทุน($) ::
			 <div id="totalLoss" class="bordergray flex">			       
			 </div>
		  </div>
			 
          </div>

<?php
} // end function

function TabPlanTrade() {  ?>
<div class="tab-pane fade" id="contact5" role="tabpanel" aria-labelledby="contact-tab">
            
			<h2>Plan Trade</h2>            
             <div id="resultRawData" class="bordergray flex" style='border:1px solid gray;padding:10px'>  
			   <ol>
			    <li>Rise/Fall 
				   <ol>
				    <li>ตั้งเวลา n นาที แล้วรอให้ครบนาที </li>
				    <li>ตั้งเวลา n นาที แต่ไม่รอให้ครบนาที ดูจากเป้ากำไรที่ตั้งไว้  </li>
				    <li> </li>
				    <li> </li>
				   </ol>
				</li>
			    <li> Multiply 

				</li>
			    <li> </li>
			    <li> </li>
			   </ol>
             </div>
			 <div id="" class="bordergray flex">
			    คู่สกุลเงินและสินทรัพย์ที่มักจะเคลื่อนไหวสวนทางกันมีหลายคู่ ที่สำคัญมีดังนี้:
<ol>
<li>
ทองคำ (XAU) กับดอลลาร์สหรัฐ (USD)


เมื่อดอลลาร์แข็งค่า ทองคำมักจะอ่อนค่าลง
เพราะทองคำซื้อขายในสกุลดอลลาร์ และมักถูกใช้เป็นสินทรัพย์ปลอดภัยแทนดอลลาร์
<li>

USD/CHF กับ EUR/USD


สวิสฟรังก์ (CHF) มักเคลื่อนไหวคล้ายยูโร
เมื่อ EUR/USD ขึ้น USD/CHF มักจะลง และในทางกลับกัน

<li>
USD/JPY กับทองคำ


เยนญี่ปุ่นเป็นสกุลเงินปลอดภัยเช่นเดียวกับทองคำ
เมื่อตลาดผันผวน นักลงทุนมักจะเข้าหาทั้งเยนและทองคำ

<li>
สกุลเงินโภคภัณฑ์กับ USD


เช่น AUD, CAD, NZD มักเคลื่อนไหวสวนทางกับ USD
เพราะราคาสินค้าโภคภัณฑ์มักผกผันกับค่าเงินดอลลาร์

<li>
S&P 500 กับ VIX (ดัชนีความผันผวน)


VIX วัดความกลัวในตลาด จึงมักสวนทางกับตลาดหุ้น
เมื่อตลาดลง VIX มักจะพุ่งขึ้น

อย่างไรก็ตาม ความสัมพันธ์เหล่านี้ไม่ได้เป็นกฎตายตัว และอาจเปลี่ยนแปลงได้ตามสภาวะตลาดและปัจจัยอื่นๆ การเทรดควรพิจารณาปัจจัยพื้นฐานและเทคนิคประกอบด้วย
<li>
เพิ่มอีก 10 คู่สกุลเงินและสินทรัพย์ที่มักเคลื่อนไหวสวนทางกัน:

EUR/GBP กับ GBP/USD


เมื่อ GBP/USD แข็งค่า EUR/GBP มักจะอ่อนค่า
เนื่องจากปอนด์เป็นตัวหารร่วมในทั้งสองคู่
<li>

USD/CAD กับราคาน้ำมัน


แคนาดาเป็นผู้ส่งออกน้ำมันรายใหญ่
เมื่อราคาน้ำมันขึ้น USD/CAD มักจะลง เพราะ CAD แข็งค่า
<li>

AUD/JPY กับดัชนี VIX


AUD เป็นสกุลเงินเสี่ยง ขณะที่ JPY เป็นสกุลเงินปลอดภัย
เมื่อ VIX สูง (ตลาดกลัว) AUD/JPY มักจะลง
<li>

USD/BRL กับราคาสินค้าเกษตร


บราซิลเป็นผู้ส่งออกสินค้าเกษตรรายใหญ่
เมื่อราคาสินค้าเกษตรขึ้น BRL มักแข็งค่าเทียบ USD

<li>
USD/RUB กับราคาน้ำมัน


รัสเซียเป็นผู้ส่งออกน้ำมันรายใหญ่
เมื่อราคาน้ำมันขึ้น RUB มักแข็งค่าเทียบ USD

<li>
GBP/JPY กับดัชนีหุ้นโลก


GBP เป็นสกุลเงินเสี่ยง JPY เป็นสกุลปลอดภัย
เมื่อหุ้นโลกลง GBP/JPY มักจะลงตาม

<li>
EUR/NOK กับราคาน้ำมัน


นอร์เวย์เป็นผู้ส่งออกน้ำมันรายใหญ่ในยุโรป
เมื่อน้ำมันขึ้น NOK มักแข็งค่าเทียบ EUR

<li>
USD/MXN กับราคาทองแดง


เม็กซิโกเป็นผู้ผลิตทองแดงรายใหญ่
เมื่อราคาทองแดงขึ้น MXN มักแข็งค่า

<li>
AUD/NZD กับราคานม


นิวซีแลนด์เป็นผู้ส่งออกผลิตภัณฑ์นมรายใหญ่
เมื่อราคานมโลกขึ้น NZD มักแข็งค่าเทียบ AUD

<li>
USD/ZAR กับราคาทองคำ


แอฟริกาใต้เป็นผู้ผลิตทองคำรายใหญ่
เมื่อทองคำขึ้น ZAR มักแข็งค่าเทียบ USD

เช่นเดียวกับคู่แรก ความสัมพันธ์เหล่านี้ไม่ได้เป็นกฎตายตัว และควรใช้ร่วมกับการวิเคราะห์ปัจจัยอื่นๆ ในการตัดสินใจเทรด นอกจากนี้ ยังต้องระวังเรื่องสภาพคล่องของบางคู่เงินที่อาจมีน้อย ทำให้ spread กว้างและต้นทุนการเทรดสูง
</ol>
			 </div>
          </div>

<?php
} // end function


?>  

<script>
async function getActionForTrade(){

    let result ;
    let ajaxurl = 'https://thepapers.in/iqlab/AjaxTradeView.php';
    let data = { "Mode": 'getActionForTrade' ,
    "curpair" : document.getElementById("symbolSelect").value ,
    "candleData" : document.getElementById('result').innerText
    } ;
    data2 = JSON.stringify(data);
	//alert(data2);
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
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
		console.log(result)
        document.getElementById("signalSuggest").innerHTML = result;
        
		
		//document.getElementById("mainBoxAsset").innerHTML = result ;
		//hideWatingScreen();
        return result;
    } catch (error) {
        console.error(error);
    }
}

function CalTakeProfitMoney() {
	     
 let percentTakeProfit = parseFloat(document.getElementById("profitPercent").value) ;
 let moneyTrade = parseFloat(document.getElementById("moneyTrade").value) ;

 document.getElementById("takeprofitmoney").value = (moneyTrade * percentTakeProfit)/100 ;

 


} // end func

function displaySymBol() {

   document.getElementById("symbolStatus").innerHTML = 
   document.getElementById("symbolSelect").value  ;
   localStorage.setItem('selectedAsset',document.getElementById("symbolSelect").value );

} // end func 

function getlocalAsset() {

document.getElementById("symbolSelect").value = localStorage.getItem('curpairSelected');

} // end func


</script>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

 </body>
</html>
