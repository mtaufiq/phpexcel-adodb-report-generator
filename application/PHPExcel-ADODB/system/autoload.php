<?php

/*System files - DO NOT REMOVE*/

//Configurations
require('system/config.php'); 

//ADODB Classes
require('system/ADODB/adodb.inc.php'); 

//PHPExcel Classes
require('system/PHPExcel/PHPExcel.php');

//Libchart Classes
include ('system/Libchart/classes/libchart.php');

/*Default Report,Chart and Database Controller Classes*/
require('system/Charting.class.php');
require('system/dbControl.class.php');
require('system/Report.class.php');

/*INCLUDE OWN SECURITY HERE*/
/*Perform Security Check here
include ('system/sf_guard_logger/sf_guard_logger.class.php');
$logger = new sfGuardLogger($_GET['user'],$_GET['pass']); //for symfony
$parameters['real_user'] = $logger->getUser();
/*Comment all of these to stop security check*/

/*GET ALL PARAMETERS*/
while ($param = current($_GET)) {
    $parameters[key($_GET)] = $param;
    next($_GET);
}

/*Specific  Report files - name passed as parameter*/
$report = $_GET['rep'];

require 'model/mod_'.$report.'.php';
require 'controller/ctr_'.$report.'.php'; 
include 'reports/'.$report.'.php';
include 'system/main_template.php';

?>
