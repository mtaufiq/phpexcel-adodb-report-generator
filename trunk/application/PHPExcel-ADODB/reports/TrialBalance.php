<?php //Creating the report
$details = array();
$details['fileName'] = "TrialBalance";
$details['format'] = "pdf";
$details['creator'] = "duazo";
$details['title'] = "Trial Balance";

$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view() //get view report components (logs and html)
?>
