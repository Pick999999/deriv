async function doAjaxFindLoss() {

    let result ;
    let ajaxurl = 'AjaxNewLab.php';
    let data = { "Mode": 'findloss' ,
    "datecheck" : document.getElementById("dateCandle").value,
    "timeframe" : 5
    } ;
    data2 = JSON.stringify(data);
	document.getElementById("result").innerHTML = '';

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
		document.getElementById("result").innerHTML = result ;

        return result;
    } catch (error) {
        console.error(error);
    }
}

function goNext() {

	document.getElementById("dateCandle").selectedIndex++;

} // end func

function goPrevious() {

	document.getElementById("dateCandle").selectedIndex-- ;

} // end func
