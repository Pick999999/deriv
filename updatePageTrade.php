<?php
//updatePageTrade.php  
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("newutil2.php");
$pdo = getPDONew()  ;


$assetCode   = $_GET["assetCode"];
$isOpenTrade = $_GET["isOpenTrade"];
//$isOpenTrade = "Y";

$sql = 'select moneyTrade from pageTradeStatus '; 
$params = array();		
$moneyTrade  =pdogetValue($sql,$params,$pdo) ;

$sql = 'select targetTrade from pageTradeStatus '; 
$params = array();		
$targetTrade  =pdogetValue($sql,$params,$pdo) ;




//$moneyTrade = 1 ; //$_GET["moneyTrade"];
//$targetTrade = 0.5 ; //$_GET["targetTrade"];

	$sql = "UPDATE pageTradeStatus SET assetCode=?,isopenTrade=?,moneyTrade=?,targetTrade=?
	" ; // WHERE assetCode=?"; 
	//$dbname = 'ddhousin_lab' ;
	
	$params = array($assetCode,$isOpenTrade,$moneyTrade,$targetTrade);
	if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
		  echo 'Error' ;
		  return false;
	} 

	$sql = "select * from pageTradeStatus"; 		
	$params = array();
	$row = pdoRowSet($sql,$params,$pdo) ;
	echo  $row['assetCode'] . '|'. $row['isopenTrade'].'|'. $row['moneyTrade'] .'|'. $row['targetTrade'];
	
	

	


return;  

// ตัวอย่างการส่ง response กลับไป (เช่น JSON)
header('Content-Type: application/json');
$response = array(
    'status' => 'success',
    'message' => 'Parameters received successfully!',
    'received_params' => $_GET
);

$response = array(
    'status' => 'success',
    'message' => 'Parameters received successfully!',
    'received_params' => $_GET
);
echo json_encode($response);

?>