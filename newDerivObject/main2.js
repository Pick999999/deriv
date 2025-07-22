
        $(document).ready(function() {
            // Set moment locale to Thai
			if (!localStorage.getItem('GroupData')) {
              createGroupData();
			}
			if (!localStorage.getItem('AssetData')) {
			   createAssetData();
			}




			createGroupButton();

			InitTradeFromLocal();
			DrawAssetSelectedButton();
			AjaxgetNewTradeno();
			document.getElementById("thisMoneyTrade").value = document.getElementById("realmoneyTrade").value ;

        });

		function createGroupData() {

			 GroupData = JSON.parse(document.getElementById("symbolGroup").value) ;
			 //ar = array();
		     newFields = { selectItem: "n"};
			 for (i=0;i<=GroupData.length-1 ;i++ ) {
                GroupData[i].selectItem = 'n' ;
			 }
			 localStorage.setItem('GroupData',JSON.stringify(GroupData));

		} // end func

        function createAssetData() {

			 AssetData = JSON.parse(document.getElementById("symbolAsset").value) ;
			 //ar = array();
		     newFields = { selectItem: "n"};
			 for (i=0;i<=AssetData.length-1 ;i++ ) {
                AssetData[i].selectItem = 'n' ;
				AssetData[i].isDefault = 'n' ;
			 }
			 localStorage.setItem('AssetData',JSON.stringify(AssetData));

		} // end func



		function AssetSelected(id,thisCode) {


			    //alert(id);
				id = parseInt(id);
				let Mode = '';
				if ($("#"+thisCode).hasClass('green')) {
					$("#"+thisCode).removeClass('green');
					Mode = 'remove';
                } else {
				   $("#"+thisCode).addClass('green');
				   Mode = 'append';
				}

				assetList = JSON.parse(localStorage.getItem('AssetData'));
				let updatedData = assetList.map(item => {
                 if (item.symbol_id === id) {
					if (Mode === 'remove') {
                      return { ...item, selectItem : 'n' }; // สร้าง object ใหม่โดยคัดลอก properties เดิมและอัปเดต age
					} else {
                      return { ...item, selectItem : 'y' }; // สร้าง object ใหม่โดยคัดลอก properties เดิมและอัปเดต age
					}
                 }
                   return item; // คืนค่า object เดิมหากไม่ตรงเงื่อนไข
                });


				localStorage.setItem('AssetData',JSON.stringify(updatedData));
				return ;








				assetSelectedList = document.getElementById("assetSelectedList").value ;
				assetSelectedListAr = assetSelectedList.split(',');
				let found = false;
				for (let i=0;i<=assetSelectedListAr.length-1 ;i++ ) {
                    if (assetSelectedListAr[i] === assetname+":"+thisid) {
					    found = true;
                    }
				}

				if (found == false ) {
					document.getElementById("assetSelectedList").value += assetname+":"+thisid +',';
					//document.getElementById("assetSelectedList").value = document.getElementById("assetSelectedList").value.slice(0, -1);
				}
				if (found == true && Mode=='remove') {
					sList = '';
					for (let i=0;i<=assetSelectedListAr.length-1 ;i++ ) {
                      if (assetSelectedListAr[i] !== assetname+":"+thisid) {
					    sList += assetSelectedListAr[i] + ',';
                      }
				    }
					sList = sList.slice(0, -1);
					document.getElementById("assetSelectedList").value = sList;
				}


                AssetList  = JSON.parse(localStorage.getItem('AssetData')) ;
				for (i=0;i<=AssetList.length-1 ;i++ ) {
					if (AssetList[i].symbol === thisid) {
						if (Mode === 'append') {
							AssetList[i].selectItem = 'y'
						} else {
                            AssetList[i].selectItem = 'n'
						}
					}
				}

				localStorage.setItem('AssetData',JSON.stringify(AssetList)) ;







		} // end func

		function createGroupButton() {

		   groupList = JSON.parse(document.getElementById("symbolGroup").value);

		   st = '';
		   for (i=0;i<=groupList.length-1 ;i++ ) {
			   st +="<button type='button' class='mBtn' onclick=createAssetButton('" + groupList[i].symbol_type +"')>"+ groupList[i].symbol_type +'</button>' ;
		   }

		   document.getElementById("result").innerHTML = st ;

		} // end func

        function createAssetButton(groupname) {

           if (groupname ==='') {
               groupname ='forex';
           }

		   //AssetList = JSON.parse(document.getElementById("symbolAsset").value);
		   AssetList = JSON.parse(localStorage.getItem('AssetData'));
		   st = '';
		   //alert(AssetList.length);
		   for (i=0;i<=AssetList.length-1 ;i++ ) {
             if (AssetList[i].symbol_type === groupname) {
               if (AssetList[i].selectItem === 'y') {
                  sclassname = 'green';
			   } else {
				  sclassname = '';
			   }
			   st +="<button type='button' class='mBtn " + sclassname + "' id='"+  AssetList[i].symbol + "'";
			   st +="onclick=AssetSelected('" + AssetList[i].symbol_id + "','" + AssetList[i].symbol + "')>" ;
			   st +=  AssetList[i].display_name +"</button>" ;
			  }
		   }
		   //alert(st);


		   document.getElementById("collapseOneInner").innerHTML = st ;

		} // end func

		function setClassGraph(sType) {
			/*

			.width0 { width:0% ; display:none }
.width30 { width:30% }
.width50 { width:50% }
.width70 { width:70% }
.width100 { width:100% }
			*/

			  chartName1 = '#chart1Container' ; chartName2 = '#chart2Container' ;
			  $(chartName1).removeClass('width0');
			  $(chartName1).removeClass('width30');
			  $(chartName1).removeClass('width50');
			  $(chartName1).removeClass('width70');
			  $(chartName1).removeClass('width100');

			  $(chartName2).removeClass('width0');
			  $(chartName2).removeClass('width30');
			  $(chartName2).removeClass('width50');
			  $(chartName2).removeClass('width70');
			  $(chartName2).removeClass('width100');


			  $(chartName2).removeClass('width0');

			  if (sType === 'Chart1-100') {
                 $(chartName1).addClass('width100');
				 $(chartName2).addClass('width0');

			  }
			  if (sType === 'Chart2-100') {
				  $(chartName1).addClass('width0');
				  $(chartName2).addClass('width100');
			  }
			  if (sType === 'Chart1-50') {
				  $(chartName1).addClass('width50');
				  $(chartName2).addClass('width50');
			  }



		} // end func

     function setTradeData() {

     tradeData = {
	   "assetGroup" : "",
       "assetName" : "",
       "timeduration": "",
	   "unitTime" : "",
	   "isAutoTrade" : "",
	   "MoneyTrade" : "",
	   "StopLoss" : "",
	   "Profit" : ""
	  }
      localStorage.setItem('tradeData',tradeData);




     } // end func

	 function getAssetofGroup(groupvalue) {

		      //alert(groupvalue)
			  symbolAsset = document.getElementById("symbolAsset").value ;



	 } // end func

function SaveInitTrade() {



sObj = {
	 "groupSelect" : document.getElementById("groupList").value,
	 "assetList"   :  document.getElementById("assetSelectedList").value,
     "assetName"   : document.getElementById("realAssetName").value ,
	 "timeduration" : document.getElementById("realTimeduration").value,
	 "UnitTime"     : document.getElementById("timedurationunit").value ,
	 "ContractType" : document.getElementById("contracttype").value ,
	 "isAutotrade"  : document.getElementById("autotrade").checked,
	 "MoneyTrade"   : document.getElementById("realmoneyTrade").value ,
	 "isCheckStopLoss"     :  document.getElementById("isCheckStopLoss").checked,
	 "stopLoss"     :  document.getElementById("realmoneyStopLoss").value,
	 "takeProfit"   : document.getElementById("realmoneyProfit").value,
     "autoTrade"    : document.getElementById("chkAutotrade").checked
}
console.log('sObj',sObj);


localStorage.setItem('tradeDerivInit',JSON.stringify(sObj));

} // end func

function InitTradeFromLocal() {

 //return;
 sData = JSON.parse(localStorage.getItem('tradeDerivInit'));
 document.getElementById("groupList").value = sData.groupSelect ;
 document.getElementById("realAssetName").value = sData.assetName ;
 document.getElementById("assetSelectedList").value = sData.assetList;
 document.getElementById("timeduration").value  = sData.timeduration ;
 document.getElementById("timedurationunit").value  = sData.UnitTime ;

 document.getElementById("contracttype").value = sData.ContractType ;
 document.getElementById("autotrade").checked = sData.isAutotrade ;

 document.getElementById("moneytrade").value = sData.MoneyTrade ;
 document.getElementById("moneystoplossPercent").value = sData.stopLoss ;

 document.getElementById("moneyprofitPercent").value = sData.takeProfit ;

 document.getElementById("realTimeduration").value = sData.timeduration ;
 document.getElementById("realmoneyTrade").value = sData.MoneyTrade ;
 document.getElementById("realmoneyStopLoss").value =  parseFloat(sData.stopLoss);

 document.getElementById("realmoneyProfit").value =  parseFloat(sData.takeProfit );
 document.getElementById("chkAutotrade").checked = sData.autoTrade ;

 document.getElementById("isCheckStopLoss").checked = sData.isCheckStopLoss ;
 

 groupname = '';
 createAssetButton(groupname);

} // end func

function DrawAssetSelectedButton() {

         AssetList = JSON.parse(localStorage.getItem('AssetData'));
		 st = '';

		 for (i=0;i<=AssetList.length-1 ;i++ ) {
		  if (AssetList[i].selectItem === 'y') {
             let sclassname = AssetList[i].isDefault == 'y' ? "green" : "";
			 //console.log(i,'-->',AssetList[i].isDefault);

			 if (AssetList[i].isDefault === 'y') {
				 document.getElementById("realSelectedAssetID").value = AssetList[i].symbol_id ;
				 document.getElementById("realSelectedAsset").value = AssetList[i].symbol ;
				 sclassname =  "green";
			 } else {
                 sclassname =  "";
			 }
			 st +="<button type='button' class='mBtn  " + sclassname + "' id='real_"+  AssetList[i].symbol_id + "'";
			 st +="onclick=AssetSelectedReal(" + AssetList[i].symbol_id + ",'"+ AssetList[i].symbol + "')>" ;
			 st +=  AssetList[i].display_name +"</button>" ;
		  }
		 }
		 //alert(st);
		 document.getElementById("btnAssetContainer").innerHTML = st ;

} // end func



function AssetSelectedReal(id,thisCode) { //(assetname,thisid) {

				//alert('stap1 '+id)
				lastid = document.getElementById("realSelectedAssetID").value ;
				$("#real_"+lastid).removeClass('green');



				let Mode = '' ;
				assetList = JSON.parse(localStorage.getItem('AssetData'));
				for (let i=0;i<=assetList.length-1 ;i++ ) {
                   if (assetList[i].isDefault == 'y') {
					   $("#real_"+i).removeClass('green');
                   }
				   if (assetList[i].symbol_id == id ) {
                      assetList[i].isDefault = 'y';
				   } else {
 				     assetList[i].isDefault = 'n';
				   }
				}
				thisBtnID = 'real_' + id ;
				//alert(document.getElementById(thisBtnID).innerHTML)  ;
				document.getElementById("realAssetName").value = document.getElementById(thisBtnID).innerHTML;




				document.getElementById("realSelectedAssetID").value = id;
				localStorage.setItem('AssetData',JSON.stringify(assetList));
				$("#real_"+id).addClass('green');
				document.getElementById("realSelectedAsset").value = thisCode;

				return;



				if ($("#real_"+id).hasClass('green')) {
					$("#real_"+id).removeClass('green');
					Mode = 'remove';
                } else {
				   $("#real_"+id).addClass('green');
				   Mode = 'append';
				}


				updatedData = assetList.map(item => {
                 if (item.symbol_id === id) {
					if (Mode === 'remove') {
                      return { ...item, isDefault : 'n' }; // สร้าง object ใหม่โดยคัดลอก properties เดิมและอัปเดต age
					} else {
                      return { ...item, isDefault : 'y' }; // สร้าง object ใหม่โดยคัดลอก properties เดิมและอัปเดต age
					}
                 }
                   return item; // คืนค่า object เดิมหากไม่ตรงเงื่อนไข
                });

				document.getElementById("assetSelectedList").value = thisCode;


				localStorage.setItem('AssetData',JSON.stringify(updatedData));


/*

				assetSelectedList = document.getElementById("assetSelectedList").value ;
				assetSelectedListAr = assetSelectedList.split(',');
//				real_frxAUDUSD
				let found = false;
				for (let i=0;i<=assetSelectedListAr.length-1 ;i++ ) {
					ss = assetSelectedListAr[i];
					ss2 = ss.split(':');
					thisname = 'real_'+  ss2[1] ;
					console.log(thisname ,' vs  ', thisid) ;
					$("#"+thisname).removeClass('green');

                    if (thisname ===  thisid) {
					    found = true;
						$("#"+thisname).addClass('green');
						document.getElementById("realSelectedAsset").value = ss2[1];
						//alert(thisid);
                    } else {
						$(thisname).removeClass('green');
					}
				}

				//alert(assetSelectedListAr.length);

/*
				if (found == false ) {
					document.getElementById("assetSelectedList").value += assetname+":"+thisid +',';
					//document.getElementById("assetSelectedList").value = document.getElementById("assetSelectedList").value.slice(0, -1);
				}
				/*
				if (found == true && Mode=='remove') {
					sList = '';
					for (let i=0;i<=assetSelectedListAr.length-1 ;i++ ) {
                      if (assetSelectedListAr[i] !== assetname+":"+thisid) {
					    sList += assetSelectedListAr[i] + ',';
                      }
				    }
					sList = sList.slice(0, -1);
					document.getElementById("assetSelectedList").value = sList;
				}

                */
					/*
                AssetList  = JSON.parse(localStorage.getItem('AssetData')) ;
				for (i=0;i<=AssetList.length-1 ;i++ ) {
					if (AssetList[i].symbol === thisid) {
						if (Mode === 'append') {
							AssetList[i].selectItem = 'y'
						} else {
                            AssetList[i].selectItem = 'n'
						}
					}
				}

				localStorage.setItem('AssetData',JSON.stringify(AssetList)) ;
				*/







		} // end func

function ClearAllSelectAsset() {


          AssetList  = JSON.parse(localStorage.getItem('AssetData')) ;
		  for (i=0;i<=AssetList.length-1 ;i++ ) {
               AssetList[i].selectItem = 'n' ;
			   AssetList[i].isDefault  = 'n' ;
		  }
		  localStorage.setItem('AssetData',JSON.stringify(AssetList)) ;

} // end func

function SetPlanTrade(planno) {

	     alert(planno);


} // end func

async function AjaxgetNewTradeno() {

    let result ;
    let ajaxurl = 'AjaxGetSignal.php';
    let data = { "Mode": 'getNewTradeno' ,

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
		//console.log(result)
		if (result.trim() === '' ) {
           document.getElementById("maintradeno").value = 1 ;
		} else {
           document.getElementById("maintradeno").value = parseInt(result.trim()) ;
		}


        return result;
    } catch (error) {
        console.error(error);
    }
}

async function AjaxSaveTradeList(MainTrade) {

/*

tradeNo   : document.getElementById("maintradeno").value ,
           assetCode : document.getElementById("realSelectedAsset").value ,
           timeframe : document.getElementById("realTimeduration").value ,
		   starttime : starttimeTrade ,
		   endtime : stoptimeTrade ,
           totalTrade: TradeList.length ,
           maxLossCon : maxLossCon,
           totalProfit : balance2.toFixed(2),

*/
    for (let i=0;i<=MainTrade.tradeList.length-1 ;i++ ) {

    }

    let result ;
    let ajaxurl = 'AjaxGetSignal.php';
    let data = { "Mode": 'saveTradeList' ,
     "tradeNo"   : MainTrade.tradeNo,
     "assetCode" : MainTrade.assetCode,
     "timeframe" : MainTrade.timeframe,
     "starttime" : MainTrade.starttime,
     "endtime"   : MainTrade.endtime,
     "totalTrade" : MainTrade.tradeList.length,
     "grandProfit" : document.getElementById("closedbalance").value ,
     "maxLossCon" : MainTrade.maxLossCon,
     "TradeList" : MainTrade.tradeList
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

		//document.getElementById("maintradeno").value = result.trim() ;
        return result;
    } catch (error) {
        console.error(error);
    }
}


