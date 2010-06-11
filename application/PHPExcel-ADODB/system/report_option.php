<?php

$redirect_string = "http://localhost/testExcel/PHPExcel-ADODB/view.php?";

/*GET ALL PARAMETERS*/
while ($rep_param = current($_POST)) {
    
    $rep_key = key($_POST);

    	$redirect_string .= "".$rep_key."=".$rep_param."&";		

    next($_POST);    
}

echo $redirect_string;

header('Location: '.$redirect_string);
?>
