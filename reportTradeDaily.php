
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>



<script>
function getBalance() {



	const ws = new WebSocket('wss://ws.derivws.com/websockets/v3?app_id=66726');
	ws.onopen = () => {
		// Authorize
		ws.send(JSON.stringify({authorize: 'lt5UMO6bNvmZQaR'}));
		
		// Request daily trades
		ws.send(JSON.stringify({
			profit_table: 1,
			description: 1,
			date_from: Math.floor(new Date().setHours(0,0,0,0) / 1000),
			date_to: Math.floor(new Date().setHours(23,59,59,999) / 1000)
		}));
	};

	ws.onmessage = (event) => {
		const data = JSON.parse(event.data);
		console.log(event.data)
		
		if (data.profit_table) {
			console.log('Trades:', data.profit_table.transactions);
		}
	};

}
</script>

<script>
$(document).ready(function () {
  console.log("Hello World!");
  getBalance();
});

</script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>



?>