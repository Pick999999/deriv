import { Deriv } from './mainDeriv.js';
import { TViewChart } from './Chart.js';
import { getCandles1M,getCandle2Sec,OnTrade,authenticate,subscribeToTime,FindGrandTotal } from './request.js';
import { mainResponse } from './Deriv_Response.js';



const deriv = new Deriv();
const chart1 = new TViewChart(1,'chart1Container','chart1Data','chart1PriceLine','btnDrawLine1');
const chart2 = new TViewChart(2,'chart2Container','chart2Data','chart1PriceLine','btnDrawLine1');

function drawHorizontalLine99() {

         //alert('Draw');
         //chart1.drawHorizontalLine() ;
		 //chart2.drawHorizontalLine() ;
		 chart1.addHorizontalPriceLine();
		 chart2.addHorizontalPriceLine();
		 
		 //chart1.drawHorizontalLine() ;

} // end func


function RequestCandle999() {

        getCandles1M(deriv) ;

} // end func

function RequestCandle2Sec() {

        getCandle2Sec(deriv);
}

function SubscriptTime() {

         subscribeToTime(deriv);

} // end func


function Authen() {

         authenticate(deriv);

} // end func

function CallTrade() {
         OnTrade(deriv,'CALL')   ;

} // end func

function PutTrade() {
         OnTrade(deriv,'PUT')   ;

} // end func



function DrawChart1(data) {

         chart1.updateChart(data)

} // end func



//drawHorizontalLine()
document.getElementById('drawLineBtn').addEventListener('click', function() {
	   drawHorizontalLine99();
});

document.getElementById('btnStart').addEventListener('click', function() {
	   SubscriptTime();
	   Authen();
	   RequestCandle999();
	   RequestCandle2Sec();

});

document.getElementById('btnSubscript').addEventListener('click', function() {
	   SubscriptTime();
});


document.getElementById('btn1').addEventListener('click', function() {
	   RequestCandle999();
});

document.getElementById('btn2').addEventListener('click', function() {
	   RequestCandle2Sec();
});


document.getElementById('btnAuthen').addEventListener('click', function() {

		 Authen();
});


document.getElementById('btnCallTrade').addEventListener('click', function() {
		 CallTrade();
});

document.getElementById('btnPutTrade').addEventListener('click', function() {
		 PutTrade();
		 
});

document.getElementById('FindGrandTotalBtn').addEventListener('click', function() {
		 FindGrandTotal();
});



/*
window.chart1Data.addEventListener('input', function() {
      console.log('sss');
});
*/

//onclick="RequestCandle999()"
//export { RequestCandle999}
