<?php
  
  
   $st = "";   
   
   
   $sFileName = 'sortGetAction.php';
   $file = fopen($sFileName,"r");
   $caseno = 1;
   while(! feof($file))  {
     $ch = fgets($file) ;
	 $word = 'Number of conditions';
	 if (str_contains($ch, $word)) {
        $ch = $ch . "" . '// Case No =' . $caseno++ . "\n" ;
	 }
	 $st .= $ch ;

   }
   fclose($file);

   $myfile = fopen("tmp.txt", "w") or die("Unable to open file!");
   fwrite($myfile, $st);
   fclose($myfile);

  


?>