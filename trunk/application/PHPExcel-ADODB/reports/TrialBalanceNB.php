<?php //Creating the report
$details = array();
$details['fileName'] = "TrialBalanceNB";
$details['format'] = "2007";
$details['creator'] = "duazo";
$details['title'] = "Trial Balance (Do not show balance)";

$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view() //get view report components (logs and html)
?>
