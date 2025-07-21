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
        
        require_once("newutil2.php");		
        if ($data['Mode'] == 'GetSymBolGroup') { GetSymBolGroup($data); }

		if ($data['Mode'] == 'getSubGroup') { getSubGroup($data); }
		
        return;
     }
  
  function GetSymBolGroup($data) { 
  
	     $ErrMsg  = '';
	     $pdo = getPDONew();
	     $sql = "SELECT * FROM `symbol_group` ";		
		 $params = array();		
		 $rs= pdogetMultiValue2($sql,$params,$pdo) ;
		 $st = '<select id="symbolGroup" onclick="getSymBolListFromGroup(this.value)">';
		 $stBtn = '';
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			$st .= '<option value="'. $row['id'] .'">'   . $row['symbol_type']  ;
			$stBtn .="<button type='button' id='' class='mBtn' onclick=fff()>" . $row['symbol_type']. "</button>";
		 }
		 $st .= '</select>';
		 

		 echo $stBtn ;
		   
	     
	      
	     
  
  } // end function

function getSubGroup($data) { 

         $ErrMsg  = '';
	     $pdo = getPDONew();
	     $sql = "SELECT * FROM `trading_symbols` where symbol_type = ? ORDER by symbol ";		
		 $params = array($data['grouptype']);		
		 $rs= pdogetMultiValue2($sql,$params,$pdo) ;
		 $st = '<select id="symbolGroup" onclick="getSymBolListFromGroup(this.value)">';
		 $stBtn = '';
		 $stSubGroup = '';
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			//$st .= '<option value="'. $row['symbol'] .'">'   . $row['symbol_type']  ;
			$stBtn .="<button type='button' id='btn_". $row['symbol'] . "' class='mBtn' onclick=AssetSelected('" . $row['symbol'] . "')>" . $row['display_name']. "</button>";
			$stSubGroup .= $row['symbol'] . ',';
		 }
		 $st .= '</select>';

		 $stSubGroup = substr($stSubGroup,0,strlen($stSubGroup)-1);		 
		 echo $stBtn . '@#' . $stSubGroup ;


} // end function


?>
