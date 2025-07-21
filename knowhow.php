<?php
/*
1.derivSender.js->fetchCandles()
2.derivReciver.js->handleResponse(response)
  2.1 กรณี Update Candle check response
    response.msg_type === 'candles' 
    //เรียกจาก candleDataProcess จาก file deReciver.js ซึ่ง candleDataProcess จะ
          ทำการ เปลี่ยน จาก epoch-->time โดยการ map พร้อมทั้ง คำนวณ ema3,ema5 และ pack รวมเป็น data2 ส่งกลับมา
    data2 = candleDataProcess(response.candles);
          จากนั้นทำการ เรียก function  updateChart99(data2.candleData) จากไฟล์ graphTreeview.js 
          ซึ่ง จะทำการ Set Data ให้กับ Series
      
      candleSeries.setData(candleData) ;
	


*/
?>