<?php //Creating the report
$details = array();
$details['fileName'] = "TrialBalance";
$details['format'] = "2007";
$details['creator'] = "duazo";
$details['title'] = "Trial Balance";

$parameters = array();
$parameters['month'] = $_GET['month'];
$parameters['year'] = $_GET['year'];
$parameters['user'] = $_GET['user'];
$parameters['pass'] = $_GET['pass'];

$trialBalance = new TrialBalance($details , $parameters);
$trialBalance->create();
?>

<?php //Showing the report with logs ?>
<?php $view = $trialBalance->view() //get view report components (logs and html) ?>
<html>
<head>
<LINK REL=StyleSheet HREF="web/css/main.css" TYPE="text/css">
<?php echo $view['html']['style']; ?>
</head>
<body>
<div id="header">
<h1>Trial Balance</h1>
</div>

<div id="reportLog">
<fieldset><legend>Report Log</legend>
<?php foreach( $view['logs'] as $log ): ?>
<?php echo $log ?>
<?php endforeach; ?>
</fieldset>
</div>

<div id="reportView">

<fieldset><legend>Report Preview - <a href="<?php echo $trialBalance->report->getFileLink(); ?>" target="_self">click here to download</a></legend>

<?php echo $view['html']['body']; ?>

</fieldset>
</div>

<div id="footer">
PHPExcel-ADODB Report Generator by Jeff
</div>
</body>
</html>

