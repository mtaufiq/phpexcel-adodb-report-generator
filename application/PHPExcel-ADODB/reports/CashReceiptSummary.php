<?php
//EDIT THIS PART - REPORT DETAILS AND CREATION
$details = array();
$details['fileName'] = "CRBSummary";
$details['format'] = $parameters['format'];
$details['creator'] = "duazo";
$details['title'] = "Cash Receipt Summary";


/*
REPORT GENERATOR - PLS MINIMIZE EDITING THIS CODE

$objReport = a created object based on the 'rep' parameter
$view = fetches the view data from the created report object

*/
$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view();
?>
