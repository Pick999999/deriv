<?php
  
 
	require_once("newutil2.php"); 	
	$pdo = getPDONew();
	$sql = 'select * from AnalyEMATmp  order by id '; 
	$params = array();
	
	$rs= pdogetMultiValue2($sql,$params,$pdo) ;	

	$results = [];
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	    $dataObj = new stdClass();
	    foreach ($row as $key => $value) {
	        $dataObj->$key = $value;
	    }
	    $results[] = $dataObj;
		//print_r($dataObj) ;
			    
	}
	// แสดงผลข้อมูลในรูปแบบ JSON
	echo json_encode($results, JSON_UNESCAPED_UNICODE);

?>