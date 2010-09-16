<?php
//EDIT THIS PART - REPORT DETAILS AND CREATION
$details = array();
$details['fileName'] = "CRBSummary"; //Report Filename to use
$details['format'] = $parameters['format']; //Report format given
$details['creator'] = "duazo"; //Report creator
$details['title'] = "Cash Receipt Summary"; //Report title


/*
REPORT GENERATOR - PLS DONT EDIT THIS CODE

$objReport = a created object based on the 'rep' parameter
$view = fetches the view data from the created report object

*/
$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view();
?>
