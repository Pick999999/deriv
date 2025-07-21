<?php
  header('Access-Control-Allow-Methods: GET, POST');
  header('Access-Control-Allow-Origin: *'); 
  ob_start();
  //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
  //https://www.cyfence.com/article/design-secured-api/
  
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);   
     $data = json_decode(file_get_contents('php://input'), true);
     if ($data) {
        $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
        require_once($newUtilPath ."/src/newutil.php");		
        if ($data['Mode'] == 'getAction') { getAction($data); }
        return;
     }
  
function getAction($data) { 


require_once("../iqlab/newutil2.php"); 
require_once("clsCandlestickIndy.php"); 
$clsCandlestickIndy = new CandlestickIndy();

$candleDataList = $data['candleRawDataList'];
$analyData = $clsCandlestickIndy->CreateAnalyisData($candleDataList)  ;
$pdo = getPDONew()  ;





  
	     
  
} // end function

?>