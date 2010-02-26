<?php //Creating the report
$details = array();
$details['fileName'] = "CDBSummary";
$details['format'] = "2007";
$details['creator'] = "duazo";
$details['title'] = "Cash Disbursement Summary";

$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view() //get view report components (logs and html)
?>