import { TViewChart }    from './Chart.js'
import { CloseContract,doAjaxGetSignal, FindGrandTotal } from './request.js'

function mainResponse(response,deriv) {

         //console.log('Response',response) ;
/*
เมือ  call/put-->request(jsonDataSale) --> response.buy ทำการขอ portfolio
              request.portfolio(เพื่อให้ได้ ลิสท์รายการ Contract ID)
			  ได้ response.msg_type=portfolio ซึ่งก็เป็น detail ของ contract ต่างๆที่เปิดอยู่
			  ถ้าจะติดตาม รายการ Update Profit ของแต่ละ  Contract ต้องขอ  proposal_open_contract: 1,
			  จะได้  response.portfolio

สรุป  ใช้  3 Request เพื่อจะ เข้า Trade และ ได้มาซึ่ง  Tracking
request(jsonDataSale)->request(portfolio)->request(proposal_open_contract)
และ response แต่ละขั้น
response.buy->response.msg_type === 'portfolio'-->response.msg_type ===  'proposal_open_contract'
*/

         console.log('REsponse',response.msg_type)

		 if (response.msg_type=='candles') {
			 let textarea = document.getElementById("chart1Data");
			 let data1 = response.candles.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close
             }));
             let totalCandle = response.candles.length
		     let sdate = new Date(response.candles[totalCandle-1].epoch  * 1000);
			 let timeserver = parseInt(document.getElementById("timeserver").value) ;
			 const date = new Date(timeserver * 1000);
//             const LastSecondCandle= response.candles[totalCandle-1].epoch ;
		     //console.log('LastSecondCandle',date.getSeconds());

//% 60 === 0



			 textarea.value = JSON.stringify(data1);
			 textarea.dispatchEvent(new Event('change'));
		 }

		 if (response.time) {
             //updateServerTime(response.time);
			 //console.log('Time=',response.time)
             if (document.getElementById("timeserver").value === '') {
			     document.getElementById("timeserver").value = response.time
             } else {
               document.getElementById("timeserver").value = parseInt(document.getElementById("timeserver").value)+1
			 }
			 const date = new Date(response.time * 1000);
             const timeStr = date.toLocaleTimeString();
             document.getElementById('showTime').textContent = timeStr;


             document.getElementById('showSecond').textContent = date.getSeconds();
			 document.getElementById('showTime2').textContent = 60- date.getSeconds();
			 if (date.getSeconds() === 0 && document.getElementById("chkAutotrade").checked === true) {
				// alert('Fetch');
				doAjaxGetSignal();
			 }
			 if (date.getSeconds() === 5 || date.getSeconds() === 15 ){
				 $('FindGrandTotalBtn').trigger('click');
			 }

         }
		 if (response.msg_type=='ohlc') {

			 let textarea = document.getElementById("chart2Data");
			 let data = [{
                time: response.ohlc.epoch,
				open: response.ohlc.open,
				high: response.ohlc.high,
				low: response.ohlc.low,
				close: response.ohlc.close
              }];

			 //textarea.value = JSON.stringify(response.candles);
			 textarea.value = JSON.stringify(data);
			 textarea.dispatchEvent(new Event('change'));
		 }

		 if (response.buy ) {
		     console.log('Buy Resonse',response) ;
			 const request = {
               portfolio: 1
             }
             deriv.ws.send(JSON.stringify(request));
			 console.log('Send Request Portfolio') ;
		 }
		 if (response.msg_type === 'sell' ) {
		     console.log('Sell Contract Resonse',response) ;
			 UpdateTableForSale(response);
             //deriv.ws.send(JSON.stringify(request));
		 }



		 if (response.msg_type === 'portfolio' && response.portfolio  ) {
            console.log('Response -',response.msg_type) ;
		    console.log('Response -',response) ;

            if (response.portfolio.contracts.length === 0) {
              //console.log('No Portfolio',response) ;
			  return;
            } else {
              console.log('Portfolio',response) ;
			}
			let contractList = response.portfolio.contracts;
			let found = false

			for (let i=0;i<= contractList.length-1 ;i++ ) {
               let found = deriv.TradeList.filter(contractid => contractid === contractList[i].contract_id);
			   //console.log('Found Status ',contractList[i].contract_id,'=',found.length);
			   if (found.length === 0) {
				  deriv.TradeList.push(contractList[i].contract_id);
		  	      InsertContractToTable(response.portfolio.contracts,deriv);
				  // Track Profit By Send proposal_open_contract
				  deriv.ws.send(JSON.stringify({
                     proposal_open_contract: 1,
                     contract_id: contractList[i].contract_id,
                     subscribe: 1
                 }));
			   } else {
				  //const contract = response.proposal_open_contract;
                  //UpdateContract(response);
			   }
			}

			return;
			response.portfolio.contracts.forEach(contract => {
				let divTrade = document.getElementById(contract.contract_id);
				deriv.TradeList.push(contract.contract_id);
				if (!divTrade) {
					st = `
					Contract ID: ${contract.contract_id}
					Type: ${contract.contract_type}
					Purchase Time: ${new Date(contract.purchase_time * 1000).toLocaleString()}
					Purchase Price: ${contract.buy_price}
					Payout: ${contract.payout}
					Expiry Time: ${new Date(contract.expiry_time * 1000).toLocaleString()}
				`
                 let sDiv  = '<div id="'+contract.contract_id+ '">';


				 sDiv =  st + '</div>';
				 console.log(sDiv)
				 deriv.ws.send(JSON.stringify({
                     proposal_open_contract: 1,
                     contract_id: contract.contract_id,
                     subscribe: 1
                 }));
				 document.getElementById("showTradeList").innerHTML =  sDiv;
				}
            });
		 }

         if ( response.msg_type ===  'proposal_open_contract') {
			  //console.log('Proposal',response);
			  UpdateContract(response,deriv);
         }



} // end func

function  requestPortfolio(deriv){

    if (deriv.ws.readyState === 1) {  // ตรวจสอบว่า WebSocket เชื่อมต่ออยู่
        deriv.ws.send(JSON.stringify({
            portfolio: 1
        }));
    }
}

function timestampToDateString(timestamp) {
        const date = new Date(timestamp * 1000); // Convert timestamp (seconds) to milliseconds

        // Format the date as a string (e.g., "YYYY-MM-DD HH:mm:ss")
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        const formattedDate = date.toLocaleString('en-US', options);

        return formattedDate;
} //end func

function UpdateContract(response,deriv) {

        //console.log('Update Contract')

        const contract = response.proposal_open_contract;
        if (contract.is_expired) {
            let endPrice = contract.exit_tick;
            const profit = contract.profit;
			let balance2 = 0.0;

			//alert(balance2);
		    document.getElementById("closedbalance").value =  balance2 ;
			let contractClosedList = document.getElementById("contractClosedList").value ;
			let contractClosedListArray = contractClosedList.split(';');
			let foundID = false;
			for (let i=0;i<=contractClosedListArray.length-1 ;i++ ) {
			  if (parseInt(contractClosedListArray[i].trim()) === contract.contract_id) {
                  foundID = true ; break ;
			  }
			}



			let thisrowId = '#tr_' + contract.contract_id ;
			$(thisrowId).addClass('gray');
			let closedID = 'td_closed_' +contract.contract_id ;
			document.getElementById(closedID).innerHTML = 'Y';
			if (foundID === false) {
				if (profit < 0) {
				 let lastLossCon = parseInt(document.getElementById("lossCon").value)+1;
				 document.getElementById("lossCon").value = lastLossCon  ;
				 let maxlossCon  = parseInt(document.getElementById("maxlossCon").value);
				 if (lastLossCon > maxlossCon) {
					 document.getElementById("maxlossCon").value = lastLossCon;
				 }

				 if (document.getElementById("useMartingale").checked === true) {
					 let gailMoney=document.getElementById("gailMoney").value;
				     let gailMoneyAr = gailMoney.split(',');
					 document.getElementById("thisMoneyTrade").value = gailMoneyAr[lastLossCon];
				 }


				 document.getElementById("winCon").value = 0;
			   } else {
				 let lastWinCon = parseInt(document.getElementById("winCon").value);
				 document.getElementById("winCon").value =  lastWinCon+1 ;
					 //document.getElementById("winCon").value+1;
				 document.getElementById("lossCon").value = 0 ;
			   }
			   document.getElementById("contractClosedList").value = document.getElementById("contractClosedList").value + contract.contract_id+';';

			}
           //alert(profit);
        }


        let totalLoss = 0;
        //id = contract.contract_id ;
        let tdProfitID = 'td_profit_' + contract.contract_id;
        document.getElementById(tdProfitID).textContent = '';
        document.getElementById(tdProfitID).textContent = contract.profit;

		//td_entrySpot
        let tdentrySpot = 'td_entrySpot_' + contract.contract_id;
        document.getElementById(tdentrySpot).textContent = contract.entry_spot;

        //if (document.getElementById("chart1PriceLine").value === '') {
		document.getElementById("chart1PriceLine").value = contract.entry_spot;
		$('#drawLineBtn').trigger('click');

		//}


        let tdcurpriceID = 'td_curprice_' + contract.contract_id;
        document.getElementById(tdcurpriceID).textContent = '';
        document.getElementById(tdcurpriceID).textContent = contract.current_spot; //entry_spot;


//		td_diffprice_
	    let tddiffPriceID = 'td_diffprice_' + contract.contract_id;
        document.getElementById(tddiffPriceID).textContent = '';
		let priceDiff = contract.current_spot - contract.entry_spot ;
        document.getElementById(tddiffPriceID).textContent = priceDiff.toFixed(4);
		/*
        if (this.lastCurrentSpot > contract.current_spot) {
            this.priceDirection = 'D';
        }
        if (this.lastCurrentSpot < contract.current_spot) {
            this.priceDirection = 'U';
        }
        if (this.lastCurrentSpot === contract.current_spot) {
            this.priceDirection = 'E';
        }

        //document.getElementById("priceDirection").innerHTML +=  this.priceDirection +';';


        this.lastCurrentSpot = contract.current_spot
		*/


		let tdExpireTimeID = 'td_ExpireTime_' + contract.contract_id;
        document.getElementById(tdExpireTimeID).textContent = '';
        //document.getElementById(tdremainTimeID).textContent = contract.remainTime ;
        document.getElementById(tdExpireTimeID).textContent =   timestampToDateString(contract.expiry_time);

        // เวลาปัจจุบัน
        let timeserver = parseInt(document.getElementById("timeserver").value);

		let expireTime = contract.expiry_time ;
        let remainTimeSec = contract.expiry_time - contract.current_spot_time ;


        let tdremainTimeID = 'td_remainTime_' + contract.contract_id;
        document.getElementById(tdremainTimeID).textContent = '';
        //document.getElementById(tdremainTimeID).textContent = contract.remainTime ;
        document.getElementById(tdremainTimeID).textContent = remainTimeSec ;

        if (document.getElementById("isCheckStopLoss").checked === true ) {
          if (contract.profit >= -0.3) {
			  CloseContract(contract.contract_id,deriv) ;
		  }
   		  if ( remainTimeSec <= 30 && contract.profit <= -0.65) {
			//CloseContract(contract.contract_id,deriv) ;
			//alert('Auto Close');
		  }
		}


/*
        if (this.useTakeProfit == true) {
            //console.log('Check Profit') ;
            this.totalLossOnTable += contract.profit;

            this.takeprofitmoney = parseFloat(document.getElementById("takeprofitmoney").value);
            if (parseFloat(contract.profit) > 0.0) {
                if (parseFloat(contract.profit) >= this.takeprofitmoney) {
                    console.log('contract want close', contract);
                    this.closeContract(contract.contract_id, contract.profit, contract);
                }
            }
        }
*/
} //end showContract

function InsertContractToTable(contracts,deriv) {

	if (contracts && contracts.length > 0) {
        const table = document.getElementById('trades-table');
		let totalRow = table.rows.length ;
        const tbody = document.getElementById('trades-body');
		let contractIDList = [];

        contracts.forEach(contract => {
			    let id = contract.contract_id ;
				let found = false;
				for (let i=0;i<=contractIDList.length -1 ;i++ ) {
					 console.log(id ,' vs ', contractIDList[i]);
				     if (parseInt(id) === parseInt(contractIDList[i])) {
				        found = true; break ;
						alert('Found')
				     }
				}
				let thisrowid= 'tr_'+ contract.contract_id ;
				let rowObj = document.getElementById(thisrowid);
				if (found === false ) {

				    contractIDList.push(id);
					const row = tbody.insertRow();
					console.log('Contract-', contract);
					//AppendTradeObject(contract);


					row.setAttribute('data-contract-id', contract.contract_id);
					row.setAttribute('id','tr_'+ contract.contract_id);

					// เทรดครั้งที่
					var cell0 = row.insertCell(0)
                    totalRow = table.rows.length ;
					cell0.textContent = totalRow-1;
					cell0.id = 'td_' + totalRow-1;

					// Contract ID
					var cell0 = row.insertCell(1)
					cell0.textContent = contract.contract_id;
					cell0.id = 'td_' + contract.contract_id;


					// Contract Type
					row.insertCell(2).textContent = contract.contract_type;

					// Symbol
					row.insertCell(3).textContent = contract.symbol;

					// Buy Price
					const buyPrice = parseFloat(contract.buy_price);
					//row.insertCell(3).textContent = buyPrice.toFixed(2);
					var cell2 = row.insertCell(4)
					cell2.textContent = buyPrice.toFixed(2);
					cell2.id = 'td_Cost_' + contract.contract_id;



					// entrySpot Price
					//const entry_spot = parseFloat(contract.entry_spot);
					//row.insertCell(4).textContent = entry_spot.toFixed(2);
					// Current Price (will be updated)
					var cell3 = row.insertCell(5)
					cell3.textContent = "กำลังโหลด...";
					cell3.id = 'td_entrySpot_' + contract.contract_id;

					// Current Price (will be updated)
					var cell4 = row.insertCell(6)
					cell4.textContent = "กำลังโหลด...";
					cell4.id = 'td_curprice_' + contract.contract_id;

					var cell5 = row.insertCell(7)
					cell5.textContent = "กำลังโหลด...";
					cell5.id = 'td_diffprice_' + contract.contract_id;





					// Profit/Loss (will be updated)
					const profitCell = row.insertCell(8);
					profitCell.textContent = "กำลังโหลด...";
					profitCell.id = 'td_profit_' + contract.contract_id;
					profitCell.className = 'pending';


					// Purchase Time
					row.insertCell(9).textContent = new Date(contract.purchase_time * 1000).toLocaleString('th-TH');


					const expireTimeCell = row.insertCell(10);
					expireTimeCell.textContent = contract.remaining_time;
					expireTimeCell.id = 'td_ExpireTime_' + contract.contract_id;
					expireTimeCell.className = 'pending';

					const remainTimeCell = row.insertCell(11);
					remainTimeCell.textContent = contract.remaining_time;
					remainTimeCell.id = 'td_remainTime_' + contract.contract_id;
					remainTimeCell.className = 'pending';

					//

					// Action Button
					const actionCell = row.insertCell(12);
					const closeButton = document.createElement('button');
					//console.log('On Close', contract)

					closeButton.textContent = 'ปิด Order';
					closeButton.className = 'close-btn';
					closeButton.onclick = () => CloseContract(contract.contract_id,deriv);
					actionCell.appendChild(closeButton);

					const closedCell = row.insertCell(13);
					closedCell.id = 'td_closed_'+ contract.contract_id;
					closedCell.textContent = 'N';
				}


            });

            table.style.display = 'table';
           // document.getElementById("totalLoss").innerHTML = totalLoss;

            //this.showStatus('แสดงรายการเทรดที่เปิดอยู่ทั้งหมด ' + contracts.length + ' รายการ', 'success');
        } else {
            table.style.display = 'none';
            this.showStatus('ไม่พบรายการเทรดที่เปิดอยู่', 'success');
        }


} // end func InsertContractToTable()

function UpdateTableForSale(response) {


		 let contractID = response.echo_req.sell;
		 let soldFor = response.sell.soldFor ;
//		 td_Cost_273084981908
         let tdCostID   = 'td_Cost_' + contractID ;
		 let tdProfitID = 'td_profit_' + contractID ;
		 let closedID = 'td_closed_' + contractID ;

/*
		 let thisrowId = '#tr_' + contractID ;
		 $(thisrowId).addClass('gray');
*/

		 let Cost = document.getElementById(tdCostID).innerHTML ;
		 let Profit = soldFor - parseFloat(Cost) ;
		 document.getElementById(tdProfitID).innerHTML = Profit ;
/*
		 if (Profit < 0.0 ) {
			 let lossCon = parseInt(document.getElementById("lossCon").value) ;
			 lossCon++ ;
			 document.getElementById("lossCon").value = lossCon ;
		 }
*/
		 //document.getElementById("closedbalance").value = parseFloat(document.getElementById("closedbalance").value)+ parseFloat(document.getElementById("balance").value);

		// alert(Profit);


/*
		 let balance = 0 ;
		 let tradestable = document.getElementById("trades-table");
		 for (i=0;i<=tradestable.rows.length-1 ;i++ ) {
			   var profit = parseFloat(cells[7].textContent);
			   balance = balance + profit ;
		 }

		 document.getElementById("closedbalance").value = balance.toFixed(2);
*/
		 document.getElementById(closedID).innerHTML = 'Y';
		 /*
		 if (Profit < 0) {
			 document.getElementById("lossCon").value = parseInt(document.getElementById("lossCon").value)+1;
			 document.getElementById("winCon").value = 0;
		 } else {
			 document.getElementById("winCon").value = parseInt(document.getElementById("winCon").value)+1;
			 document.getElementById("lossCon").value = 0 ;

		 }
 */


		 let trName = '#tr_' + contractID ;
		 $(trName).addClass('gray');

} // end func



function getNewContractObject(deriv,contract) {
/*
					st = `
					Contract ID: ${contract.contract_id}
					Type: ${contract.contract_type}
					Purchase Time: ${new Date(contract.purchase_time * 1000).toLocaleString()}
					Purchase Price: ${contract.buy_price}
					Payout: ${contract.payout}
					Expiry Time: ${new Date(contract.expiry_time * 1000).toLocaleString()}
*/

let found = false
for (let i=0;i<=deriv.contractList.length-1 ;i++ ) {
   if (deriv.contractList[i].contract_id === contract.contract_id) {
	   found = true; break ;
   }
}

if (found=== false) {
	purchaseTimeTmp    =  new Date(contract.purchase_time * 1000).toLocaleString();
	ExpiryTimeTmp      =  new Date(contract.expiry_time * 1000).toLocaleString() ;
	TotalSecond        =  contract.expiry_time - contract.purchase_time ;
	TakeProfitMoney    =  0 ;
	StopLossMoney      =  0 ;
/*
	sObj = {
	  contract_id   : contract.contract_id
	  type          : contract.contract_type
	  BuyPrice      : contract.buy_price
	  purchaseTime  : purchaseTimeTmp
	  ExpiryTime    : ExpiryTimeTmp
      RemainTime    : 0
	  TotalSecond   : TotalSecond
	  TakeProfit    : TakeProfitMoney
	  StopLoss      : StopLossMoney
	}
	*/
}
return sObj ;

} // end func getNewContractObject



export { mainResponse }



/*
response = Proposal_contract				  {
    "account_id": 191869168,
    "barrier": "2163.143",
    "barrier_count": 1,
    "bid_price": 0.55,
    "buy_price": 1,
    "contract_id": 273002152688,
    "contract_type": "CALL",
    "currency": "USD",
    "current_spot": 2162.899,
    "current_spot_display_value": "2162.899",
    "current_spot_time": 1739959312,
    "date_expiry": 1739959334,
    "date_settlement": 1739959334,
    "date_start": 1739959274,
    "display_name": "Volatility 25 Index",
    "entry_spot": 2163.143,
    "entry_spot_display_value": "2163.143",
    "entry_tick": 2163.143,
    "entry_tick_display_value": "2163.143",
    "entry_tick_time": 1739959276,
    "expiry_time": 1739959334,
    "id": "73ca7955-ba43-bd65-6863-7b49b2fb808c",
    "is_expired": 0,
    "is_forward_starting": 0,
    "is_intraday": 1,
    "is_path_dependent": 0,
    "is_settleable": 0,
    "is_sold": 0,
    "is_valid_to_cancel": 0,
    "is_valid_to_sell": 1,
    "longcode": "Win payout if Volatility 25 Index is strictly higher than entry spot at 1 minute after contract start time.",
    "payout": 1.95,
    "profit": -0.45,
    "profit_percentage": -45,
    "purchase_time": 1739959274,
    "shortcode": "CALL_R_25_1.95_1739959274_1739959334_S0P_0",
    "status": "open",
    "transaction_ids": {
        "buy": 544233295568
    },
    "underlying": "R_25"
}
*/


 /*
[
    {
        "app_id": 66726,
        "buy_price": 1,
        "contract_id": 272995300348,
        "contract_type": "CALL",
        "currency": "USD",
        "date_start": 1739953951,
        "expiry_time": 1739954011,
        "longcode": "Win payout if Volatility 25 Index is strictly higher than entry spot at 1 minute after contract start time.",
        "payout": 1.95,
        "purchase_time": 1739953951,
        "shortcode": "CALL_R_25_1.95_1739953951_1739954011_S0P_0",
        "symbol": "R_25",
        "transaction_id": 544219712508
    }
]
*/

/*
จาก  response =
{
    "echo_req": {
        "adjust_start_time": 1,
        "count": 20,
        "end": "latest",
        "granularity": 60,
        "start": 1,
        "style": "candles",
        "subscribe": 1,
        "ticks_history": "R_25"
    },
    "msg_type": "ohlc",
    "ohlc": {
        "close": "2212.695",
        "epoch": 1739611724,
        "granularity": 60,
        "high": "2215.131",
        "id": "1c3756dd-9eb0-1305-4500-cd835a4a377a",
        "low": "2212.695",
        "open": "2215.131",
        "open_time": 1739611680,
        "pip_size": 3,
        "symbol": "R_25"
    },
    "subscription": {
        "id": "1c3756dd-9eb0-1305-4500-cd835a4a377a"
    }
}
แล้วใช้ คำสั่ง
let data = response.ohlc.map(candle => ({
                    time: candle.epoch,
                    open: candle.open,
                    high: candle.high,
                    low: candle.low,
                    close: candle.close
              }));
เกิด error
 TypeError: response.ohlc.map is not a function
*/
