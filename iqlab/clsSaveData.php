<?php
//  clsSaveData.php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class cls_SaveData {


function __construct() { 

	$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	require_once('newutil2.php'); 
	$dbname = 'thepaper_lab' ;
	$pdo = getPDO2($dbname,false)  ;


} // end __construct

function CreateTable($data) { 

	$dbname = 'ddhousin_lab' ;
	$pdo = getPDO2($dbname,true)  ;
	
	

// อ่าน JSON ไฟล์
$jsonData = file_get_contents('data.json');
$data = json_decode($jsonData, true); // แปลง JSON เป็น PHP Array

// กำหนดชื่อ Table
$tableName = "json_data";
//$overwrite = readline("Do you want to overwrite the table? (y/n): ");
$overwrite = 'y';

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "ddhousin";
$password = 'y4e2Q44rBw' ;
$dbname = "ddhousin_lab";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่า Table มีอยู่หรือไม่
$checkTable = $conn->query("SHOW TABLES LIKE '$tableName'");

if ($checkTable->num_rows > 0) {
    // ถ้า table มีอยู่แล้ว
    echo "Table '$tableName' already exists.\n";

    // ให้ตัวเลือกว่าจะ overwrite หรือไม่
    //$overwrite = readline("Do you want to overwrite the table? (y/n): ");
    if ($overwrite == 'y') {
        // ลบ table เดิม
        $conn->query("DROP TABLE $tableName");
        echo "Table '$tableName' has been overwritten.\n";
    } else {
        echo "Table '$tableName' has not been changed.\n";
        exit();
    }
}

// เริ่มสร้างคำสั่ง SQL สร้าง table ใหม่ พร้อมกำหนด charset และ comment
$sql = "CREATE TABLE $tableName (";

// ตรวจสอบข้อมูลใน json และสร้างฟิลด์ตามข้อมูล
foreach ($data[0] as $key => $value) {
    // ระบุประเภทข้อมูลตามชนิดข้อมูลใน JSON
    if (is_int($value)) {
        $sql .= "$key INT, ";
    } elseif (is_float($value)) {
        $sql .= "$key FLOAT, ";
    } elseif (is_bool($value)) {
        $sql .= "$key BOOLEAN, ";
    } elseif (preg_match("/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/", $value)) {
        // ตรวจสอบว่าเป็น DATETIME
        $sql .= "$key DATETIME, ";
    } elseif (strlen($value) > 255) {
        // ถ้าข้อความยาวเกิน 255 ตัวอักษร จะใช้ BLOB
        $sql .= "$key BLOB, ";
    } else {
        // ถ้าเป็นข้อความทั่วไปให้ใช้ VARCHAR
        $sql .= "$key VARCHAR(255), ";
    }
}

// ลบคอมม่าอันสุดท้ายออกและปิดคำสั่ง SQL พร้อมกำหนด charset และ comment
$sql = rtrim($sql, ", ") . ") 
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci 
    COMMENT = 'This table is created from JSON data'";

// สร้าง table
if ($conn->query($sql) === TRUE) {
    echo "Table '$tableName' created successfully with utf8mb4 encoding and comment.\n";
} else {
    echo "Error creating table: " . $conn->error;
}

// ปิดการเชื่อมต่อ
$conn->close();



} // end function


function genScriptMACD($tablename,$data,$id,$curpairID) { 

$sql = 'replace INTO macd(id, curpairID, ema3, ema5, ema9, macd, signalLine,histogram,slope3Degrees, 
slope3Value, slopeDirection3, slope5Value, slopeDirection5, slopeDirection9, emaAbove, emaConflict, ConvergenceValue, ConvergenceType, TurnPointType, IdOfLastTurnPoint, TurnPointNo, CutPointType, IdOfLastCutPoint) VALUES('; 

if (isset($data['analysis']['IdOfLastCutPoint'])) {
   $IdOfLastCutPoint = $data['analysis']['IdOfLastCutPoint']  ;
} else {
   $IdOfLastCutPoint = '' ;
}
if (isset($data['analysis']['slope5Value'])) {
   $slope5Value = $data['analysis']['slope5Value']  ;
} else {
   $slope5Value = '' ;
}

if (isset($data['analysis']['ConvergenceValue'])) {
   $ConvergenceValue = $data['analysis']['ConvergenceValue']  ;
} else {
   $ConvergenceValue = 0 ;
}

$params = array($id,$curpairID,
$data['ema3'], $data['ema5'], $data['ema9'], $data['macd'], $data['signalLine'],
$data['histogram'],$data['analysis']['slope3Degrees']*1000*10,
$data['analysis']['slope3Value'], $data['analysis']['slopeDirection3'], 
$slope5Value, 
$data['analysis']['slopeDirection5'],
$data['analysis']['slopeDirection9'],
$data['analysis']['emaAbove'], 
$data['analysis']['emaConflict'], 
$ConvergenceValue, 
$data['analysis']['ConvergenceType'], 
$data['analysis']['TurnPointType'], 
$data['analysis']['IdOfLastTurnPoint'], 
$data['analysis']['TurnPointNo'], 
$data['analysis']['CutPointType'], 
$IdOfLastCutPoint
);

$valueClause = ' "'  ;
for ($i=0;$i<=count($params)-1;$i++) {
	$valueClause .=  $params[$i].'","';   
}
$valueClause = substr($valueClause,0,strlen($valueClause)-2) . ')';

$sql .= $valueClause;
//echo count($params) . ' ****** ';
//echo "$sql";
/*$myfile = fopen("newfile.json", "w") or die("Unable to open file!");

fwrite($myfile, $sql);
fclose($myfile);
*/
return $sql;

} // end function




function UniversalSave2($tablename,$data,$id,$curpairID) { 

//echo $tablename . ' ,  ' ;
if ($tablename=='macd') {
	$sqlmacd = $this->genScriptMACD($tablename,$data,$id,$curpairID);
	
	return $sqlmacd;

}

if ($tablename == 'AnalyEMA' ) {    
	$tablename = 'AnalyEMA_HistoryForLab'  ;
	$insertClause = "REPLACE INTO " . $tablename . ' (curpairID,'; 
    $valueClause = ') VALUES('. $curpairID .',' ;
	//echo '--->' . $insertClause ;
} else {
	if ($tablename != 'RawData' ) {    
	   $insertClause = "REPLACE INTO " . $tablename . ' (id,curpairID,'; 
	   $valueClause = ') VALUES('. $id. ','. $curpairID .',' ;
	} else {
	   $insertClause = "REPLACE INTO " . $tablename . ' (curpairID,'; 
	   $valueClause = ') VALUES('. $curpairID .',' ;
	}
}

// วนลูปอ่านค่า key และ value
$fieldName = [] ; $fieldValue = [] ;
foreach ($data as $key => $value) {   
    if ($key  == 'from'  ) { $key = 'timefrom' ; }    
	if ($key == 'to') { $key = 'timeto' ; }
	//if ($tablename=='AnalyEMA' && $key!='id') {
	 
		$fieldName[] = $key ; 	
		$fieldValue[]  = trim($value) ;
		$insertClause .= $key . ',';
		$type = gettype($value) ;
		if ($type== 'string') $value= '"' . $value . '"' ;	
		$valueClause .= $value . ','; 
	//}

   
}
$insertClause = substr($insertClause,0,strlen($insertClause)-1) ;
$valueClause = substr($valueClause,0,strlen($valueClause)-1) . ')' ;

$sql = $insertClause . $valueClause ; 


$myfile = fopen("newfile777.txt", "w") or die("Unable to open file!");

fwrite($myfile, $sql);
fclose($myfile);


return $sql  ;

} // end function


function UniversalSave($tablename,$data) { 

//  $data = json_decode($data);
  $insertClause = "REPLACE INTO " . $tablename . ' ('; 
  $valueClause = ') VALUES(' ;


// วนลูปอ่านค่า key และ value
$data = $data[1] ;
$fieldName = [] ; $fieldValue = [] ;

foreach ($data as $key => $value) {   

	$fieldName[] = $key ; 	
	$fieldValue[]  = $value ;
	$insertClause .= $key . ',';
	
	$value= '"' . $value . '"' ;	
	$valueClause .= $value . ','; 
   
}
$insertClause = substr($insertClause,0,strlen($insertClause)-1) ;
$valueClause = substr($valueClause,0,strlen($valueClause)-1) . ')' ;

$sql = $insertClause . $valueClause ; 
echo $sql;




return $sql  ;

} // end function




} // end class

// Access denied. Option -c requires administrative privileges.

?>