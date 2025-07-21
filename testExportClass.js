// utils.js
import { greet, sayGoodbye, PI, add, subtract } from './utils.js';
// สร้าง class
class Calculator {
    constructor() {
        this.result = 0;
		//setA(this);
		greet(this);
    }

    add(a, b) {
        this.result = a + b;
        return this.result;
    }

    subtract(a, b) {
        this.result = a - b;
        return this.result;
    }

    multiply(a, b) {
        this.result = a * b;
        return this.result;
    }

    divide(a, b) {
        if (b === 0) {
            throw new Error("Cannot divide by zero");
        }
        this.result = a / b;
        return this.result;
    }
}


function setA(classa) {

	     alert(classa.result);


} // end func


// ส่งออก class
export default Calculator;