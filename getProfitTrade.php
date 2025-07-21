<?php

    require_once("newutil2.php"); 	
    $pdo = getPDONew()  ;	
	$today = date('Y-m-d');
	$sql = 'select count(DISTINCT(LotNo)) from detailTrade where DATE(created_at) = ?'; 
	$params = array($today);		
	$numLotNo  =pdogetValue($sql,$params,$pdo) ;

	$sql = 'select sum(profit) from detailTrade where DATE(created_at) = ?'; 
	$params = array($today);		
	$totalProfit  =pdogetValue($sql,$params,$pdo) ;

	$sql = 'select max(moneyTrade) from detailTrade where DATE(created_at) = ?'; 
	$params = array($today);		
	$maxMoneyTrade  =pdogetValue($sql,$params,$pdo) ;


	echo 'Total Lot No= '. $numLotNo .' totalProfit = ' . $totalProfit. ' (' . $totalProfit*33  .' บาท)' . ' Max Money Trade=' . round($maxMoneyTrade,0) ;
	


?>

