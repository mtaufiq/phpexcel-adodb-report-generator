<?php
/*FRONT CONTROLLER*/

/*System files - DO NOT REMOVE*/

//ADODB Classes
require('system/ADODB/adodb.inc.php'); 

//PHPExcel Classes
require('system/PHPExcel/PHPExcel.php');

/*Default Report and Database Controller Classes*/
require('system/dbControl.class.php');
require('system/Report.class.php');

/*Perform Security Check here*/
include ('system/sf_guard_logger/sf_guard_logger.class.php');
$logger = new sfGuardLogger($_GET['user'],$_GET['pass']); //for symfony
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