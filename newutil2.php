<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getPDONew($dbname='thepaper_lab',$username='thepaper_lab',$password='maithong') { 

	$dsn = 'mysql:host=localhost;dbname='. $dbname ;
	/*$username = 'thepaper_lab';
	$password = 'maithong';
	*/

	try {
		$pdo = new PDO($dsn, $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
		$pdo->exec("set names utf8mb4") ;
		// Set error mode to exception
		//echo "Connected to database successfully!";
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}



	return $pdo;

} // end function


function getData_RS($sql) { 
 

         $pdo = getPDONew()  ;
         $pdo->exec("set names utf8mb4") ;
		 $params = array();

		 $rs= pdogetMultiValue2($sql,$params,$pdo) ;
		  
		 $results = [];
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		     $dataObj = new stdClass();
		     foreach ($row as $key => $value) {
		         $dataObj->$key = $value;
		     }
		     $results[] = $dataObj;
				    
		 }
		 // แสดงผลข้อมูลในรูปแบบ JSON
		 //echo json_encode($results);
		 // แสดงผลข้อมูลในรูปแบบ Table สามารถส่งทั้ง String หรือ json Object ไปได้เลย
		 //$jsonDataString = results;
		 //$jsonDataString = json_decode($results,true);
		 //json2Table($jsonDataString);

		 return $results;
		 
} // end function

function getData_Row($sql) { 
 

         $pdo = getPDONew()  ;
         $pdo->exec("set names utf8mb4") ;
		 $params = array();

		 //$rs= pdogetMultiValue2($sql,$params,$pdo) ;
		 try {
          $rs = $pdo->prepare($sql);
          $rs->execute($params);		   
         } catch (PDOException $e)   {
            echo  $e->getMessage();
            return false;
         }
		  
		 $results = [];
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		     $dataObj = new stdClass();
		     foreach ($row as $key => $value) {
		         $dataObj->$key = $value;
		     }
		     $results[] = $dataObj;
				    
		 }
		 // แสดงผลข้อมูลในรูปแบบ JSON
		 //echo json_encode($results);
		 // แสดงผลข้อมูลในรูปแบบ Table สามารถส่งทั้ง String หรือ json Object ไปได้เลย
		 //$jsonDataString = results;
		 //$jsonDataString = json_decode($results,true);
		 //json2Table($jsonDataString);

		 return $results[0];
		 
} // end function

function pdoExecuteQueryV2($pdo,$sql,$params) {


       /* $result = $pdo->query($sql) ;
		return $result ;
		*/
       $ErrMsg  = '';                     
       try {                     
          $rs = $pdo->prepare($sql);
          $rs->execute($params);          

		  return true ;
       } catch (PDOException $ex) {
          echo  $ex->getMessage();
		  return false ;
       
       } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
		  return false ;
       }
}

function pdogetMultiValue2($sql,$params,$pdo) {

          
         
         try {
          $rs = $pdo->prepare($sql);
          $rs->execute($params);
		  return $rs ;
		   
         } catch (PDOException $e)   {
            echo  $e->getMessage();
            return false;
         }

} // end func

function pdogetRowSet($sql,$params,$pdo) {


          
         try {
          $rs = $pdo->prepare($sql);
          $rs->execute($params);
		  $row = $rs->fetch();
		  return $row ;


         } catch (PDOException $e)   {
            echo  $e->getMessage();
            return false;
         }

} // end func

function pdoRowSet($sql,$params,$pdo) {

 
         try {
          $rs = $pdo->prepare($sql);
          $rs->execute($params);
		  $row = $rs->fetch();
		  return $row ;
         } catch (PDOException $e)   {
            echo  $e->getMessage();
            return false;
         }

} // end func

function pdogetValue($sql,$params,$pdo) {
         

         try {
          $rs = $pdo->prepare($sql);
          $rs->execute($params);
          $row = $rs->fetch();
		  
		  if ($rs->rowCount() > 0 ) {
             //echo 'Found';
		     return $row[0] ;
		  } else {
			//echo 'Not Found';
			return -1;
		  }

         } catch (PDOException $e)   {
            echo  "Error DB " . $e->getMessage();
            return -1;
         }

} // end func

function echoJson($jsonObject) {

echo '<pre>';
echo 'Description <br>';
echo JSON_ENCODE($jsonObject, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
echo '</pre>' ;

} // end function

function deleteAllFilesInFolder($folderPath) {
    // ตรวจสอบว่าโฟลเดอร์มีอยู่จริง
    if (!is_dir($folderPath)) {
        throw new Exception("โฟลเดอร์ $folderPath ไม่พบ");
    }

    // เปิด directory
    $dir = opendir($folderPath);

    // อ่านไฟล์ทั้งหมดในโฟลเดอร์
    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

            // ตรวจสอบว่าเป็นไฟล์ (ไม่ใช่โฟลเดอร์)
            if (is_file($filePath)) {
                if (!unlink($filePath)) {
                    throw new Exception("ลบไฟล์ $filePath ไม่สำเร็จ");
                }
            }
        }
    }
    
    closedir($dir);
    return true;
}


?>

