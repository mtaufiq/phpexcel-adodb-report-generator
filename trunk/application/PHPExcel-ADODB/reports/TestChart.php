<?php //Creating the report
$details = array();
$details['fileName'] = "TestChart";
$details['format'] = $parameters['format'];
$details['creator'] = $parameters['real_user'];
$details['title'] = "Test Chart";

/*
REPORT GENERATOR - PLS MINIMIZE EDITING THIS CODE

$objReport = a created object based on the 'rep' parameter
$view = fetches the view data from the created report object

*/
$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view();
?>
