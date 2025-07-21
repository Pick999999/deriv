<?php
//  testajax.php
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
      echo $data['name'] ;
      //if ($data['Mode'] == 'registershop') { Registershop($data); }
      return;
   }

function Registershop($data) { 

	     $ErrMsg  = '';
	     $pdo = getPDO(true,$ErrMsg);
	     $sql = "SELECT * FROM `CarouselGroup` where carous_group_id=?";
	     
	     try {
	             
	        $params = array($data['sDat']); 
	        $rs = $pdo->prepare($sql);
	        $rs->execute($params);
	        $pdo->commit();
	     } catch (PDOException $ex) {
	        echo  $ex->getMessage();
	     
	     } catch (Exception $exception) {
	             // Output unexpected Exceptions.
	             Logging::Log($exception, false);
	     }
	     if ($rs->rowCount() > 0) {
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	        
	         }
	     }
	     

} // end function

?>