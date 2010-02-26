<?php
//EDIT THIS PART - REPORT DETAILS AND CREATION
$details = array();
$details['fileName'] = "CRBSummary";
$details['format'] = "2007";
$details['creator'] = "duazo";
$details['title'] = "Cash Receipt Summary";

$objReport = new $report($details , $parameters);
$objReport->create();
$view = $objReport->view() //get view report components (logs and html)
?>
