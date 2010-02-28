<?php //Creating the report
$details = array();
$details['fileName'] = "TrialBalance";
$details['format'] = $parameters['format'];
$details['creator'] = $parameters['real_user'];
$details['title'] = "Trial Balance";

/*
REPORT GENERATOR - PLS MINIMIZE EDITING THIS CODE

$objReport = a created object based on the 'rep' parameter
$view = fetches the view data from the created report object

*/
$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view();
?>
