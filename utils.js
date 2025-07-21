// utils.js

function greet(classname) {
	alert(classname.result);
    return `Hello, ${classname.result}!`;
}

function sayGoodbye(name) {
    return `Goodbye, ${name}!`;
}

const PI = 3.14159;

function add(a, b) {
    return a + b;
}

function subtract(a, b) {
    return a - b;
}

// ส่งออกทั้งหมดแบบ Named Export
export { greet, sayGoodbye, PI, add, subtract };