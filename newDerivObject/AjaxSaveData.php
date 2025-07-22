<?php
  header('Access-Control-Allow-Methods: GET, POST');
 /header('Access-Control-Allow-Origin: *'); 
  ob_start();
  //https://www.thaicreate.com/community/login-php-jquery-2encrypt.html
  //https://www.cyfence.com/article/design-secured-api/
  
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);   
     $data = json_decode(file_get_contents('php://input'), true);
	 echo 'Mode=' .$data['Mode'] ;
     if ($data) {     
        if ($data['Mode'] == 'SaveAnalyData') { SaveAnalyData($data); }
        return;
     }
  
function SaveAnalyData($data) { 
  
echo "ssss><hr>";
$analyData = $data['sData'] ;
$myfile = fopen("dataTest.json", "w") or die("Unable to open file!");

fwrite($myfile, $analyData);
fclose($myfile);

echo "Success";
  
	     
  
} // end function

?>