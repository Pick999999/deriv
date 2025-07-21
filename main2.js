// file: main.js
import { DerivAPI } from '/deriv/class/DerivAPI2.js';
const deriv = new DerivAPI('66726');

window.getAssets = async function() {
    try {
        await deriv.connect();
        deriv.getActiveAssets();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
}

window.requestActiveSymbols = async function() {
    try {
        await deriv.connect();
        deriv.requestActiveSymbols();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
}

window.placeOrder = async function(signal) {

    
    try {
        await deriv.connect();
        deriv.OnTrade(signal);
		//document.getElementById("resultSuggestTrade").innerHTML = 'Wait Next';
		$("#resultSuggestTrade").addClass('gray');
		//deriv.executeTrade();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
	$("#contact-tab4").click();
	deriv.requestOpenContracts();
}

window.placeOrderTwin = async function(signal) {

    try {
        await deriv.connect();
        deriv.OnTradeTwin(signal);
		//document.getElementById("resultSuggestTrade").innerHTML = 'Wait Next';
		$("#resultSuggestTrade").addClass('gray');
		//deriv.executeTrade();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
	$("#contact-tab4").click();
	deriv.requestOpenContracts();
}



window.getProposal = async function(signal) {

    try {
        await deriv.connect();
        //deriv.OnTrade(signal);
		deriv.getProposal();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
}




window.buyContract9999 = async function() {

    document.getElementById('result').innerText += 'Start Buy :: ';
    try {
        await deriv.connect();
        //deriv.OnTrade(signal);
		document.getElementById('result').innerHTML += ' <br>Authen Success<br> ';



		//symbol = "frxEURUSD";
		let symbol = "R_10";
		let contractType = 'CALL';
		let duration = 15;
		let amount = 10 ;
        document.getElementById('result').innerHTML += ' <br>CALL BUY<br> ';
		//deriv.buyContract(symbol, contractType, duration, amount) ;
		deriv.OnTrade('CALL');
    } catch (error) {
        document.getElementById('result').innerHTML += 'Error: ' + error.message;
    }
}

//getPortfolio()
window.getPortfolio = async function(signal) {


    try {
        await deriv.connect();
		deriv.requestOpenContracts();
    } catch (error) {
        document.getElementById('resultPortfolio').innerText = 'Error: ' + error.message;
    }
}

window.InitialDeriv = async function(signal) {

    try {

		deriv.InitialData();
    } catch (error) {
        document.getElementById('resultPortfolio').innerText = 'Error: ' + error.message;
    }
}



$(document).ready(function () {
   //$('#btnConnect').click();
   getAssets();
});





