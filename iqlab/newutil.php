<?php
    @session_start() ;
	date_default_timezone_set("Asia/Bangkok");


function convertJSONToSQL() { 

$stJson = '{ "categorybox": [
{
"text_41" : "Sale @#-ItemMaster.isWork",
"text_42" : "0 @#-",
"text_43" : "d @#-",
"text_44" : "0 @#-",
"text_45" : "h @#-",
"text_46" : "0 @#-",
"text_47" : "m @#-",
"text_48" : "0 @#-",
"text_49" : "s @#-",
"text_50" : "Beach Cap @#-ItemMaster.ItemName",
"text_51" : "$13 @#-ItemMaster.SellPrice",
"text_52" : "$42 @#-ItemMaster.fullPrice",
"img_1" : "https://lovetoshopmall.com/workshop/suha/img/product/11.png @#-ItemMaster.mainImageURL"
}
]
}';

$groupname = 'categorybox';  
$postData3 = json_decode($stJson) ;
foreach($postData3->$groupname[0] as $key => $val) {
     //if ($key) { echo 'KEY IS: '.$key; };
     //if ($val) { echo 'VALUE IS: '.$val; };
	 $ValueAr = explode('@#-',$val) ;
	 if (trim($ValueAr[1] !== '')) {	 
	    $fieldName[] = $ValueAr[1] ;
		$fieldNameAr = explode('.',$ValueAr[1]); 
		$tablename[] = $fieldNameAr[0] ;
	 } else {
        $fieldName[] = chr(34) .trim($ValueAr[0]) . chr(34);
	 }

	 $keyName[] = $key ;
     //echo '<br>';
} 

//findTableName 
for ($i=0;$i<=count($fieldName)-1;$i++) {
     $fieldNameAr = explode('.',$fieldName[$i]); 
}

//$stsql = 'SELECT ' . implode(',',$fieldName) . ' FROM '  ;
$stsql = 'SELECT ' ;
for ($i=0;$i<=count($fieldName)-1;$i++) {
   $stsql .=  $fieldName[$i] . ' as ' .$keyName[$i] . ',' ;
}

$stsql = substr($stsql,0,strlen($stsql)-1)  . " FROM " . $tablename[0] . " LIMIT 0,10" ; 
echo $stsql ;

$ErrMsg  = '';
$dbname = 'ddhousin_shopproject' ;
$pdo = getPDO2($dbname,false)  ;

$doworkSuccess = false ;
try {        

   $rs = $pdo->prepare($stsql);
   $rs->execute();
   $doworkSuccess = true ;
   //$pdo->commit();
} catch (PDOException $ex) {
   echo  $ex->getMessage();

} catch (Exception $exception) {
        // Output unexpected Exceptions.
        Logging::Log($exception, false);
} 

$stJson2 =  '{' ;
if ($rs->rowCount() > 0) {
   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
     for ($i=0;$i<=count($keyName)-1;$i++) {             
       $stJson2 .=  '"'. $keyName[$i]. '" : "' . $row[$keyName[$i]] . '",'. "<br>";
     }
	 $stJson2 = substr($stJson2,0,strlen($stJson2)-5) ;
	 $stJson2 .= '<br>},<br>{'; 
   }
   $stJson2 .= ''; 
}  
//echo $stJson2; return;
$stJson2 = substr($stJson2,0,strlen($stJson2)-6);


$stJson3 = '{ "' . $groupname .'": [' . $stJson2 . '<br>]}' ;
echo "<hr>" . $stJson3 ;

return $stJson3 ;

 

//print_r($fieldName) ;

 

} // end function

function getJsonDataFromDOM($pageurl,$sectionname)  { 

	     $dbname = 'ddhousin_devshop' ;
	     $pdo = getPDO2($dbname,false)  ;
		 $sql = "select domSQL  from domsection where pageurl=? and sectionname=?"; 
		 $params = array($pageurl,$sectionname) ; 

		 $groupname = 'categorybox';  
		 
		 $domSQL = pdogetValue($sql,$params,$pdo) ;
		 $sql = "select  domJSON from domsection where pageurl=? and sectionname=?"; 
		 $domJSON = pdogetValue($sql,$params,$pdo) ;
		 //echo "$domSQL<hr>";

         $postData3 = json_decode($domJSON) ;

		 foreach($postData3->$groupname[0] as $key => $val) {			 
			 $ValueAr = explode('@#-',$val) ;
			 if (trim($ValueAr[1] !== '')) {	 
				$fieldName[] = $ValueAr[1] ;
				$fieldNameAr = explode('.',$ValueAr[1]); 
				$tablename[] = $fieldNameAr[0] ;
			 } else {
				$fieldName[] = chr(34) .trim($ValueAr[0]) . chr(34);
			 }

			 $keyName[] = $key ;
			 //echo '<br>';
		 } 

		//findTableName 
		for ($i=0;$i<=count($fieldName)-1;$i++) {
			 $fieldNameAr = explode('.',$fieldName[$i]); 
		}

		 //return $domSQL ;

		 $dbname = 'ddhousin_shopproject' ;
$pdo = getPDO2($dbname,false)  ;

$doworkSuccess = false ;
try {        

   $rs = $pdo->prepare($domSQL);
   $rs->execute();
   $doworkSuccess = true ;
   //$pdo->commit();
} catch (PDOException $ex) {
   echo  $ex->getMessage();

} catch (Exception $exception) {
        // Output unexpected Exceptions.
        Logging::Log($exception, false);
} 

$stJson2 =  '{' ;
if ($rs->rowCount() > 0) {
   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
     for ($i=0;$i<=count($keyName)-1;$i++) {             
       $stJson2 .=  '"'. $keyName[$i]. '" : "' . $row[$keyName[$i]] . '",'. "<br>";
     }
	 $stJson2 = substr($stJson2,0,strlen($stJson2)-5) ;
	 $stJson2 .= '},{'; 
   }
   $stJson2 .= ''; 
}  
//echo $stJson2; return;
$stJson2 = substr($stJson2,0,strlen($stJson2)-6);


$stJson3 = '{ "' . $groupname .'": [' . $stJson2 . ']}' ;
//echo "<hr>" . $stJson3 ; 
return $stJson3;

} // end function


function  getHost  () {
/**
 * Short description.
 * @param   type    $varname    description
 * @return  type    description
 * @access  public or private
 * @static  makes the class property accessible without needing an instantiation of the class
 */
          $PageURL = urldecode("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

          $PageURL2 = parse_url($PageURL) ;
//		  $host =  $PageURL2['scheme']  . '//' . $PageURL2['host'] ;
		  $host =   $PageURL2['host'] ;

		  return $host;

} 
function siteURL()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';
    return $protocol.$domainName;
}


function getDepartData(&$shopName,&$DepartCode,&$DepartName,&$CategoryCode,&$CategoryName,&$GroupCode,&$GroupName,&$PageNo) {


    $debugmode = false;
    $shopNameAr = explode("-",$_GET["shopname"]) ;
	$shopName =   $shopNameAr[1]  ;


	$sql = "select shopCode from shopmaster where shopName=?";
	$params = array($shopName);
	$shopCode = pdogetValue($sql,$params) ;


	$DepartmentCodeAr = explode("-",$_GET["departmentcode"]) ;

	$DepartmentCode = $DepartmentCodeAr[1] ;
	$DepartmentName = $DepartmentCodeAr[2] ;

	if ($debugmode) { echo $_GET["departmentcode"]. "<br>";
      echo $DepartmentCodeAr[1]  . "***" . $DepartmentCodeAr[2] ;
      echo "<br><br><br><br><br>";
	}

    $GroupCodeAr = explode("-",$_GET["groupcode"]) ;
	$CategoryCodeAr = explode("_",$GroupCodeAr[0]);
	$CategoryCode = $CategoryCodeAr[0] ."_". $CategoryCodeAr[1] ;

	$sql = "select categoryDesc from categorymaster where categorycode=?" ;
    $params = array($CategoryCode) ;
	$CategoryDesc = pdogetValue($sql,$params) ;




    $GroupCodeAr = explode("-",$_GET["groupcode"]) ;
	$GroupCode = $GroupCodeAr[0]  ; $GroupName =   $GroupCodeAr[1];







	$pageno = $_GET["pageno"] ;


	if ($debugmode) {
	  echo "shopName = " . $shopName ." ; ";
	  echo "ShopCode = " . $shopCode . "<br>";
	  echo "DepartMent Code = " . $DepartmentCode ." ; Department Name=".  $DepartmentName."   <br>";
	  echo "Category Code=" . $CategoryCode . ";<br> ";
	  echo "CaegoryDesc = " . $CategoryDesc . "<br>";

      echo "Group Code = " . $GroupCodeAr[0] ." ; Group Name=".  $GroupCodeAr[1]."   <br>";
	  echo "PageNo = " .  $pageno."   <br>";


	}
	$st = $shopCode ."|" . $shopName . "|". $DepartmentCode. "|". $DepartmentName."|".$CategoryCode . "|" . $CategoryDesc . "|".$GroupCodeAr[0]."|".$GroupCodeAr[1] . "|" . $pageno ;

   return $st ;








} // end func


function createLoadBox() { ?>

<div id="loading-box" style="position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background-color: rgb(0, 0, 0); opacity: 0.8; z-index: 10000; cursor: pointer; text-align: center; display: none;"><div style="margin: auto; width: 60px; height: 60px; position: relative; top: 50%; transform: translateY(-50%);"><img src="../js/default.gif" style="width: 100%; height: 100%;"></div></div>


<?php
}

function mysqldate($thaidate) {

			 $thaidateAr = explode("/" ,$thaidate) ;
			 return ($thaidateAr[2]-543) . "-" . $thaidateAr[1] . "-" . $thaidateAr[0] ;
}


function CreateModalBox($modalid,$headcaption) { ?>
<style>
/* The Modal (background) */
.99modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.99modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button */
.99close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.99close:hover,
.99close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

</style>
<!-- Modal -->
  <!-- <input type="button" id= '<?=$modalid?>btn'  style='display:none' value="open modal"  data-toggle="modal" data-target="#<?=$modalid?>"> -->
  <div class="modal fade" id="<?=$modalid?>" role="dialog">
    <div class="modal-dialog">


      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?=$headcaption?></h4>
        </div>
        <div id='<?=$modalid?>Body' class="modal-body">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button id="closeModalBtn" name="closeModalBtn" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>


<?php
}

function  AddWinModal($id,$title) { ?>


<div id="<?=$id?>" class="easyui-window" title="<?=$title?>" data-options="modal:true,closed:true,iconCls:'icon-save'" style="width:500px;height:200px;padding:10px;">
    <div id="inner<?=$id?>" class="easyui-layout" data-options="fit:true">


    </div>
</div>


<?php
}

function PFileExists22($fName) {

           //$fName = $_SERVER["DOCUMENT_ROOT"]   ;

		   if  (file_exists($fName)) {
                 return "1";
		   } else {
			    return "-1";
		   }
		   /*f (file_exists($fName)) {
               return 1 ;
		   } else {
               return -1 ;
		   }
		   */

		 /*  if (fopen($fName , "r")) {
              return 1 ;
		   } else {
             return -1 ;
		   }
		   */
		   /*$file_headers = @get_headers($fName);
           if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
                $exists = -1;
           }   else {
                $exists = 1;
           }
		   */


return $exists ;


}

function getCurrentDate()
{
  return date("Y-m-d");
}
function thaidatetime($time)
{
	$time = strtotime($time);
//    $thai_date_return="วัน".$thai_day_arr[date("w",$time)];
    $thai_date_return.= " ".date("j",$time);
    $thai_date_return.="/". date("n",$time);
    $thai_date_return.= "/".(date("y",$time)+43);
    $thai_date_return.= "  ".date("H:i",$time)."";
    return $thai_date_return;
}

function thaidate($time) {

	if ($time=="") {
	  return "-" ;
	}
	$thai_date_return = '';
	$time = strtotime($time);
//    $thai_date_return="วัน".$thai_day_arr[date("w",$time)];
    $sDate = date("j",$time);
	if (intval($sDate) >= 10 ) {
      $thai_date_return.= date("j",$time);
	} else {
      $thai_date_return.= " 0".date("j",$time);
	}
    $thai_date_return.="/". date("n",$time);
    $thai_date_return.= "/".(date("y",$time)+43);
//    $thai_date_return.= "  ".date("H:i",$time)."";
    return $thai_date_return;
}

function  newthaidate($sdate) {
/**
 * Short description.
 * @param   type    $varname    description
 * @return  type    description
 * @access  public or private
 * @static  makes the class property accessible without needing an instantiation of the class
 */
 $sdateAr = explode(' ' ,$sdate)   ;
 $sdateAr2 = explode('-',   $sdateAr[0]) ;

 return  $sdateAr2[2] . '/'. $sdateAr2[1] .'/' . ($sdateAr2[0]+543) ;
}

function  newthaidatetime($sdate) {
/**
 * Short description.
 * @param   type    $varname    description
 * @return  type    description
 * @access  public or private
 * @static  makes the class property accessible without needing an instantiation of the class
 */
 $sdateAr = explode(' ' ,$sdate)   ;
 $sdateAr2 = explode('-',   $sdateAr[0]) ;

 return  $sdateAr2[2] . '/'. $sdateAr2[1] .'/' . (floatval($sdateAr2[0])+543) . '<span style="color:red;margin-top:2px">&nbsp;' . $sdateAr[1] . '</span>&nbsp;&nbsp;' ;
}


function thailongdate($time)
{

	$thai_month_arr=array(
	"0"=>"",
	"1"=>"มกราคม",
	"2"=>"กุมภาพันธ์",
	"3"=>"มีนาคม",
	"4"=>"เมษายน",
	"5"=>"พฤษภาคม",
	"6"=>"มิถุนายน",
	"7"=>"กรกฎาคม",
	"8"=>"สิงหาคม",
	"9"=>"กันยายน",
	"10"=>"ตุลาคม",
	"11"=>"พฤศจิกายน",
	"12"=>"ธันวาคม"
);
	$time = strtotime($time);
//    $thai_date_return="วัน".$thai_day_arr[date("w",$time)];
    $thai_date_return.= " ".date("j",$time);
    $thai_date_return.=" ". $thai_month_arr[date("n",$time)];
    $thai_date_return.= " ".(date("y",$time)+43);
//    $thai_date_return.= "  ".date("H:i",$time)."";
    return $thai_date_return;
}


function openConnectionOld()
{

           $username = 'admin' ;$passw = "maithong" ;
		   //$conn = @mysql_pconnect("localhost", $username,$passw) ;

		   $username = 'admin' ;$passw = "maithong" ;
 	       $conn = @mysql_pconnect("localhost", $username,$passw) ;

		   $sterr = "<br/>Cannot Open Connection username=" . $username . ";password=" . $passw ;
		   mysql_select_db('talonplusonweb', $conn)  or die($sterr);
		   //mysql_select_db('thairealestate', $conn)  or die("Cannot Open Database");
		   mysql_query("SET NAMES UTF8");
		   //Palert("Success");
		   return $conn ;


}

function openConnection()  {

		   $username = "ddhousin";
		   $password = 'y4e2Q44rBw' ;
		   $dbname = "ddhousin_shopproject";
 	       $conn = @mysql_pconnect("localhost", $username,$password) ;

		   $sterr = "<br/>Cannot Open Connection username=" . $username . ";password=" . $password ;

		   mysql_select_db($dbname, $conn)  or die($sterr);
		   //mysql_select_db('thairealestate', $conn)  or die("Cannot Open Database");
		   mysql_query("SET NAMES UTF8");
		   //Palert("Success");

		   return $conn ;


}


function getMultiValue($sql)
   {
       $conn = openConnection() ;
	   $resultset = mysql_query($sql) or die(mysql_error() . '<p> Sql ==>' .$sql ) ;
	   //

	   return $resultset;

}

function getPDOMultiValue($pdo,$sql,$params)   {

  
/*	   $rs = $pdo->prepare($sql);
       $rs->execute();
	   //
*/    
       $ErrMsg  = '';                     
       try {                     
          $rs = $pdo->prepare($sql);
		  if ($params == '' ) {
            $rs->execute();          
		  } else {
            $rs->execute($params);          
		  }
		  return $rs ;
       } catch (PDOException $ex) {
          echo  $ex->getMessage();
		  return false ;
       
       } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
		  return false ;
       } 
  
	  //return $rs;
}

function pdoExecuteQuery($pdo,$sql,$params) {


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

function pdoExecuteQuery222($pdo,$sql,$params,&$id) {


       /* $result = $pdo->query($sql) ;
		return $result ;
		*/
       $ErrMsg  = '';                     
       try {                     
          $rs = $pdo->prepare($sql);
          $rs->execute($params);          
//		  $id = $rs->lastInsertId();
		  $id = $pdo->lastInsertId();
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

function pdoDeleteQuery($pdo,$sql,$params,&$recAffected,&$errMsg) {


       /* $result = $pdo->query($sql) ;
		return $result ;
		*/
       
       try {                     
          $rs = $pdo->prepare($sql);
          $rs->execute($params);  
		  $recAffected = $rs->rowCount(); 
		  return true ;
       } catch (PDOException $ex) {
          $errMsg = $ex->getMessage();
		  //echo $errMsg . '===>' . $sql;
		  return false ;
       
       } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
		  return false ;
       }

}


function pdoExecuteQuery3($pdo,$sql,$params,&$newID,&$errMsg) {


       /* $result = $pdo->query($sql) ;
		return $result ;
		*/
       
       try {                     
          $rs = $pdo->prepare($sql);
          $rs->execute($params);  
		  $newID= $pdo->lastInsertId(); 
		  return true ;
       } catch (PDOException $ex) {
          $errMsg = $ex->getMessage();
		  echo $errMsg . '===>' . $sql;
		  return false ;
       
       } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
		  return false ;
       }

}

function pdoExecuteQuery2($pdo,$sql) {


       /* $result = $pdo->query($sql) ;
		return $result ;
		*/
       
      
       try {
         $rs = $pdo->prepare($sql);
         $rs->execute();
       } catch (Exception $e) {
         echo 'Message: ' .$e->getMessage();
       }
	   return $rs ;
        
        
        


}

function getPDOValue($sql)   {

       $pdo= getPDO(false,$errMsg) ;
	   $result = $pdo->query($sql);
	   $row =  $result->fetch(PDO::FETCH_NUM) ;
	   if ($row) {
	     return $row[0];
	   } else {
         return -1;
	   }

}


function getPDOValue2($pdo,$sql)   {


	   $result = $pdo->query($sql);
	   $row =  $result->fetch(PDO::FETCH_NUM) ;
	   if ($row) {
	     return $row[0];
	   } else {
         return -1;
	   }

}

function getPDOValue3($pdo,$sql,$params)   {

	 
	try {	        	   
	   $rs = $pdo->prepare($sql);
	   $rs->execute($params);	   
	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
	}
	 
	$row =  $rs->fetch(PDO::FETCH_NUM) ;
	if ($row && $row[0] !== null) {	  	
	   return $row[0];
	} else {
      return -1;
	}

}

function getPDORowArray($pdo,$sql)   {

       $rs = $pdo->query($sql);
	   $row =  $result->fetch(PDO::FETCH_NUM);
	   return $row;

} 

function getPDOCountRec($pdo,$sql,$params)   {

       try {                         
          $rs = $pdo->prepare($sql);
          $rs->execute($params);
          
       } catch (PDOException $ex) {
          echo  $ex->getMessage();
       
       } catch (Exception $exception) {
               // Output unexpected Exceptions.
               Logging::Log($exception, false);
       }
       if ($rs->rowCount() > 0) {
          while($row = $rs->fetch( PDO::FETCH_NUM )) {
              return $row[0] ;
          }
       }
	   return 0;

} 


function getPDORowSet($sql,$params="")   {

       $pdo= getPDO(false,$errMsg) ;
	   if ($params=="") {
	     $rs = $pdo->query($sql);
	     $row =  $rs->fetch(PDO::FETCH_ASSOC);
	   } else {
	     $stmt = $pdo->prepare($sql) ;
	     try {
	       $stmt->execute($params);
	       $row = $stmt->fetch();
	     } catch (PDOException $e)   {
	       echo  "<span style='color:red'> Error -></span><span style='font-size:22px'> " . $sql . "</span>" .  $e->getMessage();
	       return false;
	     }
	   }
	   return $row;
}

function getPDORowSet3($pdo,$sql,$params)   {


	   if ($params=="") {
	     $rs = $pdo->query($sql);
	     $row =  $rs->fetch(PDO::FETCH_ASSOC);
	   } else {
	     $rs = $pdo->prepare($sql) ;
	     try {
	       $rs->execute($params);
	       $row = $rs->fetch(PDO::FETCH_ASSOC);
	     } catch (PDOException $e)   {
	       echo  "<span style='color:red'> Error -></span><span style='font-size:22px'> " .  "</span>" .  $e->getMessage();
	       return false;
	     }
	   } 


	   return $row;



}

function getPDORS($pdo,$sql,$params)   {

      // $pdo= getPDO(false,$errMsg) ;

	   
	     $rs = $pdo->prepare($sql) ;
	     try {
	       $rs->execute($params);	       
	     } catch (PDOException $e)   {
	       echo  "<span style='color:red'> Error -></span><span style='font-size:22px'> " . $sql . "</span>" .  $e->getMessage();
	       return false;
	     }
	   
	   return $rs;
}

function getPDORS2($pdo,$sql)   {

      // $pdo= getPDO(false,$errMsg) ;
 

	   
	     $rs = $pdo->prepare($sql) ;
	     try {
	       $rs->execute();	       
	     } catch (PDOException $e)   {
	       echo  "<span style='color:red'> Error -></span><span style='font-size:22px'> " . $sql . "</span>" .  $e->getMessage();
	       return false;
	     }
	   
	   return $rs;
}


function getPDORowSet2($pdo,$sql,$params="")   {

       //$pdo= getPDO(false,$errMsg) ;
	   if ($params=="") {
	     $rs = $pdo->query($sql);
	     $row =  $rs->fetch(PDO::FETCH_ASSOC);
	   } else {
	     $stmt = $pdo->prepare($sql) ;
	     try {
	       $stmt->execute($params);
	      // $row = $stmt->fetch();
	     } catch (PDOException $e)   {
	       echo  "<span style='color:red'> Error -></span><span style='font-size:22px'> " . $sql . "</span>" .  $e->getMessage();
	       return false;
	     }
	   }
	   return $stmt;
}





function getRowSet($sql)
   {
       $conn = openConnection() ;
	   $resultset = mysql_query($sql) or die(mysql_error() . '<p> Sql ==>' .$sql ) ;
	   $row = mysql_fetch_assoc($resultset) ;

	   return $row;

   }
function getRowArray($sql)
   {
       $conn = openConnection() ;
	   $resultset = mysql_query($sql) or die(mysql_error() . '<p> Sql ==>' .$sql ) ;
	   $row = mysql_fetch_array($resultset) ;

	   return $row;

   }
function formatn($snumber)
{
         $snumber = replace99(',','',$snumber) ; 
	     if ($snumber == '') {

			 $snumber = 0 ;
	     }  
         return number_format($snumber,2,'.',',');
}
function formati2($snumber)
{
         return number_format($snumber,0,'.',',');
}


function formatInt($snumber)
{
	     $snumber = replace99(',','',$snumber) ; 
	     if ($snumber == '') {

			 $snumber = 0 ;
	     }   
         return number_format($snumber,0,'.',',');
}

function formatInt2($snumber)
{
	     $snumber = replace99(',','',$snumber) ; 
	     if ($snumber == '') {

			 $snumber = 0 ;
	     }  
         return number_format($snumber,0,'.',','). '.-';
}

function formati($snumber)
{
	     $snumber = str_replace(",",'',$snumber);
         return $snumber ; //number_format($snumber,0, '', '');
}
function getValue($sql)
   {
       $conn = openConnection() ;
	   $resultset = mysql_query($sql) or die(mysql_error() . '<p> Sql From getValue==>' .$sql ) ;

	   if ( $resultrow = mysql_fetch_array($resultset) )
	       return $resultrow[0];
	   else
	       return -1 ;

   }
function writeheader() {

      echo '<div id="headerpage" class="span-24 last round">';
	 // <div id="headerpage" class="normalbox_shadow" )"></div>

      echo '</div>';


}

function writeheader2() {
         echo '<div id="header"><img src="../photoshop work/header2 copy.jpg"></div>';

}
function writeFooter() {

	     echo '<div class="cls">
		 <hr></div>
  <div id="footer"  align="center" style="align:center;background-image:url(images/sadv1.jpg)"> © 2012 - 2013 all rights reserved by homesale54.com</div>';

}


function writeSlideShow($id) {


      $sql = "select b.realestateCode,b.realestate_ID,b.transno,b.subject,b.urlfriendly from   realestate b   where b.realestate_ID=" . $id ;
	  $rs  = getMultiValue($sql);

      echo ' <div class="wrap" >';
      echo ' <div id="slide-holder" style="width:940px">';
      echo '    <div id="slide-runner" style="margin-left:0px;width:938px; ">';
	  $i=0;

	  $row = mysql_fetch_assoc($rs);
	  $realestateCode= $row['realestateCode'];
	  $thispath= $row['urlfriendly'];

	 // while ($row = mysql_fetch_assoc($rs)) {
      for ($i = 0;$i<8;$i++) {

		   $reffile  =  $row['urlfriendly'] . $id .".html";
		   echo '<a href="' . $reffile  .  '">';
		   $st =  '<img id="slide-img' . '-' . $i  . '" ' ;
		   $st = $st . 'src="' ;
		   $thisfilename = $thispath . $id . '_' . $i . '_' . $row['transno'] . '.jpg " ';

		   //$filename = 'advertise/imageupload/' . $id . '_' . $i .'_' .$row['transno'] . '.jpg";
		   $st = $st . $thisfilename .  '".  width="938" height="300 class="slide" alt= ""/></a>' ;
		   echo $st;
		   $subject[] =  $row[subject] ;
	  }
      echo '<div id="slide-controls" style="margin-left:0px">';
      echo '<p id="slide-client" class="text" style="margin-left:5px "><strong></strong><span></span></p>';
      echo '<p id="slide-desc" class="text"></p>';
      echo '<p id="slide-nav"></p></div>';


      echo '</div><!-- Slide Runner --> ';
	  echo '</div> <!-- Slide-holder --> ';
	  echo '</div> <!-- wrap --> ';

	 echo '<script type="text/javascript">';
     echo '      if(!window.slider) var slider={};' ;
	 echo 'slider.data=[';
	 echo '{"id":"slide-img-1","client":"' . $subject[0] . '","desc":""},';
	 echo '{"id":"slide-img-2","client":"' .$subject[1] . '","desc":""},';
	 echo '{"id":"slide-img-3","client":"' . $subject[2] . '","desc":""},';
	 echo '{"id":"slide-img-4","client":"' .$subject[3] . '","desc":""},';
	 echo '{"id":"slide-img-5","client":"' . $subject[4] . '","desc":""},';
	 echo '{"id":"slide-img-6","client":"' .$subject[5] . '","desc":""},';
	 echo '{"id":"slide-img-7","client":"' . $subject[6] . '","desc":""}]';

     echo '</script>';
	 // echo '</div> ';
     //echo '</div></div> <!-- end Wrap -->		';
 }
function writeMenu_1() {

     echo '<div id="menu2" style="margin-bottom:10px;">
           <div class="lb"><div class="lr"><div class="lt"><div class="ll">
            <ul>
			<li><a href="http://www.homesale54.com" class="first">หน้าหลัก</a></li>
			<li><a href="http://www.homesale54.com/contactus.php">ติดต่อเรา</a></li>


			</ul>
           </div></div></div></div>
    </div>
	<div class="cls"></div>';

 }
 function writeMenu_2() {

     echo '<div id="menu2" style="margin-bottom:10px;">
           <div class="lb"><div class="lr"><div class="lt"><div class="ll">
            <ul>
			<li><a href="../index.php" class="first">หน้าหลัก</a></li>
			<li><a href="addadvertise.php">รับฝากขายอสังหาริมทรัพย์</a></li>
			<li><a href="products/index.html">ต้องการ ซื้อ-ขาย-เช่า</a></li>
			<li><a href="download/index.html">รับจำนองอสังหาริมทรัพย์</a></li>
			<li><a href="../contactus.php">ติดต่อเรา</a></li>
			<li><a href="purchase/index.html">Purchase</a></li>
			<li><a href="managescreen.php">manage screen</a></li>
			</ul>
           </div></div></div></div>
    </div>
	<div class="cls"></div>';

 }

 function writeMenu_Admin() {

     echo '<div id="menu2" style="margin-bottom:10px;">
           <div class="lb"><div class="lr"><div class="lt"><div class="ll">
            <ul>';
	 echo '<li><a href="imgresize/resize.php" class="first">ย่อรูป-เพิ่มlogo</a></li>';
	 echo '<li><a href="advertise/addadvertise.php">แก้ไข Meta,URL </a></li>';
	 echo '<li><a href="products/index.html">ดูรายการ Bot</a></li>';
	 echo '<li><a href="download/index.html">SEO Tool</a></li>';
	 echo '<li><a href="contactus.php">ติดต่อเรา</a></li>';
	//		<li><a href="purchase/index.html">Purchase</a></li>
	//		<li><a href="managescreen.php">manage screen</a></li>
      echo '
			</ul>
           </div></div></div></div>
    </div>
	<div class="cls"></div>';

 }
function mailtoOld() {

     $sendto = $_POST['email'] ;
     $subject  = 'สยามแคมปิ้งฟิฃฃิ่ง ' ; // ได้ตอบรับการสมัครสมาชิกของท่านแล้วครับ ' ;

     $message = 'เรียนคุณ : ' .  $_SESSION['userNickName']. '' ;
	 $message = $message . '<br>' . 'ระบบรับสมัครสมาชิก ได้ตอบรับการลง ทะเบียนของท่านเรียบร้อยแล้ว   ' ;
     $message = $message  . '<br>' . 'กรุณา คลิกที่ Link http:www.siamcampingfishing.com/index.php?activatemember=' .  $_SESSION['userID']   ;
     $message = $message  .  ' เพื่อยืนยันการเป็นสมาชิกของท่านด้วยครับ'   ;
	 $message = $message  .  '\nชื่อในกระดานของท่าน :' . $_POST['name']   ;
	 $message = $message  .  '\nชื่อที่ใช้ Loginของท่าน :' . $_POST['username']   ;
 	 $message = $message  .  '\nรหัสผ่านของท่าน :' .
	 $_POST['password'];
 	 $message = $message  .  '\nขอขอบคุุณจาก เวบมาสเตอร์ -สยามแคมปปิ้งฟิชชิ่ง.คอม'   ;



  $replyto = 'Reply-To : admin@siamcampingfishing.com\r\n ' ;
  $from = "From : siamcampingfishing.com\r\n" ;
  $ccto = '' ;
  $bccto = ' ' ;
  $header = $from . $replyto . $ccto . $bccto ;
//  $header .=  'MIME-Version: 1. 0\r\n' ;
//  $header .=  'Content-Type:text/plain\r\n' ;
//  $header .=  'Content-Transfer : 7bit\r\n' ;
  mail($sendto,$subject,$message);


}

function getCurrentDateTime() {
   date_default_timezone_set("Asia/Bangkok");
  return date("Y-m-d H:i:s");
}

function InsertTable($tablename,$fname,$fvalue) {

   $numfields = count($fname);
   $sql = 'INSERT INTO ' . $tablename . '(' ;
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {$sql = $sql . $fname[$i] . ',' ; }
        else
		   {$sql = $sql . $fname[$i] . ") values('" ; }
	}
   //print $sql . '<BR>';
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {$sql = $sql .addslashes($fvalue[$i]) . "','" ; }
        else
		   {$sql = $sql . addslashes($fvalue[$i]) . "')"  ; }

	}
    //   print 'Generte SQL ==>'	 . $sql;

   $resulttext = openConnection();
   try {
	 if (!mysql_query($sql)) {
         throw new Exception(mysql_error());
	 }
   } catch (Exception $e) {
	 $MsgReturn  =  'เกิดข้อผิดพลาด ในการบันทึกข้อมูล :  '   .  $e->getMessage() . "\n";
	 return $MsgReturn ;
   }
   $MsgReturn = mysql_affected_rows();
   return $MsgReturn ;



}

function InsertTable2($tablename,$fname,$fvalue) {

   $numfields = count($fname);
   $sql = 'INSERT INTO ' . $tablename . '(' ;
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {$sql = $sql . $fname[$i] . ',' ; }
        else
		   {$sql = $sql . $fname[$i] . ") values('" ; }
	}
   //print $sql . '<BR>';
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {$sql = $sql . $fvalue[$i] . '","' ; }
        else
		   {$sql = $sql . $fvalue[$i] . '")'  ; }

	}
    //   print 'Generte SQL ==>'	 . $sql;
   $resulttext = openConnection();
    //   Palert($sql);

   mysql_query($sql) or die(mysql_error() . ' <br> Sql is -->' . $sql) ;
   return $sql;



}


function InsertTableString($tablename,$fname,$fvalue) {

   $numfields = count($fname);
   $sql = 'INSERT INTO ' . $tablename . '(' ;
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {$sql = $sql . $fname[$i] . ',' ; }
        else
		   {$sql = $sql . $fname[$i] . ") values('" ; }
	}
   //print $sql . '<BR>';
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {$sql = $sql .addslashes($fvalue[$i]) . "','" ; }
        else
		   {$sql = $sql . addslashes($fvalue[$i]) . "')"  ; }

	}
    //   print 'Generte SQL ==>'	 . $sql;
   $resulttext = openConnection();
   try {
	 if (!mysql_query($sql)) {
         throw new Exception(mysql_error());
	 }
   } catch (Exception $e) {
	 $MsgReturn  =  'เกิดข้อผิดพลาด ในการบันทึกข้อมูล :  '   .  $e->getMessage() . "\n";
	 return $MsgReturn ;
   }
   $MsgReturn = mysql_affected_rows();
   return $MsgReturn ;



}

function getMemberData($memberid) {

	     $sql ="select * from member where member_id = $memberid";
		 $rs = getMultiValue($sql);
		 $row = mysql_fetch_array($rs) ;

		 return $row;

}

function writeAdvertise1() {
//

echo '<div id="advertise" >

		        <table >
				 <tr><td >
				  <IMG SRC="photoshop%20work/logo.png" WIDTH="107" HEIGHT="155"  ALT="">
                 </td><td style="color:#006600;font-size:14px"><span style="font-size:14px;color:#FF00FF">Homesale54.com </span><span style="color:green">ก่อตั้งขึ้นโดยทีมงาน  ที่มีประสบการณ์เป็นที่ยอมรับ ในวงการอสังหาริมทรัพย์และสถาบันการเงินกว่า 15 ปี ทีมงานเน้นให้บริการด้าน ซื้อ-ขายที่ดินเป็นหลัก โดย ทีมงานของเรา ซึ่งให้บริการด้วยความจริงใจ และโปร่งใสในการทำงาน พร้อมทั้งให้คำปรึกษาฟรี ในทุกๆด้านเพื่อให้ ตรงความต้องการของท่านมากที่สุด ก่อนตัดสินใจ</span><span style="font-size:14px;color:#FF00FF"> www.homesale54.com  </span>เป็นเว็บไซท์ที่ให้บริการ ข้อมูล ซื้อที่ดิน-ขายที่ดิน-เช่าที่ดิน ซื้อ-ขายบ้านมือสอง  บริหารงานโดย <br> <span style="color:blue" ><b> คุณ ศุภลักษณ์ ปัทมังสังข์  (085-8225775)</b><br/></span><span style="color:#006600">   </span>
</td>
				 </tr></table>
            </div>';


}
function Palert($msg)
   {
      echo '<script language="javascript">' ;
      echo 'window.alert("' . $msg  . '") ;' ;
      echo '</script>' ;
   }
function updatetable($tablename,$fname,$fvalue,$whereclause)
{

   $numfields = count($fname);

   $sql = "UPDATE "  . $tablename . " SET " ;
   for ($i = 0;$i <= $numfields-1;$i++)
	{
	    if ( $i <> $numfields-1)
		   {
			  $sql = $sql . $fname[$i] . " = '" ;
			  $sql = $sql . $fvalue[$i] . "'," ;
		   }

        else
		  {
 			  $sql = $sql . $fname[$i] . " = '" ;
			  $sql = $sql . $fvalue[$i] . "' " ;
          }

	}
	$sql = $sql . $whereclause ;

   openConnection();
   mysql_query($sql) or die(mysql_error() . ' <br> Sql is -->' . $sql) ;
   return $sql;



}
function genFieldSave($tablename) {

			 $sql = "select * from " . $tablename ;

			 $username = 'root' ;$passw = "1234" ;
 	         $conn = @mysql_pconnect("localhost", $username,$passw) ;
	         $sterr = "Cannot Open Databases username=" . $username . ";password=" . $passw ;
 	         mysql_select_db('bminsure', $conn)  or die($sterr);
 	         mysql_query("SET NAMES UTF8");


			 $result = mysql_query("SHOW COLUMNS FROM ". $tablename);
			 if (!$result) {
				echo 'Could not run query: ' . mysql_error();
			 }
			 $fieldnames=array();
			 if (mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_assoc($result)) {
				  $fieldnames[] = $row['Field'];
				}
			 }


			 $maxlength= 0;
			 for ($i=0;$i<count($fieldnames);$i++) {
				 $st2 = '$fname[]= "'  .  $fieldnames[$i] . '";' ;
				 if ($maxlength < strlen($st2)) {
					 $maxlength = strlen($st2) ;
				 }
			  }
              print 'function Insert_' . $tablename . "()  { " . "<br/>" ;
              print "<br/>" ;
			  print space(5) . '$stError = ""; ' . "<br/>" ;
			  for ($i=0;$i<count($fieldnames);$i++) {

				   $st3 = 'if (!isset($_POST["'  .  $fieldnames[$i] . '"]))' . space(5) .  '$errorFields[] ="' . $fieldnames[$i] . '";'  ;
				   print space(10) . $st3 . "<br/>";
				   //print strlen($st2) . " ---- Numspace=" . $numspace . "<br/>";;
			  }
			  print  space(10) .'print_r($errorFields); ' . "<br/>";



              print "<br/>" . space(5) . '$tablename="' . $tablename . '";' . "<br/>";
			  for ($i=0;$i<count($fieldnames);$i++) {
				   $st2 = '$fname[]= "'  .  $fieldnames[$i] . '" ;' ;
				   $st3 = '$fvalue[]= $_POST["'  .  $fieldnames[$i] . '"] ;' ;
				   $numspace =  $maxlength- strlen($st2) ;
				   $st = $st2 . space($numspace) .  $st3 ;

				   print space(10) . $st . "<br/>";
				   //print strlen($st2) . " ---- Numspace=" . $numspace . "<br/>";;
			  }
			  print "<br/>" ;
			  print space(5) .'InsertTable($tablename,$fname,$fvalue);' . "<br/>" ;
			  print '}' ;




/*				 $st2 = substr_replace($st, $st2,5) ;
				 $st3 = '$fvalue[]= $_POST["'  .  $fieldnames[$i] . '"];' ;
				 $st3 = substr_replace($st2, $st3,120) ;
///				 echo $st3 . "<br/>";
				 //echo '$fvalue[]= $_POST["'  . $fieldnames[$i] . '"];"' . "<br/>";

			 }
	        // print_r($fieldnames);
  */

      return $fieldnames;



             $db = "bminsure" ;
			 $qColumnNames = mysql_query($sql,$db) or die("mysql error");
             $numColumns = mysql_num_rows($qColumnNames);
			 print "Numcol=" . $numColumns;
             $x = 0;
             while ($x < $numColumns)
				{
					$colname = mysql_fetch_row($qColumnNames);
					$col[$colname[0]] = $colname[0];
					$x++;
				}

             print_r($col);

}

function space2($num) {

		 $st = "";
		 for ($i=0;$i<$num;$i++) {
             $st .= "&nbsp;";
		 }
		 return $st ;

}

function space3($num) {

		 $st = "";
		 for ($i=0;$i<$num;$i++) {
             $st .= " ";
		 }
		 return $st ;

}
function newString($num) {

         $st = "";
		 for ($i=0;$i<$num;$i++) {
             $st .= "&nbsp;";
		 }
		 return $st ;
}

function executeQuery($sql,$functionname)
{
         openConnection();
		 mysql_query($sql) or
		 ( die(mysql_error() . '<br><B>$sql is-->' . $sql .
		 '<font color=blue ><B> ON function ' . $functionname) ) . '</font>' ;


}

function writeGroup1($sql)
 {
	   // Zone ทรัพย์เด่น


	   $rs = getMultiValue($sql);
	   $num = 1;

	   while ($row = mysql_fetch_assoc($rs))
	   {
          if ($num == 1 ) {
	         //echo '<div style="float:left;width:698px;border:10px solid green;margin-left:0px">&nbsp;';
		  }
		  writeImage($row[realestate_ID],$row[transno], $row[productcode], $row[subject],$row[price],$row[priceDesc]);


/*
          echo '<div align="center" style="padding-top:5px" class="fontbox">' ;
		  echo '<a href="advertise/assetlist.php?assetid=' . $row[realestate_ID] . '"><span style="color:black;">';
		  echo   $row[subject]  .  '</span></a>';
		  echo '<p style="color:red">' ;
		  if ($row[price] != 0 )
		  {
		     echo "ราคา &nbsp;". formatn($row[price])  .   "&nbsp;บาท" ;
		  }
		  else
		  {
             echo  $row[priceDesc];
		  }
		  echo '</p></div>';
	      echo '</div>';
		  //echo '<div style="color:red">' . $row[price]  . '</div>';

*/
		  $num++;
		  if ($num == 4) {
	        //echo '</div>'; $num=0;

		  }


	   }


 }

function writeImage($realestate_ID,$transno, $productcode,$subject,$price,$priceDesc)
{
	      echo '<div style="max-width:190px;min-width:190px;max-height: 280px;min-height: 280px;border: 0px solid yellow;float:left;width:200px;pading:0px;margin-top:5px;margin-left:20px;overflow:hidden">';
	        echo '<div  style="align:center;font-size:12px;  max-width:190px;min-width:190px;width:190px;height:20px;background-color:#007CB9;color:white;font-weight:bold;padding-top:5px;border:1px solid blue">รหัสทรัพย์  : ' . $productcode  . '</div><a href="assetlist.php?assetid=';
		      echo $realestate_ID   . '">';
	          $imgname = "advertise/imageupload/" . $realestate_ID  . '_0_' . $transno . ".jpg";
		      if (file_exists($imgname)) {
	             echo '<div><img src="' . $imgname . '" width="190" HEIGHT="180" ALT=""></div>' ;
		      }
		      else
		      {
                 $imgname = "images/nopic.jpg" ;
                 echo '<div><img src="' . $imgname . '" width="190" HEIGHT="180" ALT=""></div>' ;
		       }

		  echo   '</a>' ;
		  if ($_SESSION['userlevel']==9) {
			  echo '<div>';
			  echo '<TABLE>';
			  echo '<TR>';
			  echo '<TD><A HREF="/advertise/advertise.php?action=edit&assetid=' . $realestate_ID  ;
			  echo '"><IMG SRC="images/edit.png" WIDTH="22" HEIGHT="22"  ALT=""></A></TD>';
			  echo '	<TD></TD>' ;

			  echo '</TR></TABLE>';
			  echo '</div>';
		      //echo '</div>';
		  }
		  echo '<div  style="align:center;padding-top:5px" class="fontbox">' ;
		  echo '<a href="assetlist.php?assetid=' . $realestate_ID  . '"><span style="color:black;">';
		  echo   $subject   .  '</span></a>';
		  echo '<p style="color:red">' ;
		  if ($price  != 0 )
		  {
		     echo "ราคา &nbsp;". formatn($price)  .   "&nbsp;บาท" ;
		  }
		  else
		  {
             echo  $priceDesc;
		  }
		  echo '</p></div>';
	      echo '</div>';

}


function updateGuestVisited() {

	     $sql = "update statistic set membervisited = membervisited+1 ";
		 $sql = executeQuery($sql,"UpdateQuestVisited");

         $sql = "update daystatistic set num = num+1 where thisday='" . date("Y-m-d") . "'";
		// print $sql;
		 $sql = executeQuery($sql,"UpdateQuestVisited");
		//$sql = "update statistic set guestvisited = guestvisited+1 ";

		 $thistime= date("Y-m-d H:i:s") ;
		 $numday = 0 ;
		 $numhour = 1;
		 $numminute = 0;
		 $endtime = adddatetime($thistime,$numday,$numhour,$numminute);


         $sql = "INSERT INTO sessioncontrol(sessionid,starttime,endtime) values('" .
		 $_SESSION['sessid'] . "','" . $thistime . "','" . $endtime . "')" ;
		 $sql = executeQuery($sql,"UpdateQuestVisited");



}


function updateMemberVisited() {

	     $sql = "update statistic set membervisited = membervisited+1,guestvisited = guestvisited-1  ";
		 $sql = executeQuery($sql,"UpdateMemberVisited");

}

function genInsertDayStatic($year)
{
         //2012-04-04
		 return;
		 $sql = "delete from daystatistic";
		 $sql = executeQuery($sql,"UpdateMemberVisited");
		 $num = 0;
         for ($thismonth=1;$thismonth <13 ;$thismonth++) {
			 if ($thismonth==1 || $thismonth==3 || $thismonth==5 || $thismonth==7 || $thismonth==8 || $thismonth==10 || $thismonth==12 ) {
				 $lastday = 31 ;
			 }
			 else
			 {  $lastday = 30 ;  }
			  if ($thismonth==2 ) { $lastday = 28 ;  }
			  for ($thisday=1;$thisday <$lastday+1  ;$thisday++) {
				   if ($thismonth <10 ) {$thismonth2 = "0" . $thismonth;}
				   if ($thisday <10 ) {$thisday = "0" . $thisday;}
		           $thisdaystr = $year . '-' . $thismonth2 . '-' . $thisday ;
				   print $thisdaystr . '<br/>';
		           $sql = "INSERT INTO daystatistic(thisday,num) VALUES('" . $thisdaystr . "',0)" ;
		           $sql = executeQuery($sql,"UpdateMemberVisited");
			  }
			  //if ($num++ == 2 ) { return;}
		 }


}

function  adddatetime($startdate,$numday,$numhour,$numminute)
{


	$newtime = strtotime($startdate . '+' .$numday .' days');
	$newtime = date('Y-m-d H:i:s', $newtime);

    $newtime = strtotime($newtime . ' + ' . $numhour . ' hours');
    $newtime = date('Y-m-d H:i:s', $newtime);

    $newtime = strtotime($newtime . ' + ' .$numminute . ' minutes');
   // Palert($numminute);
    $newtime = date('Y-m-d H:i:s', $newtime);

	return ($newtime);
}

function chkDir($full_path) {
//  $full_path = "/var/www/vhosts/example.com/httpdocs/images/items_images";
   if ($handle = opendir("$full_path")) {
      while (false !== ($file = readdir($handle))) {
        if(is_dir($file)) continue;
        else echo $file;
    }
}
}


function getPathPic($id)
{
	  $sql = "select realestateCode,landdesc from realestate where realestate_id = " . $id ;
	  $rs= getMultiValue($sql);
      $row = mysql_fetch_assoc($rs);

	  $realestateCode= $row['realestateCode'];
	  if ( $realestateCode== "1" ) { $thispath = "asset/land/" . $row['landdesc'] . "/" . $id ."/" ; }
	  if ( $realestateCode== "2" ) { $thispath = "asset/house/" . $row['landdesc'] . "/" . $id ."/" ; }
	  if ( $realestateCode== "3" ) { $thispath = "asset/townhouse/" . $row['landdesc'] . "/" . $id ."/" ; }

	  return $thispath;


}

function createFile($strFileName) {

//$strFileName = "thaicreate.txt";
$objFopen = fopen($strFileName, 'w');
$strText1 = "I Love homesale54.com Line1\r\n";
fwrite($objFopen, $strText1);
$strText2 = "I Love homesale54.com Line2\r\n";
fwrite($objFopen, $strText2);
$strText3 = "I Love homesale54.com Line3\r\n";
fwrite($objFopen, $strText3);
//$strFileName = iconv("UTF-8","windows-874",$strFileName);
if($objFopen)
{
	echo "******* <font color=red size=15px> File $strFileName </font> is writed.******" ;
}
else
{
	echo "File can not write";
}

fclose($objFopen);

}

function showBot() {

	     $sql = "select * from bot" ;
         $rs = getMultiValue($sql) ;
		 echo '<table width="600" border="1">';
		 while ($row = mysql_fetch_array($rs))  {
			   echo '<tr>';
			   echo '<td>' ;
			   echo $row[0] . '</td>';
			   echo '<td>' ;
			   echo $row[1] . '</td>';
			   echo '<td>' ;
			   echo $row[2] . '</td>';
			   echo '</tr>' ;
		 }
		 echo '</table>';
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function linkCSS($cssfilename,$cssfilenameIE) {

	switch ($_SESSION['browser']) {
    case "CHROME" :
        echo '<link rel="stylesheet" href="' .$cssfilename . '"  type="text/css" media="screen, projection"/>';
        break;
    case "FF" :
        echo '<link rel="stylesheet" href="' .$cssfilenameIE . '"  type="text/css" media="screen, projection"/>';
        break;
    case "IE6" :
        echo '<link rel="stylesheet" href="' .$cssfilenameIE . '"  type="text/css" media="screen, projection"/>';
        break;
    case "IE7" :
        echo '<link rel="stylesheet" href="' .$cssfilenameIE . '"  type="text/css" media="screen, projection"/>';
        break;
    case "IE8" :
        echo '<link rel="stylesheet" href="' .$cssfilenameIE . '"  type="text/css" media="screen, projection"/>';
        break;
    default:
		echo '<link rel="stylesheet" href="' .$cssfilename . '"  type="text/css" media="screen, projection"/>';
        break;
}

/*function right($value, $count){
    return substr($value, ($count*-1));
}

function left($string, $count){
    return substr($string, 0, $count);
}




 */
}



function frameLogin() {

	     echo '<div  id="login" style="position:relative;left:-5px;width:940px;height:30px; align:right;color:white;background-image:url(../images/bluebg.gif) ">';

         if ($_SESSION['islogin'] == 0 )  {
		     echo'<form name="form1" id="form1" method="POST" ACTION="../checkusername.php">';
             echo '<table    style="font-size:12px;color:white"><tr><td style="width:920px">ชื่อผู้ใช้ :&nbsp;<INPUT TYPE="text" name="username" id ="username" size=6>
             &nbsp;&nbsp;รหัสผ่าน :&nbsp;&nbsp;<INPUT TYPE="password" name="password" id="password" size=6>';
             ?>
              <INPUT TYPE="submit" value= "ล๊อคอิน" onclick="" style="color:white" >
              &nbsp;&nbsp;<a href="#">&nbsp;&nbsp;ลืมรหัสผ่าน <IMG SRC="../images/hint2.png" WIDTH="20" HEIGHT="20"  ALT=""></a><a href="../register.php?action=register">&nbsp;&nbsp;สมัครสมาชิก</a></td>
              </tr></table></form></div>
     <?php }
	       else {
             echo '<table  style="padding:3px;width:940px;height:30px;border:0px solid grey; background-image:url(images/bluebg.gif);font-size:12px;color:white;font-size:14px"><tr>';
			 echo '<td >ชื่อผู้ใช้ :&nbsp;' ;
			 echo $_SESSION['member_name'] . "&nbsp;&nbsp;&nbsp;&nbsp;";
			 echo '</td>';
			 echo '<td width="90px">';
			 echo 'ประเภทสมาชิก';
			 echo '</td><td width="10px">:</td><td>';
			 //Palert($_SESSION['userlevel']);
			 if ($_SESSION['userlevel'] == 1) {
				 echo 'สมาชิกทั่วไป' ;
			 }
			 else  {
				 if ($_SESSION['userlevel'] == 9) {
					 echo 'ผู้ดูแลระบบ' ;

				 }
			 }
			 echo '</td><td><a href="index.php?action=logout">Log out </a></td>';
			 echo '</td><td width=500px>&nbsp;</td></tr></table></div>';



		 }
		// echo '</div>';

		 ?>
          <?php

}

 function writeComboBox($sql,$varname,$onchange)  {


		  $rs = getMultiValue($sql);
		  echo '<select id= "' . $varname . '" name="' .$varname .'" onBlur="' . $onchange. '">';
		  $num=0;
		  while ($row = mysql_fetch_array($rs))  {
                echo '<option value="' . $row[0] .'" selected>' . $row[1] ;
				$num++;
		  }
		  echo ' </select>';

 }

 function writeComboBoxByArray($DataArray,$varname,$onchange,$defaultTable)  {



		  echo '<select id= "' . $varname . '" name="' .$varname .'" onChange="' . $onchange. '">';
		  $num=0;
		  for ($i=0;$i<=count($DataArray)-1;$i++) {	
			if ($defaultTable == $DataArray[$i]) {			  
              echo '<option value="' . $DataArray[$i] .'" selected>' . $DataArray[$i] ;
			} else {
              echo '<option value="' . $DataArray[$i] .'">' . $DataArray[$i] ;
			}
		  }
		  echo ' </select>';

 }



 function writeArticle($articleid) {

          $sql = "select article_content from article_lib where article_id='" . $articleid . "'";
          $st = getValue($sql);
		  echo $st;

 }
 function Pmktime($startdate)
 {

   // mktime ( ชั่วโมง, นาที, วินาที, เดือน, วัน, ปี);
   $startdate2 = explode("-",$startdate);
   $year = $startdate2[0];
   $month = $startdate2[1];
   $startdate3 = explode(" ",$startdate2[2]);
   $day = $startdate3[0] ;



$timestr = explode(" ",$startdate2[2]);
$startdate4 = explode(":",$startdate2[2]);
$startdate5 = explode(" ",$startdate2[2]);

$hourtmp = explode(":",$timestr[1]);

$hour=$hourtmp[0];

$minute =$startdate4[1] ;

$sec = $startdate4[2];

//print($year . ';' . $month . ';' . $day . "-" . $hour . "-" . $minute . "-" . $sec . '<br>')  ;
return (mktime($hour,$minute,$sec,$month,$day,$year)
);
}


function getColumnNames($table){

          require_once("configuration.php");
		  $clsConfig = new PConfig;


          $username = $clsConfig->username  ;$passw = $clsConfig->password ;
	      $conn = @mysql_pconnect("localhost", $username,$passw) ;
	      $sterr = "Cannot Open Databases username=" . $username . ";password=" . $passw ;
 	      mysql_select_db($clsConfig->dbname, $conn)  or die($sterr);

//         $username = 'root' ;$passw = "1234" ;
	 /*    $conn = @mysql_pconnect("localhost", $username,$passw) ;
//	     $sterr = "Cannot Open Databases username=" . $username . ";password=" . $passw ;
 	     mysql_select_db('bminsure', $conn)  or die($sterr);
		 */
         $fields = array();

         $res=mysql_query("SHOW COLUMNS FROM $table");
         while ($x = mysql_fetch_assoc($res)){
             $fields[] = $x['Field'];
         }
          //foreach ($fields as $f) { echo "<br>Field name: ".$f; }



	return $fields;
}



function utf8_substr($str,$start_p,$len_p)
{
   preg_match_all("/./u", $str, $ar);

   if(func_num_args() >= 3) {
       $end = func_get_arg(2);
       return join("",array_slice($ar[0],$start_p,$len_p));
   } else {
       return join("",array_slice($ar[0],$start_p));
   }
}
// การใช้งาน
// $start_p คือตำแหน่งเริ่มต้นตัดข้อความ
// $len_p คือจำนวนตัวอักษรที่ต้องการแสดง
// $data="ข้อความทดสอบ ข้อความทดสอบ ข้อความทดสอบ ข้อความทดสอบข้อความทดสอบ ";
// echo utf8_substr($data,0,30);

function  bathformat($number) {
  $numberstr = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
  $digitstr = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');

  $number = str_replace(",","",$number); //ลบ comma
  $number = explode(".",$number); //แยกจุดทศนิยมออก

  //เลขจำนวนเต็ม
  $strlen = strlen($number[0]);
  $result = '';
  for($i=0;$i<$strlen;$i++) {
    $n = substr($number[0], $i,1);
    if($n!=0) {
      if($i==($strlen-1) AND $n==1){ $result .= 'เอ็ด'; }
      elseif($i==($strlen-2) AND $n==2){ $result .= 'ยี่'; }
      elseif($i==($strlen-2) AND $n==1){ $result .= ''; }
      else{ $result .= $numberstr[$n]; }
      $result .= $digitstr[$strlen-$i-1];
    }
  }

  //จุดทศนิยม
  $strlen = strlen($number[1]);
  if ($strlen>2) { //ทศนิยมมากกว่า 2 ตำแหน่ง คืนค่าเป็นตัวเลข
    $result .= 'จุด';
    for($i=0;$i<$strlen;$i++) {
      $result .= $numberstr[(int)$number[1][$i]];
    }
  } else { //คืนค่าเป็นจำนวนเงิน (บาท)
    $result .= 'บาท';
    if ($number[1]=='0' OR $number[1]=='00' OR $number[1]=='') {
      $result .= 'ถ้วน';
    } else {
      //จุดทศนิยม (สตางค์)
      for($i=0;$i<$strlen;$i++) {
        $n = substr($number[1], $i,1);
        if($n!=0){
          if($i==($strlen-1) AND $n==1){$result .= 'เอ็ด';}
          elseif($i==($strlen-2) AND $n==2){$result .= 'ยี่';}
          elseif($i==($strlen-2) AND $n==1){$result .= '';}
          else{ $result .= $numberstr[$n];}
          $result .= $digitstr[$strlen-$i-1];
        }
      }
      $result .= 'สตางค์';
    }
  }
  return $result;
}

function  Aspace($num) {

         for ($i=0;$i<$num;$i++) {
              $st = $st . "&nbsp;" ;

		 }

         return $st ;

}

function smtpmail( $email , $subject , $body )
{

  /*  $mail = new PHPMailer();
    $mail->IsSMTP();
      $mail->CharSet = "utf-8";  // ในส่วนนี้ ถ้าระบบเราใช้ tis-620 หรือ windows-874 สามารถแก้ไขเปลี่ยนได้
    $mail->Host     = "ssl://smtp.gmail.com"; //  mail server ของเรา
    $mail->SMTPAuth = true;     //  เลือกการใช้งานส่งเมล์ แบบ SMTP
    $mail->Username = "nutv99@gmail.com";   //  account e-mail ของเราที่ต้องการจะส่ง
    $mail->Password = "16102510";  //  รหัสผ่าน e-mail ของเราที่ต้องการจะส่ง

    $mail->From     = "nutv99@gmail.com";  //  account e-mail ของเราที่ใช้ในการส่งอีเมล
    $mail->FromName = "ชื่อผู้ส่ง"; //  ชื่อผู้ส่งที่แสดง เมื่อผู้รับได้รับเมล์ของเรา
    $mail->AddAddress($email);            // Email ปลายทางที่เราต้องการส่ง(ไม่ต้องแก้ไข)
    $mail->IsHTML(true);                  // ถ้า E-mail นี้ มีข้อความในการส่งเป็น tag html ต้องแก้ไข เป็น true
    $mail->Subject     =  $subject;        // หัวข้อที่จะส่ง(ไม่ต้องแก้ไข)
    $mail->Body     = $body;                   // ข้อความ ที่จะส่ง(ไม่ต้องแก้ไข)
    $result = $mail->send();
	Palert( "Mail Result===>" . $result);
    return $result;
	*/
	date_default_timezone_set('Asia/Bangkok');
	require("PHPMailer/class.phpmailer.php");  // ประกาศใช้ class phpmailer กรุณาตรวจสอบ ว่าประกาศถูก path
	$mail             = new PHPMailer();

$body             = "gdssdh";
//$body             = eregi_replace("[\]",'',$body);

$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "ssl://smtp.gmail.com"; // SMTP server
$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->CharSet = "utf-8";
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
$mail->Username   = "nutv99@gmail.com";  // GMAIL username
$mail->Password   = "16102510";            // GMAIL password

$sHeader = "!!! ลูกค้าได้แจ้ง การรับชำระเงิน เมื่อเวลา  " . getCurrentDateTime() ;
$mail->SetFrom('nutv99@gmail.com',$sHeader  );

//$mail->AddReplyTo("user2@gmail.com', 'First Last");

$mail->Subject    = "PRSPS password";

//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$body =  "ลูกค้าได้แจ้งเรื่องการรับชำระเงิน ผ่านทางเวบไซท์   กรุณา Login เข้าสู่ระบบด้วยครับ คลิก ==>www.hdlightcatcherz.com/home/fbPostwall2/index.php";

$mail->MsgHTML($body);

$address = "nutv99@gmail.com";
$mail->AddAddress($address, "user2");

//$mail->AddAttachment("images/phpmailer.gif");      // attachment
//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}


}

function writeTitle($pageID) {


		 $sql = "SELECT Title,metaDesc from metatagmaster where	pageID='" . $pageID . "'" ;
		 $rs = getMultiValue($sql);
		 $numrow = mysql_num_rows($rs) ;
		 $row = mysql_fetch_assoc($rs);
		 if ($numrow==0) {
             echo "<span style='font-size:30px;color:red'>ไม่พบ  Meta Tag รหัส "  . $pageID . "</span>";
			 return 0;
		 }
		 $st = '<meta name="description" content="BMInsure.net ' . $row['metaDesc'] . '"/>' ;
		 echo $st ;
		 echo '<title>' . $row['Title'] . '</title>' ;

		 return 1;

}

function getTitle_MetaDesc($pageID) {


		 $sql = "SELECT Title,metaDesc from metatagmaster where	pageID='" . $pageID . "'" ;
		 $rs = getMultiValue($sql);
		 $numrow = mysql_num_rows($rs) ;
		 $row = mysql_fetch_assoc($rs);
		 if ($numrow==0) {
             echo "<span style='font-size:30px;color:red'>ไม่พบ  Meta Tag รหัส "  . $pageID . "</span>";
			 return 0;
		 }
		 $st = '<meta name="description" content="BMInsure.net ' . $row['metaDesc'] . '"/>' ;
		 echo $st ;
		 echo '<title>' . $row['Title'] . '</title>' ;

		 return 1;

}


function datediff( $str_interval, $dt_menor, $dt_maior, $relative=false){

       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);

       switch( $str_interval){
           case "y":
               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; break;
           case "m":
               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h":
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i":
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s":
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
   }

/* Enjoy and feedback me ;-) */



function dateDifference($startDate, $endDate)        {


            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);

			$diff= -$endDate + $startDate ;
			//Palert($startDate. "-" . $endDate) ;
			define('DAY',60*60*24, true);
            define('MONTH',DAY*30, true);
            define('YEAR',DAY*365, true);

            $years = floor($diff / (YEAR));
            $months = floor(($diff - $years * YEAR) / (MONTH));
            $days = floor(($diff - $years * YEAR - $months*MONTH ) / (DAY));

			Palert($days);


            return array($years, $months, $days);



           // if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
           //     return 'false';

            $years = date('Y', $endDate) - date('Y', $startDate);

            $endMonth = date('m', $endDate);
            $startMonth = date('m', $startDate);

            // Calculate months
            $months = $endMonth - $startMonth;
            if ($months <= 0)  {
                $months += 12;
                $years--;
            }
            if ($years < 0)
                return false;

            // Calculate the days
                        $offsets = array();
                        if ($years > 0)
                            $offsets[] = $years . (($years == 1) ? ' year' : ' years');
                        if ($months > 0)
                            $offsets[] = $months . (($months == 1) ? ' month' : ' months');
                        $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

                        $days = $endDate - strtotime($offsets, $startDate);
                        $days = date('z', $days);


            //Palert("Month=" . $months ) ;
			$sReturn[] = $years ;
			$sReturn[] = $months ;
			$sReturn[] = $days ;
			return $sReturn  ;
            //return array($years, $months, $days);
   }

 function Zip($source, $destination)
{
    if (extension_loaded('zip') === true)
    {
        if (file_exists($source) === true)
        {
            $zip = new ZipArchive();

            if ($zip->open($destination, ZIPARCHIVE::CREATE) === true)
            {
                $source = realpath($source);

                if (is_dir($source) === true)
                {
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

                    foreach ($files as $file)
                    {
                        $file = realpath($file);
						//print $file . "<br/>";

                        if (is_dir($file) === true)
                        {
                            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                        }

                        else if (is_file($file) === true)
                        {
                            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                        }
                    }
                }

                else if (is_file($source) === true)
                {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            }

            return $zip->close();
        }
    }

    return false;
}


function fillSelectList($listName,$sql,$defaultValue,$fname0,$fname1) {

   	     $rs = getMultiValue($sql) ;
?>
         <select name="<?=$listName?>" id="<?=$listName?>"> <?php
            if ($defaultValue=="" || $defaultValue=="-1" || $defaultValue=="0") { ?>
                 <option value="-1" selected>*** กรุณาคลิกเลือก ***
			     <?php
				    while ($row = mysql_fetch_assoc($rs))  {    ?>
                        <option value="<?=$row[$fname0]?>"><?=$row[$fname1]?>
                 <?php }
			 } else {
 				    while ($row = mysql_fetch_assoc($rs) ) {
					     if ($row[$fname0] == $defaultValue) { ?>
                            <option value="<?=$row[$fname0]?>" SELECTED><?=$row[$fname1]?>
						<?php } else { ?>
                            <option value="<?=$row[$fname0]?>"><?=$row[$fname1]?>
						<?php } ?>
                 <?php }
			 }

?>
             </select>
	 <?php
}

function fillRadioList($radioName,$sql,$defaultValue,$fname0,$fname1) {

   	        $rs = getMultiValue($sql) ;
			while ($row = mysql_fetch_assoc($rs))  {
				$thisID = $radioName . $row[$fname0] ;
			   if ($defaultValue==$row[$fname0]) {
				   ?>
				   <input type="radio" checked selected name="<?=$radioName?>"  id="<?=$thisID?>" value="<?=$row[$fname0]?>"><?=$row[$fname1]?>&nbsp;&nbsp;
               <?php } else { ?>
                    <input type="radio" name="<?=$radioName?>"  id="<?=$thisID?>" value="<?=$row[$fname0]?>"  ><?=$row[$fname1]?>

			  <?php }
			}

}

function checkExistData($tablename,$whereClause) {

	         $sql = "select count(*) from " . $tablename . " " . $whereClause ;
			 $numrow = getValue($sql) ;
			 if ($numrow <= 0 ) {
				return 0 ;
			 } else {
               return 1 ;
			 }



}

function checkExistDataPDO($pdo,$tablename,$whereClause,$params) {

         $sql = "select count(*) from " . $tablename . " " . $whereClause ;			 
		 //$sql ='SELECT count(*) FROM ItemMaster WHERE ItemCode = "Test999" ';
		// echo $sql ;
		 //print_r($params); 
		 $doworkSuccess = false ;
		 try {        
		    
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);

		    $doworkSuccess = true ;
		    //$pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 } 
		  
		 while($row = $rs->fetch( PDO::FETCH_NUM )) {
		    return $row[0] ;
		 }
		 
		 
		


}

function force_download($filename) {
    $filedata = @file_get_contents($filename);

    // SUCCESS
    if ($filedata)
    {
        // GET A NAME FOR THE FILE
        $basename = basename($filename);

        // THESE HEADERS ARE USED ON ALL BROWSERS
        header("Content-Type: application-x/force-download");
        header("Content-Disposition: attachment; filename=$basename");
        header("Content-length: " . (string)(strlen($filedata)));
        header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

        // THIS HEADER MUST BE OMITTED FOR IE 6+
        if (FALSE === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE '))
        {
            header("Cache-Control: no-cache, must-revalidate");
        }

        // THIS IS THE LAST HEADER
        header("Pragma: no-cache");

        // FLUSH THE HEADERS TO THE BROWSER
        flush();

        // CAPTURE THE FILE IN THE OUTPUT BUFFERS - WILL BE FLUSHED AT SCRIPT END
        ob_start();
        echo $filedata;
    }

    // FAILURE
    else
    {
        die("ERROR: UNABLE TO OPEN $filename");
    }
}

function writeProvince($provinceVarName,$default) {

	     $sql = 'select PROVINCE_ID,PROVINCE_NAME from province ORDER BY PROVINCE_NAME ';
	     $rs = getMultiValue($sql) ;

?>

        <select class="form-control selBox99" name="<?=$provinceVarName?>" id="<?=$provinceVarName?>" tabindex="13" style='font-size:16px;height:35px' onchange="doCallAjaxManageProvince('p',this.value,'<?=$provinceVarName?>')" saved datatype="text" data-rule-required="true" onblur="isValueCheck(this.id,'-1')">
			<?php
		       echo '<OPTION VALUE="-1" tabindex="13"  >---เลือกจังหวัด---';
 	           while ($row2 = mysql_fetch_array($rs)) {
                  if (trim($row2[1])== trim($default) ) {
                    echo '<OPTION   VALUE="' . $row2[0] . '" selected>' . $row2[1] ;
                  } else {
       	            echo '<OPTION   VALUE="' . $row2[0] . '">' . $row2[1] ;
				  }
		       }
            ?>
		    </select>
			<span class="error1" style="display: none;">
              <i class="error-log fa fa-exclamation-triangle"></i>
          </span>

		 <input type="hidden" name="provincetmp" id="provincetmp" saved datatype='text'>	
		 <input type="hidden" name="provinceNametmp" id="provinceNametmp" saved datatype='text'>	

<script>
function doCallAjaxManageProvince(Mode,provinceCode,divAmhurID,provinceVarName) {

     document.getElementById("provincetmp").value = document.getElementById("provincecode").value

      

     var sel = document.getElementById('provincecode') ;
	 document.getElementById("provinceNametmp").value = sel.options[sel.selectedIndex].text ;


 	 HttPRequest = getHTTP();
     var url = '//lovetoshopmall.com/labTest/Ajax.php' ;
     var pmeters = "Mode=getAmphur";
	 pmeters += "&provincecode=" + provinceCode ;
	 //alert(pmeters);


	 HttPRequest.open("POST",url,true);
	 HttPRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     HttPRequest.setRequestHeader("Content-length", pmeters.length);
	 HttPRequest.setRequestHeader("Connection", "close");
	 HttPRequest.send(pmeters);
	 HttPRequest.onreadystatechange = function()	{
	   if(HttPRequest.readyState == 3)    {	 }
	   if(HttPRequest.readyState == 4)	  {
         //alert(HttPRequest.responseText) ;
		 //console.log("Response",HttPRequest.responseText)
	     st = HttPRequest.responseText ;
		 document.getElementById('divAmhurID').innerHTML = st;
	   }
	  } // end onreadystatechange
				 

}

function doCallAjaxManageAmphur(Mode,provinceCode,AmphurCode) {

     document.getElementById("amphurtmp").value = document.getElementById("amphurid").value ; 

     
	 




   var sel = document.getElementById('amphurid') ;
   document.getElementById("amphurNametmp").value = sel.options[sel.selectedIndex].text ;




 	 HttPRequest = getHTTP();
     var url = '//lovetoshopmall.com/labTest/Ajax.php' ;
     var pmeters = "Mode=getTumbol";
	 pmeters += "&provincecode=" + provinceCode ;
	 pmeters += "&amphurcode=" + AmphurCode ;


	 HttPRequest.open("POST",url,true);
	 HttPRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     HttPRequest.setRequestHeader("Content-length", pmeters.length);
	 HttPRequest.setRequestHeader("Connection", "close");
	 HttPRequest.send(pmeters);
	 HttPRequest.onreadystatechange = function()	{
	   if(HttPRequest.readyState == 3)    {	 }
	   if(HttPRequest.readyState == 4)	  {
        // alert(HttPRequest.responseText) ;
	     st = HttPRequest.responseText ;
		 document.getElementById('tumbolID').innerHTML = st;
	   }
	  } // end onreadystatechange
				 

}


function AjaxGetPostCode(provinceCode,AmphurCode,TumBolCode) {

         document.getElementById("tumboltmp").value = document.getElementById("tumbolid").value;
     

        var sel = document.getElementById('tumbolid') ;
   document.getElementById("tumbolNametmp").value = sel.options[sel.selectedIndex].text ;
   
return;

     return;
 	 HttPRequest = getHTTP();
     var url = '//lovetoshopmall.com/labTest/Ajax.php' ;
     var pmeters = "Mode=getPostCode";
	 pmeters += "&provincecode=" + provinceCode ;
	 pmeters += "&amphurcode=" + AmphurCode ;
	 pmeters += "&tumbolcode=" + AmphurCode ;


	 HttPRequest.open("POST",url,true);
	 HttPRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     HttPRequest.setRequestHeader("Content-length", pmeters.length);
	 HttPRequest.setRequestHeader("Connection", "close");
	 HttPRequest.send(pmeters);
	 HttPRequest.onreadystatechange = function()	{
	   if(HttPRequest.readyState == 3)    {	 }
	   if(HttPRequest.readyState == 4)	  {
        // alert(HttPRequest.responseText) ;
	     st = HttPRequest.responseText ;
		 document.getElementById('tumbolID').innerHTML = st;
	   }
	  } // end onreadystatechange
				 

}


</script>



<?php

}

function writeAmphur($provinceID,$amphurVarName) {


           $sql = 'SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE PROVINCE_ID= ' . $provinceID . '  ORDER BY AMPHUR_NAME' ;
	       $rs = getMultiValue($sql) ;
		   ?>
		   <select class="form-control selBox99" name="<?=$amphurVarName?>" id="<?=$amphurVarName?>" tabindex="13"  style='font-size:16px;height:35px' onchange="doCallAjaxManageAmphur('a','<?=$provinceID?>',this.value)" saved datatype="text" onblur="isValueCheck(this.id,'-1')" >

		   <?php
		   if (!isset($_GET['assetid'])) {
		      echo '<OPTION VALUE="-1" >---เลือกอำเภอ--- ';
		      while ($row2 = mysql_fetch_array($rs)) {
 	           echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ;
	          }
		   } else {
		      while ($row2 = mysql_fetch_array($rs))
              {
				 if ($row2[0] !== $amphurCode) {
 	                echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ;
				 } else {
					 echo '<OPTION VALUE="' . $row2[0] . '" SELECTED>' . $row2[1] ;
				 }
	          }
		 }
	     echo '</SELECT>'; 
		 ?>
		 <input type="hidden" name="amphurtmp" id="amphurtmp" saved datatype='text'>	 
		 <input type="hidden" name="amphurNametmp" id="amphurNametmp" saved datatype='text'>	 
		 <?php
}

function writeAmphurByProvinceName($provinceName,$amphurVarName,$default) {

	       $sql = "select * from province where PROVINCE_NAME='" . $provinceName ."'"; 
		   $provinceID = getValue($sql);

	       

           $sql = 'SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE PROVINCE_ID= ' . $provinceID . '  ORDER BY AMPHUR_NAME' ;
	       $rs = getMultiValue($sql) ;
		   ?>
		   <select class="form-control selBox99" name="<?=$amphurVarName?>" id="<?=$amphurVarName?>" tabindex="13"  style='font-size:16px;height:35px' onchange="doCallAjaxManageAmphur('a','<?=$provinceID?>',this.value)" saved datatype="text" onblur="isValueCheck(this.id,'-1')">

		   <?php
		   if (!isset($_GET['assetid'])) {
		      echo '<OPTION VALUE="-1" >---เลือกอำเภอ--- ';
		      while ($row2 = mysql_fetch_array($rs)) {
                if (trim($row2[1]) == trim($default) ) {
                  echo '<OPTION VALUE="' . $row2[0] . '" SELECTED>' . $row2[1] ;
                } else {
 	              echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ;
				}
	          }
		   } else {
		      while ($row2 = mysql_fetch_array($rs))
              {
				 if ($row2[1] !== $default) {
 	                echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ;
				 } else {
					 echo '<OPTION VALUE="' . $row2[0] . '" SELECTED>' . $row2[1] ;
				 }
	          }
		 }
	     echo '</SELECT>'; 
		 ?>
		 <input type="hidden" name="amphurtmp" id="amphurtmp" saved datatype='text'>	<input type="hidden" name="amphurNametmp" id="amphurNametmp" saved datatype='text'>		 
		 <?php
}


function writeTumBol($provinceID,$amphurID,$tumBolVarName) {
           
?>
     <SELECT class="form-control selBox99" name="<?=$tumBolVarName?>" 
	 id="<?=$tumBolVarName?>" tabindex="15"  style="font-size:16px;height:35px" onchange="AjaxGetPostCode('<?=$provinceID?>','<?=$amphurID?>',this.value)" saved datatype="text" 
	 onblur="isValueCheck(this.id,'-1')">
<?php
         // Palert($districtCode);
		  $districtid= '';
		  if (!isset($_GET['assetid'])) {

			 $sql = 'select DISTRICT_ID,DISTRICT_NAME FROM district where PROVINCE_ID='. $provinceID. '  and AMPHUR_ID= '.  $amphurID ;
	         $rs3 = getMultiValue($sql) ;
			 
		     echo '<OPTION VALUE="-1" >---เลือกตำบล--- ';
		     while ($row2 = mysql_fetch_array($rs3))
       	        {
				   if ($districtid != $row2[0])
				   { echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ; }
                   else
				   {  echo '<OPTION VALUE="' . $row2[0] . '" SELECTED>' . $row2[1] ; }
			    }
		  } else {
			  //Palert("Case 2");
			  $sql = 'select DISTRICT_ID,DISTRICT_NAME FROM district where PROVINCE_ID=' . $provinceCode .  ' and AMPHUR_ID= ' . $amphurCode ;
	          $rs3 = getMultiValue($sql) ;
			  while ($row2 = mysql_fetch_array($rs3))
       	        {
				   if ($districtCode !== $row2[0])
				   { echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ; }
                   else
				   {  echo '<OPTION VALUE="' . $row2[0] . '" SELECTED>' . $row2[1] ; }
			    }
		  } 

	     echo '</SELECT>';
		?>
	    <input type="hidden" name="tumboltmp" id="tumboltmp" saved datatype='text'>		 
		<input type="hidden" name="tumbolNametmp" id="tumbolNametmp" saved datatype='text'>		 

		<?php

}

function writeTumBolByProvinceAndAmphurName($provinceName,$amphurName,$tumBolVarName) {

           $sql = "select PROVINCE_ID from province where PROVINCE_NAME='" . $provinceName ."'"; 
		 //  echo "$sql";
		   $provinceID = getValue($sql);

		   $sql = "select AMPHUR_ID from amphur where PROVINCE_ID='" . $provinceID ."' and AMPHUR_NAME='" . $amphurName . "'"; 
		   //echo "$sql";
		   $amphurCode = getValue($sql);

	   
           

           echo '<SELECT class="form-control" name="' . $tumBolVarName . '" id="' . $tumBolVarName . '" tabindex="15" "  >';
		   echo '<OPTION VALUE="-1">---เลือกตำบล---' ;


         // Palert($districtCode);
		  
			  //Palert("Case 2");
			  $sql = 'select DISTRICT_ID,DISTRICT_NAME FROM district where PROVINCE_ID=' . $provinceID .  ' and AMPHUR_ID= ' . $amphurCode ;
			
	          $rs3 = getMultiValue($sql) ;
			  while ($row2 = mysql_fetch_array($rs3))
       	        {
				   
				    echo '<OPTION VALUE="' . $row2[0] . '">' . $row2[1] ; 
                   
			    }
		  

	     echo '</SELECT>';

}

function getConfigValue($configName,$menuConfig) {

		    //echo "sConfig Name=" . $configName. "<br/>";

			for ($i=0;$i<count($menuConfig);$i++ ) {
				// echo $menuConfig[0][0]  . "---->". $menuConfig[0][1] . "------<br/>" ;
                 if (trim($configName) == trim($menuConfig[$i][0]) ) {
					 echo  "Found Result-->" . $configName . "==> " . $menuConfig[$i][0] . "<br/>" ;
					return  $menuConfig[$i][1] ;
				 } else {
                    echo  "Result-->" . $configName . "==> " . $menuConfig[$i][0] . "<br/>" ;
				 }
			}
			return -1 ;

			//$sValue =  $menuConfig[$key][1] ;
			//function searchForId($id, $array) {
           /*foreach ($array as $key => $val) {
                 if ($val['uid'] === $id) {
                   return $key;
                 }
            }
            return null;
			*/
}


function mysql_field_array( $query ) {

		$rs = getRowSet($query) ;
        $field = mysql_num_fields($rs);
        for ( $i = 0; $i < $field; $i++ ) {
            $names[] = mysql_field_name( $rs, $i );
        }

        return $names;

}

function getCommentfield($sometable2) {
		//Palert($sometable2);

		$username = 'admin' ;$passw = "maithong" ;
		$dbname = 'talonplusonweb';
 	    $conn = @mysql_pconnect("localhost", $username,$passw) ;
		mysql_select_db('talonplusonweb', $conn)  or die($sterr);
	   //mysql_select_db('thairealestate', $conn)  or die("Cannot Open Database");
	   mysql_query("SET NAMES UTF8");

		$result = mysql_query("SHOW FULL COLUMNS FROM $sometable2");

		if (!$result) {
			echo 'Could not run query: ' . mysql_error();
			exit;
		}
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_assoc($result)) {
//				print_r("&nbsp;&nbsp;&nbsp;" .$row['Comment'] . ",");
				$fcomment[] = $row['Comment'] ;
			}
		}
		//echo '</td></tr></table>';
//        print_r($fcomment) ;
		return $fcomment ;
}



function getConfig() {


         require_once("/shopA/configuration.php") ;
         $clsConfig = new PConfig ;

		 return $clsConfig ;

}

function GenerateInsertHead($sometable2) {

		
		openConnection() ;
		$result = mysql_query("SHOW COLUMNS FROM $sometable2");
		echo '<div id="sResult" style="width:900px;border:1px solid grey;word-wrap:break-word;">' . $sometable2.' Field List==>';
		//echo  '<tr><td>';
		if (!$result) {
			echo 'Could not run query: ' . mysql_error();
			exit;
		}
		$sqlInsert = "INSERT INTO $sometable2 (" ;
		$sqlInsert2 = "" ;
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_assoc($result)) {
				//print_r("&nbsp;&nbsp;&nbsp;" .$row['Field'] . ",");
				$sqlInsert .= $row['Field'] . ',' ;
				$sqlInsert2 .=  '<br/>/***********************' . $row['Field'] . '***************/<br/>' ;
				$sqlInsert2 .= '$fname[] = "' . $row['Field'] . '"; ' .  "<br/>"  .' $thisCol = GetSearchCol("' . $row['Field'] . '",$fData,$objWorkSheet,$rowNo) ;'  . "<br/>";
				$sqlInsert2 .= '$cellValue = trim($objWorksheet->getCellByColumnAndRow($thisCol,$rowNo)->getValue()) ; <br/>$fvalue[]= $cellValue ;'  ;

			}
		}
        $sqlInsert = substr($sqlInsert,0,strlen($sqlInsert)-1) . ") " ;
		echo $sqlInsert  . "<br/>";
		//echo '</td></tr></table>';
		echo '</div>';
		echo $sqlInsert2  . "<br/>";
		return  $sqlInsert ;
}


function getExcelObj($inputFileName,$sheetno=0) {
require_once '../PHPExcelReader/PHPExcel/Classes/PHPExcel.php';

/** PHPExcel_IOFactory - Reader */
include '../PHPExcelReader/PHPExcel/Classes/PHPExcel/IOFactory.php';


//$inputFileName = "../../temp/CONVERT_U12_591202.xls" ;
  $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
  echo '<br/>inputFileType= ' . $inputFileType ;
  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
  $objReader->setReadDataOnly(true);
  $objPHPExcel = $objReader->load($inputFileName) ;
  $objWorksheet = $objPHPExcel->setActiveSheetIndex($sheetno);

  return $objWorksheet ;

}

function writeMobile($mobileno) {

	        $st = "" . "(<span style='color:red'>" . substr($mobileno,0,3) . "</span>)" . substr($mobileno,3,3) . "-" . substr($mobileno,6,4) ;
			echo $t ;
			return $st ;


}

function writeHeaderSection($caption) { ?>

<div class='headerForm' >
             <table border=0 style="width:100%;margin:0px" class="coolBlue1">
			 <tr><td rowspan=2 width=100><img src='https://www.talonplus.co.th/port/images/eaglelogo-talonplus.png' width=90 height=90></td>
			 <td style="font-size:26px;color:black;font-family: 'Trirong', serif;"><?=$caption?>
			 </td>
			 </tr><tr>
			 <td style="font-size:16px;color:black;font-family: 'Trirong', serif;">บ.ทาลอนพลัส สำนักงาน กทม.
			 </td>
			 </tr>
          </table>
</div>

<?php
}

function CheckCSSFileExists($cssPath,$listFile)  {

            echo "<table border=1 style='font-size:14px'>" ;
			echo "<tr>";
			echo "<th>No</th><th>FileName</th><th>Status<th></tr>" ;
            for ($i=0;$i<=count($listFile)-1;$i++) {
				  $thisName = $cssPath . $listFile[$i] ;
				  echo "<tr>" ;
				  echo "<td>" . ($i+1 ). "</td>" ;
				  echo "<td><a href='" .  $thisName. "' target=_blank>" . $_SERVER['DOCUMENT_ROOT'] .  $thisName . "</a></td>" ;

                  if (file_exists($_SERVER['DOCUMENT_ROOT'] . $thisName)) {
					 echo "<td algin=center>" . "Found" . "</td>";
				  }  else {
                     echo "<td algin=center>" . "Not Found" . "</td>";
				  }
                  echo "</tr>" ;
			}
			echo "</table>" ;
}


function Pfileexists($sFileName) {


         /*$sFileName = $_SERVER['DOCUMENT_ROOT'] . $sFileName ;
         if (file_exists($sFileName)) {
			 return true;
         else {
            return false;
         }
		 */


}

function genFieldList($sql,$tablename2)  {

   $conn = openConnection() ;
   //$sql = "select * from customermaster " ;
   $result = mysql_query($sql);

/* Lecture des méta données de la colonne */
$i = 0;
echo "INSERT INTO $tablename2 (" ;
while ($i < mysql_num_fields($result)) {
  // echo "Field List $i:<br />\n";
   $meta = mysql_fetch_field($result, $i);
   if (!$meta) {
     // echo "Field List<br />\n";
   }
 /*  echo "<pre>
    blob:         $meta->blob
    max_length:   $meta->max_length
    multiple_key: $meta->multiple_key
    name:         $meta->name
    not_null:     $meta->not_null
    numeric:      $meta->numeric
    primary_key:  $meta->primary_key
    table:        $meta->table
    type:         $meta->type
    unique_key:   $meta->unique_key
    unsigned:     $meta->unsigned
    zerofill:     $meta->zerofill
   </pre>"; */
   $i++;
   //$st .=  $meta->name .  "=" .  "NEW." . $meta->name . "," . "<BR/>" ;;
  // $st .= $meta->name . "," ;
 //  echo '$fname[] = ' . $meta->name . ";" . space2(20) . " " ;
//   echo '$fvalue[] = ' . '$rowImport["' . $meta->name . '"]' .";" . "<br/>" ;
   //echo 'a.' . $meta->name . ' = NEW.'  . $meta->name ."," . "<br/>" ;
   if ( isHaveField($meta->name,$tablename2)) {
        // echo   "" . $meta->name . ','  ;
	    // echo   "NEW." . $meta->name . ','  ;
		//echo 'a.' . $meta->name . ' = NEW.'  . $meta->name ."," . "<br/>" ;
   }
	//echo  "NEW." . $meta->name . ' ,'  . "" ;
	//echo '$fvalue[] = $rowUpload["' . $meta->name . '"];' . "<br/>" ;
	echo '$fname[] = "' . $meta->name . '";' . space2(10) .
	'$fvalue[] = $_POST["' . $meta->name . '"];'  .
	"<br/>" ;
}

 mysql_free_result($result);
 return $st . " " ;

}

function isHaveField($fieldname,$tablename) {

           $conn = openConnection() ;
           $sql = "select * from $tablename " ;
           $result = mysql_query($sql);
		   $i=0;
		   while ($i < mysql_num_fields($result)) {
			   $meta = mysql_fetch_field($result, $i) ;
			   if ($meta->name == $fieldname) {
                  return true ;
			   }
			   $i++ ;
		   }
}

function DeclareCSSJS() { ?>


<link rel="stylesheet" type="text/css" href="..../css/formstyle.css">
<link rel="stylesheet" type="text/css" href="../../css/mainstyle.css">
<link rel="stylesheet" type="text/css" href="../../css/panel.css">
<link rel="stylesheet" type="text/css" href="../../demo.css">
<link rel="stylesheet" type="text/css" href="../../css/newstyle2.css">
<link rel="stylesheet" type="text/css" href="../../css/nutv99style.css">
<link rel="stylesheet" type="text/css" href="../../css/myinput.css">
<link rel="stylesheet" type="text/css" href="../../css/mybutton.css">
<link href="https://fonts.googleapis.com/css?family=Trirong" rel="stylesheet">



<script src="js/mylibcar.js"></script>
<script src="http://999ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://www.talonplus.co.th/port/js/cb.js"></script>

<link type="text/css" href="https://www.talonplus.co.th/port/css/jquery-ui-1.8.10.custom.css" rel="stylesheet" />
          <script type="text/javascript" src="https://www.talonplus.co.th/port/js/jquery-1.4.4.min.js"></script>
          <script type="text/javascript" src="https://www.talonplus.co.th/port/js/jquery-ui-1.8.10.offset.datepicker.min.js"></script>
<script src="../../js/messager.js"></script>




<script>jQueryTab=jQuery.noConflict(true);</script>


<?php
}

function openPDOConn() {

           $conn=mysqli_connect("localhost","talonplu_16102510","285043541","talonplu_onweb");
           $conn->autocommit(FALSE);
		   return $conn ;

}

function getPDO($withtrans,&$ErrorMsg,$dbname="") {

  $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/shopA/';
  require_once($newUtilPath."configuration.php");
  $clsConfig=new PConfig;
  if ($dbname=="") {
	  $dbname = $clsConfig->dbname ;
  }
  

  //echo $clsConfig->username . "<br>";

  try {
    $pdo = new PDO("mysql:host=localhost;dbname=$dbname", $clsConfig->username, $clsConfig->password, array(
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => true));
  }
  catch( PDOException $Exception ) {
    // Note The Typecast To An Integer!
    //throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
    $ErrorMsg =   $Exception->getMessage( );
	return false;
  }

  $pdo->exec("set names utf8") ;
  if ($withtrans != "" || $withtrans == true) {

     $pdo->beginTransaction();
	 //echo "<br>BeginTrans<br>";
   }
   $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
   return $pdo ;
}

function getPDODB($withtrans,&$ErrorMsg,$dbname="") {


  require_once("configuration.php");
  $clsConfig=new PConfig;
  if ($dbname=="") {
	  $dbname = $clsConfig->dbname ;
  }
  

  //echo $clsConfig->username . "<br>";

  try {
    $pdo = new PDO("mysql:host=localhost;dbname=$dbname", $clsConfig->username, $clsConfig->password, array(
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false));
  }
  catch( PDOException $Exception ) {
    // Note The Typecast To An Integer!
    //throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
    $ErrorMsg =   $Exception->getMessage( );
	return false;
  }

  $pdo->exec("set names utf8") ;
  if ($withtrans != "" || $withtrans == true) {

     $pdo->beginTransaction();
	 //echo "<br>BeginTrans<br>";
   }
   $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
   return $pdo ;
}


function getSQLFieldInsert($fname,$fvalue,$tablename) {


			$sql = "INSERT INTO $tablename (" ;
            $sql2 = " VALUES (" ;
			for ($i=0;$i<=count($fname)-1;$i++) {
                $sql .= trim($fname[$i]) . "," ;
				$sql2 .=  "?," ;
			}
			$sql = substr($sql,0,strlen($sql)-1) . ")"  ;
			$sql2 = substr($sql2,0,strlen($sql2)-1) . ")"  ;
			$sql = $sql . $sql2 ;
			return $sql ;
}


function getSQLFieldUpdate($fname,$fvalue,$tablename) {


			$sql = "UPDATE $tablename SET " ;
            //$sql2 = " SET " ;
			for ($i=0;$i<=count($fname)-1;$i++) {
               $sql .= trim($fname[$i]) . "=?," ;
			}
			$sql = substr($sql,0,strlen($sql)-1)  . " "   ;
			//$sql2 = substr($sql2,0,strlen($sql2)-1) . ")"  ;
			//$sql = $sql . $sql2 ;
			return $sql ;
}

function getMemberLogin($token,&$membercode,&$membername,&$balancetime) {

    /*
	Return Value  ----> -1 : No Token
  	                    ----> -2 : Token  Bad
						---->  1 : Token  Good Time Expire
						---->  2 : Token  Good Time Good


	*/

   if($token == "") {
	  return -1;
   }

//591007d48bb0b 1494223104

   //Palert($token) ;
   $pos1 = strpos($token,'149',0) ;

   $realtoken = substr($token,0,$pos1) ;
   $endTime = substr($token,$pos1,strlen($token)) ;
   $thisTime = strtotime("now") ;
   $balancetime1 = $endTime- $thisTime  ;
   $balancetime = ($balancetime1/60) ;

   //Palert($pos1 . "-" .$realtoken. "-". $endTime) ;


   $tokenAr = explode("|",$token ) ;
   $membercode  = $tokenAr[1] ;

          $sql="select * from staffinfo where uniqid='" . $realtoken . "'" ;
		   //echo $sql ;
		  $row = getRowSet($sql) ;
		  if ($row['ID'] != "") {
			   $thisTime = getCurrentDateTime() ;
		       $thisTimeStamp = strtotime("now") ;
			   $balanceTime = $endTime - $thisTimeStamp ;
			   //Palert($balanceTime) ;
			   if ($balanceTime <0) {
                   return false;
			   }
			   $membercode = $row['CODE'] ;
			   $membername = $row['staffname'] . " " . $row['staff_surname'];
			   $timeSt = strtotime($thisTime) ;
               $endtimeSt = strtotime($thisTime) + 300 ;
			   $token = uniqid (rand(), true) ;
			  // Palert($row['staffname'] . " " .$row['staff_surname']  ) ;
			  session_start() ;

			   $_SESSION['staffname'] = $row['staffname'] . " " .  $row['staff_surname'] ;
			   $_SESSION['picturename'] = $row['picturename'] ;
			   return "1|" ;

		   } else {
			   echo "-2|" ;
			   return "-2|" ;
		   }
		   return 1 ;
}

function full_url()

{

//$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";  
$s = '';


$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;

$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);

return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];

}

function curPageURL2() {
         $pageURL = 'http';
         if(isset($_SERVER["HTTPS"]))
           if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
           }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
 }

function CreateLoadingDialog() { ?>

     <div id="w" class="easyui-window" title="Loading Window...." data-options="modal:true,border:'thin',cls:'c6',closed:true" style="postion:relative;top:400px;width:400px;height:300px;spadding:10px;text-align:center">
        <img src="images/loading.gif" width="150" height="150" border="0" alt="" style='position:relative;top:50px'>
    </div>
<?php
}


function getCurrentURL() {

	$uri = $_SERVER['REQUEST_URI'];
//echo $uri; // Outputs: URI

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//echo $url; // Outputs: Full URL
return $url;

$query = $_SERVER['QUERY_STRING'];
//echo $query; // Outputs: Query String

} // end func




function pdogetValue($sql,$params,$pdo='') {
         
		 if ($pdo == '') {
			$pdo= getPDO(false,$ErrMsg);
			
		 }  

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

function pdogetMultiValue($sql,$params,$dbname='') {

         if ($dbname=='') {
            $pdo= getPDO(false,$ErrMsg);
         } else {
			$pdo= getPDO(false,$ErrMsg,$dbname);
         } 
         
         try {
          $rs = $pdo->prepare($sql);
          $rs->execute($params);
		  return $rs ;
		  



         } catch (PDOException $e)   {
            echo  $e->getMessage();
            return false;
         }

} // end func

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

function pdogetRowSet($sql,$params,$dbname='') {


         if ($dbname=='') {
            $pdo= getPDO(false,$ErrMsg);
         } else {
			$pdo= getPDO(false,$ErrMsg,$dbname);
         } 
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

function nutv_eval($code) {
  ob_start();
  echo eval('?>'. $code);
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
};


function getNewID($pdo,$fieldname,$tablename,$whereClause) {


	     $sql = "select max(". $fieldname . ") from $tablename $whereClause ";		 
		 try {
		   $rs = $pdo->prepare($sql);
		   $rs->execute();
		 } catch (Exception $e) {
		   echo 'Message: ' .$e->getMessage();
		 }

		 $row = $rs->fetch( PDO::FETCH_BOTH);		   
		 $MaxNo = $row[0];
		 if ($MaxNo <= 0) {
		    $MaxNo = 1;
		 } else {
            $MaxNo++ ;
		 }
		 return $MaxNo;

} // end func

function  GetTableNameList  ($dbname) {

          
          $pdo = getPDO2($dbname,true)  ;
          
          
		  
$sql ="SELECT table_name
FROM information_schema.tables
WHERE table_type = 'BASE TABLE' AND table_schema='sample'
ORDER BY table_name DESC";
$sql = "SHOW TABLES";
//Prepare our SQL statement,
$statement = $pdo->prepare($sql);

//Execute the statement.
$statement->execute();

//Fetch the rows from our statement.
$tables = $statement->fetchAll(PDO::FETCH_NUM);

//Loop through our table names.
foreach($tables as $table){
    //Print the table name out onto the page.
    $tblList[]  =  $table[0];
}

return $tblList ;

}

function  GetTableNameListPDO ($pdo) {


		  
$sql ="SELECT table_name
FROM information_schema.tables
WHERE table_type = 'BASE TABLE' AND table_schema='sample'
ORDER BY LOWER(table_name) DESC";
$sql = "SHOW TABLES";
//Prepare our SQL statement,
$statement = $pdo->prepare($sql);

//Execute the statement.
$statement->execute();

//Fetch the rows from our statement.
$tables = $statement->fetchAll(PDO::FETCH_NUM);

//Loop through our table names.
foreach($tables as $table){
    //Print the table name out onto the page.
	if (substr($table[0],0,5) !='infor') {	
       $tblList[]  =  $table[0];
	}
}

return $tblList ;

}

function fExists($pdo,$TableName,$fieldName,$fieldValue,&$FieldWant) {


            $sql = "select count(*) from $TableName where $fieldName ='" . $fieldValue . "'" ;
			   echo $sql;
			try {
			    $stmt = $pdo->query($sql);
			    $row = $stmt->fetch();
				if ($row[0] > 0 ) {
				   return true ;
				} else {
				   return false ;
				}


			} catch (Exception $e) {
			    echo 'Message: ' .$e->getMessage();
				return false;
			}



}

function getMax($pdo,$TableName,$fieldName,$whereClause='' ) {

             if ($fieldName !=='') {
                 $sql = "select Max(". $fieldName . ")  from $TableName $whereClause " ;
             }  else {
                 $sql = "select Max(". $filedName . ")  from $TableName $whereClause" ;
             }
            //   echo $sql;
			try {
			    $stmt = $pdo->query($sql);
			    $row = $stmt->fetch();
				if ($row[0] > 0 ) {
				   return $row[0]+1 ;
				} else {
				   return 1;
				}

			} catch (Exception $e) {
			    echo 'Message: ' .$e->getMessage();
				return false;
			}

}

function  CreateSelelct($pdo,$id,$sql,$default) {

               $st = '<select class="form-control" id="' . $id . '">';
			   try {
			       $stmt = $pdo->query($sql);
			       while ($row = $stmt->fetch()) {
					   if ($row[0] == $default) {
					      $st .= '<option value="' . $row[0] . '" selected="selected">' .  $row[1] . '</option>';
					   } else {
					       $st .= '<option value="' . $row[0] . '" ">' .  $row[1] . '</option>';
					   }
                   }
			   } catch (Exception $e) {
			       echo 'Message: ' .$e->getMessage();
			   }
			   $st .='</select>';
			   echo $st ;
}

function  CreateRadio($pdo,$id,$sql,$default) {

               //$st = '<select class="form-control" id="' . $id . '">';
			   //<input type="radio" name="">
			   $st = '';
			   try {
			       $stmt = $pdo->query($sql);
			       while ($row = $stmt->fetch()) {
					  if ($row[0] == $default) {
				       $st .= '&nbsp;&nbsp;<input type="radio" name="'.$id .'" checked value="'.$row[0] .'" onclick=Setsname("' . $row[1] .'")>&nbsp;&nbsp;' . $row[1];
					  } else {
                       //$st .= '&nbsp;&nbsp;<input type="radio" name="'.$id .'"   value="'.$row[0] .'">&nbsp;&nbsp;' . $row[1];
					   $st .= '&nbsp;&nbsp;<input type="radio" name="'.$id .'" checked value="'.$row[0] .'" onclick=Setsname("' . $row[1] .'")>&nbsp;&nbsp;' . $row[1];
					     
					  }
                   }
			   } catch (Exception $e) {
			       echo 'Message: ' .$e->getMessage();
			   }
			   $st .='</select>';
			   echo $st ;
}

function GetFieldNameList($pdo,$rs,$thisTblName) {  

  /* rs FROM pdo */
  $fieldName = '';
  $colcount = $rs->columnCount();
 // $pdo= getPDO(true,$ErrMsg) ;

 $types = array(
    PDO::PARAM_BOOL => 'bool',
    PDO::PARAM_NULL => 'null',
    PDO::PARAM_INT  => 'int',
    PDO::PARAM_STR  => 'string',
    PDO::PARAM_LOB  => 'blob',
    PDO::PARAM_STMT => 'statement'  //Not used right now
);

  
  for ($i = 0; $i <= $colcount-1; $i++) {
      $meta = $rs->getColumnMeta($i);
	  $FieldName = $meta['name'] ; 
	  $fieldcomment = getCommentFieldPDO($pdo,$thisTblName,$FieldName ) ;

      echo '-Meta Type=' .  $meta['native_type']  .' @@@@# ';
	  if ($meta['native_type'] === 'VAR_STRING' || $meta['native_type'] === 'STRING') {
          $sType = 'string';
	  }
	  if ($meta['native_type'] === 'TINY' || $meta['native_type'] === 'INT' || $meta['native_type'] === 'INT24') {
          $sType = 'int';
	  }
	  if ($meta['native_type'] === 'DATETIME' || $meta['native_type'] === 'DATE') {
          $sType = 'datetime';
	  }
	  if ($meta['native_type'] === 'FLOAT' || $meta['native_type'] === 'LONG' ) {
          $sType = 'float';
	  }
	  if ($meta['native_type'] === 'BLOB'   ) {
          $sType = 'blob';
	  }

//BLOB ,LONG, TINY,DOUBLE,DECIMAL,SHORT,LONG,LONGLONG,INT24,DATETIME,DATE,TIMESTAMP
      

	  if ($thisTblName!='') {	  
	    $fieldName .=  $meta['name'] .'='. $fieldcomment .'='. $sType .'@#' ;
		echo $meta['name'] .'='. $fieldcomment .'='. $sType .'---' ;
		
	  } else {
		$fieldName .= $meta['name'] .'@#' ;      
	  }
  }	  



  $fieldName = substr($fieldName,0,strlen($fieldName)-2);

  return $fieldName;

}

function GetFieldNameListPDO($pdo,$thisTblName) {  
 
  $sql ="select * from $thisTblName" ;
  try {
    $rs = $pdo->prepare($sql);
    $rs->execute();
  } catch (Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }
   

  /* rs FROM pdo */
  $fieldName = '';
  $colcount = $rs->columnCount();
 // $pdo= getPDO(true,$ErrMsg) ;

  $FieldName = array();
  for ($i = 0; $i <= $colcount-1; $i++) {
      $meta = $rs->getColumnMeta($i);
	  $FieldName[] = $meta['name'] ;
	  /*$fieldcomment = getCommentFieldPDO($pdo,$thisTblName,$FieldName ) ;
	  if ($thisTblName!='') {	  
	    $fieldName .= $thisTblName . '.' . $meta['name'] .'='. $fieldcomment .'='.$meta['native_type'] .'@#' ;       
	  } else {
		$fieldName .= $meta['name'] .'@#' ;      
	  }
	  */
  }	  
//  $fieldName = substr($fieldName,0,strlen($fieldName)-1);

  return $FieldName;

}



function GetFieldNameListAndTypePDO($pdo,$thisTblName) {  

 $datatypes = array(
        MYSQLI_TYPE_TINY => "TINY",
        MYSQLI_TYPE_SHORT => "SHORT",
        MYSQLI_TYPE_LONG => "LONG",
        MYSQLI_TYPE_FLOAT => "FLOAT",
        MYSQLI_TYPE_DOUBLE => "DOUBLE",
        MYSQLI_TYPE_TIMESTAMP => "TIMESTAMP",
        MYSQLI_TYPE_LONGLONG => "LONGLONG",
        MYSQLI_TYPE_INT24 => "INT24",
        MYSQLI_TYPE_DATE => "DATE",
        MYSQLI_TYPE_TIME => "TIME",
        MYSQLI_TYPE_DATETIME => "DATETIME",
        MYSQLI_TYPE_YEAR => "YEAR",
        MYSQLI_TYPE_ENUM => "ENUM",
        MYSQLI_TYPE_SET    => "SET",
        MYSQLI_TYPE_TINY_BLOB => "TINYBLOB",
        MYSQLI_TYPE_MEDIUM_BLOB => "MEDIUMBLOB",
        MYSQLI_TYPE_LONG_BLOB => "LONGBLOB",
        MYSQLI_TYPE_BLOB => "BLOB",
        MYSQLI_TYPE_VAR_STRING => "VAR_STRING",
        MYSQLI_TYPE_STRING => "STRING",
        MYSQLI_TYPE_NULL => "NULL",
        MYSQLI_TYPE_NEWDATE => "NEWDATE",
        MYSQLI_TYPE_INTERVAL => "INTERVAL",
        MYSQLI_TYPE_GEOMETRY => "GEOMETRY",
    );

 $trans = array(
  'VAR_STRING' =>
'string',
  'STRING' =>
'string',
  'BLOB' =>
'blob',
  'LONGLONG' =>
'int',
  'LONG' =>
'int',
  'SHORT' =>
'int',
  'DATETIME' =>
'Date',
  'DATE' =>
'Date',
  'DOUBLE' =>
'real',
  'TIMESTAMP' =>
'timestamp'
);

  $sql ="select * from $thisTblName" ;
  try {
    $rs = $pdo->prepare($sql);
    $rs->execute();
  } catch (Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }
   

  /* rs FROM pdo */
  $fieldName = '';
  $colcount = $rs->columnCount();
// $pdo= getPDO(true,$ErrMsg) ;
  $FieldName = array() ;
  $FieldType = array() ;

  
  for ($i = 0; $i <= $colcount-1; $i++) {
      $meta = $rs->getColumnMeta($i);

	  $FieldName[] = $meta['name'] ;
	  $thisFieldType = '' . $meta['native_type'] ;
	  if ($meta['native_type'] ==='TINY' || $meta['native_type'] ==='SHORT' ||$meta['native_type'] ==='LONG' || $meta['native_type'] ==='INT' || $meta['native_type'] ==='FLOAT' || $meta['native_type'] ==='DOUBLE' || $meta['native_type'] ==='INT24' || $meta['native_type'] ==='NEWDECIMAL' ) {
         $thisFieldType = 'number' ;
	  }

	  if ($meta['native_type'] ==='st' || $meta['native_type'] ==='VAR_STRING' || $meta['native_type'] ==='STRING'  ) {
         $thisFieldType = 'string' ;
	  }
	  if ($meta['native_type'] ==='DATE'  ) {
         $thisFieldType = 'Date' ;
	  }
	  if ($meta['native_type'] ==='DATETIME'  ) {
         $thisFieldType = 'Date' ;
	  }
	  if ($meta['native_type'] ==='BLOB'  ) {
         $thisFieldType = 'string' ;
	  }
	  if ($meta['native_type'] ==='TIMESTAMP' || $meta['native_type'] ==='TIME'  ) {
         $thisFieldType = 'string' ;
	  }


      $FieldType[] = $thisFieldType;
	 

//	  $FieldType[] = $meta['native_type'] ;
	  //$FieldType[] = translateNativeType($meta['native_type']);
	  //$FieldType[] = $meta['native_type'];

	  $fieldcomment[] = getCommentFieldPDO($pdo,$thisTblName,$meta['name'] ) ;
	  /*if ($thisTblName!='') {	  
	    $fieldName .= $thisTblName . '.' . $meta['name'] .'='. $fieldcomment .'='.$meta['native_type'] .'@#' ;       
	  } else {
		$fieldName .= $meta['name'] .'@#' ;      
	  }
	  */
  }	  
//  $fieldName = substr($fieldName,0,strlen($fieldName)-1);
  $sValue[] = $FieldName ;
  $sValue[] = $FieldType ;
  $sValue[] = $fieldcomment ;


  return $sValue;

}

function translateNativeType($orig) {
$trans = array(
 'TINY' =>'number',
 'FLOAT' =>'number',
 'TIME' =>'time',
 'NEWDECIMAL' =>'number',


  'VAR_STRING' =>
'string',
  'STRING' =>
'string',
  'BLOB' =>
'blob',
  'LONGLONG' =>
'int',
  'LONG' =>
'int',
  'SHORT' =>
'int',
  'DATETIME' =>
'datetime',
  'DATE' =>
'date',
  'DOUBLE' =>
'real',
  'TIMESTAMP' =>
'timestamp'
);
return $trans[$orig];
} 

function GetFieldNameListAr($rs,$thisTblName) {  

  /* rs FROM pdo */
  $colcount = $rs->columnCount();
  for ($i = 0; $i <= $colcount-1; $i++) {
      $meta = $rs->getColumnMeta($i);
	  //$fieldName[] = $meta['name'] ;      
	  if ($thisTblName!='') {	  
	    $fieldName[] = $thisTblName . '.' .$meta['name'];      
	  } else {
		$fieldName[] = $meta['name']  ;      
	  }
  }	     

  return $fieldName;

} 



function replace99($find,$replace,$st) {
/*
	$GLOBALS['ReplaceString'] = $replace;
    $st = preg_replace_callback(
   '/' . $find .'/', function($matches) {
		      $GLOBALS['ReplaceString'];
		      return  $GLOBALS['ReplaceString'];
		   },
   $st) ;
*/
  $st = preg_replace("/" . $find . "/", $replace, $st);

  return $st;

}

function  GetTableNameListDB  ($dbname) {

          $pdo = getPDO(false,$Errmsg,$dbname) ;
		  $sql = "SHOW TABLES";

//Prepare our SQL statement,
$statement = $pdo->prepare($sql);

//Execute the statement.
$statement->execute();

//Fetch the rows from our statement.
$tables = $statement->fetchAll(PDO::FETCH_NUM);

//Loop through our table names.
foreach($tables as $table){
    //Print the table name out onto the page.
    $tblList[]  =  trim($table[0]);
}

return $tblList ;

}

function  GetTableNameListDB2($pdo) {

        //  $pdo = getPDO(false,$Errmsg,$dbname) ;
		  $sql = "SHOW TABLES";

//Prepare our SQL statement,
$statement = $pdo->prepare($sql);

//Execute the statement.
$statement->execute();

//Fetch the rows from our statement.
$tables = $statement->fetchAll(PDO::FETCH_NUM);

//Loop through our table names.
foreach($tables as $table){
    //Print the table name out onto the page.
    $tblList[]  =  trim($table[0]);
}
asort($tblList) ;
return $tblList ;

}

function DeleteData($pdo,$sql,$fvalue) {


try {
        
   $params = $fvalue; 
   $cmd = $pdo->prepare($sql);
   $cmd->execute($params);
  //$pdo->commit();
} catch (PDOException $ex) {
   echo  $ex->getMessage();

} catch (Exception $exception) {
        // Output unexpected Exceptions.
        Logging::Log($exception, false);
}


}

function getCommentFieldPDO($pdo,$tableToDescribe,$FieldName) {

	

$statement = $pdo->query('DESCRIBE ' . $tableToDescribe);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$query = $pdo->query('SHOW FULL COLUMNS FROM ' . $tableToDescribe );                
$sql ='SHOW FULL COLUMNS FROM ' . $tableToDescribe . " WHERE Field='" . $FieldName ."'";
$query = $pdo->query($sql );        
//echo $sql . '<hr>' ;

$results = $query->fetchAll(PDO::FETCH_ASSOC);
//echo '<pre>';
//print_r($results);
for ($i=0;$i<=count($results)-1;$i++) {
	 if ($results[$i]['Field'] == $FieldName) {
		 if ($results[$i]['Comment'] ==='') {
			return  $FieldName ;
		 }
		 return $results[$i]['Comment'] ;
	 }
   
}



}

function getCommentFieldPDO2($pdo,$tableToDescribe,$FieldName) {

	

$statement = $pdo->query('DESCRIBE ' . $tableToDescribe);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$query = $pdo->query('SHOW FULL COLUMNS FROM ' . $tableToDescribe );                
$sql ='SHOW FULL COLUMNS FROM ' . $tableToDescribe . " WHERE Field='" . $FieldName ."'";
$query = $pdo->query($sql );        
//echo $sql . '<hr>' ;

$results = $query->fetchAll(PDO::FETCH_ASSOC);
//echo '<pre>';
//print_r($results);
for ($i=0;$i<=count($results)-1;$i++) {
	 if ($results[$i]['Field'] == $FieldName) {
		 if ($results[$i]['Comment'] ==='') {
			return  $FieldName ;
		 }
		 return $results[$i]['Comment'] ;
	 }
   
}



}

function getTypeFieldPDO($pdo,$tableToDescribe,$FieldName) {

	

$statement = $pdo->query('DESCRIBE ' . $tableToDescribe);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$query = $pdo->query('SHOW FULL COLUMNS FROM ' . $tableToDescribe );                
$sql ='SHOW FULL COLUMNS FROM ' . $tableToDescribe . " WHERE Field='" . $FieldName ."'";
$query = $pdo->query($sql );               

$results = $query->fetchAll(PDO::FETCH_ASSOC);
//echo '<pre>';
//print_r($results);
for ($i=0;$i<=count($results)-1;$i++) {
	 if ($results[$i]['Field'] == $FieldName) {
		 return $results[$i]['Type'] ;
	 }
   
}



}

function getFieldListByView($pdo,$viewname) { 


   
   
   $sql = 'select * from ' . $viewname ; 
   $params = array();
   $sValue =pdogetValue($sql,$params,$pdo) ;
   $stmt= pdogetMultiValue2($sql,$params,$pdo) ;
   
   // $stmt = $conn->query($sql);
    
    // ดึงรายการฟิลด์
    $fields = [];
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $meta = $stmt->getColumnMeta($i);
        $fields[] = $meta['name'];
    }

	return $fields;


} // end function

function getPDO2($dbname,$withtrans) {

  $clsConfig= getConfig2() ;

  $username = $clsConfig->username ;
  $passw = $clsConfig->password ;
  $username = 'ddhousin';
  $password = 'y4e2Q44rBw';
  $username = 'thepaper_lab';
  $password = 'maithong';
  $stconnect = "mysql:host=localhost;dbname=" . $dbname;
  //echo $username . '-' .$passw . '<hr>';
  try {
    $pdo = new PDO($stconnect, $username, $passw, array(
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => true));
   
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
   }
   $pdo->exec("set names utf8") ;
  if ($withtrans != "") {
     $pdo->beginTransaction();
   }
   $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

   return $pdo ;
}

 
 function getConfig2() {

           $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
           
           require_once($newUtilPath ."configuration.php") ;
		   $clsConfig = new PConfig ;


          //require_once("configuration.php") ;

		   return $clsConfig ;

}

function getConfig3() {

           $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
           
           require_once($newUtilPath ."src/define.php") ;
		   $clsConfig = new ShopConfig ;


          //require_once("configuration.php") ;

		   return $clsConfig ;



} // end function


function getFieldDesc99($columnname,$tablename,$dbname,&$fieldtype,&$fieldlegth) {

	$sql = "SELECT COLUMN_COMMENT,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM    INFORMATION_SCHEMA.COLUMNS WHERE " ;
    $sql .= "TABLE_SCHEMA = '" . $dbname . "' AND TABLE_NAME = '" . $tablename . "' AND COLUMN_NAME = '" . $columnname . "'" ;

	//echo $sql .'<br>' ;
	//$row = getRowSet($sql) ;

	$dbname = 'ddhousin_devshop' ;
	$pdo = getPDO2($dbname,true)  ;
	
	
	$params = array();
	
	$row = pdoRowSet($sql,$params,$pdo) ;
	
	
	
	
    $fieldDesc = $row['COLUMN_COMMENT'] ;
	if ($fieldDesc == "") {
		$sql = "select mLabel From fparam where mFieldName='" . $columnname . "' and mFieldName <> mLabel" ;

		//$fieldDesc = getValue($sql) ;
		$params = array();
		$fieldDesc = pdogetValue($sql,$params,$pdo) ;
//		$fieldDesc = getValue($sql) ;
		
	}
	if ($fieldDesc == "" || $fieldDesc == -1 ) {
        $fieldDesc = $columnname ;
	}
	if ($fieldDesc == '-1' )  {
       $fieldDesc = $columnname ;
	}
	$fieldtype = $row['DATA_TYPE'] ;
	$fieldlegth = $row['CHARACTER_MAXIMUM_LENGTH'] ;

//	echo $sql ;
	return  $fieldDesc ;
}


function WriteBoxRow($rs,$colmd,$numcolperline) {

    $ColNo = 1 ;
	while ($row =  mysql_fetch_assoc($rs) ) {
		   if ($ColNo==1) { ?>
		     <div class="row" style='margin:0px'>
		    <?php } ?>
		   <div class="col-md-<?=$colmd?> col-sm-6">
			<?php 
			boxItem($row) ; 
		    $ColNo++ ;
			if ($ColNo > $numcolperline) {
				$ColNo = 1;
				?>
			  </div><!-- End Row -->	 
			<?php }
			?>
		  </div>
   <?php }  // end While ?>
    </div>  
<?php
}

function  boxItem  ($row) {
/**
 * Short description.
 * @param   type    $varname    description
 * @return  type    description
 * @access  public or private
 * @static  makes the class property accessible without needing an instantiation of the class
 */
 ?>
 <style>
 .lineclamp1 {
     display: -webkit-box;
     -webkit-box-orient: vertical;
     -webkit-line-clamp: 1;
     overflow: hidden;
   }

  .lineclamp2 {
     display: -webkit-box;
     -webkit-box-orient: vertical;
     -webkit-line-clamp: 2;
     overflow: hidden;
   }
 .lineclamp3 {
     display: -webkit-box;
     -webkit-box-orient: vertical;
     -webkit-line-clamp: 3;
     overflow: hidden;
   }
   .boxProductCaption  {
      background:#BEC7CB;
	  padding:3px;
	  height:34px;overflow:hidden;
	  text-align:center;
	  font-size:18px;
	  margin-top:10px;
   }
   .boxDesc {
      font-size:14px;
   }
 </style>
<a href='<?=$row['ItemNameURL']?>'>
        <div id="" class="lineclamp2 boxProductCaption Sarabun " style='sheight:57px'>
		    <?=$row['ItemName']?>
	    </div>
		<div id="" class="99imgBox"  style='margin-top:10px' >
		   <img src="<?=$row['mainImageURL'] ?>" alt="Lights"  class=' img-fluid mx-auto d-block' style='max-height:180px'>
		</div>
		<div id="ssaa" class=" boxDesc  lineclamp3" style='padding-top:15px '>
				 <?php
				   if (trim($row['Description']) !== "") {
				     echo  $row['Description'] ;
				   } else {
				     echo $row['ItemName'] ;
				   }
	             ?>
	     </div>
</a>
	


<?php } 

function GetPageType() {

  	     $uri = $_SERVER['REQUEST_URI'];
		 $uriAr = explode("/",$uri) ; 
		 echo "Check URI :" . urldecode($uri) . '<BR>' ;

		 $uri = urldecode($uri);

		 preg_match("/กลุ่ม-/", $uri, $match);
		 if ($match) {
			 return "ProductGroupList"  ;
		 }
		 preg_match("/สินค้า-/", $uri, $match);
		 if ($match) {
			 return "ProductDetail"  ;
		 }
		 
		 preg_match("/checkoutpage2/", $uri, $match);
		 if ($match) {
			 return "checkoutpage2"  ;
		 }
		 preg_match("/checkout/", $uri, $match);
		 if ($match) {
			 return "checkout"  ;
		 }
		 preg_match("/customerprofile/", $uri, $match);
		 if ($match) {
			 return "CustomerProfile"  ;
		 }
		 preg_match("/profile/", $uri, $match);
		 if ($match) { return "mainprofile"  ;}

		 preg_match("/editcustomerSendPlace/", $uri, $match);
		 if ($match) { return "editcustomerSendPlace"  ;}

		 preg_match("/cutomerBankAccount/", $uri, $match);
		 if ($match) { return "cutomerBankAccount"  ;}

		 

		 preg_match("/mycoupon/", $uri, $match);
		 if ($match) { return "mycoupon"  ;}

		 preg_match("/myorder/", $uri, $match);
		 if ($match) { return "myorder"  ;}


		if (preg_match("/register/", $uri, $match)) { 
			echo "---Register Page";
			return 'register' ;
		}
		 

		 return 'home';


}

function SaveByJson($data,$tablename,$fListAr,$fTypeAr) { 


         require_once("../shopA/newutil.php"); 
//		 $tablename = 'Payment';
		 $sql = "INSERT INTO $tablename("; 
		 $sql2 = 'values(';
		 $paramClause = 'array("';
		 
		 for ($i=0;$i<=count($fListAr)-2;$i++) {
          // echo $data[$fListAr[$i]] .'-' ;
		   $fname[] = $fListAr[$i] ;
		   $fvalue[] = $data[$fListAr[$i]] ;
		   $sql .= $fListAr[$i].',';
		   $sql2 .= '?,' ; 

		   $Data = getJsonByType($data[$fListAr[$i]],$fTypeAr[$i]);
		   $paramClause .= ''. $Data  . '","';
		  
         }
		 

         $sql = substr($sql,0,strlen($sql)-1)  .')';
		 $sql2 = substr($sql2,0,strlen($sql2)-1)  .')';

		 $paramClause = substr($paramClause,0,strlen($paramClause)-2)  .')';

		 $sql = $sql . $sql2 ; 
		 echo $paramClause ; 
		 $pdo = getPDO(true,$ErrMsg);

		 
		 try {
		    
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    $pdo->commit();
			echo "success|Saveed";
		 } catch (PDOException $ex) {
		   // echo  $ex->getMessage();
			echo "fail|"  . $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
 
}

function base64ToImage($base64_string, $output_file) {

    $file = fopen($output_file, "wb");
    $data = explode(',', $base64_string);
	if (base64_decode($data[1]) != '') {
	
	if (fwrite($file, base64_decode($data[1]))) {
	    fclose($file);
		return true;
	} else {
		return false;
	}
	} 
	return false;
    
   

    return $false;
}



function getJsonByType($Data,$fType) {

	     if ($fType == 'image') {
			 $base64_string = $Data;
			 $output_file ='../images/11.jpg';
			 base64ToImage($base64_string, $output_file);
			 return $output_file ;
	     }

		 if ($fType == 'date') {
			 $DataAr = explode('/',$Data);
			 return $DataAr[2].'-' . $DataAr[0] . '-'.$DataAr[1] ;
	     }

		 if ($fType == 'time') {
			 return $Data;
	     }

		 if ($fType == 'int') {
			 return formatint($Data) ;
	     }
		 if ($fType == 'float') {
			 return formatn($Data);
	     }

		 return $Data ;

 
}

function getDBList() {
//แสดง DBList

    $pdo= getPDO(true,$ErrMsg) ;

	$stmt = $pdo->query('SHOW DATABASES');

//Fetch the columns from the returned PDOStatement
$databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

//Loop through the database list and print it out.
foreach($databases as $database){
    //$database will contain the database name
    //in a string format
    //echo $database, '<br>';
	$dblist[] = $database ;
	
}
return $dblist ;

}

function getDBListPDO($pdo) {
//แสดง DBList

//    $pdo= getPDO(true,$ErrMsg) ;

	$stmt = $pdo->query('SHOW DATABASES');

//Fetch the columns from the returned PDOStatement
$databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

//Loop through the database list and print it out.
foreach($databases as $database){
    //$database will contain the database name
    //in a string format
    //echo $database, '<br>';
	$dblist[] = $database ;
	
}
return $dblist ;

}



function ConverToPre($st) { 


		 $st = replace99($find='<','&lt;',$st) ;
		 $st = replace99($find='>','&gt;',$st) ;

		 $st = replace99($find='\n','<br/>',$st) ;

		 return $st ;


}


function writeJSON($st,$sql) { 

       
         $pdo= getPDO(true,$ErrMsg) ;        
	     try {
          $rs = $pdo->prepare($sql);
          $rs->execute();
         } catch (Exception $e) {
           echo "Fail Fetch Message: " .$e->getMessage();
         }
         $row = $rs->fetchAll() ; 
		 //echo ','. $st .':' ;
         echo json_encode($row);  

}


function NewSave($data) {
	 
	     $tablename = substr($data['Mode'],4,20) ;
         
//		 $data['Data']
		 $dataList = $data['Data'] ; 
		 echo $dataList .'----<br>'; 
		 $dataListAr = explode('|',$dataList) ; 

         $tablename = 'member' ;
		 $sqlInsert = "INSERT INTO $tablename(" ;
		 $sqlInsert = "REPLACE INTO $tablename(" ;

		 for ($i=0;$i<=count($dataListAr)-1;$i++) {
			 $thisData = explode('=',$dataListAr[$i])  ;
			 if (trim($thisData[0])!= '' && trim($thisData[0])!= 'null') {			 
			   $FieldName[] = $thisData[0] ;
			   $FieldType[] = $thisData[1] ;
			   if ($thisData[1] !='FileImage') {			   
			     $params[] = $thisData[2] ;
			   }
			   if ($thisData[1] =='FileImage') {			   
                  
			     $params[] = SaveImageBase64($data,$thisData[0]) ;
			   }
                 
			   
			 }
		 }
         
		 for ($i=0;$i<=count($FieldName)-1;$i++) {
             $sqlInsert .=  $FieldName[$i] .',';
		 }

		 $sqlInsert = substr($sqlInsert,0,strlen($sqlInsert)-1) .") values(" ; 
         $sqlInsert .= str_repeat('?,',count($FieldName)) ; 
		 $sqlInsert = substr($sqlInsert,0,strlen($sqlInsert)-1) .") " ; 

		 echo $sqlInsert ;
		 print_r($params);

		 //echo "DataImage:: " . $data['DataImage'] ;

		 $pdo = getPDO(true,$ErrMsg);
		 
		 
		 try {
		         
		     
		    $rs = $pdo->prepare($sqlInsert);
		    $rs->execute($params);
		    $pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
		  
		 

}

function NewSaveB($tablename,$data) {
	 

         
//		 $data['Data']
		 $dataList = $data['FieldList'] ; 
		 echo $dataList .'----<br>'; 
		 $dataListAr = explode('|',$dataList) ; 


		 $sqlInsert = "INSERT INTO $tablename(" ;
		 $sqlInsert = "REPLACE INTO $tablename(" ;

		 for ($i=0;$i<=count($dataListAr)-1;$i++) {
			 //$thisData = explode('=',$dataListAr[$i])  ;
			 if (trim($dataListAr[$i])!= '' && trim($dataListar[$i])!= 'null') {		 
			   $FieldName[] = $dataListAr[$i] ;
			   /*
			   $FieldType[] = $thisData[1] ;
			   if ($thisData[1] !='FileImage') {			   
			     $params[] = $thisData[2] ;
			   }
			   if ($thisData[1] =='FileImage') {			                     
			     $params[] = SaveImageBase64($data,$thisData[0]) ;
			   }
			   */
			   $params[] = $data[$dataListAr[$i]] ;

                 
			   
			 }
		 }
         
		 for ($i=0;$i<=count($FieldName)-1;$i++) {
             $sqlInsert .=  $FieldName[$i] .',';
		 }

		 $sqlInsert = substr($sqlInsert,0,strlen($sqlInsert)-1) .") values(" ; 
         $sqlInsert .= str_repeat('?,',count($FieldName)) ; 
		 $sqlInsert = substr($sqlInsert,0,strlen($sqlInsert)-1) .") " ; 

		 echo $sqlInsert ;
		 print_r($params);

		 //echo "DataImage:: " . $data['DataImage'] ;

		 $pdo = getPDO(true,$ErrMsg);
		 
		 
		 try {
		         
		     
		    $rs = $pdo->prepare($sqlInsert);
		    $rs->execute($params);
		    $pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
		  
		 

}

function SaveImageBase64($data,$FieldDataImageName) { 

     echo $FieldDataImageName . '***';
     $dataImageAll = $data['DataImage'] ;
	 $dataImageAllAr = explode('|',$dataImageAll);
	 for ($i=0;$i<=count($dataImageAllAr)-1;$i++) {
         $thisData2 = $dataImageAllAr[$i];
		 $thisData2Ar = explode("=",$thisData2);
		 echo $thisData2Ar[0] . '---'; 
		 if ($thisData2Ar[0] ==  "dataImage_" . $FieldDataImageName ) {
			$rnd = rand(0,9999);
            $output_file = '../../../../../images/' . $rnd. '.jpg' ;


            $img = $thisData2Ar[1] ;
			$image_parts = explode(";base64,", $img);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_base64 = base64_decode($image_parts[1]);  
			if (file_put_contents($output_file, $image_base64)) {
				 return $output_file;
			} else {
				 return false;
			} 

		 }


	    
	 } 
	 return "Not found";

	// print_r($dataImageAll) ; return ;

     $rnd = rand(0,9999);
     $output_file = '../../../../images/' . $rnd. '.jpg' ;
     $img = $data['imageFileName'] ;
     $image_parts = explode(";base64,", $img);
     $image_type_aux = explode("image/", $image_parts[0]);
     $image_type = $image_type_aux[1];
     $image_base64 = base64_decode($image_parts[1]);  
	 if (file_put_contents($output_file, $image_base64)) {
		 return $output_file;
	 } else {
		 return false;
	 }
     
	     



}

function cleanurl($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^ก-ฮA-Za-z0-9\-]/', '_', $string); // Removes special chars.
}


function getCurrentShopURL() {

	    $url = $_SERVER['REQUEST_URI']; 
        $urlAr  = explode("/",$url); 
        $shopName = $urlAr[2] ;  
		$url  =  'https://'.$_SERVER['HTTP_HOST'].'/shop/'. $shopName .'/';
		return $url ;

}

function getRootShopURL() {

	    $url = $_SERVER['REQUEST_URI']; 
        $urlAr  = explode("/",$url); 
        $shopName = $urlAr[1] ;  
		$url  =  'https://'.$_SERVER['HTTP_HOST'].'/'. $shopName .'/';
		return $url ;

} 

function getRootURL() {

	    $url = $_SERVER['REQUEST_URI']; 
        $urlAr  = explode("/",$url); 
        $shopName = $urlAr[1] ;  
		$url  =  'https://'.$_SERVER['HTTP_HOST'].'/';
		return $url ;

} 


function getShopName() { 

	    $url = $_SERVER['REQUEST_URI']; 
        $urlAr  = explode("/",$url); 
        $shopName = $urlAr[1] ; 

		return $shopName ;

}

function EncCode($ItemCode,$MemberID) {

// Ref https://qastack.in.th/programming/10916284/how-to-encrypt-decrypt-data-in-php

$name =  $ItemCode. '|' . $MemberID ;
$enc_name = openssl_encrypt(
    pkcs7_pad($name, 16), // padded data
    'AES-256-CBC',        // cipher and mode
    $encryption_key,      // secret key
    0,                    // options (not used)
    $iv                   // initialisation vector
);
//print_r($enc_name); 
return $enc_name ;

/* How to Use
     $enCode = EncCode($ItemCode='1232111',$MemberID='9s9')  ; 
	 echo $enCode ."<hr>";

	 $Decryp = DecCode($enCode,$ItemCode,$MemberID)  ;
	 echo $Decryp;
*/
}

function DecCode($enc_name,&$ItemCode,&$MemberID) {

    $name = pkcs7_unpad(openssl_decrypt(
    $enc_name,
    'AES-256-CBC',
    $encryption_key,
    0,
    $iv
));
   //print_r($name); 
   return $name ;
}


function pkcs7_pad($data, $size) {
    $length = $size - strlen($data) % $size;
    return $data . str_repeat(chr($length), $length);
}

function pkcs7_unpad($data)
{
    return substr($data, 0, -ord($data[strlen($data) - 1]));
}

function getCSSPath($path2) {

	$path2Ar = explode("/",$path2); 
for ($i=0;$i<=count($path2Ar)-1;$i++) {
    $st = $path2Ar[$i] ;
	$pos1 = strpos($st,'.com');
	if ($pos1) { 
	   $thisDomain = $path2Ar[$i] ;
	}
}
  //echo $thisDomain ."<hr>";


         $s = 'https://' . $thisDomain . '/'; 
		 $start = array_search("private_html",$path2Ar)+1 ; 

        //echo "<br>";
		 for ($i=$start;$i<=count($path2Ar)-1;$i++) {
            $s .=  $path2Ar[$i] .'/';		    
		 }
		// echo '<hr>'. " cssPath ->"   . $s . '<hr>';
		 return $s ;

}

function ResizeImage($itemcode,$source,$target) {

 //copy($source,'source.jpg');
 $filename = 'source.jpg' ;
 $filename2 = 'sourceResize.jpg' ;

 $filename = $source;
 $percent = 2;


$percent = 0.5  ;
$MaxWidth = 400 ;

// Content type
//header('Content-Type: image/jpeg');

// Get new dimensions
list($width, $height) = getimagesize($filename); 
//echo "Scale Source :: " . $width . ":" .  $height . "<br>";

if ($width  > $MaxWidth) {
   $new_width = $MaxWidth ;
   $ratio = $width/$height ;
   $new_height = $new_width*$ratio ;

}



if ($width > $height) {
  $new_width = 800;
  $new_height = 600;
}
if ($width == $height) {
  $new_width = $width;
  $new_height = $height;

}
if ($width > $height) {
  $new_width = 800;
  $new_height = 600;
}

if ($width > $MaxWidth) {
  $new_width = $MaxWidth ;
  $ratio = $width/$height ;
  $new_height = $new_width*$ratio ;

  
}




//$new_width = $width * $percent;
//$new_height = $height * $percent;



// Resample
$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($filename);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Output
$target = 'tmp/' . $itemcode . '.jpg' ;
imagejpeg($image_p, $target, 100); 
//echo '<hr>';
//echo "New Scale Source :: " . $new_width . ":" .  $new_height . "<br>";
return $target ;
//echo $image_p;

}

function jsonresponse($restext1,$restext2) { 

$st = '[{';

$st .= '"workstatus" : "' . $restext1 .'",' ;
$st .= '"message" : "' . $restext2 .'"' ;

$st .= '}]'; 
echo $st ;
//echo json_encode($st);








} // end function

function DivInput($caption,$id,$alertMsg,$datatype,$required,$svalue) {
	
	     if ($required == true) {
			$requiredText = 'required="required"' ;
	     } else {
            $requiredText = '' ;
		 }
	
	?>

      <div class="hs_firstname field hs-form-field">        
          <label for="<?=$id?>" style='font-weight:bold'><?=$caption?><span style='color:#ff0080'>*</span></label>
		  <br>
          <span id='span_<?=$id?>'>
          <input id="<?=$id?>" name="<?=$id?>" <?=$requiredText?> type="<?=$datatype?>"  placeholder="" style='width:100%;border : 1px solid lightgray;height:40px;'
		  saved datatype='<?=$datatype?>' value='<?=$svalue?>' 
		  onchange="CheckInputError('<?=$id?>')">
		  </span>
          <span id='Error_<?=$id?>' name='Error_<?=$id?>' class="error1" style="display:none;">
              <i class="error-log fa fa-exclamation-triangle"></i>
			  <?=$alertMsg?>
          </span>
        </div>
    <!-- End What's Your First Name Field -->

<?php
} // end function

function Unzip($zipName,$targetfolder) {

	$zip = new ZipArchive;
    $res = $zip->open($zipName);
    if ($res === TRUE) {
      $zip->extractTo($targetfolder);
      $zip->close();
      echo 'woot!';
    } else {
      echo 'doh!';
    }

} // end function


function convertYoutube($string,$height) {
    return preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "<iframe width=100% height=$height src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
        $string
    );
}


function GetFieldNameListAr2($rs,$thisTblName) {  

  /* rs FROM pdo */
  $colcount = $rs->columnCount();
  for ($i = 0; $i <= $colcount-1; $i++) {
      $meta = $rs->getColumnMeta($i);
	  //$fieldName[] = $meta['name'] ;      
	  if ($thisTblName!='') {	  
	    $fieldName[] = $thisTblName . '.' .$meta['name'];      
		$fieldType[] = $meta['native_type'];      

	  } else {
		$fieldName[] = $meta['name']  ;      
		$fieldType[] = $meta['native_type'];      
		//echo $meta["native_type"];
	  }
  }	     

  $sValue[] = $fieldName  ;      
  $sValue[] = $fieldType  ;      
  return $sValue;

}

function GetFieldNameListAr22($rs,$thisTblName) {  

  /* rs FROM pdo */
  $colcount = $rs->columnCount();
  for ($i = 0; $i <= $colcount-1; $i++) {
      $meta = $rs->getColumnMeta($i);
	  //$fieldName[] = $meta['name'] ;      
	  if ($thisTblName!='') {	  
	    $fieldName[] = $thisTblName . '.' .$meta['name'];      
		$fieldType[] = $meta['native_type'];      

	  } else {
		$fieldName[] = $meta['name']  ;      
		$fieldType[] = $meta['native_type'];      
		//echo $meta["native_type"];
	  }
  }	     

 // $sValue[] = $fieldName  ;      
  
  return  $fieldName ;

}

function isComponentExists($pdo,$fncName,&$fncID) {

         $ErrMsg  = '';
        
         $sql = "SELECT * FROM ComponentFunction where functionName=?";
         
         try {
                 
            $params = array($fncName); 
            $rs = $pdo->prepare($sql);
            $rs->execute($params);

         } catch (PDOException $ex) {
            echo  $ex->getMessage();
         
         } catch (Exception $exception) {
                 // Output unexpected Exceptions.
                 Logging::Log($exception, false);
         }

         $row = $rs->fetch( PDO::FETCH_ASSOC ) ;
		 if ($row) {
           
		   $fncID = $row['component_Function_ID'] ;
		   //echo "EditMode $fncName --- $fncID <br>";		   
		   return true ;
		 } else {
           $fncID = -1 ;
           return false; 
		 }
         
         
           


} // end function


function ListFunction($filename) {

    
    $filenameAr = explode("/",$filename) ; 

    $classname = $filenameAr[count($filenameAr)-1] ; 
	$classnameAr = explode(".",$filenameAr[count($filenameAr)-1]) ; 


	$st = "";   
    echo '<div style="width:100%;padding:20px;padding-top:0px">';
    echo '<h4>' . $filename . '</h4>';
	echo '<h5>' . $classname . '</h5>';

    $file = fopen($filename,"r");
	$fAr = ''; $fAr2 = '';
	$LineNo = 1;
    while(! feof($file))  {
      $st = trim(fgets($file)) ;
	  $stAll = $st;
	  if ( substr($st,0,8) === 'function' ) { 

		  $find = '{' ; $replace=' ' ;
          $st = replace99($find,$replace,$st) ;
		  if (substr($st,-2) =='?>' ) {
			  $st = substr($st,0,strlen($st)-2) ;
		  }
		  echo $LineNo++ . ' ' . $st . '<br>';
		  $find = 'function' ; $replace='' ;
          $st = replace99($find,$replace,$st) ;
		  $find = '&' ; $replace='' ;
          $st = replace99($find,$replace,$st) ;
		  $fAr[] = strtolower($st) ;
		 // echo "<hr>$stAll" ;
		  //if (strpos($stAll,"//Component") !== false) {
			 // echo "<hr>Componet Data --- $stAll" ;
			  $fAr2[] = $st ; 			  
		  //}		  
	  }
    }

    fclose($file);
    sort($fAr);
	//print_r($fAr2);
	echo "Step1 List function เก็บ ใน Array Total Component ="  . count($fAr2). '<hr>';  

    $pdo= getPDO(true,$ErrMsg) ;
	for ($i=0;$i<=count($fAr2)-1;$i++) {	
      if (trim($fAr2[$i]) !== '') {    
		$pos1 = strpos($fAr2[$i],'//Component')   ;
		$fncName = trim(substr($fAr2[$i],0,$pos1)); 
        echo  $i . ') Check Function ' . $fncName .' ---> ';

		if (!isComponentExists($pdo,$fncName,$fncID) ) {
			$sql = "INSERT INTO ComponentFunction(component_TypeID, functionName, ClassName) VALUES (?,?,?)";
			$params = array('',$fncName,trim($classnameAr[0])); 
			echo '<span style="color:red">Insert ' . $fncName . "</span><br>";

		} else {
			$sql = "update ComponentFunction set functionName=? where component_Function_ID=? ";
			$params = array($fncName,$fncID); 
			echo 'Update ' . $fncName . "<br>";
		}
		try {				     		   
		   $rs = $pdo->prepare($sql);
		   $rs->execute($params);

		} catch (PDOException $ex) {
		   echo  $ex->getMessage();
		
		} catch (Exception $exception) {
				// Output unexpected Exceptions.
				Logging::Log($exception, false);
		}
	  }
	}

    $pdo->commit();
	

    $pdo= getPDO(true,$ErrMsg) ;
	$sql ="DELETE FROM ComponentFunctionParam" ;
	try {
	  $rs = $pdo->prepare($sql);
	  $rs->execute();
	} catch (Exception $e) {
	  echo 'Message: ' .$e->getMessage();
	}
	 
    echo '<hr>Param Clause<hr>'; 
    $sql ="select * from ComponentFunction WHERE  functionName <> '' order by functionName" ;
    try {
      $rs = $pdo->prepare($sql);
      $rs->execute();
    } catch (Exception $e) {
      echo 'Message: ' .$e->getMessage();
    }
	$i = 0 ;
    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
       $ComponentFuncID = $row['component_Function_ID'] ;
	   $fncName =  trim($row['functionName']) ;
	   $fncName = substr($fncName,0,strlen($fncName)-1) ;
	   echo "<br>Func Name--->" . $fncName . '<br>';
	   $fncNameAr = explode("(",$fncName) ;
	   $paramList = $fncNameAr[1] ;
	   echo " Param --" .$paramList ;
	   $fncParamAr = explode(",",$paramList); 
	   for ($i=0;$i<=count($fncParamAr)-1;$i++) { 
          $sql ='INSERT INTO ComponentFunctionParam(component_Function_ID, FunctionName,orderno, paramname) VALUES (?,?,?,?)';
		  $ErrMsg  = '';		  
		  try {		          
             $thisParam = $fncParamAr[$i] ;
			 $thisParamAr = explode('=',$thisParam);

		     $params2 = array($ComponentFuncID,$fncNameAr[0],($i+1),$thisParamAr[0]); 
		     $rs2 = $pdo->prepare($sql);
		     $rs2->execute($params2);
		     
		  } catch (PDOException $ex) {
		     echo  $ex->getMessage();
		  
		  } catch (Exception $exception) {
		          // Output unexpected Exceptions.
		          Logging::Log($exception, false);
		  }
		  

	      
	   }
	   
	   
	 	    
	} // End Loop
	$pdo->commit();
	echo '<hr>';
	echo '</div>';  

	echo "Total Component ="  . count($fAr2). '<hr>'; 
	return;


	

	
	

?>

<style>
 .fncSelected { color:red } 
 .fncUnSelected { color:#2979FF } 

</style>
<link href="" rel="stylesheet">
<script>
function AddFunctionToList(fncName,classname) {

	     //alert(classname+'->'+fncName.trim()) ;
		 
		 var selList =   document.querySelectorAll("input:checked"); 
//		 alert(""+selList.length);
		 console.log("selList",selList) ;
		 st = ''; 
		 for (i=0;i<=selList.length-1 ;i++ ) {
		   st += '$' + classname+ '->' + selList[i].getAttribute('fncName')+';'+"\n";
		 } 
		 document.getElementById("listallselfnc").value = st ;
		 copyTextToClipboard(st) ;

		 console.log("",classname+'->'+fncName.trim()); 
		 LayoutStr = '$'+classname+'->'+fncName.trim();

		 //doAjaxSaveLayout('SaveLayout',LayoutStr) ;

}


function copyTextToClipboard(text) {
  if (!navigator.clipboard) {
    fallbackCopyTextToClipboard(text);
    return;
  }
  navigator.clipboard.writeText(text).then(function() {
    console.log('Async: Copying to clipboard was successful!');
  }, function(err) {
    console.error('Async: Could not copy text: ', err);
  });
}

async function doAjaxSaveLayout(args,LayoutStr) {

    let result ;
    let ajaxurl = 'src/SaveLayout.php';
    let data = { "sCode": args ,"sDat" : LayoutStr} ;
    data2 = JSON.stringify(data);
    try {
        result = await $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data2
        });
        alert(result);                 
        return result;
    } catch (error) {
        console.error(error);
    }
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


<?php

} // end function

function ListAllFunction() {
 
          
         $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
         //require_once($newUtilPath ."/shopA/newutil.php"); 
         /* OR */
         //require_once($newUtilPath.'/phpClassLib/clsUtil.php') ;
         
         $filename = $newUtilPath.'src/clsLayoutNew.php';
		 ListFunction($filename) ;
		 RETURN;
         
         $filename = $newUtilPath.'src/clsUtil.php';
		 ListFunction($filename,$className) ;
		
		 $filename = $newUtilPath.'src/clsLayoutMobile.php';
		 ListFunction($filename) ;

		 $filename = $newUtilPath.'src/clsMobile.php';
		 ListFunction($filename) ;
		 ?>
         <input type="text" id="listallselfnc" value='' style='width:100%'>

<?php

} // end function

function getComponentDat($SectionID,$componentName,$themename) { 

         
		 $stText = '' ;
		 $ErrMsg  = '';
		 $cssValue = '';
         $pdo = getPDO(true,$ErrMsg);

		 $sql = "SELECT componentName,cssValue,sData1
         FROM Component a inner join cssClassLib b 
         on a.ClassID=b.ClassID  where componentName='". $componentName. "' and b.themename='" . $themename . "'";
//		 echo "$sql<br>";

         $sql = "SELECT componentName,cssValue,sData1
         FROM Component a inner join cssClassLib b 
         on a.ClassID=b.ClassID  where componentName=? and b.themename=?";

		 //echo '--' . $themename ;

		 
         
         try {                 
            $params = array($componentName,$themename); 
            $rs = $pdo->prepare($sql);
            $rs->execute($params);
            
         } catch (PDOException $ex) {
            echo  $ex->getMessage();
         
         } catch (Exception $exception) {
                 // Output unexpected Exceptions.
                 Logging::Log($exception, false);
         } 

         if($row = $rs->fetch( PDO::FETCH_ASSOC )) {
             $cssValue = $row['cssValue'] ;
			 //echo ">>>" . $cssValue;
			 $cssValue = replace99('@pageid',"#" .$SectionID,$cssValue) ; 
			 $cssValue = replace99('@SectionID',"#" .$SectionID,$cssValue) ; 
			 $sDat = $row['sData1'] ;

         } else {
			 echo 'Not Found';
			 return;
		 }
         echo "<!-- --------------------- $SectionID  --------------------->\n";
		 echo '<style>'. "\n"; 		 
		 //echo  '#'. $SectionID .  '  { } ' ."\n";
		 echo $cssValue . "\n";
		 
		 echo '</style>'; 

		 $stText = "<style>\n"; 
         $stText .= $cssValue . "\n"; 
		 $stText .= "</style>\n"; 


		 //echo  '#'. $pageID .  ' .' . $this->componentName  .' { ' ."\n";
		 

         $sValue[] = $stText ;
		 $sValue[] = $sDat ;

		 return $sValue ;

         


} // end function

function getConfigData() { 

	     require_once("configuration.php");
		 $clsConfig = new NConfig();

		 return $clsConfig ;

} // end function

function DivList($caption,$id,$alertMsg,$datatype,$required,$svalue,$sql) {
	
	     if ($required == true) {
			$requiredText = 'required="required"' ;
	     } else {
            $requiredText = '' ;
		 }
         
		 //$rs = getPDORowSet($sql,$params="") ;
         $pdo= getPDO(true,$ErrMsg) ;

         
		 try {
		   $rs = $pdo->prepare($sql);
		   $rs->execute();
		 } catch (Exception $e) {
		   echo 'Message: ' .$e->getMessage();
		 }
		 
	
	?>

      <div class="hs_firstname field hs-form-field">        
          <label for="<?=$id?>" style='font-weight:bold'><?=$caption?><span style='color:#ff0080'>*</span></label>
<!-- 
          <input id="<?=$id?>" name="<?=$id?>" <?=$requiredText?> type="<?=$datatype?>"  placeholder="" 
		  saved datatype='<?=$datatype?>' value='<?=$svalue?>' onchange="CheckInputError('<?=$id?>')">
		   -->
		  <select name="<?=$id?>" id="<?=$id?>" style='width:100%;height:45px;border:1px solid lightgray' saved datatype='<?=$datatype?>' <?=$requiredText?> type="<?=$datatype?>">
		    <option value="-1">**** เลือกค่า ****
			<?php

			   while($row = $rs->fetch(PDO::FETCH_NUM))  { ?>
                 <option value="<?=$row[0]?>"><?=$row[1]?>
   
               <?php } ?>

			
		  </select>
          <span id='Error_<?=$id?>' name='Error_<?=$id?>' class="error1" style="display:none;">
              <i class="error-log fa fa-exclamation-triangle"></i>
			  <?=$alertMsg?>
          </span>
        </div>
    <!-- End What's Your First Name Field -->

<?php
} // end function

function DivInputText($caption,$id,$alertMsg,$datatype,$required,$svalue) {
	
	     if ($required == true) {
			$requiredText = 'required="required"' ;
	     } else {
            $requiredText = '' ;
		 }
         
		 //$rs = getPDORowSet($sql,$params="") ;
         
	
	?>

      <div class="hs_firstname field hs-form-field">        
          <label for="<?=$id?>" style='font-weight:bold'><?=$caption?><span style='color:#ff0080'>*</span></label>
          <br> 
		  <span id='span_<?=$id?>'>
          <input id="<?=$id?>" name="<?=$id?>" <?=$requiredText?> type="<?=$datatype?>"  placeholder="" style='width:100%;border:1px solid lightgray;padding:10px;border-radius:3px' required
		  saved datatype='<?=$datatype?>' value='<?=$svalue?>' onchange="CheckInputError('<?=$id?>')">
		  </span>

          <span id='Error_<?=$id?>' name='Error_<?=$id?>' class="error1" style="display:none;">
              <i class="error-log fa fa-exclamation-triangle"></i>
			  <?=$alertMsg?>
          </span>
        </div>
    <!-- End What's Your First Name Field -->

<?php
} // end function

function getComponentData($id,&$LinkArray,&$TextArray,&$ImgArray) { 

$ErrMsg  = '';
$pdo = getPDO(true,$ErrMsg);
$sql = "SELECT sData1 FROM Component where component_Function_ID=?"; 
$sql = "SELECT sData1 FROM Component where componentID=?"; 



try {
        
   $params = array($id); 
   $rs = $pdo->prepare($sql);
   $rs->execute($params);
   $pdo->commit();
} catch (PDOException $ex) {
   echo  $ex->getMessage();

} catch (Exception $exception) {
        // Output unexpected Exceptions.
        Logging::Log($exception, false);
}

if ($row = $rs->fetch( PDO::FETCH_ASSOC )) {
  $sData = $row['sData1'] ;
} else {
  $sData = '' ; return false ;
}

//Link=|Text=|ImageURL=https://res.cloudinary.com/dxfq3iotg/image/upload/v1565190720/gallery/preview/02_o_car.jpg
//echo '<hr>Data-->'. $sData . '<hr>';
$sDataAr = explode("@#",$sData); 
for ($i=0;$i<=count($sDataAr)-1;$i++) { 
   $sList = $sDataAr[$i] ;
   if ($sList !== '') {   
      $sListAr = explode("|",$sList);
	  
		  $Link = replace99('Link=','',$sListAr[0]) ; 
		  if (isset($sListAr[1] )) {	  
		    $Desc = replace99('Desc=','',$sListAr[1]) ; 
		  }
		  if (isset($sListAr[2] )) {	  
		    $ImgURL = replace99('ImageURL=','',$sListAr[2]) ; 
		  }
		  $LinkArray[] = $Link ;
		  $TextArray[] = $Desc ;
		  $ImgArray[] = $ImgURL;   
	 
   }
}


return true;




} // end function


function writeComponentClassID($functionName,$classid) {  

         
	     $ErrMsg  = '';

	     $pdo = getPDO(true,$ErrMsg);
	     $sql = "SELECT * FROM ComponentClass  where SourceFunctionName=? and ClassID=?" ; 

	     
	     try {	             
	        $params = array($functionName,$classid); 
	        $rs = $pdo->prepare($sql);
	        $rs->execute($params);

	     } catch (PDOException $ex) {
	        echo  $ex->getMessage();
	     
	     } catch (Exception $exception) {
	             // Output unexpected Exceptions.
	             Logging::Log($exception, false);
	     }
		 if ($rs->rowCount()) {
		     $row = $rs->fetch( PDO::FETCH_ASSOC ) ;
            
             echo '<style>'. "\n";
			 echo $row['cssValue'] ."\n";

			 echo '</style>' . "\n" ;


	        
	     } else {
            echo "Not Found CSS Class";
		 }
	     



} // end function

function GetBoxData($BoxID,&$BoxCaption,&$BannerArray) { 


	      $pdo= getPDO(true,$ErrMsg) ;           
          $sql = "select * from BoxProduct where BoxID='". $BoxID. "'" ; 
		  //echo "$sql<hr>";

		  try {
		    $rs = $pdo->prepare($sql);
		    $rs->execute();
		  } catch (Exception $e) {
		    echo 'Message: ' .$e->getMessage();
		  }
		  if ($rs->rowCount() > 0) {		  
		    $row = $rs->fetch( PDO::FETCH_ASSOC );
		    $ItemCodeList = $row['ItemCodeList']   ;
			if ($BoxCaption =='') {
				$BoxCaption = $row['BoxCaption'] ;
			}

			$BannerArray =explode('|',$row['BannerImageList']) ;


			
			$ItemCodeListAr = explode('|',$ItemCodeList) ;
			$sql = "	SELECT * FROM ItemMaster where ";
			$st = '';
			for ($i=0;$i<=count($ItemCodeListAr)-1;$i++) {
			   $st .= ' ItemCode="' .$ItemCodeListAr[$i].'" OR';
			}
			$st =  substr($st,0,strlen($st)-2);
			$sql .= $st ;
			//$rs = getMultiValue($sql) ;
			$rs =getPDOMultiValue($pdo,$sql)   ;
			
		  }
		  return $rs ;

} // end function


function GetArticleData($articleID) {

	    $ErrMsg  = '';
	    $pdo = getPDO(true,$ErrMsg);
	    $sql = "SELECT * FROM Article where id=?";
	    
	    try {
	            
	       $params = array($articleID); 
	       $rs = $pdo->prepare($sql);
	       $rs->execute($params);
	        
	    } catch (PDOException $ex) {
	       echo  $ex->getMessage();
	    
	    } catch (Exception $exception) {
	            // Output unexpected Exceptions.
	            Logging::Log($exception, false);
	    } 
		if ($rs->rowCount() > 0) {
		   return $rs ;	       
	    }
	    

} // end function


function GetMenuLiData($menuLI_ID) {

	    $ErrMsg  = '';
	    $pdo = getPDO(true,$ErrMsg);
	    $sql = "SELECT * FROM LiMenu where id=?";
	    
	    try {
	            
	       $params = array($menuLI_ID); 
	       $rs = $pdo->prepare($sql);
	       $rs->execute($params);
	        
	    } catch (PDOException $ex) {
	       echo  $ex->getMessage();
	    
	    } catch (Exception $exception) {
	            // Output unexpected Exceptions.
	            Logging::Log($exception, false);
	    } 
		if ($rs->rowCount() > 0) {
		   return $rs ;	       
	    }
	    

} // end function

function getLinkDesc($linkcode) {

		 $ErrMsg  = '';
		 $pdo= getPDO(true,$ErrMsg) ;

         
	     $sql ="select * from LinkType where TypeID=?" ;

		 try {
		         
		    $params = array($linkcode); 
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
		         return $row['LinkTypeDesc'] ;
		    }
		 }
	     

} // end function



function geturlDataShop(&$urlType,&$id1,&$callfrom,&$shopname,&$pageType,&$pageParam) { 

   $urlRegist = array("สินค้า","กลุ่มสินค้า","บทความ","ลงทะเบียน" );

   $url =  $_SERVER['HTTP_HOST'] ; 
   echo $url . '<br>';
   $url2 = explode('.',$url) ;
   if (count($url2) >1) {
	  // Have Subdomain
	  $shopname = $url2[0] ;
   } else {
      $shopname = 'noshop' ;
   }

  echo '<hr>' .urldecode($_SERVER['REQUEST_URI']);
  $url3Ar = explode("/",urldecode($_SERVER['REQUEST_URI'])); 
  $salename = '';
  $pagename = $url3Ar[1]; 
  if ($salename !='') { 
	  $urlType = "sale"; 
  } else {
      $urlType = "shop"; 
  }

  echo '<hr>shopname=' . $shopname . '<br>';
  echo '<hr>salename=' . $salename . '<br>';
  echo '<hr>PageName=' . urldecode($url3Ar[1]) . '<br>';
  $pagenameAr = explode('-',$pagename) ;
  $pageType = $pagenameAr[0] ;
  $pageParam = $pagenameAr[1]  ;

  echo '<hr>PageType=' . urldecode($pageType) . '<br>';
  echo '<hr>PageParam=' . urldecode($pageParam) . '<br>';

  if (in_array(urldecode($pageType),$urlRegist)) {
	  echo '<span style="color:red">----Page ถูกต้อง---<br></span>' ;
  } else {
	  echo '<span style="color:red">----Page ไม ่ถูกต้อง---<br></span>' ;
  }


 


  
  $ErrMsg  = '';
  $pdo = getPDO(true,$ErrMsg);
  $sql = "SELECT * FROM shopMaster where shopURL=?";  
  try {
          
     $params = array($shopname); 
     $rs = $pdo->prepare($sql);
     $rs->execute($params);
     
  } catch (PDOException $ex) {
     echo  $ex->getMessage();
  
  } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
  }
  if ($rs->rowCount() > 0) {
     $shopCorrect = true;
  } else {
     $shopCorrect = false;
  }

  $sql = "SELECT * FROM SaleMaster a inner join shopMaster b on a.shopID=b.shopID where SaleURL=?";  
  try {          
     $params = array($salename); 
     $rs = $pdo->prepare($sql);
     $rs->execute($params);
     
  } catch (PDOException $ex) {
     echo  $ex->getMessage();
  
  } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
  }
  if ($rs->rowCount() > 0) {
     $saleCorrect = true;
  } else {
     $saleCorrect = false;
  } 


  if ($saleCorrect && $shopCorrect ) {
	  echo "<hr>URL Exists<hr>" ;
  }






   if (isset($_GET["id1"])) {  
    $id1 = $_GET["id1"];
    
  } else {
	  $id1= ''; 
  }

// รูปแบบ Callfrom https://mall.lovetoshopmall.com/abc/aaa/?callfrom=ssss
  if ($id1<> '') {  
	  $callfrom =  $_GET["callfrom"];   
  } 
  return;




} // end function

function geturlDataSale(&$urlType,&$id1,&$callfrom,&$shopname,&$pageType,&$pageParam) { 

   $urlRegist = array("สินค้า","กลุ่มสินค้า","บทความ","ลงทะเบียน" );

   $url =  $_SERVER['HTTP_HOST'] ; 
   echo $url . '<br>';
   $url2 = explode('.',$url) ;
   if (count($url2) >1) {
	  // Have Subdomain
	  $shopname = $url2[0] ;
   } else {
      $shopname = 'noshop' ;
   }

  echo '<hr>' .urldecode($_SERVER['REQUEST_URI']);
  $url3Ar = explode("/",urldecode($_SERVER['REQUEST_URI'])); 
  $salename = trim($url3Ar[1]);
  $pagename = $url3Ar[2]; 
  if ($salename !='') { 
	  $urlType = "sale"; 
  } else {
      $urlType = "shop"; 
  }

  echo '<hr>shopname=' . $shopname . '<br>';
  echo '<hr>salename=' . $url3Ar[1] . '<br>';
  echo '<hr>PageName=' . urldecode($url3Ar[2]) . '<br>';
  $pagenameAr = explode('-',$pagename) ;
  $pageType = $pagenameAr[0] ;
  $pageParam = $pagenameAr[1]  ;

  echo '<hr>PageType=' . urldecode($pageType) . '<br>';
  echo '<hr>PageParam=' . urldecode($pageParam) . '<br>';

  if (in_array(urldecode($pageType),$urlRegist)) {
	  echo '<span style="color:red">----Page ถูกต้อง---<br></span>' ;
  } else {
	  echo '<span style="color:red">----Page ไม ่ถูกต้อง---<br></span>' ;
  }


 


  
  $ErrMsg  = '';
  $pdo = getPDO(true,$ErrMsg);
  $sql = "SELECT * FROM shopMaster where shopURL=?";  
  try {
          
     $params = array($shopname); 
     $rs = $pdo->prepare($sql);
     $rs->execute($params);
     
  } catch (PDOException $ex) {
     echo  $ex->getMessage();
  
  } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
  }
  if ($rs->rowCount() > 0) {
     $shopCorrect = true;
  } else {
     $shopCorrect = false;
  }

  $sql = "SELECT * FROM SaleMaster a inner join shopMaster b on a.shopID=b.shopID where SaleURL=?";  
  try {          
     $params = array($salename); 
     $rs = $pdo->prepare($sql);
     $rs->execute($params);
     
  } catch (PDOException $ex) {
     echo  $ex->getMessage();
  
  } catch (Exception $exception) {
          // Output unexpected Exceptions.
          Logging::Log($exception, false);
  }
  if ($rs->rowCount() > 0) {
     $saleCorrect = true;
  } else {
     $saleCorrect = false;
  } 


  if ($saleCorrect && $shopCorrect ) {
	  echo "<hr>URL Exists<hr>" ;
  }






   if (isset($_GET["id1"])) {  
    $id1 = $_GET["id1"];
    
  } else {
	  $id1= ''; 
  }

// รูปแบบ Callfrom https://mall.lovetoshopmall.com/abc/aaa/?callfrom=ssss
  if ($id1<> '') {  
	  $callfrom =  $_GET["callfrom"];   
  } 
  return;




} // end function

function getShopConfig(&$urlType,&$id1,&$callfrom,&$shopname,&$pageType,&$pageParam) { 

   require_once("configuration.php"); 
   $clsConfig = new NConfig() ;
   $ownerpagetype = $clsConfig->ownerpagetype ;
   $rootpageurl = $clsConfig->rootpageurl ;

   echo '<span style="font-size:25px">ownerpagetype = ' . $ownerpagetype . '</span><br>';
   if ($ownerpagetype =='sale') {
     geturlDataSale($urlType,$id1,$callfrom,$shopname,$pageType,$pageParam);
   }

   if ($ownerpagetype =='shop') {
     geturlDataShop($urlType,$id1,$callfrom,$shopname,$pageType,$pageParam);
   } 
   ?>
	    
    <input type="hidden" id="currentMemberID" value='<?=$_SESSION['memberid']?>'>
	<input type="hidden" id="currentShop" value='<?=$clsConfig->shopname?>'>
	<input type="hidden" id="currentShopID" value='<?=$clsDefine->shopID?>'>
	<input type="hidden" id="currentShopURL" value='<?=$clsConfig->rootpageurl?>'>
	<input type="hidden" id="currentDomain" value='<?=getRootURL()?>'> 
	<input type="hidden" id="userfullname" value='<?=$_SESSION['userfullname']?>'> 
	<input type="hidden" id="RootShopURL" value='<?=$shopurl?>'> 



<?php
} // end function

function testCookies() {

		$cookie_name = "usertest";
		$cookie_value = "John Doe";
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

		if(!isset($_COOKIE[$cookie_name])) {
			echo "Cookie named '" . $cookie_name . "' is not set!";
		} else {
			echo "Cookie '" . $cookie_name . "' is set!<br>";
			echo "Value is: " . $_COOKIE[$cookie_name];
		}

}

function getArticleData2($articleid) {

	     $ErrMsg  = '';
	     $pdo = getPDO(true,$ErrMsg);
	     $sql = "SELECT * FROM Article where id=?";
	     
	     try {
	             
	        $params = array($articleid); 
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
	        $row = $rs->fetch( PDO::FETCH_ASSOC );
			return $row;
	       
	     } else {

           return false;
		 }

} // end function

function getShopName2(&$shopID) { 

    preg_match('/([^.]+)\.lovetoshopmall\.com/', $_SERVER['SERVER_NAME'], $matches);
    if(isset($matches[1])) {
      $subdomain = $matches[1];
	  $ErrMsg  = '';
	  $pdo = getPDO(true,$ErrMsg);
	  $sql = "SELECT * FROM shopMaster where shopURL=?";
	  
	  try {
	          
	     $params = array($subdomain); 
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
	     $row = $rs->fetch( PDO::FETCH_ASSOC );
	     $shopID= $row['shopID'] ;
	     
	  }

	  return  $subdomain;
	  //echo $subdomain ;
    }

} // end function

function CreateShopFolder($shopid,$useThemeName) { 

         echo 'ShopID=' . $shopid . '<br>';  
$pdo= getPDO(true,$ErrMsg) ;


$sql = "DELETE from SalePage where shopID=?"; 

try {
  $params = array($shopid); 
  $rs = $pdo->prepare($sql);
  $rs->execute($params);

} catch (Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
 
$pdo->commit();
//return;

	     $ErrMsg  = '';
	     $pdo = getPDO(true,$ErrMsg);
	     $sql = "SELECT * FROM shopMaster where shopID=?";
	     
	     try {
	             
	        $params = array($shopid); 
	        $rs = $pdo->prepare($sql);
	        $rs->execute($params);
	       
	     } catch (PDOException $ex) {
	        echo  $ex->getMessage();
	     
	     } catch (Exception $exception) {
	             // Output unexpected Exceptions.
	             Logging::Log($exception, false);
	     }
	     if ($rs->rowCount() > 0) {
            $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	        while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
				$url = $newUtilPath .trim($row['shopURL'])  ;
				if ($url == '') {
					echo 'Cannot Create Shop URL isEmpty' ;  return false;
				}
				echo 'URL IS' . $row['shopURL'] . '<hr>' ;
				if (!file_exists($url)) {
				    mkdir($url,true);chmod($url,0777);
				}
				$sDir = $url.'/'. 'asset' ;
				if (!file_exists($sDir)) {
				  mkdir($sDir,true);chmod($sDir,0777);
				}
				$sDir = $url.'/'. 'asset/css' ;
                if (!file_exists($sDir)) {
					mkdir($sDir,true);chmod($sDir,0777);
				}
				$sDir = $url.'/'. 'asset/js' ;
				if (!file_exists($sDir)) {
					mkdir($sDir,true);chmod($sDir,0777);
				}
				$sDir = $url.'/'. 'asset/images' ;
                if (!file_exists($sDir)) {
					mkdir($sDir,true);chmod($sDir,0777);					
				}
				$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
				$source = $newUtilPath. 'labTest/' . $useThemeName  ;
				$target= $newUtilPath.  $row['shopURL'] . '/asset/css/default.less' ; 
				if (copy($source,$target)) {
				   //echo 'Copy Less Success ' . $source; 
				} else {
                       //echo 'Fail Copy Less'; 
				}
				$st = "";   
				 $file = fopen("../admin/configuration.prototype.php","r");
				 while(! feof($file))  {
				   $st .= fgets($file) ;
				 }
				 fclose($file);
				 $st = replace99('@#shopname',$row['shopName'],$st)  ;
				 $st = replace99('@#shopFolder',$row['shopURL'],$st) ;
       
	             $target= $newUtilPath. trim($row['shopURL']) .'/asset/configuration.php' ; 
				 $myfile = fopen($target, "w") or die("Unable to open file!");
				  
				 fwrite($myfile, $st);
				 fclose($myfile); 
				 $shopURL = $row['shopURL'] ;

$_SESSION['memberid'] = '9999' ;
$sql ='INSERT INTO SalePage(OwnerID, shopID, SaleID, pageName, fncList, IndexCode, HeadFunction, BodyFunction, FooterFunction)
select ' . $_SESSION['memberid']  .',' .$shopid . ',-1, "salepage", fncList, IndexCode, HeadFunction, BodyFunction, FooterFunction from SalePage where pageName="mainPage1"' ;



try {
  $rs = $pdo->prepare($sql);
  $rs->execute();
} catch (Exception $e) {
  echo 'Message: ' .$e->getMessage();
}
if ($rs->rowCount() > 0) {
  while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
   
  }
}

$pdo->commit();				 



$sql = "select * from SalePage where shopID=?"; 
$ErrMsg  = '';
$pdo = getPDO(true,$ErrMsg);


try {
        
   $params = array($shopid); 
   $rs = $pdo->prepare($sql);
   $rs->execute($params);
  
} catch (PDOException $ex) {
   echo  $ex->getMessage();

} catch (Exception $exception) {
        // Output unexpected Exceptions.
        Logging::Log($exception, false);
}
if ($rs->rowCount() > 0) {
   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	  $indexCode = $row['IndexCode'] ;
	  $target= $newUtilPath. trim($shopURL) .'/index.php' ; 
	  $myfile = fopen($target, "w") or die("Unable to open file!");
	  
	  fwrite($myfile, $indexCode);
	  fclose($myfile);
	
   }
}

$source= $newUtilPath. 'admin/test.less' ; 
$target= $newUtilPath. trim($shopURL) .'/test.less' ; 
copy($source,$target);

$source= $newUtilPath. 'admin/access.htaccess' ; 
$target= $newUtilPath. trim($shopURL) .'/.htaccess' ; 
copy($source,$target);


				
				/*$source = $newUtilPath. 'mall/abc/configuration.php' ;
				$target= $newUtilPath. 'maithong/asset/configuration.php' ; 
				if (copy($source,$target)) {
				   echo 'Copy Config Success ' . $source; 
				} else {
                   echo 'Fail Copy Config'; 
				}
				*/

	        
	        }
	     } 
		 return true;

} // end function

function getClassConfig() {
	     
         $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
         require_once($newUtilPath ."/src/newutil.php"); 
		 require_once("asset/configuration.php"); 
		 $clsConfig = new NConfig;
		 return $clsConfig ;


} // end function

function getPHPSalePage($salePageName,&$HeaderFunction,&$BodyFunction,&$FooterFunction,$shopID='') {   

	     $ErrMsg  = '';
	     $pdo = getPDO(false,$ErrMsg);
		 if ($shopID==='') {		 
	       $sql = "SELECT * FROM SalePage where pageName=?";
		   $params = array($salePageName); 
		 } else {
		   $sql = "SELECT * FROM SalePage where pageName=? and shopID=?";
		   $params = array($salePageName,$shopID); 
		  // echo $salePageName. ',' . $shopID;
		 }
	     
	     try {
	             
	        
	        $rs = $pdo->prepare($sql);
	        $rs->execute($params);
	        //$pdo->commit();
	     } catch (PDOException $ex) {
	        echo  $ex->getMessage();
	     
	     } catch (Exception $exception) {
	             // Output unexpected Exceptions.
	             Logging::Log($exception, false);
	     }
	     if ($rs->rowCount() > 0) {
	        while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	           $HeaderFunction = $row['HeadFunction'];
			   $BodyFunction  = $row['BodyFunction'];
			   $FooterFunction = $row['FooterFunction'];
			   return true;
	        }
	     } else {
            return false;
		 }

	     

} // end function

function concludeformtype() { ?>

<ol>
 <li>array of ul-li </li>
 <li>array of ItemCode </li>
 <li>array of Image Banner </li>
 <li>array of Article </li>
 <li>array of Social Icon </li>


 <li>Category Code </li>
</ol>

<?php

} // end function

function RecreateFunction($shopID,$pageName,$ParamNameAr,$ParamNameValue,$fncName) { 

        // echo "fncName=" . $fncName ; 
         $ErrMsg  = '';
         $pdo = getPDO(true,$ErrMsg);

         $sql = "SELECT fncListID_Body FROM SalePage where shopID=? and pageName=?";
		 $params = array($shopID,$pageName); 

		 $FncList = pdogetValue($sql,$params,$pdo) ;
		 $fncListAr = explode('@#',$FncList) ;

         
          

		 $newFnc  = '';
		 for ($i=0;$i<=count($fncListAr)-1;$i++) {
           if ($fncListAr[$i] != '') {
            // echo $fncListAr[$i] .'<hr>';
			 $fncListAr2 = $fncListAr[$i] ;
		     $thisNameAr = explode('-',$fncListAr2) ;
			 if (trim($thisNameAr[1]) == trim($fncName)) {
                 $newStFnc = $fncName .'(' ;
				 for ($k=2;$k<=count($thisNameAr)-1;$k++) { 
				    $newStFnc .= $thisNameAr[$k] .'=' .$ParamNameValue[$k-2].',' ;
				 }
				 $newStFnc = substr($newStFnc,0,strlen($newStFnc)-1) . ');';
			 } else {
               //$newFnc .= $fncListAr[$i] ;
			 }
           } 
		 } 



		 echo  'First Step -->' . $newStFnc .' -- Second Step is ';

		 $newStFnc = '$clsLayout->' . $newStFnc;

		 $sql = "SELECT BodyFunction FROM SalePage where shopID=? and pageName=?";
		 $params = array($shopID,$pageName); 

		 $FncList = pdogetValue($sql,$params,$pdo) ;
		 $fncListAr = explode(';',$FncList) ;
		 $newBodyFnc = ''; 
		 for ($i=0;$i<=count($fncListAr)-1;$i++) {
             $st1 =  $fncListAr[$i] ;
			 if ($st1 != '') {
                $st1Ar = explode('->',$st1);
				$st2 = explode('(',$st1Ar[1]) ;
				if ($st2[0] === $fncName) {
				  $newBodyFnc .=$newStFnc  .''  ; 
				} else {
                  $newBodyFnc .= $st1 .';' ;
				}

			 }		    
		 } // end for

		 $sql = "update SalePage set BodyFunction=? where shopID=? and pagename=?"; 
		 $params = array($newBodyFnc,$shopID,'salepage');
		 pdoExecuteQuery($pdo,$sql,$params) ;
		 $pdo->commit();
		 

		 return $newBodyFnc;





	      

} // end function

function getFuncParamStr($pdo,$fncName) {

	$sql = "select * from ComponentFunctionParam where FunctionName=? order by orderno"; 
	$params = array($fncName) ;
	$rs = getPDOMultiValue($pdo,$sql,$params)  ; 
	 
	if ($rs->rowCount() > 0) {
	   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	      $componentFncID = $row['component_Function_ID'] ;
		  $paramNameAr[] = $row['paramname'] ;

	   }
	   $st = $componentFncID .'-' . $fncName.'-' . implode('-',$paramNameAr) ;
	   return $st ;
	}											



} // end function


function CreateDataDictFromTable($pdodev,$dbname,$tablename) { 
		 
		 $ErrMsg  = ''; 

		 $pdo = getPDO2($dbname,true)  ;
		
		 
		 $sql ="DESCRIBE $tablename" ;
		 try {
		   $rs = $pdo->prepare($sql);
		   $rs->execute();
		 } catch (Exception $e) {
		   echo 'Message: ' .$e->getMessage();
		   return;
		 }
		 if ($rs->rowCount() > 0) {
           $fieldOrderno = 1;
		   $sql = 'DELETE FROM DataDict where dbname=? and tablename=?'; 
		   $params = array($dbname,$tablename);
		   pdoExecuteQuery($pdodev,$sql,$params) ;

		   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			   $fieldName =  $row['Field'] ;
			   $fieldType =  $row['Type'] ;
			   if ($row['Key'] == 'PRI') {
				 $isPrimaryKey =  'y' ;
			   } else {				 
                 $isPrimaryKey =  'n' ;
			   }
			   if ($row['Key'] == 'MUL') {
				 $isIndex =  'y' ;
			   } else {				 
                 $isIndex =  'n' ;
			   }
			   $fieldComment ='';
			   $fieldComment = getCommentFieldPDO($pdo,$tablename,$fieldName) ;
			   if ($fieldComment=='') {
				  $fieldComment= $fieldName ;
			   }
			   //echo $fieldName . '::' . $fieldComment . '<br>';
			   $lastupdate = getCurrentDateTime() ;
			   $sql='INSERT INTO DataDict(DBName,TableName, FieldOrderNo,FieldName, FieldComment, FieldType, FieldSize, isPrimaryKey, isIndex, lastupdate) 
               VALUES (?,?,?,?,?,?,?,?,?,?)'; 
			   $ErrMsg  = '';			   			  			   
			   try {
			           
			      $params = array($dbname,$tablename,$fieldOrderno,$fieldName,$fieldComment,$fieldType,0,$isPrimaryKey,$isIndex,$lastupdate); 
			      $rs2 = $pdodev->prepare($sql);
			      $rs2->execute($params);
				  $fieldOrderno++ ;
			      
			   } catch (PDOException $ex) {
			      echo  $ex->getMessage();
			   
			   } catch (Exception $exception) {
			           // Output unexpected Exceptions.
			           Logging::Log($exception, false);
			   }
			    			  		    
		   } // end while
		    
		 }


} // end function

function GetTableNameListONDB($dbname='') {

	
	$ErrMsg  = '';
	if ($dbname == '') {		 
	  $pdo = getPDO(true,$ErrMsg);
	} else {
	 
      $pdo = getPDO2($dbname,true);
	}

	$result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
       $tblAr[] = $row[0] ;
    }
	return $tblAr ;


	try {
	  $rs = $pdo->prepare($sql);
	  $rs->execute();
	} catch (Exception $e) {
	  echo 'Message: ' .$e->getMessage();
	}
	if ($rs->rowCount() > 0) {
	  while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	    $tblAr[] = $row['TableName'] ;	   
	  }
	} else {
  	$tblAr = array('');
	}
	return $tblAr ;


} // end function

function UniverSalSavemage($dbname,$data,$pathImages,$TypeProduct,&$output_1fileList999,&$output_multiplefileList) {

         if ($TypeProduct === 'realestate') {
			 //$sPath = 'asset/land/' ;
			 $sDat = explode('|',$data['sDat']) ;
             $realestateID = explode('->',trim($sDat[0])) ;
			 $realestateID = $realestateID[1] ;

			 //echo "----realestateID= " . $realestateID;
			 $dbname = 'ddhousin_home2hand111' ;
			 $pdo = getPDO2($dbname,false)  ;			 
			 $sql = "select TypeCode from realestate where realestate_ID=?" ; 
			 
			 $params = array($realestateID) ;
			 $OldTypeCode = pdogetValue($sql,$params,$pdo) ;
			   
			 $TypeCode = explode('->',$sDat[1]) ;
			 $NewTypeCode = trim($TypeCode[1]) ;

			 if ($OldTypeCode != $NewTypeCode) {
				$MoveAssetFolder = true;
				echo "---Move Asset OldTypecode= $OldTypeCode VS  $NewTypeCode";
			 } else {
				$MoveAssetFolder = false;
				echo "---Not Move Asset ";
			 }
 


			 $realestateID = '';$AssetTypeCode = '';
			 $AssetPath = getPathofAsset($dbname,$data,$realestateID,$AssetTypeCode) ;
			 if (substr($AssetPath,0,2) == '..')  {
               $AssetPath2 = substr($AssetPath,3,strlen($AssetPath)) ;
			 } else {
               $AssetPath2 = $AssetPath;
			 }

			 /*echo "realestateID==>" . $realestateID ; 
             echo '<br>AssetTypeCode=' . $AssetTypeCode . ' PathAsset ='. $AssetPath ;
			 */

			 /* Save Main Images */
     $imgData = trim($data['sDat1Images']) ;
	 $output_1fileList999 = '';
	 if ($imgData != '') {	 
		      	 
			 $rnd = rand(0,9999);
			 //$output_file = $pathImages . '/' . $rnd. '.jpg' ; 
			 $output_file = $AssetPath . '/' . $rnd. '.jpg' ; 
			 //$output_file2 = $AssetPath2 . '/' . $rnd. '.jpg' ; 
			 $output_file2 =   $rnd. '.jpg' ; 
			 $img = trim($imgData) ;
			 if (substr($img,0,5) ==='data:' ) {			 
				 $image_parts = explode(";base64,", $img);
				 $image_type_aux = explode("image/", $image_parts[0]);
				 $image_type = $image_type_aux[1];
				 $image_base64 = base64_decode($image_parts[1]); 
				 file_put_contents($output_file, $image_base64);
				 $output_1fileList999 =  $output_file2 ;
			 } else {
				 $output_1fileList999 =  $img ;
			 }
			 if (substr($img,0,4) ==='http' ) {			 
				 $imgAr = explode("/",$img) ;
				 $output_1fileList999 = $imgAr[count($imgAr)-1] ;

			 }

		  
		  ?>
          <hr>
		  <img src="<?=$output_1fileList999?>" class="img-fluid" style='width:100px;margin:10px'>
<?php		 
	 } 

			$imgData = trim($data['sDatMultiPleImages']) ;
	        if ($imgData != '') {	 
              if (substr($imgData,-2) == '@#') {
                 $imgData =  substr($imgData,0,-2) ;
              }
		      $imgDataAr = explode('@#',$imgData) ;
		      for ($i=0;$i<=count($imgDataAr)-1;$i++) {	    	 
                if (substr($imgDataAr[$i],0,5) ==='data:' ) {
				 $rnd = rand(0,9999);
				 $output_file = $AssetPath . '/' . $rnd. '.jpg' ; 
				 $output_file2 =   $rnd. '.jpg' ; 
				 $img = trim($imgDataAr[$i]) ;
				 $image_parts = explode(";base64,", $img);
				 $image_type_aux = explode("image/", $image_parts[0]);
				 $image_type = $image_type_aux[1];
				 $image_base64 = base64_decode($image_parts[1]); 
				 file_put_contents($output_file, $image_base64);
				 $output_multiplefileList[] =  $output_file2 ;
				} else {

                  //$output_multiplefileList[] =  $imgDataAr[$i];
				  $imgAr = explode("/",$imgDataAr[$i]) ;
				  $output_multiplefileList[] = $imgAr[count($imgAr)-1] ;
				}
				

		     }	 
			 for ($i=0;$i<=count($output_multiplefileList)-1;$i++) { ?>
				<img src="<?=$output_multiplefileList[$i]?>" class="img-fluid" style='width:100px;margin:10px'>
			 <?php }
	       } // end if
		   echo implode('---',$output_multiplefileList);




         }

$imgData = trim($data['sDatMultiPleImages']) ;



} // end function

function getPathofAsset($dbname,$data,&$realestateID,&$AssetTypeCode) {

         $sDatAr = explode('|',$data['sDat']) ;
		 for ($i=0;$i<=count($sDatAr)-1;$i++) { 
				 $st = substr($sDatAr[$i],0,13);
                 if ($st === 'span_TypeCode') {
					$AssetTypeCodeAr= explode('->',$sDatAr[$i]);
					$AssetTypeCode = $AssetTypeCodeAr[1];
					//break;
                 }
				 
                 if ($st === 'realestate_ID') {
					$realestateIDAr = explode('->',$sDatAr[$i]);
					$realestateID = $realestateIDAr[1] ;
					
                 }				 
		 } // end for



         $sql = "select sPath from realestate_type where TypeCode=?"; 			
		 $pdo= getPDO2($dbname,false) ;		 	
		 $params = array($AssetTypeCode) ;
		 $sPath = pdogetValue($sql,$params,$pdo) ;

		 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
		  
		 

		 $Path = "../ARealestate/";
		 //$Path = $newUtilPath . "../ARealestate/";
		 if (!file_exists($Path)) {
            mkdir($Path,true,0777); 
			chmod($Path,0777); 
		 }

         $Path = "../ARealestate/". $sPath ;
		 if (!file_exists($Path)) {
            mkdir($Path,true); 
			chmod($Path,0777); 
		 }
		 $Path = "../ARealestate/" . $sPath. "/" . $realestateID;
		 if (!file_exists($Path)) {
            mkdir($Path,true); 
			chmod($Path,0777); 
		 }

		 
         //mkdir($sPath,true); 

		 return $Path ;





} // end function


function UniversalSaveData($dbname,$data,$pathImages='') {


$output_multiplefileList = '';
$output_1fileList999 = '';     

         return;
		 

 //echo $thisURL ; return;
// print_r($data); return ;

 $sMIm = $data['sDatMultiPleImages'] ;
 $sMIm = explode("@#",$sMIm) ;
// echo "-->" . count($sMIm);


      
$output_1fileList999 = '';
$output_multiplefileList = '';
       
		 
		$TypeProduct = 'realestate';
        UniverSalSavemage($dbname,$data,$pathImages='',$TypeProduct,$output_1fileList999,$output_multiplefileList)  ;

		 $tablename = $data['TableName'];
		 $formname = $data['FormName'];
		// $formname = 'realestate_V5';


		 $sDatAr = explode('|',$data['sDat']) ;
		 //print_r($sDatAr);
		 
		 $sql = "select * from formParam where FormName=?
         and (useInputName <> 'FileImage' and useInputName <> 'MultipleFileImages' )        
		 ORDER BY FieldOrderNo "; 

		 
		 
		 

		 $pdo= getPDO2($dbname,true) ;

		 $sql = "select * from formParam where FormName=?                 
		 ORDER BY FieldOrderNo "; 

         echo 'FormName=' . $formname;
		 $params = array($formname);
		 $rs  = getPDORS($pdo,$sql,$params) ;
		 //echo '<hr> totalfield='  . $rs->rowCount() . '<hr>' ;
		 



		 $sqlInsert = 'INSERT INTO ' . $tablename .'(' ; 
		 $sqlInsert2 = ' VALUES(';

		 $sqlUpdate = 'UPDATE ' . $tablename .' SET ' ; 
		 $whereClause = ' WHERE '; 

		 $sqlReplace = 'REPLACE INTO ' . $tablename .'(' ;
		 
		 if ($rs->rowCount() > 0) {
           
		   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		      $sqlInsert .= $row['FieldName'] . ',' ;
			  $sqlReplace .= $row['FieldName'] . ',' ;
			  $sqlInsert2 .= '?,';
			  $sqlUpdate .= $row['FieldName'] . '=?,' ;
			  if ($row['isPrimaryKey'] === 'y') { 
                 $whereClause .= $row['FieldName'] .'=?,' ;
			  }
		   }
		   $sqlInsert = substr($sqlInsert,0,strlen($sqlInsert)-1) .') ' ;
		   $sqlInsert2 = substr($sqlInsert2,0,strlen($sqlInsert2)-1) .')' ;           
		   $sqlUpdate = substr($sqlUpdate,0,strlen($sqlUpdate)-1) ;
		   $whereClause = substr($whereClause,0,strlen($whereClause)-1) ;

		   $sqlReplace = substr($sqlReplace,0,strlen($sqlReplace)-1) .') ' ;
		 } 
		  
		 //echo  $sqlInsert .  $sqlInsert2 ;
		 //echo  $sqlUpdate .  $whereClause ;
		 //echo $sqlReplace . $sqlInsert2 ;

		 $sqlReplace = $sqlReplace . $sqlInsert2 ;
		// echo $sqlReplace . '<br>'  ;
		 $output_multiplefileList2 = '' ;
		 $output_1fileList = '' ; 

/* New Method Section */ 
         for ($i=0;$i<=count($sDatAr)-1;$i++) {
			 $sField = explode('->',$sDatAr[$i]) ;
			 $fNameTmp = $sField[0] ; $fValue[] = trim($sField[1]) ;
			 if (substr($fNameTmp,0,4) == 'span' ) {
				$fNameTmp = substr($fNameTmp,5,strlen($fNameTmp));
			 }  
			 if (substr($fNameTmp,0,3) == 'chk' ) {
				$fNameTmp = substr($fNameTmp,4,strlen($fNameTmp));
			 } 
			 if (substr($fNameTmp,0,7) == 'txtArea' ) {
				$fNameTmp = substr($fNameTmp,8,strlen($fNameTmp));
			 } 

			 if ($fNameTmp == 'DISTRICT_ID' ) {
				$fNameTmp = 'tumbol';
			 } 
			 if ($fNameTmp == 'AMPHUR_ID' ) {
				$fNameTmp = 'amphor';
			 }
			 if ($fNameTmp == 'PROVINCE_ID' ) {
				$fNameTmp = 'provinceCode';
			 }			 
			 $fName[] = $fNameTmp;			 
         } 
		 array_push($fName,'mainImageFileName') ;
		 array_push($fName,'ImageFileName') ;

		 array_push($fValue,$output_1fileList999) ;
		 array_push($fValue,implode('|',$output_multiplefileList)) ;

		 $sqlNew = "REPLACE INTO $tablename(" . implode(',',$fName) . ') VALUES('; 
		 $sqlNew .= implode(',',$fValue) . ')' ;


		 $totalField = count($fName) ;
		 $sqlNew2 = "REPLACE INTO $tablename(" . implode(',',$fName) . ') VALUES('; 
		 $sqlNew2 .=  str_repeat('?,',$totalField) ;
		 $sqlNew2 =  substr($sqlNew2,0,-1) . ')' ;

		  
		 $myfile = fopen("../src/resulttmp.txt", "w") or die("Unable to open file!");
		 try {		         
		    //$params = array($sDatAr[0],$sDatAr[1]); 
			$params =  $sDatAr;
			if ($output_multiplefileList != '' && count($output_multiplefileList) > 0) {
			  $output_multiplefileList2 = implode("|",$output_multiplefileList);
			} else {
			  $output_multiplefileList2 ='';
			}
			
			//echo "Image Saved:-- " . $output_multiplefileList2 ;
			array_push($params,$output_1fileList999) ;
			array_push($params,$output_multiplefileList2) ;
			
			for ($i=0;$i<=count($params)-1;$i++) {
				if (trim($params[$i])!== '') {				
				  $cc = explode('->',$params[$i]) ;
				  if (isset($cc[1])) {				
				    $params2[] = $cc[1] ;
				  } else {
					 $params2[] = $params[$i];
				  }
				} else {
                  $params2[] = '';
				}
			}

			//fwrite($myfile, $sqlReplace."\n");
			//$st2 = implode('|',$params2) ;
			
			//fwrite($myfile, $st2."\n");
			fwrite($myfile, $sqlNew2."\n");
			fwrite($myfile, implode('|',$fValue) ."\n");


		    fclose($myfile);
			//echo 'MainImage : ' . $output_1fileList999 . '<br>';
			//echo 'Params : ' ;
			//print_r($params);
			
//		    $rs = $pdo->prepare($sqlReplace);
			$rs = $pdo->prepare($sqlNew2);

		    //$rs->execute($params2);
			$rs->execute($fValue);
		    
		 } catch (PDOException $ex) {
		    echo  "Error--" . $ex->getMessage();
			return false;
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
              echo "Error2";
		      Logging::Log($exception, false);
			  return false;
		 } 

		 $sql = "UPDATE  realestate a inner join realestate_type b on a.TypeCode=b.TypeCode 
         set a.ImagePath1 = concat('ARealestate/',b.sPath) "; 
		 
		 
		 try {
		   $rs = $pdo->prepare($sql);
		   $rs->execute();
		 } catch (Exception $e) {
		   echo 'Message: ' .$e->getMessage();
		 } 

         $realestateID = explode('->',$sDatAr[0]) ;
		 $realestateID  = $realestateID[1] ;


		 $OldImagePath = $sDatAr[2] ;
		 $OldImagePath2 = explode('->',$OldImagePath) ;
		 $OldImagePath3 = trim($OldImagePath2[1]) ;


		 $sql = "select ImagePath1 from realestate where realestate_ID=?" ; 			 
		 $params = array($realestateID) ;
		 $NewImagePath = pdogetValue($sql,$params,$pdo) ;
			   
		
		 if ($OldImagePath  != $NewImagePath) {
		 	$MoveAssetFolder = true;
			echo "---Move Asset999999  ";
			$source = "../$OldImagePath3/$realestateID" ;
			$target = "../$NewImagePath/$realestateID" ;
			rename($source,$target);
		 } else {
			$MoveAssetFolder = false;
			echo "---Not Move Asset ";
		 }
		  
		$pdo->commit();
		echo 'Success' ; 
		return true;
		 



} // end function

function writeProvinceStyleTable() {  


		 $ErrMsg  = '';
		 $pdo = getPDO(false,$ErrMsg);
		 $sql = "SELECT PROVINCE_ID,PROVINCE_NAME FROM province order by PROVINCE_ID" ;
		 $params = ''; 
		 $rs = getPDORS($pdo,$sql,$params) ;
		  
		 ?>
          
		    
		  <div class= ' ' style=' height:100vh;overflow:scroll'>
		     <table class='mTable999 TableFK' id= 'tblData_PROVINCE_ID' style='width:100%;border-collapse: collapse;   '>
			   <thead>
		         <tr style='background:#CFD8DC'>
			 	   <td style='width:100px'>รหัสจังหวัด</td>
				    <td>ชื่อจังหวัด</td>
		         </tr>
			  </thead>
			  <tbody>

         <?php
		 if ($rs->rowCount() > 0) {
           $rowno = 1 ;
		   while($row = $rs->fetch( PDO::FETCH_ASSOC )) { ?>
			 <tr onclick="SetFKKey('PROVINCE_ID',
			   <?=$rowno++?>,
			   '<?=$row['PROVINCE_ID']?>',
			   '<?=$row['PROVINCE_NAME']?>'
			   )">
				<td><?=$row['PROVINCE_ID']?></td>
				<td><?=$row['PROVINCE_NAME']?></td>
		     </tr>		      
		   <?php 			    
		   }
		 }

		 ?>
		  </tbody>	  
		</table>
       </div>
 	      

<?php
} // end function 


function writeAmphurStyleTable($data) {  


		 $ErrMsg  = '';
		 $pdo = getPDO(true,$ErrMsg);
		 $sql = "SELECT AMPHUR_ID,AMPHUR_NAME FROM amphur WHERE PROVINCE_ID=? order by AMPHUR_ID" ;
		 $ErrMsg  = '';	
		 $params = array($data['sDat']); 
		 $rs = getPDORS($pdo,$sql,$params) ;
		  
		 ?>
          
		    
		  <div class= ' ' style=' height:100vh;overflow:scroll'>
		     <table id= 'tblData_AMPHUR_ID' class= 'mTable999 TableFK' style='width:100%;border-collapse: collapse;   '>
			   <thead>
		         <tr style='background:#CFD8DC'>
			 	   <td style='width:100px'>รหัสอำเภอ</td>
				    <td>ชื่ออำเภอ</td>
		         </tr>
			  </thead>
			  <tbody>

         <?php
		 if ($rs->rowCount() > 0) {
           $rowno = 1 ;
		   while($row = $rs->fetch( PDO::FETCH_ASSOC )) { ?>
			   <tr onclick="SetFKKey('AMPHUR_ID',
			   <?=$rowno++?>,
			   '<?=$row['AMPHUR_ID']?>',
			   '<?=$row['AMPHUR_NAME']?>'
			   )">
				<td><?=$row['AMPHUR_ID']?></td>
				<td><?=$row['AMPHUR_NAME']?></td>
		     </tr>
		      
		   <?php 
			    
		   }
		 }

		 ?>
		  </tbody>	  
		</table>
       </div>
 	      

<?php
} // end function 


function writeTumbolStyleTable($data) {  


		 $ErrMsg  = '';
		 $pdo = getPDO(true,$ErrMsg);
		 $sql = "SELECT DISTRICT_ID,DISTRICT_NAME FROM district WHERE PROVINCE_ID=? AND AMPHUR_ID=? order by DISTRICT_ID" ;
		 $ErrMsg  = '';	
		 $params = array($data['sDat'],$data['sDat2']); 
		 $rs = getPDORS($pdo,$sql,$params) ;
		  
		 ?>
          
		    
		  <div class= ' ' style='width:100%; height:100vh;overflow:scroll'>
		     <table id= 'tblData_DISTRICT_ID' class='mTable999 TableFK' style='width:100%;border-collapse: collapse;   '>
			   <thead>
		         <tr style='background:#CFD8DC'>
			 	   <td style='width:100px'>รหัส ตำบล</td>
				    <td>ชื่อ ตำบล</td>
		         </tr>
			  </thead>
			  <tbody>

         <?php
		 if ($rs->rowCount() > 0) {
           $rowno = 1 ;
		   while($row = $rs->fetch( PDO::FETCH_ASSOC )) { ?>
			   <tr onclick="SetFKKey('DISTRICT_ID',
			   <?=$rowno++?>,
			   '<?=$row['DISTRICT_ID']?>',
			   '<?=$row['DISTRICT_NAME']?>'
			   )">
				<td><?=$row['DISTRICT_ID']?></td>
				<td><?=$row['DISTRICT_NAME']?></td>
		     </tr>
		      
		   <?php 
			    
		   }
		 }

		 ?>
		  </tbody>	  
		</table>
       </div>
 	      

<?php
} // end function 


function getAssetOld($data)  { 

	     $realestateID = $data['sDat'] ;
		 $tablename = 'realestate'; 
		 $FormName= 'realestate_V5'; 

//		 $FormName= 'tmp'; 

		 
		 $dbname = 'ddhousin_home2hand111';
		 $pdo = getPDO2($dbname,true);

		 $sql = "SELECT * FROM formParam where FormName=? and TableName=? ORDER BY FieldOrderNo";
		 
		 try {
		         
		    $params = array($FormName,$tablename); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		   
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		       $FieldNameAr[] = $row['FieldName'] ;
		    }
		 }

		 $sql  = "SELECT " . implode(",",$FieldNameAr) . " FROM $tablename WHERE realestate_ID=?" ;

		 $ErrMsg  = ''; 
		// echo $sql . '-->' . $realestateID;
		 
		 
		 
		 try {
		         
		    $params = array($realestateID); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
		 $sData = '';
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		       for ($i=0;$i<=count($FieldNameAr)-1;$i++) {
		        //  $sData .= $row[$i]. '@#' ;
				  $sData .= $row[$FieldNameAr[$i]]. '@#' ;
		       }
		    }
		 } else {
           echo 'Not-Found'; return ;
		 }

		 $ImgFileNameList = getImageAsset($pdo,$data);
		 $sData .= $ImgFileNameList ;
		 echo $sData ; 
		 
		  



} // end function

function getAsset($data)  { 

	     $realestateID = $data['sDat'] ;
		 $tablename = 'realestate'; 
		 $FormName= 'realestate_V5'; 

//		 $FormName= 'tmp'; 

		 
		 $dbname = 'ddhousin_home2hand111';
		 $pdo = getPDO2($dbname,true);

		 $sql = "SELECT * FROM formParam where FormName=? and TableName=? ORDER BY FieldOrderNo";
		 
		 try {
		         
		    $params = array($FormName,$tablename); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		   
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		       $FieldNameAr[] = $row['FieldName'] ;
		    }
		 }

		 $sql  = "SELECT " . implode(",",$FieldNameAr) . " FROM $tablename WHERE realestate_ID=?" ;

		 $ErrMsg  = ''; 
		//echo $sql . '-->' . $realestateID;
		 
		 
		 
		 try {
		         
		    $params = array($realestateID); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }
		 $sData = '';
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		       for ($i=0;$i<=count($FieldNameAr)-1;$i++) {
		        //  $sData .= $row[$i]. '@#' ;
				  $sData .= $FieldNameAr[$i].'->'.$row[$FieldNameAr[$i]]. '@#' ;
		       }
		    }
		 } else {
           echo 'Not-Found'; return ;
		 }

		 $ImgFileNameList = getImageAsset($pdo,$data);
		 $sData .= $ImgFileNameList ;
		 echo $sData ; 
		 
		  



} // end function

function getImageAsset($pdo,$data) {



	     $sql = "select transno,sPath from realestate a inner join realestate_type b on a.TypeCode=b.TypeCode where realestate_ID=?"; 
		 $ErrMsg  = '';
		 $realestateID = $data['sDat'] ;

		 
		 try {
		         
		    $params = array($realestateID); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 }

		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		        $transno = $row['transno'] ;
				$assetPath  = $row['sPath'] ;
		    }
		 } else {
           $ImgFileNameList = '';
		   return $ImgFileNameList ;
		 }

         $MainPath  ='asset/' . $assetPath .'/' . $realestateID.'/realimage/';
		 $ImgFileNameList = '';
		 for ($i=0;$i<=3;$i++) {
            $ImgFileNameList .= $MainPath . $realestateID. '_'.$i .'_'. $transno . '.jpg'. '|';		     
		 }
		 return $ImgFileNameList ;



	     

} // end function

function MoveFolder($source,$target) {

	if( !rename($source,$target) ) {  
      echo "File can't be moved!";  
	  return false;
    } else {  
      echo "File has been moved! From $source To $target";  
	  return true ;
    } 
	  
	     

} // end function

function MoveAssetPath($AssetTypeCodeSource,$AssetTypeCodeTarget,$realestateID) { 

         $dbname ='ddhousin_home2hand111'  ;
         $pdo= getPDO2($dbname,true) ;		 

         $sql = "select sPath from realestate_type where TypeCode=?"; 	     
		 $params = array($AssetTypeCodeSource) ;
		 $sourcePath = pdogetValue($sql,$params,$pdo) ; 

		 $sql = "select sPath from realestate_type where TypeCode=?"; 	     
		 $params = array($AssetTypeCodeTarget) ;
		 $targetPath = pdogetValue($sql,$params,$pdo) ; 

		 $source = 'ARealestate/' . $sourcePath .'/' . $realestateID ;
		 $target = 'ARealestate/' . $targetPath  .'/' . $realestateID ;

         if (MoveFolder($source,$target)) {
			 $sql = "select mainImageFileName,ImageFileName from realestate where realestate_ID=?"; 
			  
			 try {
			         
			    $params = array($realestateID ); 
			    $rs = $pdo->prepare($sql);
			    $rs->execute($params);

			 } catch (PDOException $ex) {
			    echo  $ex->getMessage();
			 
			 } catch (Exception $exception) {
			         // Output unexpected Exceptions.
			         Logging::Log($exception, false);
			 }
			 if ($rs->rowCount() > 0) {
			    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			        $mainImageFileName = $row['mainImageFileName'] ;
					$ImageFileName = $row['ImageFileName'] ;
					$NewmainImageFileName =replace99($sourcePath,$targetPath,$mainImageFileName ) ;
					$NewImageFileName =replace99($sourcePath,$targetPath,$ImageFileName ) ;
					
			    }
			 }
 
             echo "<hr>";
			 echo $mainImageFileName . ' --- > ' . $NewmainImageFileName  ."<br>"; 
			 echo $ImageFileName . ' ----> '. $NewImageFileName  ."<br>"; 

			 $sql = "update realestate set mainImageFileName=?,ImageFileName =?  where realestate_ID=?"; 
			 try {
			         
			    $params = array($mainImageFileName,$ImageFileName,$realestateID); 
			    $rs = $pdo->prepare($sql);
			    $rs->execute($params);
			    $pdo->commit();
			 } catch (PDOException $ex) {
			    echo  $ex->getMessage();
			 
			 } catch (Exception $exception) {
			         // Output unexpected Exceptions.
			         Logging::Log($exception, false);
			 }		
         }

} // end function

function getAllPathImageRealestate($pdo) {
	 
	     
	     $sql = "select concat(TypeCode,'-',sPath) as sPath from realestate_type order by  TypeCode";  
		 $params = array();   
		 
		 
		 try {
		   $rs = $pdo->prepare($sql);
		   $rs->execute();
		 } catch (Exception $e) {
		   echo 'Message: ' .$e->getMessage();
		 }
		  
         $ImagePath =  ''; 
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		       $ImagePath .= $row['sPath'] . '@#' ;
		    }
		 }
		 $ImagePath = substr($ImagePath,0,strlen($ImagePath)-2);
		 return $ImagePath ;
	     
} // end function

function TestPath() {

	     echo 'Test Call From SubDomain ' ;

} // end function

function CreateSubdomain() {

// ref https://www.directadmin.com/api.html
// ref https://pantip.com/topic/39672942
// httpsocket.php ทำได้หลายอย่างควร หา ต.ย มาดู

include 'httpsocket.php';

$sock = new HTTPSocket;
$sock->connect('localhost',2222);

$sock->set_login("ddhousin","y4e2Q44rBw");

$sock->set_method('POST');

$sock->query('/CMD_API_SUBDOMAINS',
        array(
                'action' => 'create',
                'domain' => 'https://lovetoshopmall.com',
                'subdomain' => 'testsubdomain',
                'create' => 'Create'
 ));

$result = $sock->fetch_body();


} // end function

function pathToURL($path) {
  //Replace backslashes to slashes if exists, because no URL use backslashes
  $path = str_replace("\\", "/", realpath($path));

  //if the $path does not contain the document root in it, then it is not reachable
  $pos = strpos($path, $_SERVER['DOCUMENT_ROOT']);
  if ($pos === false) return false;

  //just cut the DOCUMENT_ROOT part of the $path
  return substr($path, strlen($_SERVER['DOCUMENT_ROOT']));
  //Note: usually /images is the same with http://somedomain.com/images,
  //      So let's not bother adding domain name here.
}


function ManageDataService($data,$ownerPath,$ownerURL) { 

         
	     echo $ownerPath . '<br>' ;
		 echo $ownerURL ; 
         //$ownerURL = substr($ownerURL,1,100);
		 if ($data) {
			 if ($data['Mode'] === 'showImages') {
				$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	            require_once($newUtilPath . "src/clsLayoutNew.php"); 
                $clsLayout = new LayOutService(__DIR__);
                $clsLayout->TestHeader($ownerPath,$data) ;
	            return;
             }
		 }



} // end function

function getJsonComponentData($componentName,&$jsonData,&$cssStyle,&$cssDecorate){
global $shopid ;

	$dbname = 'ddhousin_devshop' ;
	//$shopcode = '6664';
	$pdo = getPDO2($dbname,false)  ;
	$sql = "select jsonData,cssStyle,cssDecorate  from ShopSalePage where shopCode=? and componentName=?"; 
	$ErrMsg  = ''; 

	

	try {
	        
	   $params = array($shopid,$componentName); 
	   $rs = $pdo->prepare($sql);
	   $rs->execute($params);
	   //$pdo->commit();
	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
	}
	if ($rs->rowCount() > 0) {
	   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	       $jsonData = $row['jsonData'];
		   $cssStyle = $row['cssStyle'];
		   $cssDecorate = $row['cssDecorate'];
	   }
	  // return json_decode($jsonData);
	}
	

	 

} // end function

function getShopCode($shopname,&$themeName) { 

	$dbname = 'ddhousin_shopproject' ;
	$pdo = getPDO2($dbname,false)  ;
	$ErrMsg  = '';
	$sql = "SELECT shopID,ThemeName FROM shopMaster where shopname=?";
	
	try {
	        
	   $params = array($shopname); 
	   $rs = $pdo->prepare($sql);
	   $rs->execute($params);

	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
	}
	if ($rs->rowCount() > 0) {
	   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	       $shopid =$row['shopID'];
		   $themeName = trim($row['ThemeName']) ;
	   }
	} else {
       return -1 ; 
	}
	return  $shopid    ;
	

} // end function

function isExist($tablename,$sname,$value) { 

	
	$dbname = 'ddhousin_shopproject' ;
	$pdo = getPDO2($dbname,false)  ;
	$sql = "SELECT * FROM $tablename where $sname = ?" ;
	
	try {
	        
	   $params = array($value); 
	   $rs = $pdo->prepare($sql);
	   $rs->execute($params);
	   //$pdo->commit();
	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
	}
	echo 'Count-->' .$rs->rowCount();
	if ($rs->rowCount() > 0) {
	   return $rs->rowCount() ;
	} else {
       return '-1' ;
	}
	


} // end function


function getNumRow($pdo,$tablename,$sname,$value) { 

	
	//$dbname = 'ddhousin_shopproject' ;
	//$pdo = getPDO2($dbname,false)  ;
	$sql = "SELECT count(*) as NumRow FROM $tablename where $sname = ?" ;
	
	try {
	        
	   $params = array($value); 
	   $rs = $pdo->prepare($sql);
	   $rs->execute($params);
	   //$pdo->commit();
	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
	}
	//echo 'Count-->' .$rs->rowCount();
	if ($rs->rowCount() > 0) {	    
	   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		   return $row['NumRow']  ;
	   }	   
	} else {
       return '-1' ;
	}
	



} // end function


function CreateShop($shopname) {
         
		 $shopDir = '../'.$shopname  ; 
		 if (!file_exists($shopDir) ) {
		   mkdir($shopDir,true); 
		   chmod($shopDir,0777); 
		 }

		 $thisDir = $shopDir . '/images' ;
		 if (!file_exists($thisDir) ) {
		   mkdir($thisDir,true); 
		   chmod($thisDir,0777); 
		 }

		 $thisDir = $shopDir . '/component' ;
		 if (!file_exists($thisDir) ) {
		   mkdir($thisDir,true); 
		   chmod($thisDir,0777); 
		 }
         if (!file_exists($thisDir) ) {
		  $thisDir = $shopDir . '/admin' ;
		  mkdir($thisDir,true); 
		  chmod($thisDir,0777); 
		 }

		 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
		 
		 $somePath = $newUtilPath .'/component' ;
         $all_dirs = glob($somePath . '/*' , GLOB_ONLYDIR);

         for ($i=0;$i<=count($all_dirs)-1;$i++) {
	        echo $all_dirs[$i] .  "<hr>";     
			$st = explode('/',$all_dirs[$i]) ;
			$compo = $st[count($st)-1] ;

			$MallDir = $newUtilPath .'/component/'. $compo . '' ; 
			$compDir = $newUtilPath .$shopname .'/component/'. $compo ; 


		    if (!file_exists($compDir)) {
		       mkdir($compDir,true); 
			   chmod($compDir,0777); 
		    }
			$compDir = $newUtilPath .$shopname .'/component/'. $compo ;
			xcopy($MallDir,$compDir);
          }

		 


} // end function

function xcopy($src, $dest) {


    foreach (scandir($src) as $file) {
        if (!is_readable($src . '/' . $file)) continue;
        if (is_dir($src .'/' . $file) && ($file != '.') && ($file != '..') ) {
            mkdir($dest . '/' . $file);
            xcopy($src . '/' . $file, $dest . '/' . $file);
        } else {
			if (!is_Dir($src . '/' . $file) ) {
              copy($src . '/' . $file, $dest . '/' . $file);
			}
        }
    }
}

function JsonResult($resultcode,$resultmsg,$resultdata) {

$myObj = new stdClass();
$myObj->resultcode = $resultcode;
$myObj->resultmsg = $resultmsg;
$myObj->resultdata = $resultdata;

$myJSON = json_encode($myObj);
echo $myJSON ;


} // end function

function jpg2webp($source,$target) {

	  //The file path of your image.

$imagePath = 'asset/house/2059/realimage/2059_9_1576480778.jpg';
$imagePath = $source;
//Create an image object.
$im = imagecreatefromjpeg($imagePath);

//The path that we want to save our webp file to.
$newImagePath = str_replace("jpg", "webp", $imagePath);
$newImagePath = $target;


//Quality of the new webp image. 1-100.
//Reduce this to decrease the file size.
$quality = 100;

//Create the webp image.
imagewebp($im, $newImagePath, $quality);


} // end function

function png2webp($source,$target) {

	  //The file path of your image.

$imagePath = 'asset/house/2059/realimage/2059_9_1576480778.jpg';
$imagePath = $source;
//Create an image object.

$im = imagecreatefrompng($imagePath);

//The path that we want to save our webp file to.
$newImagePath = str_replace("jpg", "webp", $imagePath);
$newImagePath = $target;


//Quality of the new webp image. 1-100.
//Reduce this to decrease the file size.
$quality = 100;

//Create the webp image.

imagewebp($im, $newImagePath, $quality);


} // end function

function CheckTokenData($token) { 

	     
		$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/src/class/component/';
        require_once($newUtilPath. "clsEncrypt.php");      
        $clsEncrypt = new clsEncrypt ; 

		$pageTokenData = $clsEncrypt->getUser($token) ; 
		$pageTokenData = json_decode($pageTokenData);

        return $pageTokenData->user; 
		echo $pageTokenData->PageCallerName; 



} // end function


function NewUniversalSaveData($data) { 

     if ($_SESSION['csrf'] !== $data['token'] ) {
		 echo "token miss"; 
		 return;
     } else {
          echo "token ok"; 
	 }

	 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	  
	 require_once($newUtilPath . "src/class/component/clsEncrypt.php"); 

	 $clsEncrypt = new clsEncrypt ; 
	 $sdat = $clsEncrypt->getUser($data['token']) ;
	 echo $sdat ; return;

	 

     echo $data['FormName'] ;
	 
	 $sql = "select TableName,FieldName from formParam where isSaved='y' and FormName=? order by FieldOrderNo"; 

	$ErrMsg  = '';
	$dbname = 'ddhousin_shopproject' ;
	$pdo = getPDO2($dbname,true)  ;	 		
	try {
	        
	   $params = array($data['FormName']); 
	   $rs = $pdo->prepare($sql);
	   $rs->execute($params);
	   //$pdo->commit();
	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
	}
	//$sqlInsert = "REPLACE INTO $TableName (" ;
//    $fList = ''; 
	if ($rs->rowCount() > 0) {
	   while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	      $fList[] = $row['FieldName'];  
		  $TableName = $row['TableName'] ;
	   }
	}
 
    $TableName =  'categoryShop';
	$sqlInsert = "REPLACE INTO $TableName (" ;
	$sqlInsert .= implode(',',$fList) . ') values(' .  str_repeat("?,",count($fList) )  ; 
	$sqlInsert = substr($sqlInsert,0,-1) .')' ; 

	/*$sqlInsert ='REPLACE INTO categoryShop(ShopID,Level,categoryDesc,categoryDesc_Eng) values(?,?,?,?)';

	echo "$sqlInsert";
	*/
	$sDat = explode('|',$data['sDat']) ; 

	for ($i=0;$i<=count($sDat)-1;$i++) {
		$thisDat = explode('->',$sDat[$i]) ;
		//echo $sDat[$i] . ',';
		echo $thisDat[1]. ",";
		$DataPost[] = $thisDat[1];
	   
	}

	$ErrMsg  = ''; 
	//print_r($DataPost);
	 
	
	try {
	        
	   //$params = array(?,?,?); 
	   $params = $DataPost;
	   $rs = $pdo->prepare($sqlInsert);
	   $rs->execute($params);
	   $pdo->commit();
	   $work = true ;
	} catch (PDOException $ex) {
	   echo  $ex->getMessage();
	
	} catch (Exception $exception) {
	        // Output unexpected Exceptions.
	        Logging::Log($exception, false);
			 $work = false ;

	}
	 
	$dbdata = '';
	// เธซเธฃเธทเธญ 
	if ($work) {
	     
	    $resultcode = 'success';
	    $resultmsg = 'เธเธ user'; 
	   // $resultdata = json_encode($dbdata) ;
	    $resultdata = ''; ;
	    JsonResult($resultcode,$resultmsg,$resultdata) ; 
	
	 } else {
	     $resultcode = '-1';
	     $resultmsg = 'เธเธทเนเธญเธเธนเนเนเธเนเธซเธฃเธทเธญเธฃเธซเธฑเธชเธเนเธฒเธเนเธกเนเธ–เธนเธเธ•เนเธญเธ'; 
	     $resultdata = '';
	     JsonResult($resultcode,$resultmsg,$resultdata) ;	     
	    return false;
	
	 }



} // end function

function UpdateFormParamHead() { 

  $newUtilPath = '/home/ddhousin/domains/home2hand111.com/private_html/';
  require_once($newUtilPath ."src/newutil.php"); 
  $dbname = 'ddhousin_home2hand111' ;
  $pdo = getPDO2($dbname,true)  ;

  
  $ErrMsg  = '';

  $sql ="select distinct(FormName) from formParam " ;
  try {
    $rs = $pdo->prepare($sql);
    $rs->execute();
  } catch (Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }
  if ($rs->rowCount() > 0) {
    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
       $formNameAr[] = $row['FormName'] ;
    }
  }

  

  $sql ="DELETE from formParamHead" ;
  try {
    $rs = $pdo->prepare($sql);
    $rs->execute();
  } catch (Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }
   

 // $pdo = getPDO2($dbname,true)  ;
  
  for ($i=0;$i<=count($formNameAr)-1;$i++) {
      $sql = "select * from formParam where FormName=? order by FieldOrderNo" ; 
      $params = array($formNameAr[$i]);
	  $rs = pdogetMultiValue2($sql,$params,$pdo) ;
	  echo $formNameAr[$i] . '<hr>' ;
//	  $fieldList = '';
	  if ($rs->rowCount() > 0) {
        while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
          $fList[] = $row['FieldName']  ;
		  $TableName = $row['TableName'] ;
      }
	  //echo implode(',',$fieldList) ;
	  
	  $sqlReplace = "REPLACE INTO ". $TableName ."(" ;
	  $sqlReplace .= implode(',',$fList) . ') values(' .  str_repeat("?,",count($fList) )  ; 
	  $sqlReplace = substr($sqlReplace,0,-1) .')' ; 
	  echo "$sqlReplace"; 

	  $sqlInsert = 'INSERT INTO formParamHead(formCode, TableName, formName, SQLReplace, NumField,lastupdate) 
	  VALUES (?,?,?,?,?,?)'; 
	  $formCode = uniqid();
	  
	  try {
	     $lastupdate = getCurrentDateTime() ;     
	     $params = array($formCode,$TableName,$formNameAr[$i],$sqlReplace,count($fList),$lastupdate); 
	     $rs = $pdo->prepare($sqlInsert);
	     $rs->execute($params);
	     
	  } catch (PDOException $ex) {
	     echo  $ex->getMessage();
	  
	  } catch (Exception $exception) {
	          // Output unexpected Exceptions.
	          Logging::Log($exception, false);
	  }
	   unset($fList); 
	  
	  echo '<hr>';
  }
        

     
  }
 $pdo->commit();  


} // end function


function gettokenData999($token,&$datatoken) {

$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
require_once($newUtilPath ."src/class/component/clsEncrypt.php");  


        

         if (isset($_GET["token"])) {
		   $token =  $_GET["token"];
             
         } 
		 $token =  replace99(' ','+',$token);

         
		 $clsEncrypt = new clsEncrypt ;        

		 $datatoken = $clsEncrypt->getUser($token) ;
		 $datatoken = json_decode($datatoken,true);


		 if (isset($datatoken['result']) &&  $datatoken['result'] === false) {
			 echo 'Token Mismatch' ; return false ;
		 }


		 //print_r( $datatoken ) ;

		 return $datatoken ;
		 //echo 'data -' .( $data) ;



} // end function

function AddClassCrudToForm($QueryName) { 

	     $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
		 require_once($newUtilPath ."src/newutil.php"); 		  		 
		 $dbname = 'ddhousin_shopproject' ;
         $pdo = getPDO2($dbname,false)  ;

		 require_once($newUtilPath . "src/clsCrud.php"); 
		  

		   
		  //$sql = "select * from categoryShop"; 
		  $sql='' ;
		  $keyCol = 3 ;
		  $clsCrud = new clsCrud($dbname,$QueryName,$pdo,$sql,$keyCol) ;
		  //$clsCrud->StyleFixHeader();
		  //$clsCrud->StyleClean();
		  $clsCrud->ResponsiveTable2();

} // end function

function SetOG_GraphA($title,$ItemCode,$ItemDesc,$ItemImage) {


	// getDataForGraph($ItemCode,$ItemName,$ItemDesc,$ItemURL,$ItemImage);
	 $PageURL =  getCurrentURL() ; 
	
	?>

<meta property="og:title"  content="<?=substr($title,0,560)?>"/>
<meta property="og:type"  content="website"/>
<meta property="og:url"  content="<?=urldecode($PageURL)?>"/>
<meta property="og:image"  content="<?=$ItemImage?>"/>
<meta property="og:site_name"    content="https://cigar.lovetoshopmall.com"/>
<meta property="fb:admins" content="000000000000000000"/>
<meta property="og:description"  content="<?=$ItemDesc?>"/>


<?php
}

function SetSessionLineID() { 

	     $_SESSION['login'] = 'y' ;


} // end function


function getJsonStringFromSQL($pdo,$sql,$params) { 

		
		$doworkSuccess = false ;
		try {        
		   
		   $rs = $pdo->prepare($sql);
		   $rs->execute($params);
		   $doworkSuccess = true ;
		   //$pdo->commit();
		} catch (PDOException $ex) {
		   echo  $ex->getMessage();

		} catch (Exception $exception) {
				// Output unexpected Exceptions.
				Logging::Log($exception, false);
		} 
		if ($rs->rowCount() > 0) {
           $DataArray2 = 
		   htmlspecialchars(json_encode($rs->fetchAll(PDO::FETCH_ASSOC)),ENT_QUOTES, 'UTF-8');
           //$json_decode = htmlspecialchars(json_encode($json_array), ENT_QUOTES, 'UTF-8');
           $st = $DataArray2;		   
		   $DataArray2 =   $DataArray2 ;
		   return $DataArray2 ;
		}


} // end function

function getPrimaryKey($pdo,$dbname,$tablename) { 

		 $sql = "SHOW COLUMNS FROM $tablename ";
		 $doworkSuccess = false ;
		 try {        
		    $params = array(); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    $doworkSuccess = true ;
		    //$pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 } 
		 $PKArray = array();
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
              if ($row['Key'] ==='PRI') {
		         $PKArray[] =  $row['Field'] ; 
			  }
		    }
		 }  
		  
		 return $PKArray ; 

	     

} // end function

function getForignKey($pdo,$dbname,$tablename) { 

	     
 $sql = "SHOW COLUMNS FROM $tablename ";
		 $doworkSuccess = false ;
		 try {        
		    $params = array(); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    $doworkSuccess = true ;
		    //$pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		         // Output unexpected Exceptions.
		         Logging::Log($exception, false);
		 } 
		 $PKArray = array();
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
              if ($row['Key'] ==='MUL') {
		         $PKArray[] =  $row['Field'] ; 
			  }
		    }
		 }  
		  
		 return $PKArray ; 

	     

} // end function

function getCSSFile($url) {

$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';


require_once $newUtilPath. 'simplehtmldom_1_9_1/simple_html_dom.php';

//echo "$url";
//$url = 'www.website-to-scan.com';
$website = file_get_html($url);

// You might need to tweak the selector based on the website you are scanning
// Example: some websites don't set the rel attribute
// others might use less instead of css
//
// Some other options:
// link[href] - Any link with a href attribute (might get favicons and other resources but should catch all the css files)
// link[href="*.css*"] - Might miss files that aren't .css extension but return valid css (e.g.: .less, .php, etc)
// link[type="text/css"] - Might miss stylesheets without this attribute set
$cssFile = '';

foreach ($website->find('link[rel="stylesheet"]') as $stylesheet)
{
    $stylesheet_url = $stylesheet->href;
	//echo '<br>' . $stylesheet_url ;
	$cssFile .= $stylesheet_url .' ; ';

    // Do something with the URL
}
return $cssFile ;

} // end function

function randomToken($len) { 
   srand( date("s") ); 
   // srand((double)microtime()*1000000);
   $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"; 
   $chars.= "1234567890!@#$%^&*()"; 
   $ret_str = ""; 
   $num = strlen($chars); 
   for($i=0; $i < $len; $i++) { 
     $ret_str.= $chars[rand()%$num]; 
   } 
   return $ret_str; 
}

function getJONOBJDOM($pagename,$lang,&$langList) { 

		 $dbname = 'ddhousin_devshop' ;
		 $pdo = getPDO2($dbname,false)  ;
		 $ErrMsg  = '';
		 
		 $sql = "SELECT domJSON FROM domproject_pagelist where pagename=?";
		 //echo "$sql" . '-->' . $pagename ;
		 $doworkSuccess = false ;
		 try {        
		    $params = array($pagename); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    $doworkSuccess = true ;
		    //$pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		    // Output unexpected Exceptions.
		    Logging::Log($exception, false);
		 } 
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		      $st =  trim($row['domJSON']) ; 
		    }
		 } else {
            echo "ไม่มีค่า domJSON ของ Page " .  $pagename ;  
			return false;
		 }
		// echo $st ;
		/*
		 $myfile = fopen("tmp.txt", "w") or die("Unable to open file!");
		 fwrite($myfile, $st);
		 fclose($myfile);
*/
		 $st = "";   
		 $file = fopen("tmp.txt","r");
		 while(! feof($file))  {
		    $st .= fgets($file) ;
		 }
		 fclose($file); 

		 //$st = clean($st) ;

		 


	 
		 $st = replace99("'","",$st) ;
		 $st = replace99("&amp;","",$st) ;

		 //echo $st2 ;
		  
		 $langList = '';
		 $error = isJsonOk($st,$pagename) ;
		 if (!isJsonOk($st,$pagename)) {
           return false;
		 } 
		 
		  

		 $stObj = json_decode($st); 
		 //$json_OK=	json_last_error() == JSON_ERROR_NONE;
		 //echo 'JSON-OK = ' . json_last_error() . '<br>' ; return ;

		
		 $foundLang = false;
		 for ($i=0;$i<=count($stObj)-1;$i++) {
			if ($stObj[$i]->lang === $lang) {
               $stLang = json_encode($stObj[$i]);
			   $foundLang = true ;
			   return $stLang;
			} 	
			$langList .= $stObj[$i]->lang .';';
		 }
		 
		// ไม่พบ  ข้อมูล ภาษาที่ ร้องมา เปลี่ยนค่าเป็น EN แทน ;
		$foundLang = false;
		$lang = 'en';
		 for ($i=0;$i<=count($stObj)-1;$i++) {
			if ($stObj[$i]->lang === $lang) {
               $stLang = json_encode($stObj[$i]);
			   $foundLang = true ;
			   return $stLang;
			} 	
			$langList .= $stObj[$i]->lang .';';
		 }


		 return $stLang;
	 
} // end function


function getJONOBJDOM2($pagename,$lang,&$langList) { 

		 $dbname = 'ddhousin_devshop' ;
		 $pdo = getPDO2($dbname,false)  ;
		 $ErrMsg  = '';
		 
		 $sql = "SELECT domJSON FROM domproject_pagelist where pagename=?";
		 //echo "$sql" . '-->' . $pagename ;
		 $doworkSuccess = false ;
		 try {        
		    $params = array($pagename); 
		    $rs = $pdo->prepare($sql);
		    $rs->execute($params);
		    $doworkSuccess = true ;
		    //$pdo->commit();
		 } catch (PDOException $ex) {
		    echo  $ex->getMessage();
		 
		 } catch (Exception $exception) {
		    // Output unexpected Exceptions.
		    Logging::Log($exception, false);
		 } 
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		      $st =  trim($row['domJSON']) ; 
		    }
		 } else {
            echo "ไม่มีค่า domJSON ของ Page " .  $pagename . '<br>';  
			return false;
		 } 
		 return $st ;
		// echo $st ;
		/*
		 $myfile = fopen("tmp.txt", "w") or die("Unable to open file!");
		 fwrite($myfile, $st);
		 fclose($myfile);
*/
		 $st = "";   
		 $file = fopen("tmp.txt","r");
		 while(! feof($file))  {
		    $st .= fgets($file) ;
		 }
		 fclose($file); 

		 //$st = clean($st) ;

		 


	 
		 $st = replace99("'","",$st) ;
		 $st = replace99("&amp;","",$st) ;

		 //echo $st2 ;
		  
		 $langList = '';
		 $error = isJsonOk($st,$pagename) ;
		 if (!isJsonOk($st,$pagename)) {
           return false;
		 } 
		 
		  

		 $stObj = json_decode($st); 
		 //$json_OK=	json_last_error() == JSON_ERROR_NONE;
		 //echo 'JSON-OK = ' . json_last_error() . '<br>' ; return ;

		
		 $foundLang = false;
		 for ($i=0;$i<=count($stObj)-1;$i++) {
			if ($stObj[$i]->lang === $lang) {
               $stLang = json_encode($stObj[$i]);
			   $foundLang = true ;
			   return $stLang;
			} 	
			$langList .= $stObj[$i]->lang .';';
		 }
		 
		// ไม่พบ  ข้อมูล ภาษาที่ ร้องมา เปลี่ยนค่าเป็น EN แทน ;
		$foundLang = false;
		$lang = 'en';
		 for ($i=0;$i<=count($stObj)-1;$i++) {
			if ($stObj[$i]->lang === $lang) {
               $stLang = json_encode($stObj[$i]);
			   $foundLang = true ;
			   return $stLang;
			} 	
			$langList .= $stObj[$i]->lang .';';
		 }


		 return $stLang;
	 
} // end function

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function isJsonOk($string,$pagename) {

echo "<hr>";
$error = 0 ;
         //echo '<span style="color:#ff0080">Page ' . $pagename . " </span>Have JSON Status : "; 
         json_decode($string);
		 $error  = json_last_error() ;
		 switch (json_last_error()) {
          
		  case JSON_ERROR_NONE:
			//echo "No errors";
		    return true ; 
			break;
		  case JSON_ERROR_DEPTH:
			echo "Maximum stack depth exceeded";
			break;
		  case JSON_ERROR_STATE_MISMATCH:
			echo "Invalid or malformed JSON";
			break;
		  case JSON_ERROR_CTRL_CHAR:
			echo "Control character error";
			break;
		  case JSON_ERROR_SYNTAX:
			echo "Syntax error";
			break;
		  case JSON_ERROR_UTF8:
			echo "Malformed UTF-8 characters";
			break;
		  default:
			echo "Unknown error";
			break;
		 }
         echo "<hr>";
		 return false ;
} 

function ReadJson_Recursive($arr,$modelname){
global $keyname,$workModelName ;

  

  foreach ($arr as $key => $val) {
    if (is_array($val)) {
      if (count($val) > 1 && !is_numeric($key)) {
        echo "<span style='color:red'>Array Name " . $key ."</span>" . '<br>';
		$modelname = $key;
		$workModelName = $key ;

      }
       
     ReadJson_Recursive($val,$key);
    } else {
       if (is_numeric($val)) {
		 $sType = 'int' ;
		 if (substr($val,0,1)=== '0' && strlen($val)> 3) {
			 $sType = 'varchar(255)' ;
		 }
       } else {
         
         if (strtotime($val) !== false) {
		   $sType= 'datetime' ;
         } else {
           if (is_bool($val)) {
             $sType= 'boolean';
           } else {
		    $sType= 'varchar(255)' ;
		   }
		 }
	   }
       echo("$key => type is $sType <br/>");
	   if ( !is_numeric($key)) {
	     $keyname[] = $workModelName .'->'.$key .'->' . $sType;
	   }
    }
  }

return $keyname;

}


function MainGetAllPageInProject($data) {


  $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
  //require_once($newUtilPath ."src/newutil.php"); 
  
  $dbname = 'ddhousin_devshop' ;
  $pdo = getPDO2($dbname,true)  ;
  

  $sql = 'select hosturl from domproject_main where projectname=?'; 
  $params = array($data['projectName']);

  $url =pdogetValue($sql,$params,$pdo) ;
  echo "Project Name ::" . $data['projectName'] . '<br>';
  echo "First URL==>" . $url . '<br>';
  
 
  require_once($newUtilPath.'/src/clsManageDOM.php') ;



  $dbname = 'ddhousin_devshop' ;
  $pdo = getPDO2($dbname,true)  ;
  $clsManageDOM = new clsManageDOM();
  $projectname = $data['projectName'] ;
  $startPage = $data['pageName'] ; 
  //$startPage = 'about-us.html'; 
  $isMainPage = true;
  //$url = 'https://lovetoshopmall.com/workshop/swiggi/';
  echo "url ที่จะดึงเพจ ->" . $url . '<br>';
  $clsManageDOM->findAllPageInProjectV2($projectname,$url,$startPage,true)  ;

 


  $sql = "select pagename from domproject_pagelist where numChildPage = -1 and project_id=? LIMIT 0,50";  
  $projectid = 1 ;
  $params = array($projectid);
  $isMainPage = false ;
  
  $rs= pdogetMultiValue2($sql,$params,$pdo) ;
  while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
    $startPage = $row['pagename'] ;
	$clsManageDOM->findAllPageInProjectV2($projectname,$url,$startPage,$isMainPage)  ;	    
  }
  $pdo->commit();

} // end function

function getJONOBJDOM3($projectid,$pagename,$lang,&$langList) { 


 
		 $dbname = 'ddhousin_devshop' ;
		 $pdo = getPDO2($dbname,false)  ;
		 $ErrMsg  = '';
		 
		 $sql = "SELECT domJSON_HTML FROM domPageMaster where project_id=? and  pagename=?";
         $params = array($projectid,$pagename);
		 
		
		 $rs= pdogetMultiValue2($sql,$params,$pdo);		
		 if ($rs->rowCount() > 0) {
		    while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
		      $st =  trim($row['domJSON_HTML']) ; 
		    }
		 } else {
            echo "ไม่มีค่า  domJSON ของ Page " .  $pagename . '<br>';  
			return false;
		 } 

		  
	 
		 $st = replace99("'","",$st) ;
		 $st = replace99("&amp;","",$st) ;

		 //echo $st2 ;
		  
		 $langList = '<ol>';
		 $error = isJsonOk($st,$pagename) ;
		 if (!isJsonOk($st,$pagename)) {
           echo 'JSON ERROR ' . $pagename ;
           return false;
		 } 
		 
		 $stLang = ''; 

		 $stObj = json_decode($st); 
		 //$json_OK=	json_last_error() == JSON_ERROR_NONE;
		 //echo 'JSON-OK = ' . json_last_error() . '<br>' ; return ;

		 //echo "<br>ค้นหา ภาษา " . $lang ;
		 $foundLang = false;
		 for ($i=0;$i<=count($stObj)-1;$i++) {
			if ($stObj[$i]->lang === $lang) {
               $stLang = json_encode($stObj[$i]);
			   //echo " พบข้อมูล ภาษา " . $stObj[$i]->lang . '<br>';
			   $foundLang = true ;
			   $langList .= '' . $stObj[$i]->lang .' ; ';
			   return $stObj[$i];
			} 	
			$langList .= '' . $stObj[$i]->lang .';';
		 }
		 
		// ไม่พบ  ข้อมูล ภาษาที่ ร้องมา เปลี่ยนค่าเป็น EN แทน ;
		//echo '<h2>ใช้ ภาษา Eng แทน  </h2>' ;
		$foundLang = false;
		$lang='en';
		//$lang = 'en';
		 for ($i=0;$i<=count($stObj)-1;$i++) {
			if ($stObj[$i]->lang === $lang) {
			   //echo " พบข้อมูล ภาษา " . $stObj[$i]->lang . '<br>';
               $stLang = json_encode($stObj[$i]);
			   $foundLang = true ;
			   return ($stObj[$i]);
			} 	
			$langList = $stObj[$i]->lang .';';
		 }
		 //echo " ไม่พบข้อมูล ภาษา " . $lang . '<br>';

		return $stLang;
	 
} // end function


function WriteComponentBox99($projectid,$pageid,$componentname) {


	$dbname = 'ddhousin_devshop' ;
	$pdo = getPDO2($dbname,true)  ;
	
	$sql = 'select * from domComponentLib where project_id=? and pageid=? and componentName=?'; 
	$params = array($projectid,$pageid,$componentname);	
	$row = pdoRowSet($sql,$params,$pdo) ;
	$phpSource = $row['phpSource']; 
	$sqlText = $row['sqlText'] ;
	
	 
	

} // end function

function writePHPCode($sCode) { 

         $sCode =   replace99('<','&lt;',$sCode) ;
		 $sCode =   replace99('>','&gt;',$sCode) ;
		 $sCode =   replace99('?php','&nbsp;PHPTAG&nbsp;;',$sCode) ;




} // end function 

function GetDataofComponent($projectid,$pageid,$componentname) {


$dbname = 'ddhousin_devshop' ;
$pdo = getPDO2($dbname,true)  ;

$sql = 'select * from domComponentLib where project_id=? and pageid=? and componentname=?'; 
$params = array($projectid,$pageid,$componentname);

$row = pdoRowSet($sql,$params,$pdo) ;
$sqlText = $row['sqlText'] . " LIMIT 0,15 " ;
//echo $sqlText ;


/*
$sqlText = 'SELECT ItemMaster.SellPrice as span_41,ItemMaster.NumSale as a_6,"https://lovetoshopmall.com/workshop/suha/single-product.html" as a_7,"https://lovetoshopmall.com/workshop/suha/img/product/11.png" as img_4,"0" as span_42,"d" as span_43,"0" as span_44,"h" as span_45,"0" as span_46,"m" as span_47,"0" as span_48,"s" as span_49,"https://lovetoshopmall.com/workshop/suha/single-product.html" as a_8,"Beach Cap" as span_50,"$13 $42 " as p_1,"$13" as span_51,"$42" as span_52,"https://lovetoshopmall.com/workshop/suha/#" as a_9 FROM ItemMaster LIMIT 0,10';
*/
$dbname = 'ddhousin_shopproject' ;
$pdo2 = getPDO2($dbname,true)  ;



$params = array();
$rs= pdogetMultiValue2($sqlText,$params,$pdo2) ;


while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	  $emparray[] = $row;
		    
} 

return json_encode($emparray); 




 



} // end function


function urlExists($url=NULL)
    {
        if($url == NULL) return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        if($httpcode>=200 && $httpcode<300){
            return true;
        } else {
            return false;
        }
    }
// https://www.7sevenvape.com/th/frontend/css/frontend.css?id=037146b5cbc251eeb6a9



function xcopy2($src, $dest) { 

    foreach (scandir($src) as $file) {
        if (!is_readable($src . '/' . $file)) continue;
        if (is_dir($src .'/' . $file) && ($file != '.') && ($file != '..') ) {
            mkdir($dest . '/' . $file);
            xcopy($src . '/' . $file, $dest . '/' . $file);
        } else {
            copy($src . '/' . $file, $dest . '/' . $file);
        }
    }
} 

function Reflect() { 

echo '<li><span style="color:#ff0080">' . basename(__FILE__, ".php") . '->'  . (__METHOD__) . '()<br>';

$ref = new ReflectionFunction(__METHOD__);
        $functionParameters = [];
        foreach($ref->getParameters() as $key => $currentParameter) {
          $functionParameters[$currentParameter->getName()] = func_get_arg($key);
        }
       // print_r($functionParameters) ;


} // end function

function openJson($st) {

    $json = json_decode($st); 

	
	foreach($json as $key => $val) {
	     if ($key) { echo 'KEY IS: '.$key; };
	     if ($val) { echo 'VALUE IS: '.$val; };
	     echo '<br>';
	}
	 
	
	 

    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
		    return $obj ;
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }

    echo PHP_EOL;

}

function HeadTranslate($sourceLang) {  ?>

<h1>My Translate Web Page</h1>
<p>Translate this page:</p>
<div id="google_translate_element"></div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: '<?=$sourceLang?>'}, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>


<?php


} // end function

function getJSONFromClass($pdo,$projectid,$classid) {  


$sql = 'select jsonData from domPHPClass where project_id=? and classid=?'; 
$params = array($projectid,$classid);
//$rowJson = pdoRowSet($sql,$params,$pdo) ; 

$sValue = pdogetValue($sql,$params,$pdo) ;
if (!$sValue) {
	echo 'Not Found Json on ID:: ' .  $classid . '<br>' ;
}


//$rowJson = str_replace("/\\",'',$sValue) ;
$rowJson = preg_replace('/\\\\/', '',$sValue);


return $rowJson ;



} // end function

function getPathPrefix() { 

$mainurl = 'https://lovetoshopmall.com/workshop/layout2/';
$mainurlAr = explode('/',$mainurl) ;
$thisURL = getCurrentURL() ;
if (substr($thisURL,-1) != '/') {
   $thisURL .= '/' ;
}
$thisURLAr = explode('/',$thisURL) ;
$sub = count($thisURLAr) - count($mainurlAr) ; 
$pathPrefix = str_repeat('../',$sub-1) ;

return $pathPrefix ;


} // end function


function getItemMasterJSON($pdo,$numrec) { 

         $sql = "select mainImageURL from ItemMaster order by rand() limit 0,8"; 
		 $params = array();
		 $AuxImage = array();
		 $rs= pdogetMultiValue2($sql,$params,$pdo) ;		 
		 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			$AuxImage[]  = $row;
		 }

         


	     $ErrMsg  = '';
	     
	     $sql = "SELECT a.ItemCode,a.ItemName,a.fullPrice,a.SellPrice,a.mainImageURL,
		 b.departmentDesc,c.categoryDesc,d.categoryDesc as groupDesc
		 FROM ItemMaster a inner join  department b 
		 on a.departmentCode = b.departmentCode 
		 inner join categorymaster c on a.categoryCode = c.categorycode
		 inner join groupItem d on a.groupcode = d.categorycode
		 where b.lang='th' and a.lang='th' and c.lang='th'  and d.lang='th' limit 250,$numrec";
	     
	     try {
	             
	        $params = array(); 
	        $rs = $pdo->prepare($sql);
	        $rs->execute($params);
	       
	     } catch (PDOException $ex) {
	        echo  $ex->getMessage();
	     
	     } catch (Exception $exception) {
	             // Output unexpected Exceptions.
	             Logging::Log($exception, false);
	     }
		 $ItemArray = array() ;
	     if ($rs->rowCount() > 0) {
		  while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
	        $ItemArray[] = $row;
          }
	     }

		 $jsonObj = json_decode(json_encode($ItemArray, JSON_UNESCAPED_UNICODE));
		 for ($i=0;$i<=count($jsonObj)-1;$i++) {
			 $sql = "select mainImageURL from ItemMaster order by rand() limit 0,8"; 
		     $params = array();
		     $AuxImage = array();
		     $rs= pdogetMultiValue2($sql,$params,$pdo) ;		 
		     while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			    $AuxImage[]  = $row;
		     }
			 $jsonObj[$i]->auxImage = $AuxImage ;		    
		 }

		 return json_decode(json_encode($jsonObj, JSON_UNESCAPED_UNICODE));

} // end function 

function getJSONObjFromSQL($pdo,$sql,$params) {  



	     
	    
	     $rs= pdogetMultiValue2($sql,$params,$pdo) ;
	     $resultArray  = array() ;
	     while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
             $resultArray[] = $row;				    
	     } 

		 $st = json_encode($resultArray, JSON_UNESCAPED_UNICODE) ;
	     
		 $st = stripslashes($st) ;
		 

		 $jsonObj = json_decode($st) ;

		 switch (json_last_error()) {
			case JSON_ERROR_NONE:
				echo ' - No errors';
			break;
			case JSON_ERROR_DEPTH:
				echo ' - Maximum stack depth exceeded';
			break;
			case JSON_ERROR_STATE_MISMATCH:
				echo ' - Underflow or the modes mismatch';
			break;
			case JSON_ERROR_CTRL_CHAR:
				echo ' - Unexpected control character found';
			break;
			case JSON_ERROR_SYNTAX:
				echo ' - Syntax error, malformed JSON';
			break;
			case JSON_ERROR_UTF8:
				echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
			default:
				echo ' - Unknown error';
			break;
          }

	      return $jsonObj ;
		 

		 

		 //return json_decode(json_encode($resultArray, JSON_UNESCAPED_UNICODE));


} // end function

function writePagePHPORM() { 



} // end function

function trimslash($st) {  

	     if (substr($st,0,1) == '/' ) {
			 $st = substr($st,1,strlen($st)) ; 
	     }

		 if (substr($st,-1) == '/' ) {
			 $st = substr($st,0,strlen($st)-1) ; 
	     }

		 return $st;


echo '<li><span style="color:#ff0080">' . basename(__FILE__, ".php") . '->'  . (__METHOD__) . '()</span><br>';

} // end function

function decodejson($stJson) { 

	$json_Obj = json_decode($stJson) ;
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
		    return $json_Obj ;
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }





} // end function

function checkurlparameter() {  
global $mainProjectPath   ; 

        $thisurl  = getCurrentURL() ;
		$urlParam = str_replace($mainProjectPath,'',$thisurl);
		if (substr($urlParam,-1) ==='/') {
          $urlParam = substr($urlParam,0,strlen($urlParam)-1) ;
		}

		echo '<h2>url parameter->' . urldecode($urlParam) . '</h2>';
		$urlParamAr = explode('/',$urlParam);

		$numParam = count($urlParamAr) ; 
		$sValue[]  = $urlParam ;
		$sValue[]  = count($urlParamAr) ; 
		$sValue[]  = $urlParamAr[0] ; 

		return $sValue;


} // end function

function space5($num) { 

	     return str_repeat(" ",$num) ;

echo '<li><span style="color:#ff0080">' . basename(__FILE__, ".php") . '->'  . (__METHOD__) . '()</span><br>';

} // end function

function ConvertPageNameToAngName($pagename) { 

$newPageName = str_replace(' ','', $pagename); 
$newPageName = str_replace('_','', $pagename); 
$newPageName = str_replace('-','', $pagename); 

$newPageName = strtoupper(substr($newPageName,0,1)) . substr($newPageName,1,100) .'Component' ; 

return $newPageName ;




} // end function

function ParamsOnly($uri,$basename) { 

$url = $_SERVER['REQUEST_URI'] ;
$urlAr = explode('/',$uri) ; 

$posStart = -1 ;
for ($i=0;$i<=count($urlAr)-1;$i++) {
	if ($urlAr[$i]=== $basename) {
        $posStart = $i; break; 
	}    
}

for ($i=$posStart+1;$i<=count($urlAr)-1;$i++) {
   $paramsAr[] = $urlAr[$i] ;
}

$sValue[] = $_SERVER['REQUEST_METHOD'] ;
$sValue[] =  $paramsAr ;
$sValue[] = implode('/',$paramsAr) ;

return $sValue ;

} // end function


function genSQLReplaceINTO($pdo,$tablename) { 

    $sql = 'SHOW COLUMNS FROM ' . $tablename; 
    $params = array();

	$rs= pdogetMultiValue2($sql,$params,$pdo) ;
	while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			echo $row['Field'] . '<br>';    
			$fieldAr[] = $row['Field'] ;
			$fieldAr2[] = '?' ;
	}

	$st = '$sql = "REPLACE INTO ' . $tablename . '(' . implode(',',$fieldAr) . ') VALUES(';
	$st .= implode(',',$fieldAr2) . ')";' . "\n"; 
	$sParams = '$paramsAr = array('; 

	for ($i=0;$i<=count($fieldAr)-1;$i++) { 
		$sParams .= '$data["' .$fieldAr[$i] . '"],';    
	}

    $sParams = substr($sParams,0,strlen($sParams)-1) . ');';
    $st .= $sParams ;

	$st .= "\n" . 'if (!pdoExecuteQuery($pdo,$sql,$params)) {
	   echo "Error" ;
	   return false;
	}
	$pdo->commit();'; 

	return $st ;


} // end function

function InsertTableFromJSON() { 

$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
require_once($newUtilPath ."src/newutil.php"); 
require_once($newUtilPath ."src/jsonUtil.php"); 

$dbname = 'ddhousin_shopproject' ;
$pdo = getPDO2($dbname,false)  ;


$st  = '{
  "id": 0,
  "departmentCode": "string",
  "shopID": "string",
  "code2": 0,
  "departmentDesc": "แผนกคอม",
  "lang": "string",
  "imageName": "string",
  "faIcon": "string",
  "isDelete": "string",
  "updatedby": "string",
  "createdAt": "string",
  "lastupdate": "string",
  "category": [
    {
      "id": 88880,
      "name": "ธีระ"
    }
  ]
}';
/*
$json = json_decode('{"S01":["001.cbf","002.cbf","003.cbf","004.cbf","005.cbf","006.cbf","007.cbf","008.cbf","009.cbf"],"S02":["001.sda","002.sda","003.sda"],"S03":["001.klm","002.klm"]}');
foreach($json as $key => $val) {
     if ($key) { echo 'KEY IS: '.$key; };
     if ($val) { echo 'VALUE IS: '.$val; };
     echo '<br>';
}
หรือ 
foreach($json as $key => $val) {
    echo "KEY IS: $key<br/>";
    foreach(((array)$json)[$key] as $val2) {
        echo "VALUE IS: $val2<br/>";
    }
}

*/

$obj = json_decode($st) ;
$tablename = 'departmentShop' ;
$sql ='INSERT INTO ' . $tablename. '('; 
$stTmp = '';
$st = '';
$params  = array();
$subGroup =  array();
foreach($obj as $key => $value) {
 //echo 'Your key is: '.$key.' and the value of the key is:'.$value . '<br>';
 $sql .= $key .',';
 $stTmp .= '?,';
 if (is_array($value)) {
     $subgroup =  $key;
 }


 $st .= '$params[]  = "' . $value . '";' . "\n";
}

$stTmp = substr($stTmp,0,strlen($stTmp)-1);
$sql = substr($sql,0,strlen($sql)-1) . ') VALUES(' . $stTmp .') ;' ;

echo $sql . "<br>";
echo nl2br($st) ;
//$st = implode(',',$params) ;

echo "-->" . $subgroup;
$ss = $obj->$subgroup ;


foreach($ss[0] as $key => $value) {
     if ($key) { echo 'KEY IS: '.$key; };
     if ($value) { echo 'VALUE IS: '.$value; };
}


} // end function

function getAllFieldList($pdo,$tablename) {  

     
//	$statement = $pdo->query('DESCRIBE ' . $tablename);
	$statement = $pdo->query('SHOW FULL COLUMNS FROM  ' . $tablename);


//Fetch our result.
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//The result should be an array of arrays,
//with each array containing information about the columns
//that the table has.
//var_dump($result);

//For the sake of this tutorial, I will loop through the result
//and print out the column names and their types.


foreach($result as $column){
    
	
	$sType = explode('(',$column['Type']);

    //echo $column['Field'] . ' - ' . $sType[0] . '-'. $column['Key']. '<br>';
	$fieldName[] = $column['Field'] ;
	$fieldType[] = $sType[0] ;
	$fieldKey[] = $column['Key'] ;
    if ($column['Comment'] !=='') {    
	   $fieldComment[] = $column['Comment'] ;
	} else {
	   $fieldComment[] = $column['Field'] ;

	}

}

return array($fieldName,$fieldType,$fieldKey,$fieldComment);




} // end function

function getAllFieldListWithSize($pdo,$tablename) {  

     
//	$statement = $pdo->query('DESCRIBE ' . $tablename);
	$statement = $pdo->query('SHOW FULL COLUMNS FROM  ' . $tablename);

	$sql = 'select * from '. $tablename ; 
	$params = array();
	$rs= pdogetMultiValue2($sql,$params,$pdo) ;


//Fetch our result.
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//The result should be an array of arrays,
//with each array containing information about the columns
//that the table has.
//var_dump($result);

//For the sake of this tutorial, I will loop through the result
//and print out the column names and their types.

$n=0;
foreach($result as $column){
    
	
	$sType = explode('(',$column['Type']);

    //echo $column['Field'] . ' - ' . $sType[0] . '-'. $column['Key']. '<br>';
	$fieldName[] = $column['Field'] ;
	$fieldType[] = $sType[0] ;
	$fieldKey[] = $column['Key'] ;
    if ($column['Comment'] !=='') {    
	   $fieldComment[] = $column['Comment'] ;
	} else {
	   $fieldComment[] = $column['Field'] ;
	}
	
	 
	$columnMeta = $rs->getColumnMeta($n);
	$length = $columnMeta['len'];
	$fieldSize[] = $length ;

	$n++ ;

}

return array($fieldName,$fieldType,$fieldKey,$fieldComment,$fieldSize);




} // end function

function readFileTxt($sFileName) { 

//$newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';

//$sFileName = $newUtilPath .  $sFileName ;
 $st = "";   
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);

 return $st ;



} // end function

function remove_numbers(string $str){
    return preg_replace("/\d/u", "", $str);
}

function get_numbers(string $str){

// สกัดเอาตัวเลขจาก string ใช้ใน การ แยกCode เช่น A001

    return $int_var = (int)filter_var($str, FILTER_SANITIZE_NUMBER_INT);
   
}

function ExtractLabelFromFormModel($projectid,$formCode,$lang) { 

$dbname = 'ddhousin_devshop' ;
$pdo = getPDO2($dbname,true)  ;

$sql = 'select * from angularPage where projectid=? and formCode=?'; 
$params = array($projectid,$formCode);
$rs= pdogetMultiValue2($sql,$params,$pdo) ;
while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
    $stModel = $row['formModel'];
}

echo $stModel . '<hr>';
$objSource = json_decode($stModel);
echo $objSource->FormCaption ;

$objTarget = json_decode('{}');

$objTarget->FormCode = $objSource->FormCode ;
$objTarget->lang = 'th' ;
$objTarget->FormCaption = $objSource->FormCaption ;

$objTarget->msg1 = '' ;
$objTarget->msg2 = '' ;
$objTarget->msg3 = '' ;
$objTarget->msg4 = '' ;
$objTarget->msg5 = '' ;


$sql = 'DELETE from angularPageResource where projectid=? and formCode=? and lang=?'; 
$params = array($projectid,$formCode,$lang);
if (!pdoExecuteQuery($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}

echo "<br>เริ่มอ่านค่า Field จาก Source  ProjectiD=$projectid FormCode=$formCode "; 

for ($i=0;$i<=count($objSource->FieldList)-1;$i++) {
	$thisName = 'label_' . $objSource->FieldList[$i]->fieldName;
	$thisValue =  $objSource->FieldList[$i]->fieldCaption;	
	$objTarget->$thisName = $thisValue ;
	$thisName = 'txt_danger' . $objSource->FieldList[$i]->fieldName;
	$thisValue =  ' กรุณาป้อนค่า -' .$objSource->FieldList[$i]->fieldCaption;	
	$objTarget->$thisName = $thisValue ;
}

$str_json=json_encode($objTarget, JSON_UNESCAPED_UNICODE);
echo '<br>'. $str_json . '<br>' ; 

$sql = 'select * from angularPageResource where projectid=? and formCode=? '; 
$params = array($projectid,$formCode);
$rs= pdogetMultiValue2($sql,$params,$pdo) ; 
if ($rs->rowCount() === 0) {

  $sql='INSERT INTO angularPageResource(projectid, formCode, lang, labelList) VALUES (?,?,?,?)';
  $params = array($projectid, $formCode, $lang,$str_json) ;
  echo "<br>เพิ่มค่า ลง  angularPageResource <br>"; 

} else {

  $sql='UPDATE angularPageResource SET labelList=? WHERE projectid=?,formCode=?,lang=?';
  $params = array($str_json,$projectid, $formCode, $lang) ;
  echo "<br> Update   angularPageResource"; 

}

echo '<hr>'.$sql . '<hr>';
if (!pdoExecuteQuery($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}

$pdo->commit();




} // end function

function AppendFieldCreatedAT($pdo,$tablename) { 

$sql = "ALTER TABLE $tablename
ADD  COLUMN  IF NOT EXISTS createdAT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD  COLUMN  IF NOT EXISTS lastupdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
ADD  COLUMN  IF NOT EXISTS updatedBy varchar(30) NOT NULL ";
$params= array();
if (!pdoExecuteQuery($pdo,$sql,$params)) {
   echo 'Error' ;
   return false;
}



} // end function

function genMapTable($projectid) { 

	      $dbname = 'ddhousin_devshop' ;
	      $pdo = getPDO2($dbname,true)  ;

	      $sql = 'select projectPath from angularPage where projectid=?'; 
	      $params = array($projectid);		  
		  $projectPath = pdogetValue($sql,$params,$pdo) ;
		  $projectPath = 'itAsset' ;
	      
	      $sql = 'select formModel from angularPage where formModel <> "" and projectid=?'; 
	      $params = array($projectid);
	      
	      $rs= pdogetMultiValue2($sql,$params,$pdo) ;	      

		  $stAll = '<?php' . "\n" ;
	      while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
			  $formModel = $row['formModel'] ;
			  $obj = json_decode($formModel);
			  $formCode = $obj->FormCode ;
			  $modelname = $obj->ModelName;
			  $ch= '    if ($scode==="' . $formCode .'") { $tablename ="'. $modelname . '";} ;'."\n"; 
			  //$ch = '$' . $formCode .' = "' . $modelname . '" ;' . "\n" ;
			  $stAll .= $ch ;
	      }
		  $stAll .= '?>' ;

          $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
		  $sFileName = $newUtilPath .'apiservice/' . $projectPath .'/maptable.php' ;
		  $myfile = fopen($sFileName, "w") or die("Unable to open file!");		  
		  fwrite($myfile, $stAll);
		  fclose($myfile);




} // end function

function json_validator($data) {

        if (!empty($data)) {
            @json_decode($data);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
}

function CheckTableExists($dbname,$tablename) { 

    
    $pdo = getPDO2($dbname,true)  ;
    
	$stmt = $pdo->prepare("SHOW TABLES LIKE '" . $tablename ."'");
    $stmt->execute();
    if($stmt->rowCount()>0){  
      return true  ;
    }else{
      return false ;
    }


} // end function

function isFK($pdo,$fieldName) { 
 
  //echo $fieldName ."<br>";
  if ($fieldName =='id' )  {
	  return false;
  }

  if (substr($fieldName,-2) !=='ID' )  {
	  return false;
  }
  
  if (substr($fieldName,-3 ==='_ID') ) {
	 $tblName =  substr($fieldName,0,strlen($fieldName)-3);    
  }
  if (substr($fieldName,-2 ==='ID')  ) {
	 $tblName =  substr($fieldName,0,strlen($fieldName)-2);    
  }

  //echo $tblName . '-->';

  $stmt = $pdo->prepare("SHOW TABLES LIKE '" . $tblName ."'");
  $stmt->execute();
  
 if ($stmt->rowCount() > 0) {
     //echo ' เป็น FK ' . '<br>'; 
	 return true;
 }   else {
	// echo ' ไม่เป็น FK ' .'<br>'; 
	 return false;
 }
  
  

} // end function 

function genWhereClauseArray($whereClause,$fieldSearchArray) { 

	     if (count($fieldSearchArray) ===0 ) {
			 return '';
	     }
		 
         if ($whereClause ==='') {         
	        $whereclause = ' WHERE ' ;
		 } else {
            $whereclause =  $whereClause.  ' AND ' ;
		 }
         for ($i=0;$i<=count($fieldSearchArray)-1;$i++) {
			 if ($fieldSearchArray[$i] != '') {			 
               $whereclause .= ' ' . $fieldSearchArray[$i] . ' LIKE "%' . $_GET["searchText"] .'%" OR';
			 }
         }
         $whereclause = substr($whereclause,0,strlen($whereclause)-2);		 

		 return $whereclause ;



} // end function

function genWhereClause($whereClause,$fieldSearch) { 

	      
		 
         if ($whereClause ==='') {         
	        $whereclause = ' WHERE ' ;
		 } else {
            $whereclause =  $whereClause.  ' AND ' ;
		 }
         
		 if ($fieldSearch  != '') {			 
        $whereclause .= ' ' . $fieldSearch  . ' LIKE "%' . $_GET["searchText"] .'%" ';			 
         }
         //$whereclause = substr($whereclause,0,strlen($whereclause)-2);		 

		 return $whereclause ;



} // end function

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function encryptJWT($plaintext) { 

	// Set the plaintext and encryption key
//$plaintext = "Hello World!";
$key = "MySecretKey"; 

$now = new DateTime();

// Add 60 minutes to the current time
$interval = new DateInterval('PT60M');
$now->add($interval);

// Format the result as a string
$expireTime = $now->format('Y-m-d H:i:s');

$plaintext .= $expireTime ;


// Set the encryption method and options
$method = "aes-256-cbc";
$options = OPENSSL_RAW_DATA;

// Generate a random initialization vector (IV)
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));

// Encrypt the plaintext with the key and IV
$ciphertext = openssl_encrypt($plaintext, $method, $key, $options, $iv);

// Concatenate the IV and ciphertext into a single string
$encrypted_data = base64_encode($iv . $ciphertext);

return $encrypted_data;



} // end function

function decryptJWT($JWT) { 

$key = "MySecretKey";

// Set the encryption method and options
$method = "aes-256-cbc";
$options = OPENSSL_RAW_DATA;

// Decode the encrypted data from base64 format
$encrypted_data = base64_decode($encrypted_data);

// Extract the IV and ciphertext from the encrypted data
$iv_length = openssl_cipher_iv_length($method);
$iv = substr($encrypted_data, 0, $iv_length);
$ciphertext = substr($encrypted_data, $iv_length);

// Decrypt the ciphertext with the key and IV
$plaintext = openssl_decrypt($ciphertext, $method, $key, $options, $iv);

// Output the decrypted plaintext
//echo "Plan Text" .  $plaintext; // Output: Hello World!

return $plaintext;


} // end function

function pRow($row,$fieldname) { 

   //return (isset($row) ? $row[$fieldname] : '');
   if (isset($row[$fieldname])) {
	   return $row[$fieldname];
   } else {
	   return '';
   }

} // end function

function pRow2($row,$fieldname,$st) { 

   //return (isset($row) ? $row[$fieldname] : '');
   if (isset($row[$fieldname])) {
	   return $row[$fieldname];
   } else {
	   return $st;
   }

} // end function

function getLang() { 

  $currentURL = getCurrentURL();
  if (strpos($currentURL,'/th/') ) {
	 $lang ='th' ;
     return $lang ;
  }
  if (strpos($currentURL,'/en/') ) {
	 $lang ='en' ;
	 return $lang ;
  }
  if (strpos($currentURL,'/ch/') ) {
	 $lang ='ch' ;
	 return $lang ;
  }
  return 'th' ;



} // end function

   function getTextLanguage($text, $default) {
      $supported_languages = array(
          'en',
          'de',
      );
      // German word list
      // from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
      $wordList['de'] = array ('der', 'die', 'und', 'in', 'den', 'von', 
          'zu', 'das', 'mit', 'sich', 'des', 'auf', 'für', 'ist', 'im', 
          'dem', 'nicht', 'ein', 'Die', 'eine');
      // English word list
      // from http://en.wikipedia.org/wiki/Most_common_words_in_English
      $wordList['en'] = array ('the', 'be', 'to', 'of', 'and', 'a', 'in', 
          'that', 'have', 'I', 'it', 'for', 'not', 'on', 'with', 'he', 
          'as', 'you', 'do', 'at');
      // French word list
      // from https://1000mostcommonwords.com/1000-most-common-french-words/
      $wordList['fr'] = array ('comme', 'que',  'tait',  'pour',  'sur',  'sont',  'avec',
                         'tre',  'un',  'ce',  'par',  'mais',  'que',  'est',
                         'il',  'eu',  'la', 'et', 'dans', 'mot');

      // Spanish word list
      // from https://spanishforyourjob.com/commonwords/
      $wordList['es'] = array ('que', 'no', 'a', 'la', 'el', 'es', 'y',
                         'en', 'lo', 'un', 'por', 'qu', 'si', 'una',
                         'los', 'con', 'para', 'est', 'eso', 'las');
      // clean out the input string - note we don't have any non-ASCII 
      // characters in the word lists... change this if it is not the 
      // case in your language wordlists!
      $text = preg_replace("/[^A-Za-z]/", ' ', $text);
      // count the occurrences of the most frequent words
      foreach ($supported_languages as $language) {
        $counter[$language]=0;
      }
      for ($i = 0; $i < 20; $i++) {
        foreach ($supported_languages as $language) {
          $counter[$language] = $counter[$language] + 
            // I believe this is way faster than fancy RegEx solutions
            substr_count($text, ' ' .$wordList[$language][$i] . ' ');;
        }
      }
      // get max counter value
      // from http://stackoverflow.com/a/1461363
      $max = max($counter);
      $maxs = array_keys($counter, $max);
      // if there are two winners - fall back to default!
      if (count($maxs) == 1) {
        $winner = $maxs[0];
        $second = 0;
        // get runner-up (second place)
        foreach ($supported_languages as $language) {
          if ($language <> $winner) {
            if ($counter[$language]>$second) {
              $second = $counter[$language];
            }
          }
        }
        // apply arbitrary threshold of 10%
        if (($second / $max) < 0.1) {
          return $winner;
        } 
      }
      return $default;
    }


function calculateCandlestickDegree($open, $high, $low, $close, $volume) {
    // ตรวจสอบเงื่อนไขของ Candlestick และกำหนดค่า Degree ตามเงื่อนไข
    if ($close > $open && ($close - $open) >= 0.5 * ($high - $low)) {
        return "Bullish"; // Candlestick เป็นแนวโน้มขาขึ้น (Bullish)
    } elseif ($open > $close && ($open - $close) >= 0.5 * ($high - $low)) {
        return "Bearish"; // Candlestick เป็นแนวโน้มขาลง (Bearish)
    } else {
        return "Indecisive"; // Candlestick ไม่แน่นอน
    }
}

function calculateCandlestickDegree2($open, $high, $low, $close) {
    $bodySize = abs($open - $close); // ขนาดของ Body
    $wickSize = $high - max($open, $close); // ขนาดของ Wick ด้านบน
    $degree = atan($wickSize / $bodySize) * 180 / M_PI; // คำนวณองศา

    if ($open < $close) {
        return $degree; // Candlestick เป็นแนวโน้มขาขึ้น
    } elseif ($open > $close) {
        return -$degree; // Candlestick เป็นแนวโน้มขาลง
    } else {
        return 0; // Candlestick ไม่แน่นอน
    }
}

function HintCandle() {  ?>

<h3># ตัวแปรที่บ่งบอกสภาวะของ แท่งเทียนมีดังนี้  </h3>
<ol>
    <li> Color
    <li> MACD จะเป็นตัวบอก Trend ว่า ขึ้นหรือลง ถ้า + ก็จะขึ้น ถ้า - ก็จะลง
    <li> EMAAbove จะเป็นตัวบอก ว่าขณะนั้น EMA3 เหนือ EMA5 หรือ EMA5 เหนือ EMA3
    <li> EMASlopeValue บ่งบอกค่าความชันของ EMA3 ถ้าน้อยกว่า 20 แสดงว่า EMA3 จะวิ่งแนวขนาน  
    <li> EMAConvergence จะเป็นตัวบอกว่า กราฟมีการ ลู่เข้า หรือถ่างออก
    <li> TurnPoint จะบอกว่า จุดที่ผ่านมา เป็น จุดกลับตัวหรือไม่ 
    <li> CutPoint จะบอกว่า เกิดการตัดกันของ ema3 หรือ 5 หรือยัง 
    <li> UHeight,BodyHeigh,LHeight จะเป็นตัวบอก ลักษณะแท่งเทียน 
    <li> EMAConflict บ่งบอกว่า เกิดการขัดแย้งกันระหว่าง EMAAbove กับ Trend มักจะเกิดใน
    กรณีที่ เส้น EMA ยังปรับตัวไม่ทันหลังจาก เกิดการกลับตัว เราเรียกว่า สภาวะ Conflict
</ol>
<h3># แนวทางการ เข้าเทรด  </h3>
<ol>
  <li>เข้าเทรดในจุด cutpoint 
  <li>เข้าเทรดโดย ดูจาก เทรนด์ปัจจุบัน (emaabove)
  <li>โดยปกติ การเข้า เทรดโดยใช้ follow trend จะใช้ทำกำไรได้ดี แต่จะมีจุด loss
  ตอนที่กราฟเกิดการกลับตัว (เกิด turnpoint) ณ จุดนี้ กว่า ema 2 เส้นจะวิ่งไล่ทันกัน ก็ผ่านไปถึง แท่งที่ 2 แล้วทำให้เรา loss ไป 2 จุด ทำให้ เสีย profit target ไปถึง 2 การวิ่งตาม ต้องถอยหลังกลับมาอีก <br>
  ซึ่ง จากการ คำนวณแล้ว การเสีย 2 จุด ทำให้ต้องวิ่งตามอีกถึง 4 จุด ทำให้ การถึงเป้าหมาย ยากเข้าไปอีก ยิ่งถ้ากราฟ เกิดการเปลี่ยน Trend บ่อยๆ ทุก 2-3 จุด ทำให้ เกิดการ loss profit กันมโหฬาร 
  <li>เราจึงใช้ จุด turnpoint มาพิจารณาประกอป ตามนี้ 
    <ol>
	  <li>ให้เทรด ตามสีไปก่อน โดย loop ไปเรื่อยๆ จนเมื่อเกิด loss หรือ สี ไม่ตรง ณจุดนี้ กราฟจะเลือก ทางไป 2 ทาง <br>คือ 1.เป็น StopPoint  หรือ 2. เป็น Turn Point  ณจุดนี้ ให้ Idle ไว้ก่อน  กำหนดให้เป็นจุด A
	  <li>หลังจาก Idle เมื่อผลออกมา เราจะกำหนดให้เป็นจุด B แล้วมาดู ข้อมูลวิเคราะห์กัน ดังนี้ 
	  <ol>
	    <li>ถ้า ออกซ้ำสีเดิม กับ B แสดงว่า A มีแนวโน้มเป็น TurnPoint ให้พิจารณาว่าเกิด Cutpoint หรือยัง 
        CutPoint จะเป็นตัวยืนยันที่แข็งแรงว่า Trend เปลี่ยนไปแล้ว เริ่ม  Follow Trend (Follow Trend ใหม่) ต่อได้เลย
		<li>ถ้า ออกซ้ำสีเดิม กับ A แสดงว่า B มีแนวโน้มเป็น StopPoint 
		
		<li>ข้อสังเกตุ แท่ง Red + DownTrend + Doji-->Green
      </ol>
	</ol>
   <li>ข้อสังเกตุ แท่ง Red + DownTrend + Doji-->Green
   <li>ข้อสังเกตุ SlopeValue+ MACD มีค่าน้อยกว่า 10 แนวโน้มจะเป็น Sideway ให้ Idle
   <li>ค่า Color Con จะสูงสุดที 6-7 ซึ่งโอกาสจะเกิดน้อยมาก 
   <li>ค่า Color Con จะเกิดที่ 2 ขึ้นไป ไม่งั้นจะออก Sideway
   <li>ให้หา ค่า กรอบราคาเวลา Sideway ออกมา
  <li>
  <li>

<?php

} // end function


function getJsonObject($sql) { 

$dbname = 'ddhousin_lab' ;
$pdo = getPDO2($dbname,true)  ;
$params = array();

$result= pdogetMultiValue2($sql,$params,$pdo) ;
$objectsArray = array();

// Check if there are results
if ($result->rowCount() > 0) {
    // Loop through each row in the result set
    while($row = $result->fetch( PDO::FETCH_ASSOC )) {
        // Create a standard object for each row
        $obj = new stdClass();

        // Dynamically assign fields to the object
        foreach ($row as $key => $value) {
            $obj->$key = $value;  // Create a property with the key name
        }

        // Add the object to the array
        $objectsArray[] = $obj;
    }
} else {
    echo "No results found.";
}

return $objectsArray;


} // end function

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
//$dataArray = json_decode($jsonDataString, true);

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