<!DOCTYPE html>
<html>
<head>
<style>
  .datetime-container {
    margin: 20px;
    font-family: Arial, sans-serif;
  }
  .time-input {
    padding: 5px;
    margin: 5px;
  }
  select {
    padding: 5px;
    margin: 2px;
  }
  .formatted-output {
    margin-top: 10px;
    font-size: 16px;
  }
</style>
</head>
<body>
<div class="datetime-container">
  <input type="date" id="dateInput" class="time-input">
  
  <select id="hourSelect"></select>
  <select id="minuteSelect"></select>
  <select id="secondSelect"></select>
  
  <div class="formatted-output">
    เวลาที่เลือก: <span id="output"></span>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const hourSelect = document.getElementById('hourSelect');
  const minuteSelect = document.getElementById('minuteSelect');
  const secondSelect = document.getElementById('secondSelect');
  const dateInput = document.getElementById('dateInput');
  const output = document.getElementById('output');

  // สร้างตัวเลือกชั่วโมง (00-23)
  for (let i = 0; i <= 23; i++) {
    const option = document.createElement('option');
    option.value = i;
    option.text = i.toString().padStart(2, '0');
    hourSelect.appendChild(option);
  }

  // สร้างตัวเลือกนาที (00-59)
  for (let i = 0; i <= 59; i++) {
    const option = document.createElement('option');
    option.value = i;
    option.text = i.toString().padStart(2, '0');
    minuteSelect.appendChild(option);
  }

  // สร้างตัวเลือกวินาที (00-59)
  for (let i = 0; i <= 59; i++) {
    const option = document.createElement('option');
    option.value = i;
    option.text = i.toString().padStart(2, '0');
    secondSelect.appendChild(option);
  }

  // ตั้งค่าเริ่มต้นเป็นเวลาปัจจุบัน
  const now = new Date();
  dateInput.valueAsDate = now;
  hourSelect.value = now.getHours();
  minuteSelect.value = now.getMinutes();
  secondSelect.value = now.getSeconds();

  // ฟังก์ชันแปลงวันที่เป็นรูปแบบ dd/mm/yyyy
  function formatDate(dateString) {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  }

  // ฟังก์ชันอัพเดทการแสดงผล
  function updateOutput() {
    const date = dateInput.value;
    const hour = hourSelect.value.toString().padStart(2, '0');
    const minute = minuteSelect.value.toString().padStart(2, '0');
    const second = secondSelect.value.toString().padStart(2, '0');
    
    if (date) {
      const formattedDate = formatDate(date);
      output.textContent = `${formattedDate} ${hour}:${minute}:${second}`;
    }
  }

  // เพิ่ม event listeners
  dateInput.addEventListener('change', updateOutput);
  hourSelect.addEventListener('change', updateOutput);
  minuteSelect.addEventListener('change', updateOutput);
  secondSelect.addEventListener('change', updateOutput);

  // แสดงค่าเริ่มต้น
  updateOutput();
});
</script>
</body>
</html>