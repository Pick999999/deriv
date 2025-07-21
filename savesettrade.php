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
        require_once($newUtilPath ."newutil2.php");		
        if ($data['Mode'] == 'savesettrade') { SaveSetTrade($data); }
        return;
     }
	 SaveSetTrade($data) ; 

  
function SaveSetTrade($data) { 
  
	      require_once("newutil2.php");

          //$assetCode   = $data["assetName"];
		  $assetCode   = $_POST["assetName"];
		  if ($_POST["enableTrade"] === '1') {
			$isOpenTrade = 'Y';
		  } else {
            $isOpenTrade = 'N';
		  }

		  if ($_POST["enableMartingale"] === '1') {
			$isMartingale = 'Y';
		  } else {
            $isMartingale = 'N';
		  }

		  $moneyTrade = $_POST["tradeAmount"];
          $targetTrade = $_POST["targetAmount"];

	$sql = "UPDATE pageTradeStatus SET assetCode=?,isopenTrade=?,isMartingale=?,moneyTrade=?,targetTrade=?
	" ; // WHERE assetCode=?"; 
	//$dbname = 'ddhousin_lab' ;
	$pdo = getPDONew()  ;	
	$params = array($assetCode,$isOpenTrade,$isMartingale,$moneyTrade,$targetTrade);
	if (!pdoExecuteQueryV2($pdo,$sql,$params)) {
		  echo 'Error' ;
		  return false;
	} 

	$sql = "select * from pageTradeStatus"; 		
	$params = array();
	$row = pdoRowSet($sql,$params,$pdo) ;
	echo  $row['assetCode'] . '|'. $row['isopenTrade'].'|'. $row['isMartingale'].'|'. $row['moneyTrade'] .'|'. $row['targetTrade'];
	
	

  
  } // end function

?>