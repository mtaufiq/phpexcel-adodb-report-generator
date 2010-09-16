<?php
$redirect_string = "../view.php?";

/*GET ALL PARAMETERS*/
while ($rep_param = current($_POST)) {
    
    $rep_key = key($_POST);

    	
        $redirect_string .= "".$rep_key."=".$rep_param."&";		

    next($_POST);    
}
header('Location: '.$redirect_string);
?>
