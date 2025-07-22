<?php
  header('Access-Control-Allow-Methods: GET, POST');
  //header('Access-Control-Allow-Origin: *'); 
  ob_start();
  //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
  //https://www.cyfence.com/article/design-secured-api/
  
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);   
     $data = json_decode(file_get_contents('php://input'), true);
     if ($data) {
        if ($data['Mode'] == 'testClassTrade') { testClassTrade($data); return;}
        if ($data['Mode'] == 'createIndy') { createIndy($data); }
		
        return;
     }
  
function createIndy($data) { 
  

   require_once('api/phpCandlestickIndy.php');
   $clsStep1 = new TechnicalIndicators();   

   require_once('api/phpAdvanceIndy.php');
   $clsStep2 = new AdvancedIndicators();   



   $dataRaw = JSON_DECODE($data['dataRaw'],true);
   //$dataRaw = $data['dataRaw'];
   //echo JSON_ENCODE($dataRaw[0]);
   //return;
   $result = $clsStep1->calculateIndicators($dataRaw);
   $result2= $clsStep2->calculateAdvancedIndicators($result);
   

   for ($i=0;$i<=count($result2)-1;$i++) {

       $previousColor = null ;$previousColorBack2 = null;
	   $previousColorBack3 = null ;$previousColorBack4 = null; 

	   $previousTurnType = null ;$previousTurnTypeBack2 = null;
	   $previousTurnTypeBack3 = null ;$previousTurnTypeBack4 = null; 

       $macdconverValue = 0.0 ;
	   $MACDConvergence = '';

	   if ($i >= 1) {
		   $previousColor = $result2[$i-1]['thisColor'] ;
		   $previousTurnType = $result2[$i-1]['TurnType'] ;
		   $macdconverValue = $result2[$i]['MACDHeight'] - $result2[$i-1]['MACDHeight'];
		   if ($macdconverValue < 0) {
			   $MACDConvergence ='Conver';
		   }
		   if ($macdconverValue > 0) {
			   $MACDConvergence ='Diver';
		   }
		   if ($macdconverValue == 0) {
			   $MACDConvergence ='P';
		   }

	   }
	   if ($i >= 2) {
		   $previousColorBack2 = $result2[$i-2]['thisColor'] ;
		   $previousTurnTypeBack2 = $result2[$i-1]['TurnType'] ;
	   }
	   if ($i >= 3) {
		   $previousColorBack3 = $result2[$i-3]['thisColor'] ;
		   $previousTurnTypeBack3 = $result2[$i-1]['TurnType'] ;
	   }
	   if ($i >= 4) {
		   $previousColorBack4 = $result2[$i-4]['thisColor'] ;
		   $previousTurnTypeBack4 = $result2[$i-1]['TurnType'] ;
	   }

	   $result2[$i]['previousColor'] = $previousColor;
	   $result2[$i]['previousColorBack2'] = $previousColorBack2;
	   $result2[$i]['previousColorBack3'] = $previousColorBack3;
	   $result2[$i]['previousColorBack4'] = $previousColorBack4;

	   $result2[$i]['PreviousTurnType'] = $previousTurnType ; 
       $result2[$i]['PreviousTurnTypeBack2'] = $previousTurnTypeBack2 ; 
	   $result2[$i]['PreviousTurnTypeBack3'] = $previousTurnTypeBack3 ; 
	   $result2[$i]['PreviousTurnTypeBack4'] = $previousTurnTypeBack4 ; 

       $result2[$i]['macdconverValue'] = $macdconverValue ; 
	   $result2[$i]['MACDConvergence'] = $MACDConvergence ; 

      
   }

   testClassTradeByObject($result2[1]);
   


   require_once('newutil2.php');
   $config = '';
   // $inserter = new DatabaseInserter($config);
   $rawdata = JSON_ENCODE($result2);
   InsertCandle($rawdata) ;
   
  
  
	     
  
} // end function

function InsertCandle($jsonData) { 
/*
CREATE TABLE candle_data (
    -- Primary keys and IDs
    candleID BIGINT PRIMARY KEY,
    id VARCHAR(10),
    
    -- Time related fields
    timeframe VARCHAR(5),
    timestamp BIGINT,
    timefrom_unix DATETIME,
    
    -- Price data (using DECIMAL for precise financial calculations)
    high DECIMAL(10,2),
    low DECIMAL(10,2),
    open DECIMAL(10,2),
    close DECIMAL(10,2),
    pip DECIMAL(10,2),
    
    -- EMA and technical indicators
    ema3 DECIMAL(10,2),
    ema5 DECIMAL(10,2),
    
    -- Bollinger Bands
    bb_upper DECIMAL(10,2),
    bb_middle DECIMAL(10,2),
    bb_lower DECIMAL(10,2),
    
    -- Other technical indicators
    rsi DECIMAL(5,2),
    atr DECIMAL(5,2),
    macd DECIMAL(5,2),
    ema3Slope DECIMAL(5,2),
    ema5Slope DECIMAL(5,2),
    macdconverValue DECIMAL(5,2),
    
    -- Color and direction fields
    thisColor VARCHAR(10),
    color VARCHAR(10),
    ema3SlopeDirection VARCHAR(20),
    ema5SlopeDirection VARCHAR(20),
    emaAbove VARCHAR(20),
    emaCross VARCHAR(20),
    emaConflict BOOLEAN,
    ema3Position VARCHAR(20),
    
    -- Turn type fields
    TurnType VARCHAR(20),
    PreviousTurnType VARCHAR(20),
    PreviousTurnTypeBack2 VARCHAR(20),
    PreviousTurnTypeBack3 VARCHAR(20),
    PreviousTurnTypeBack4 VARCHAR(20),
    
    -- Previous color fields
    previousColor VARCHAR(10),
    previousColorBack2 VARCHAR(10),
    previousColorBack3 VARCHAR(10),
    previousColorBack4 VARCHAR(10),
    
    -- MACD convergence
    MACDConvergence VARCHAR(20),
    
    -- Timestamps for record management
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better query performance
    INDEX idx_timestamp (timestamp),
    INDEX idx_timeframe (timeframe),
    INDEX idx_timefrom_unix (timefrom_unix)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/

 
/*
// JSON data (ตัวอย่าง - แทนที่ด้วยข้อมูลจริงหรือการอ่านไฟล์)
$jsonData = '[
    {
        "candleID": 1739088540,
        "timeframe": "1m",
        "id": "51",
        "timestamp": "1739088540",
        "timefrom_unix": "2025-02-09T15:09:00+07:00",
        "high": 1649.39,
        "low": 1645.51,
        "open": 1649.39,
        "close": 1646.03,
        "thisColor": "Green",
        "pip": "388.00",
        "ema3": "1,647.87",
        "ema5": "1,648.32",
        "BB": {
            "upper": "1,651.15",
            "middle": "1,647.31",
            "lower": "1,643.47"
        },
        "rsi": "38.13",
        "atr": "2.68",
        "color": "red",
        "macd": "0.00",
        "ema3Slope": "0.00",
        "ema5Slope": "0.00",
        "ema3SlopeDirection": "parallel",
        "ema5SlopeDirection": "parallel",
        "emaAbove": "ema5Above",
        "emaCross": "none",
        "emaConflict": false,
        "ema3Position": "belowLow",
        "TurnType": "none",
        "previousColor": "Red",
        "previousColorBack2": "Green",
        "previousColorBack3": "Red",
        "previousColorBack4": "Green",
        "PreviousTurnType": "none",
        "PreviousTurnTypeBack2": "none",
        "PreviousTurnTypeBack3": "none",
        "PreviousTurnTypeBack4": "none",
        "macdconverValue": 0,
        "MACDConvergence": "Pararell"
    }
]';
*/
class DatabaseInserter {
    private $pdo;
    
    public function __construct() {
        try {			 
			$this->pdo = getPDONew();
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function insertData($data) {
        // แปลง string numbers ให้เป็นตัวเลข
        $cleanData = $this->cleanData($data);
        
        try {
            $sql = "REPLACE INTO candle_data_Analy (
                candleID, timeframe, id, timestamp, timefrom_unix,
                high, low, open, close, thisColor,
				pip,ema3, ema5, bb_upper, bb_middle,
				bb_lower, rsi, atr, color, MACDHeight, 
			ema3SlopeValue, ema5SlopeValue,ema3SlopeDirection,ema5SlopeDirection,emaAbove,
  CutPointType, emaConflict, ema3Position, TurnType,previousColor, previousColorBack2, previousColorBack3,previousColorBack4, PreviousTurnType, PreviousTurnTypeBack2,PreviousTurnTypeBack3,                
  PreviousTurnTypeBack4,macdconverValue, MACDConvergence
            ) VALUES (
                :candleID, :timeframe, :id, :timestamp, :timefrom_unix,
                :high, :low, :open, :close, :thisColor, 
				:pip,:ema3, :ema5,:bb_upper,:bb_middle,
				:bb_lower,:rsi,:atr,:color, :macd, 
				:ema3Slope, :ema5Slope,:ema3SlopeDirection, :ema5SlopeDirection, :emaAbove,
                :CutPointType, :emaConflict, :ema3Position, :TurnType,
                :previousColor, :previousColorBack2, :previousColorBack3,
                :previousColorBack4, :PreviousTurnType, :PreviousTurnTypeBack2,
                :PreviousTurnTypeBack3, :PreviousTurnTypeBack4,
                :macdconverValue, :MACDConvergence 
            )";
            
            $stmt = $this->pdo->prepare($sql);
			$bb_upper = null;
			if (isset($cleanData['BB']['upper'])) {			
			  $bb_upper = $cleanData['BB']['upper'];
            }
			$bb_middle = null;
			if (isset($cleanData['BB']['middle'])) {			
			  $bb_middle = $cleanData['BB']['middle'];
            }
			$bb_lower = null;
			if (isset($cleanData['BB']['lower'])) {			
			  $bb_lower = $cleanData['BB']['lower'];
            }


            // Bind parameters
            $params = [
                ':candleID' => $cleanData['candleID'],
                ':timeframe' => $cleanData['timeframe'],
                ':id' => $cleanData['id'],
                ':timestamp' => $cleanData['timestamp'],
                ':timefrom_unix' => $cleanData['timefrom_unix'],
                ':high' => $cleanData['high'],
                ':low' => $cleanData['low'],
                ':open' => $cleanData['open'],
                ':close' => $cleanData['close'],
                ':thisColor' => $cleanData['thisColor'],
                ':pip' => $this->parseNumber($cleanData['pip']),
                ':ema3' => $this->parseNumber($cleanData['ema3']),
                ':ema5' => $this->parseNumber($cleanData['ema5']),
				
                ':bb_upper' => $bb_upper,
                ':bb_middle' => $bb_middle,
                ':bb_lower' => $bb_lower,
				
                ':rsi' => $this->parseNumber($cleanData['rsi']),
                ':atr' => $this->parseNumber($cleanData['atr']),
                ':color' => $cleanData['thisColor'],
                ':macd' => $this->parseNumber($cleanData['MACDHeight']),
                ':ema3Slope' => $this->parseNumber($cleanData['ema3SlopeValue']),
                ':ema5Slope' => $this->parseNumber($cleanData['ema5SlopeValue']),
                ':ema3SlopeDirection' => $cleanData['ema3slopeDirection'],
                ':ema5SlopeDirection' => $cleanData['ema5slopeDirection'],
                ':emaAbove' => $cleanData['emaAbove'],
                ':CutPointType' => $cleanData['CutPointType'],
                ':emaConflict' => $cleanData['emaConflict'],
                ':ema3Position' => $cleanData['ema3Position'],
                ':TurnType' => $cleanData['TurnType'],
                ':previousColor' => $cleanData['previousColor'],
                ':previousColorBack2' => $cleanData['previousColorBack2'],
                ':previousColorBack3' => $cleanData['previousColorBack3'],
                ':previousColorBack4' => $cleanData['previousColorBack4'],
                ':PreviousTurnType' => $cleanData['PreviousTurnType'],
                ':PreviousTurnTypeBack2' => $cleanData['PreviousTurnTypeBack2'],
                ':PreviousTurnTypeBack3' => $cleanData['PreviousTurnTypeBack3'],
                ':PreviousTurnTypeBack4' => $cleanData['PreviousTurnTypeBack4'],
                ':macdconverValue' => $cleanData['macdconverValue'],
                ':MACDConvergence' => $cleanData['MACDConvergence']

            ];
            
            $stmt->execute($params);
			return true;
            
        } catch (PDOException $e) {
			echo "Error inserting data: " . $e->getMessage();
            error_log("Error inserting data: " . $e->getMessage());
            return false;
        }
    }
    
    private function cleanData($data) {
        // แปลง timestamp เป็น MySQL datetime format
        if (isset($data['timefrom_unix'])) {
            $data['timefrom_unix'] = date('Y-m-d H:i:s', strtotime($data['timefrom_unix']));
        }
        return $data;
    }
    
    private function parseNumber($value) {
        // ลบ comma และแปลงเป็นตัวเลข
        return floatval(str_replace(',', '', $value));
    }
}

// การใช้งาน
try {
    // แปลง JSON เป็น array
    $data = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }
    
    // สร้าง instance ของ DatabaseInserter
    $inserter = new DatabaseInserter();
    
    // วนลูปเพื่อ insert ข้อมูล
    foreach ($data as $record) {
        if ($inserter->insertData($record)) {
            //echo "Successfully inserted record with candleID: {$record['candleID']}\n";
        } else {
            echo "Failed to insert record with candleID: {$record['candleID']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

} // end function


function testClassTrade() { 
 

 require_once("newutil2.php");

 require_once("api/clsTrade_V2.php");
 $clsTrade = new clsTrade ;

 $pdo=  getPDONew();
 $tableName = 'candle_data_Analy';
 $macdThershold = 1 ; $lastMacdHeight = 0.1 ;
 $sql = "select * from $tableName  where candleID=?"; 
 $params= array(1739087220+60);
 $row= pdogetRowSet($sql,$params,$pdo);

 $json_array = json_decode(json_encode($row),true);
// echo $json_array['candleID'];
// return;
 


list($thisAction,$actionReason,$nextColor,$remark)= $clsTrade->getActionFromIDVer2($pdo,$json_array,$macdThershold) ;

echo $thisAction . ' :: '. $remark . ' :: ' . $actionReason . '-->' . $nextColor;


} // end function


function testClassTradeByObject($jsonData) { 
 
 echo gettype($jsonData) . '<hr>';; 


 require_once("api/clsTrade_V2.php");
 $clsTrade = new clsTrade ;
 $pdo=  getPDONew();


 $macdThershold = 1 ; $lastMacdHeight = 0.1 ;

// $json_array = $jsonData;
 $json_array = json_decode(json_encode($jsonData),true);
 

echo $json_array['ema3slopeDirection'] ;

// $json_array = json_decode(json_encode($row),true);


list($thisAction,$actionReason,$nextColor,$remark)= $clsTrade->getActionFromIDVer2($json_array,$macdThershold) ;

echo $thisAction . ' :: ' . $actionReason . '-->' . $nextColor;


} // end function

//***************************************




?>
