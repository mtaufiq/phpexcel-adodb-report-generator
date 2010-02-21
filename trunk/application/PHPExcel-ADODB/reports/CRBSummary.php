<?php //include model and controllers ?>
<?php include '../model/mod_CashReceiptSummary.php'; ?>
<?php include '../controller/ctr_CashReceiptSummary.php'; ?>

<?php //Creating the report
$crbReport = new CashReceiptSummary();

$details = array();
$details['fileName'] = "CRBSummary";
$details['format'] = "2003";
$details['creator'] = "duazo";
$details['title'] = "Cash Receipt Summary";

$parameters = array();
$parameters['month'] = $_GET['month'];
$parameters['year'] = $_GET['year'];

$crbReport->init($details , $parameters);
$crbReport->create();
?>

<?php //Showing the report with logs ?>
<?php $view = $crbReport->view() //get view report components (logs and html) ?>
<html>
<head>
<LINK REL=StyleSheet HREF="web/css/main.css" TYPE="text/css">
<?php echo $view['html']['style']; ?>
</head>
<body>
<div id="header">
<h1>Cash Receipt Summary Report</h1>
</div>

<div id="reportLog">
<fieldset><legend>Report Log</legend>
<?php foreach( $view['logs'] as $log ): ?>
<?php echo $log ?>
<?php endforeach; ?>
</fieldset>
</div>

<div id="reportView">

<fieldset><legend>Report Preview - <a href="<?php echo 'generated/'.$crbReport->report->getFileName(); ?>" target="_self">click here to download</a></legend>

<?php echo $view['html']['body']; ?>

</fieldset>
</div>

<div id="footer">
PHPExcel-ADODB Report Generator by Jeff
</div>
</body>
</html>

