<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ตั้งค่าการเชื่อมต่อ MySQL
$host = 'localhost';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';
                
$newUtilPath = 'domains/thepapers.in/private_html/deriv/';
$newUtilPath = 'domains/thepapers.in/private_html/deriv/';
require_once("newutil2.php"); 


try {
	/*
    // เชื่อมต่อฐานข้อมูล
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	*/
	
	$dbname = 'thepaper_lab' ;
	$pdo = getPDONew($dbname)  ;
	

    // ชื่อตารางที่ต้องการอ่านโครงสร้าง
    $tableName = 'AnalyEMATmp';

    // ดึงโครงสร้างตาราง
    $stmt = $pdo->query("DESCRIBE $tableName");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แปลงโครงสร้างเป็น Standard Object
    $tableStructure = [];
	$st = '{<br>';
    foreach ($columns as $column) {
        $columnName = $column['Field'];
        /*$tableStructure[$columnName] = (object)[
            'type' => $column['Type'],
            'null' => $column['Null'] === 'YES',
            'key' => $column['Key'],
            'default' => $column['Default'],
            'extra' => $column['Extra']
        ];
		
		$obj = new stdClass();
		$obj->name = $columnName ;
		$obj->Value = $column['Default'] ;
		$tableStructure[] = $obj;
		*/
        $Default= ($column['Default'] != null) ? '"'. $column['Default'] .'"' : '"-"';
       
        
		$st .= '"'. $columnName . '" :  ' . $Default .',' . '<br>' ; 

    } 
	$st = substr($st,0,strlen($st)-5) . '<br>}'; 
	echo $st ;


	//echo json_encode($tableStructure, JSON_PRETTY_PRINT);
	//echo "<hr>" . $tableStructure[3]->name . '=' . $tableStructure[3]->Value  ;

	return ;
	

    // แสดงผลลัพธ์เป็น Standard Object
    //echo "<pre>";
    //print_r($tableStructure);
    //echo "</pre>";

    // แปลงเป็น JavaScript Object
    $jsObject = json_encode($tableStructure, JSON_PRETTY_PRINT);
    echo "<script>const tableStructure = $jsObject;</script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
function getNewObject() { 

$st = '{
    "candleID": "-",
    "curpairID": "-",
    "timeframe": "-",
    "id": "0",
    "timestamp": "0",
    "timefrom": "0",
    "timefrom_unix": "current_timestamp()",
    "minuteno": "-",
    "previousPIP": "0",
    "pip": "-",
    "pip2": "-",
    "pipGrowth": "-",
    "code": "-",
    "previousEMA3": "-",
    "ema3": "-",
    "ema5": "0.00000000",
    "differEMA": "-",
    "ema3SlopeValue": "-",
    "ema5SlopeValue": "-",
    "PreviousSlopeDirection": "N",
    "ema3slopeDirection": "-",
    "ema5slopeDirection": "-",
    "MACDHeight": "-",
    "MACDHeightCode": "N",
    "MACDConvergence": "-",
    "emaAbove": "-",
    "emaConflict": "N",
    "previousColorBack4": "-",
    "previousColorBack3": "-",
    "previousColorBack2": "-",
    "previousColor": "-",
    "thisColor": "-",
    "nextColor": "-",
    "CutPointType": "N",
    "TurnType": "-",
    "PreviousTurnType": "-",
    "PreviousTurnTypeBack2": "N",
    "PreviousTurnTypeBack3": "N",
    "PreviousTurnTypeBack4": "N",
    "bodyShape": "-",
    "resultColor": "-",
    "ADX": "-",
    "ADXShort": "-",
    "cci": "-",
    "Score": "-",
    "actionReason": "-"
}';

return JSON_DECODE($st);


} // end function

[
	{
    "close": 1660.18,
    "epoch": 1739071140,
    "high": 1663.88,
    "low": 1659.78,
    "open": 1663.88
},
{
    "close": 1657.04,
    "epoch": 1739071200,
    "high": 1659.47,
    "low": 1657.04,
    "open": 1659.47
}
]

จาก array ของ  candlestick ข้างต้น จงหา  ema3,ema5,bollinger bands,rsi,atr 
แล้วนำมาสร้าง  object ใหม่  ตามค่า
{
    "candleID": 1,    
    "timeframe": "1m",
    "id": "0",
    "timestamp": "",    
    "timefrom_unix": "",         
    "pip": "",     
    "ema3": 0,
    "ema5": 0,
    "BB" : 0 ,
    "rsi" : 0  
}

ด้วย pure javascript 

const candlesticks = [
    {
        close: 1660.18,
        epoch: 1739071140,
        high: 1663.88,
        low: 1659.78,
        open: 1663.88
    },
    {
        close: 1657.04,
        epoch: 1739071200,
        high: 1659.47,
        low: 1657.04,
        open: 1659.47
    }
];


// Calculate indicators
const ema3 = calculateEMA(candlesticks, 3);
const ema5 = calculateEMA(candlesticks, 5);
const bollingerBands = calculateBollingerBands(candlesticks, 5);
const rsi = calculateRSI(candlesticks, 14);
const atr = calculateATR(candlesticks, 14);

// Create new object
const newObject = {
    candleID: 1,
    timeframe: "1m",
    id: "0",
    timestamp: candlesticks[1].epoch,
    timefrom_unix: new Date(candlesticks[1].epoch * 1000).toISOString(),
    pip: (candlesticks[1].close - candlesticks[0].close).toFixed(2),
    ema3: ema3.toFixed(2),
    ema5: ema5.toFixed(2),
    BB: bollingerBands.middle.toFixed(2),
    rsi: rsi.toFixed(2)
};

console.log(newObject);
?>