<?php
class clsTradeDeriv {

// เป็น Class ระดับ เพจ 
// Properties
 

 public $pagename ;
 public $clsDataservice ;

function __construct() { 

     

} // end __construct

function getAction($AnalyObj,$macdThershold) {

// Color Group
$thisColor      = $AnalyObj['thisColor'] ;
$pColor  = $AnalyObj['previousColor']  ;
$pColor2  = $AnalyObj['previousColorBack2']  ;
$pColor3  = $AnalyObj['previousColorBack3']  ;
$pColor4  = $AnalyObj['previousColorBack4']  ;

// CutPoint Type
$CutPointType = $AnalyObj['CutPointType'];

// Turn Group
 $pTurn = $AnalyObj['PreviousTurnType'];
 $pTurnBack2 = $AnalyObj['PreviousTurnTypeBack2'];
 $pTurnBack3 = $AnalyObj['PreviousTurnTypeBack3'];
 $pTurnBack3 = $AnalyObj['PreviousTurnTypeBack4'];

//EMA 
$emaConflict = $AnalyObj['emaConflict'] ;
$emaAbove = $AnalyObj['emaAbove'] ;
$EMA3CandlePosition = $AnalyObj['ema3Position'] ;
$EMA5CandlePosition = $AnalyObj['ema5Position'] ;

// MACD  Group
$MACDHeight = $AnalyObj['MACDHeight'] ;

// Slope Group
/*
$slopeDirect3  = $AnalyObj['slopeDirection'] ;
$slopeDirect5  = $AnalyObj['slopeDirection'] ;
*/
//Body Group
$pip = $AnalyObj['pip'];
$thisAction = '' ; $actionCode = '' ;


$isSideway = $this->checkisSideway($thisColor,$pColor,$pColor2,$pColor3) ;
if ($isSideway) {
	list($thisAction,$actionCode) = $this->getToggle($thisColor);
	return array($thisAction,$actionCode) ;
}

if ($emaAbove==5) {
	$thisAction = 'PUT' ; $actionCode = 'DERIV1' ;
	list($thisAction,$actionCode)  = $this->ManageCase5($AnalyObj);
} 

if ($emaAbove==3) { 
	$thisAction = 'CALL' ; $actionCode = 'DERIV2' ;
	list($thisAction,$actionCode)  = $this->ManageCase3($AnalyObj);
} 

return array($thisAction,$actionCode) ;

} // end func Main 

function ManageCase3($AnalyObj) { 
// function นี้จะทำการ สนใจแต่ ema5 อยู่ บน ema3 เท่านั้น 

  $thisAction = 'CALL' ; $actionCode = 'DERIV3' ;

  
  if ($AnalyObj['MACDConvergence']=== 'Conver') {
	  if ($AnalyObj['thisColor'] === 'Red') {
         $thisAction = 'PUT' ; $actionCode = 'DERIV4' ;
	  }
  }



return array($thisAction,$actionCode) ;

} // end function

function ManageCase5($AnalyObj) { 
// function นี้จะทำการ สนใจแต่ ema5 อยู่ บน ema3 เท่านั้น 

  $thisAction = 'PUT' ; $actionCode = 'DERIV5' ;
  if ($AnalyObj['MACDConvergence']=== 'Conver') {
	  /*
	  if ($AnalyObj['thisColor']=== 'Green') {
         $thisAction = 'CALL' ; $actionCode = 'DERIV5.1' ;
	  }
	  */


  }

  if ($AnalyObj['thisColor']=== 'Green' && $AnalyObj['previousColor']=== 'Green' ) {
         $thisAction = 'CALL' ; $actionCode = 'DERIV5.1' ;
  }



return array($thisAction,$actionCode) ;

} // end function

function checkisSideway($thisColor,$pColor,$pColor2,$pColor3) { 

		if ( 
			 ($thisColor != $pColor) && 
			 ($pColor !=$pColor2)  &&
			 ($pColor2 !=$pColor3)  
			) {
			return true ;
		} else  {
			return false;   
		}

		return false;

} // end function

function getToggle($thisColor) { 

$thisAction = 'Call' ; $actionCode = 'TOGGLE';

if ($thisColor ==='Green') {
   $thisAction = 'PUT' ;
   $actionCode = 'TOGGLE';
}
if ($thisColor ==='Red') {
   $thisAction = 'CALL' ;
   $actionCode = 'TOGGLE';
}


return array($thisAction,$actionCode) ;

} // end function

  


} // end class

  


?>