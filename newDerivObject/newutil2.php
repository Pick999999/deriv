<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getPDONew($dbname='thepaper_lab',$username='thepaper_lab',$password='maithong') { 

	$dsn = 'mysql:host=localhost;dbname='. $dbname ;
	/*
	$username = 'thepaper_lab';
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


function json2Table($jsonDataString) {  ?>


<?php     

// ข้อมูล JSON หรือ Standard Class Object (ในที่นี้จะใช้ JSON เป็นตัวอย่าง)


if (is_string($jsonDataString)) {
    // ตรวจสอบว่าตัวแปร A เป็น JSON ที่ถูกต้องหรือไม่
    //json_decode($jsonDataString);
	// แปลง JSON ให้เป็น PHP Array
    $dataArray = json_decode($jsonDataString, true);
    if (json_last_error() === JSON_ERROR_NONE) {
       // echo "<br>jsonDataString ที่ส่งมาให้  เป็น JSON string ที่ยังไม่ได้ถูก json_decode() ทำการแปลงเป็น json Object";

    } else {
       // echo "<br>jsonDataString เป็น string ธรรมดา ไม่ใช่ JSON ที่ถูกต้อง";
    }
} elseif (is_array($jsonDataString) || is_object($jsonDataString)) {
    //echo "<br>jsonDataString ถูก json_decode() มาแล้วเป็น array หรือ object";
	$dataArray = $jsonDataString;
} else {
    //echo "<br>jsonDataString ไม่ใช่ JSON และไม่ใช่ string ธรรมดา";
	return ;
}

// แปลง JSON ให้เป็น PHP Array
$dataArray = json_decode($jsonDataString, true);

$stTable = '';
// ตรวจสอบว่ามีข้อมูลใน array
if (!empty($dataArray)) {
    // ดึง key ของ array ตัวแรกเพื่อใช้เป็น header
    $headers = array_keys($dataArray[0]);

    // เริ่มสร้าง table
    //echo "<table border='1'>";
	$stTable .= "<table border='1' id='tableData999'>";
    
    // สร้างหัว column โดยใช้ key จาก array
    //echo "<tr>";
	$stTable .= "<tr>";

    foreach ($headers as $header) {
        //echo "<th>" . htmlspecialchars($header) . "</th>";
		$stTable .= "<th style='color:black'>" . htmlspecialchars($header) . "</th>";
    }
    //echo "</tr>";
	$stTable .= "</tr>";
    
    // วนลูปสร้าง row ข้อมูล
	$rowno= 1 ;
    foreach ($dataArray as $row) {
        //echo "<tr>";
		$stTable .= "<tr  id='tblrowno_" . $rowno++ ."' onclick='ManageRowTable(this.id)' " . ">";
        foreach ($headers as $header) {
            //echo "<td >" . htmlspecialchars($row[$header]) . "</td>";
			$stTable .= "<td>" . htmlspecialchars($row[$header]) . "</td>";
        }
        //echo "</tr>";
		$stTable .= "</tr>";

    }
    
    //echo "</table>";
	$stTable .= "</table>";

} else {
    echo "ไม่มีข้อมูล";
	$stTable .= "ไม่มีข้อมูล";

}

return $stTable;

  /*
  
  $myObj = new stdClass();
  $myObj->result = 'Success' ;
  $myObj->numOpen = $numOpen ;
  $myJSON = json_encode($myObj);
  echo $myJSON;
  */
  



} // end function
?>

