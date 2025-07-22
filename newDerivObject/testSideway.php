<?php
 
 ob_start();
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 $jsonData = "";   
 
 
 $sFileName ='dataTest.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $jsonData .= fgets($file) ;
 }
 fclose($file);

$data = json_decode($jsonData, true);
$tUp = [] ; $tDown = [] ;
for ($i=0;$i<=count($data)-1;$i++) {
    if ($data[$i]['PreviousTurnType'] === 'TurnDown') {
      $tDown[] = $data[$i-1]['timefrom_unix'] ;
    }
	if ($data[$i]['PreviousTurnType'] === 'TurnUp') {
      $tUp[] = $data[$i-1]['timefrom_unix'] ;
    }

}

print_r($tUp);
echo "<hr>";
print_r($tDown);

?>