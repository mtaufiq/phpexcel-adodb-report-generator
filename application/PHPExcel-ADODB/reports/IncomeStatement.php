<?php //Creating the report
$details = array();
$details['fileName'] = "IncomeStatement";
$details['format'] = $parameters['format'];
$details['creator'] = "Penta Capital";
$details['title'] = "Income Statement";

/*
REPORT GENERATOR - PLS MINIMIZE EDITING THIS CODE

$objReport = a created object based on the 'rep' parameter
$view = fetches the view data from the created report object

*/
$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view();
?>