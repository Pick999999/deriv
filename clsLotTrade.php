<?php
  
class cls_LotTrade {


 public $asset;
 public $AnalysisData ;
 public $MoneyTrade ;
 public $LotNo ;
 public $LossCon;
 public $WinCon;
 public $useMartinGale ;
 public $baseMoneyTrade;
 public $ObjTrade;

 

function __construct($asset,$AnalysisData,$baseMoneyTrade,$useMartinGale) { 

     $this->asset    = $asset ;
	 $this->AnalysisData   = $AnalysisData ;
	 $this->lossCon = 0 ;$this->WinCon = 0 ;
	 $this->LotNo = 0 ;
	 $this->useMartinGale = $useMartinGale ;
	 $this->baseMoneyTrade = $baseMoneyTrade  ;
	 

} // end __construct

function TestCase() { 

         $objSubTrade = array(); 
         for ($i=0;$i<=count($this->AnalysisData)-1;$i++) {
			 $suggestColor = getActionColor($i);
			 $numTrade++ ;
			 while (!$winStatus) {			 
			   list($winStatus,$profit,$objSubTrade) = $this->LabLotTrade($suggestColor,$thisIndex,$objSubTrade);
			   if ($winStatus) {
				 $this->LotNo++ ; $this->lossCon = 0 ;
				 $this->pushLotTrade($objSubTrade);
			   } else {
                 $this->lossCon++ ;
			   }
			 } // end while

            
         }

} 

function LabLotTrade($suggestColor,$thisIndex) { 
	     
		 
		 $resultColor =  getResultColor($thisIndex);
		 $MoneyTrade =   getMoneyTrade();
		 if ($suggestColor === $resultColor ) {
			 $winStatus = true ; 
			 $profit = $MoneyTrade * 0.94 ;
		 } else {
			 $winStatus = false ;
			 $profit = $MoneyTrade * -1 ;
		 }
		 $sObj = new stdClass();
		 $sObj->tradeno = $TradeNoOnLot ;
		 $sObj->tradetime = $this->AnalysisData[$thisIndex]['timefrom_unix'];
         $sObj->lastLossCon = $this->lossCon ;
         $sObj->MoneyTrade = $MoneyTrade ;
		 $sObj->suggestColor = $suggestColor ;
		 $sObj->resultColor = $suggestColor ;
		 $sObj->winStatus = $winStatus ;
		 $sObj->profit = $profit ;






		 $objSubTrade[] = $sObj;
		 return array($winStatus,$profit,$objSubTrade) ;
		 

} // end function


function getMoneyTrade() { 

$MoneyList = [1,2,6,18,54,162,384,512];
	     
		 if ($this->useMartinGale != 'y') {
            return 1 ;
		 } else {
           return $MoneyList[$this->lossCon] *  $this->baseMoneyTrade ;            
		 }


} // end function


function ShowTradeResult() { 
global $dbname;
global $pdo ;


} // end function

function getObjTrade() { 

 $sObj = new stdClass();
 $sObj->LotNo =  0 ;
 $sObj->startTradeTime =  '' ;
 $sObj->stopTradeTime =  '' ;
 $sObj->totalTrade =  0 ;
 $sObj->TradeList =  '' ;





} // end function


function init_Data() { 

        

}

/*
require_once($_SERVER['DOCUMENT_ROOT'] ."/shopA/cls***.php"); 
$cls_aa = new $cls_aa() ;
*/


} // end class



?>