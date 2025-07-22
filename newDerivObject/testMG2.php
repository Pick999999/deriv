<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);  


function getTradeObj() {

	
	 $st = "";   
	 

	 $sFileName =  'AnalyDataSmall.json';
	 $file = fopen($sFileName,"r");
	 while(! feof($file))  {
	   $st .= fgets($file) ;
	 }
	 fclose($file);
	 $ObjTrade = JSON_DECODE($st,true);

	 return $ObjTrade;


} // end function



function Main() {

         $objTrade = getTradeObj();
		 $balance = 0 ;
		 //for ($i=0;$i<=count($objTrade)-1;$i++) {
         $i = 0 ;
         while ($i <= count($objTrade)-1) {
         
			 if ($objTrade[$i]['winStatus'] === 'Win') {
			   $balance = $balance + $objTrade[$i]['profit'] ;
			 } else {
               $balance = $balance + $objTrade[$i]['profit'] ;
			 }	
			 if ($objTrade[$i]['lossCon'] >= 3) {
				 echo 'Break at ' . $objTrade[$i]['timefrom_unix'] .'<br>';
				 $i = $i +3;
				 //break;
			 } else {
			   $i++ ;
			 }
			 
		 } // end while
		 echo "<h2>Balance=" . $balance . '</h2>';


} // end function

Main();



?>