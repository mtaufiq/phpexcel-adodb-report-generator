<?php
/*VIEW CONTROLLER*/

/*System files - DO NOT REMOVE*/

//ADODB Classes
require('system/adodb5/adodb.inc.php'); 

//PHPExcel Classes
require('system/Classes/PHPExcel.php');

/*Default Report and Database Controller Classes*/
require('system/dbControl.class.php');
require('system/Report.class.php');

/*OTHER FILES - You can include files here*/
include ('system/sf_guard_logger/sf_guard_logger.class.php');



/*Specific  Report files - name passed as parameter*/
$file = $_GET['rep'];

require 'model/mod_'.$file.'.php';
require 'controller/ctr_'.$file.'.php'; 
include 'reports/'.$file.'.php';

?>