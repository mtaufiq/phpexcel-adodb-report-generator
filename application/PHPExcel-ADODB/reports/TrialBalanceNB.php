<?php //Creating the report
$details = array();
$details['fileName'] = "TrialBalanceNB";
$details['format'] = $parameters['format'];
$details['creator'] = "duazo";
$details['title'] = "Trial Balance (Do not show balance)";

/*
REPORT GENERATOR - PLS MINIMIZE EDITING THIS CODE

$objReport = a created object based on the 'rep' parameter
$view = fetches the view data from the created report object

*/
$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view();
?>
