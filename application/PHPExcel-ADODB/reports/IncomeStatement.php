<?php //Creating the report
$details = array();
$details['fileName'] = "IncomeStatement";
$details['format'] = "pdf";
$details['creator'] = "Penta Capital";
$details['title'] = "Income Statement";

$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view() //get view report components (logs and html)
?>