const requestTime = {
      time: 1
}
const authRequest = {
      authorize: 'lt5UMO6bNvmZQaR',
      req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
};
const requestCandle_latest = {
      ticks_history: asset,
      style: "candles",
      granularity: timeframe * 60,
      count: 60,
      end : "latest"
};
const candlesSubscription = {
      ticks_history : "R_100",
	  style : "candles",
      adjust_start_time: 1,
      count: 60,
      end  : "latest",
      start: 1,
      granularity: 60,
      subscribe: 1              // ระบุว่าต้องการสมัครสมาชิกเพื่อรับข้อมูลแบบเรียลไทม์
};

// ปรับพารามิเตอร์สำหรับการเทรดให้เหมาะสม
const requestBuy = {
      buy: 1,
      price: parseFloat(amount),
      parameters: {
         amount: parseFloat(amount),
         basis: "stake",
         contract_type: contractType, // PUT,CALL
         currency: "USD",
         duration: parseInt(duration), // 1,2...
         duration_unit: "m",
         symbol: symbol
      }
}; // ถ้าซื้อสำเร็จ จะได้  response.buy คือข้อมูลสัญญา เบื้องต้นออกมาจากนั้นส่งคำขอ Track สัญญา


const requestTrackTrade = {
      proposal_open_contract: 1,
      contract_id: contractId,
      subscribe: 1 // ขอ subscribe ข้อมูลเพื่อติดตามการเปลี่ยนแปลง
}; //ทำการ Track สัญญา และรอ  response คือ data.proposal_open_contractและรอ ขาย (Sale)สัญญาเมื่อมีกำไร
// ซึ่งตรวจสอบ สถานะของ สัญญา ด้วย data.proposal_open_contract.is_sold

const requestSaleContract {
      sell: contractId,
      price: 0 // ขายด้วยราคาตลาดปัจจุบัน
}//  ทำการขายสัญญา ซึ่งถ้าขายสำเร็จ ตรวจสอบด้วย  data.proposal_open_contract.is_sold

