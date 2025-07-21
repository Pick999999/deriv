<button id="start">เริ่มป้องกันการสลีป</button>
<button id="stop">หยุดป้องกันการสลีป</button>
<p id="status">สถานะ: ไม่ได้เปิดใช้งาน</p>

<script>
let wakeLock = null;

// เริ่มป้องกันการสลีป
document.getElementById('start').addEventListener('click', async () => {
  try {
    wakeLock = await navigator.wakeLock.request('screen');
    document.getElementById('status').textContent = 'สถานะ: เปิดใช้งานแล้ว';
    
    // จัดการเมื่อเบราว์เซอร์ปล่อย wake lock อัตโนมัติ
    wakeLock.addEventListener('release', () => {
      document.getElementById('status').textContent = 'สถานะ: ถูกปล่อยโดยเบราว์เซอร์';
    });
    
    console.log('Screen Wake Lock เปิดใช้งานแล้ว');
  } catch (err) {
    document.getElementById('status').textContent = `สถานะ: เกิดข้อผิดพลาด - ${err.message}`;
  }
});

// หยุดป้องกันการสลีป
document.getElementById('stop').addEventListener('click', () => {
  if (wakeLock !== null) {
    wakeLock.release();
    wakeLock = null;
    document.getElementById('status').textContent = 'สถานะ: ปิดใช้งานแล้ว';
  }
});
</script>