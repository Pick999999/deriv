<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>
  <style>
   td,th { border:1px solid gray ;padding:4px}
   th { background:#0080ff;color:white;font-size:18px } 
  </style>
  <link href="" rel="stylesheet">
 </head>
 <body>
  <table>
<tr>
	<th>ลำดับ</th>
	<th>ไฟล์ php </th>
	<th>OutPut</th>
	<th>Input</th>
	<th>ตัวอย่างการเรียกใช้ </th>
  </tr>
  <tr>
	<td>1</td>
	<td>deriv/api/phpCandlestickIndy.php</td>
	<td>หา ema3,ema5,rsi,bb,atr</td>
	<td>รับข้อมูล  candle พื้นฐาน</td>
	<td></td>
  </tr>
  <tr>
	<td>2</td>
	<td>deriv/api/phpAdvanceIndy.php</td>
	<td>นำข้อมูล ema มาหา turnpoint,cutpoint....</td>
	<td></td>
	<td></td>
  </tr>
  <tr>
	<td>3</td>
	<td>deriv/newDerivObject/TradingConditionAnalyzer.php</td>
	<td>ทำการ Analyze แล้วบอก จำนวน Warning แยกตามประเภทต่างๆ</td>
	<td><ol><li>deriv/newDerivObject/rawData.json</li><li>phpCandlestickIndy.php</li><li>phpAdvanceIndy.php</li></ol></td>
	<td>deriv/AjaxNewTrade.php</td>
  </tr>
  <tr>
	<td>4</td>
	<td>deriv/newDerivObject/TradingSignalCheckerV3.php</td>
	<td>วิเคราะห์แท่งเทียน และทำนาย แท่งเทียนต่อไป ผลงานจาก DeekSeek</td>
	<td>deriv/newDerivObject/dataTest.json</td>
	<td>getsignalCheckerDeepSeek() จากไฟล์  deriv/newDerivObject/AjaxGetSignal.php</td>
  </tr>
  <tr>
	<td>5</td>
	<td>deriv/candleAnalyzerClaude.php</td>
	<td>วิเคราะห์แท่งเทียน และทำนาย แท่งเทียนต่อไป ผลงานจาก Claude</td>
	<td>deriv/newDerivObject/rawData.json</td>
	<td>getRiskAnalysisClaude() จากไฟล์  deriv/newDerivObject/AjaxGetSignal.php</td>
  </tr>

  <tr>
	<td>6</td>
	<td>deriv/candleAnalyzerChatGPT.php</td>
	<td>วิเคราะห์แท่งเทียน และทำนาย แท่งเทียนต่อไป ผลงานจาก ChatGPT</td>
	<td>deriv/newDerivObject/rawData.json</td>
	<td> จากไฟล์   deriv/predictAll.php</td>
  </tr>

  <tr>
	<td>7</td>
	<td>deriv/newDerivObject/labCutPoint.php</td>
	<td>ทำการสรุป ผล win/loss โดยดึง suggest color จากการพิจารณา AnalyData[$thisIndex]['CutPointType'] === '3->5'
	ใช้ร่วมกับ deriv/newDerivObject/AjaxFixTrade.php
	</td>
	<td>
	deriv/newDerivObject/rawData.json
	2.
	
	</td>
	<td></td>
  </tr>


  </table>
 </body>
</html>
