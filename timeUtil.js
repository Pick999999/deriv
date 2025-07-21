/*
1.formatTimestampLong(timestampInSeconds) รับ timestamp แล้ว return DateTime Long String
2.
*/


function formatTimestampLong(timestampInSeconds) {

  // สร้าง Date object (JavaScript ใช้มิลลิวินาที ดังนั้นต้องคูณด้วย 1000)
  const date = new Date(timestampInSeconds * 1000);
  const now = new Date();

  // สำหรับ GMT
  const daysEng = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  const monthsEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

  // สำหรับภาษาไทย
  const daysThai = ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์"];
  const monthsThai = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];

  // ฟอร์แมต GMT
  const gmtDayOfWeek = daysEng[date.getUTCDay()];
  const gmtDay = date.getUTCDate();
  const gmtMonth = monthsEng[date.getUTCMonth()];
  const gmtYear = date.getUTCFullYear();
  const gmtHours = String(date.getUTCHours()).padStart(2, '0');
  const gmtMinutes = String(date.getUTCMinutes()).padStart(2, '0');
  const gmtSeconds = String(date.getUTCSeconds()).padStart(2, '0');

  // ฟอร์แมตเวลาท้องถิ่น
  const localDayOfWeek = daysThai[date.getDay()];
  const localDay = date.getDate();
  const localMonth = monthsThai[date.getMonth()];
  const localYear = date.getFullYear();
  const localHours = String(date.getHours()).padStart(2, '0');
  const localMinutes = String(date.getMinutes()).padStart(2, '0');
  const localSeconds = String(date.getSeconds()).padStart(2, '0');

  // คำนวณ timezone offset
  const tzOffset = date.getTimezoneOffset();
  const tzHours = Math.abs(Math.floor(tzOffset / 60));
  const tzMinutes = Math.abs(tzOffset % 60);
  const tzSign = tzOffset <= 0 ? '+' : '-';

  // คำนวณเวลาเชิงเปรียบเทียบ (Relative time)
  const diffSeconds = Math.floor((now - date) / 1000);

  let relativeTime;
  if (diffSeconds < 60) {
    relativeTime = `${diffSeconds} seconds ago`;
  } else if (diffSeconds < 3600) {
    const minutes = Math.floor(diffSeconds / 60);
    relativeTime = `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
  } else if (diffSeconds < 86400) {
    const hours = Math.floor(diffSeconds / 3600);
    relativeTime = `${hours} hour${hours > 1 ? 's' : ''} ago`;
  } else {
    const days = Math.floor(diffSeconds / 86400);
    relativeTime = `${days} day${days > 1 ? 's' : ''} ago`;
  }

  // สร้างผลลัพธ์
  stLong =  `Assuming that this timestamp is in **seconds**:**GMT**: วัน${gmtDayOfWeek}ที่ ${gmtDay} ${gmtMonth} ${gmtYear} เวลา ${gmtHours}:${gmtMinutes}:${gmtSeconds}
**Your time zone**: วัน${localDayOfWeek}ที่ ${localDay} ${localMonth} ${localYear} เวลา ${localHours}:${localMinutes}:${localSeconds} GMT${tzSign}${String(tzHours).padStart(2, '0')}:${String(tzMinutes).padStart(2, '0')}
**Relative**: ${relativeTime}`;

// สร้างผลลัพธ์
  stLocale =  `**${localDayOfWeek}ที่ ${localDay} ${localMonth} ${localYear}
    เวลา ${localHours}:${localMinutes}:${localSeconds}
  **Relative**: ${relativeTime}`;

  stHour =  `${localHours}:${localMinutes}:${localSeconds}`;

  sObj = {
    timestamp : timestampInSeconds,
    stGMT : stLong ,
    stLocale : stLocale ,
    stHour : stHour ,
    relative : relativeTime
  }
  return sObj ;

}

function timestampToHHMM(timestamp) {
    // สร้างออบเจกต์ Date จาก timestamp (ถ้า timestamp เป็นวินาที ให้คูณด้วย 1000 เพื่อแปลงเป็นมิลลิวินาที)
    const date = new Date(timestamp * 1000);

    // ดึงชั่วโมงและนาที
    const hours = date.getHours();
    const minutes = date.getMinutes();

    // เติมศูนย์ข้างหน้าหากชั่วโมงหรือนาทีน้อยกว่า 10
    const formattedHours = hours < 10 ? `0${hours}` : hours;
    const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;

    // รวมเป็นรูปแบบ hh:mm
    return `${formattedHours}:${formattedMinutes}`;
}

function UseDateObject() {
/*
const now = new Date(); // สร้าง Date object ของวันและเวลาปัจจุบัน
const date = new Date(1712509200000); // มิลลิวินาทีนับจาก 1 มกราคม 1970 00:00:00 UTC
const date1 = new Date('2025-04-07T12:00:00Z'); // รูปแบบ ISO 8601
const date2 = new Date('April 7, 2025 12:00:00'); // รูปแบบวันที่แบบ US
const date3 = new Date('04/07/2025 12:00:00'); // รูปแบบ MM/DD/YYYY
const date4 = new Date('2025/04/07 12:00:00'); // รูปแบบ YYYY/MM/DD

// รูปแบบ: new Date(year, monthIndex, day, hours, minutes, seconds, milliseconds)
// หมายเหตุ: monthIndex เริ่มจาก 0 (มกราคม) ถึง 11 (ธันวาคม)

const date1 = new Date(2025, 3, 7); // 7 เมษายน 2025 00:00:00 (เดือนเริ่มจาก 0)
const date2 = new Date(2025, 3, 7, 12, 30, 15, 500); // 7 เมษายน 2025 12:30:15.500

*/


} // end func

