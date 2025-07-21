function authenticateUser() {

   const authRequest = {
      authorize: 'lt5UMO6bNvmZQaR',
      req_id: 1 // Request ID เพื่อติดตามการตอบกลับ
   };

   websocket.send(JSON.stringify(authRequest));

} // end func
