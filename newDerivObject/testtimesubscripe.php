<!-- testtimesubscripe.php -->
<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="Generator" content="EditPlus®">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
  <title>Document</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" > 

  <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tempus Dominus CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    
    <!-- Font Awesome for calendar icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
<style>
table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            cursor: pointer;
        }
        th {
            background-color: #f4f4f4;
        }
        .highlight {
            background-color: yellow;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
 
.sarabun-regular {
  font-family: "Sarabun", sans-serif;
  font-weight: 400;
  font-style: normal;
}

</style>



 </head>
 <body class='sarabun-regular'>
  



<div class="container mt-5">
        
        <div class="row">
            <div class="col-md-6">
                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker" placeholder="เลือกวันที่"/>
                    <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


  
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

 <script>

 
// สร้าง WebSocket connection
let ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=1126');

// ตัวแปรสำหรับเก็บข้อมูลเวลาล่าสุด
let lastTimeUpdate = null;
let serverTimeOffset = 0;
let isConnected = false;
let timeoutId = null;

// สร้างฟังก์ชันเพื่อตรวจสอบการเชื่อมต่อ
function checkConnection() {
  if (ws.readyState === WebSocket.OPEN) {
    return true;
  } else {
    return false;
  }
}

// ฟังก์ชันสำหรับ time subscription
function subscribeToTime() {
  if (checkConnection()) {
    ws.send(JSON.stringify({
      time: 1,
      /*subscribe: 1*/
    }));
    console.log("ทำการสมัครรับข้อมูลเวลาแล้ว");
  } else {
    console.error("ไม่สามารถ subscribe เวลาได้ - ยังไม่ได้เชื่อมต่อ");
  }
}

// ฟังก์ชันสำหรับยกเลิก time subscription
function unsubscribeFromTime() {
  if (checkConnection()) {
    ws.send(JSON.stringify({
      time: 1,
      subscribe: 0
    }));
    console.log("ยกเลิกการสมัครรับข้อมูลเวลาแล้ว");
  }
}

// ฟังก์ชันสำหรับคำนวณความต่างของเวลา
function updateTimeOffset(serverTime) {
  const localTime = new Date();
  serverTimeOffset = serverTime - localTime;
  console.log(`ความต่างเวลา: ${serverTimeOffset} มิลลิวินาที`);
}

// ฟังก์ชันคำนวณเวลาเซิร์ฟเวอร์ปัจจุบัน
function getCurrentServerTime() {
  return new Date(Date.now() + serverTimeOffset);
}

// จัดการกับเหตุการณ์เมื่อเชื่อมต่อสำเร็จ
ws.onopen = function() {
  console.log("เชื่อมต่อกับ WebSocket สำเร็จ");
  isConnected = true;
  
  // เริ่มต้น subscribe ทันทีที่เชื่อมต่อสำเร็จ
  subscribeToTime();
  
  // เริ่มระบบตรวจสอบการเชื่อมต่อ
  startConnectionCheck();
};

// จัดการกับข้อความที่ได้รับ
ws.onmessage = function(event) {
  try {
    const data = JSON.parse(event.data);
     console.log('data2',data)
    
    // ตรวจสอบว่าเป็นข้อมูลเวลาหรือไม่
    if (data.msg_type === 'time') {
      lastTimeUpdate = Date.now();

      const serverTime = new Date(data.time * 1000);
      
      // อัพเดทความต่างของเวลา
      updateTimeOffset(serverTime);
      
      console.log(`เวลาเซิร์ฟเวอร์: ${serverTime.toLocaleTimeString()}`);
    }
  } catch (error) {
    console.error("เกิดข้อผิดพลาดในการแปลงข้อความ:", error);
  }
};

// จัดการกับการปิดการเชื่อมต่อ
ws.onclose = function(event) {
  isConnected = false;
  console.log(`WebSocket ถูกปิด: รหัส ${event.code}, เหตุผล: ${event.reason}`);
  
  // หยุดการตรวจสอบการเชื่อมต่อ
  stopConnectionCheck();
  
  // พยายามเชื่อมต่อใหม่หลังจาก 5 วินาที
  setTimeout(reconnect, 5000);
};

// จัดการกับข้อผิดพลาด
ws.onerror = function(error) {
  console.error("เกิดข้อผิดพลาดใน WebSocket:", error);
};

// ฟังก์ชันเชื่อมต่อใหม่
function reconnect() {
  console.log("กำลังพยายามเชื่อมต่อใหม่...");
  
  // ปิดการเชื่อมต่อเดิมถ้ายังไม่ถูกปิด
  if (ws && ws.readyState !== WebSocket.CLOSED) {
    ws.close();
  }
  
  // สร้างการเชื่อมต่อใหม่
  ws = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=66726');
  
  // ตั้งค่า event handlers ใหม่
  ws.onopen = function() {
    console.log("เชื่อมต่อใหม่สำเร็จ");
    isConnected = true;
    subscribeToTime();
    startConnectionCheck();
  };
  
  // ตั้งค่า onmessage, onclose, onerror ใหม่เหมือนด้านบน
  ws.onmessage = function(event) {
    try {
      const data = JSON.parse(event.data);
      
      
      if (data.msg_type === 'time') {
        lastTimeUpdate = Date.now();
		// แสดงข้อมูลดิบที่ได้รับเพื่อตรวจสอบ
        console.log("ข้อมูลเวลาที่ได้รับ:", data);


        const serverTime = new Date(data.time * 1000);
        updateTimeOffset(serverTime);
        console.log(`เวลาเซิร์ฟเวอร์: ${serverTime.toLocaleTimeString()}`);
      }
    } catch (error) {
      console.error("เกิดข้อผิดพลาดในการแปลงข้อความ:", error);
    }
  };
  
  ws.onclose = function(event) {
    isConnected = false;
    console.log(`การเชื่อมต่อใหม่ถูกปิด: รหัส ${event.code}, เหตุผล: ${event.reason}`);
    stopConnectionCheck();
    setTimeout(reconnect, 5000);
  };
  
  ws.onerror = function(error) {
    console.error("เกิดข้อผิดพลาดในการเชื่อมต่อใหม่:", error);
  };
}

// ฟังก์ชันตรวจสอบการเชื่อมต่อเป็นระยะ
function startConnectionCheck() {
  const CONNECTION_TIMEOUT = 10000; // 10 วินาที
  
  // หยุดตัวตรวจสอบเดิมถ้ามี
  if (timeoutId) clearInterval(timeoutId);
  
  timeoutId = setInterval(() => {
    if (lastTimeUpdate) {
      const timeSinceLastUpdate = Date.now() - lastTimeUpdate;
      
      if (timeSinceLastUpdate > CONNECTION_TIMEOUT) {
        console.warn("ไม่ได้รับข้อมูลเวลาเกิน 10 วินาที - อาจขาดการเชื่อมต่อ");
        
        // ทดสอบการเชื่อมต่อด้วยการส่ง ping
        if (ws.readyState === WebSocket.OPEN) {
          ws.send(JSON.stringify({ ping: 1 }));
        } else {
          // หากไม่ได้เชื่อมต่อ ให้พยายามเชื่อมต่อใหม่
          reconnect();
        }
      }
    }
  }, CONNECTION_TIMEOUT); // ตรวจสอบทุก 5 วินาที
}

// ฟังก์ชันหยุดการตรวจสอบการเชื่อมต่อ
function stopConnectionCheck() {
  if (timeoutId) {
    clearInterval(timeoutId);
    timeoutId = null;
  }
}

 </script>
  

    

     



  


 </body>
</html>
