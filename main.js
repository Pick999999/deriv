// file: main.js
import { DerivAPI } from '/deriv/class/DerivAPI.js';
const deriv = new DerivAPI('66726');

window.getAssets = async function() {
    try {
        await deriv.connect();
        deriv.getActiveAssets();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
}

window.getBalance = async function() {
    try {
        await deriv.connect();
        deriv.getBalance();
    } catch (error) {
        document.getElementById('result').innerText = 'Error: ' + error.message;
    }
}

window.placeTrade = async function() {
    try {
        // ล้าง error message เก่า
        document.getElementById('error-message').innerText = '';

        await deriv.connect();
        const symbol = document.getElementById('symbol').value;
        const type = document.getElementById('contractType').value;
        const duration = parseInt(document.getElementById('duration').value);
        const amount = parseFloat(document.getElementById('amount').value);

        // เพิ่มการตรวจสอบค่า
        if (!symbol || !type || isNaN(duration) || isNaN(amount)) {
            throw new Error('Please fill in all fields correctly');
        }

        if (amount <= 0) {
            throw new Error('Amount must be greater than 0');
        }

        if (duration <= 0) {
            throw new Error('Duration must be greater than 0');
        }

        deriv.buyContract(symbol, type, duration, amount);
    } catch (error) {
        document.getElementById('error-message').innerText = error.message;
        console.error('Trade Error:', error);
    }
}