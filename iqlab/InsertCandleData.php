<?php
//  InsertCandleData.php
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
      //require_once('newutil.php');  
      require_once('newutil2.php');  
      if ($data['Mode'] == 'InsertCandleTimeFrame') { InsertCandleTimeFrame($data); }
      return;
   }

function InsertCandleTimeFrame($data) { 

	     $ErrMsg  = '';
	     $pdo = getPDONew();

		 //require_once("clsSaveData.php");	
         //$clsSaveData = new cls_SaveData ;
         
		 $tablename = 'RawData';
          
		 $tablename = 'AnalyEMA_HistoryForLab';
		 $tablename = 'AnalyEMA_HistoryForLab_5M';


         $AllDat = $data['dataPost'];
		 $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
		 $txt = json_encode($AllDat);
		 fwrite($myfile, $txt);
		 fclose($myfile);
		 $timeframe = $data['timeframe'] ;
         for ($i=0;$i<=count($AllDat)-1;$i++) {     
		   $sqlInsert = UniversalSave99($tablename,$AllDat[$i]['AnalyEMA']);	 
		   //echo $sqlInsert ;
		   $params = array();		 
		   if (!pdoExecuteQuery($pdo,$sqlInsert,$params)) {
			 echo 'Error' ;
			 return false;
		  }
        }
       // $pdo->commit();

	      


	     

} // end function

function UniversalSave99($tablename,$data) { 

 // $data = json_decode($data);
  //print_r($data);
  $insertClause = "REPLACE INTO " . $tablename . ' ('; 
  $valueClause = ') VALUES(' ;


// วนลูปอ่านค่า key และ value
//$data = $data[1] ;
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
//echo $sql;




return $sql  ;

} // end function

?>