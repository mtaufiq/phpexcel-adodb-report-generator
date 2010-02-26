<!-- HTML PART - EDIT HTML HERE -->
<!-- ALL VARIABLES CREATED ON THE REPORT FILE WILL BE VISIBLE HERE -->
<html>
<head>
<LINK REL=StyleSheet HREF="web/css/main.css" TYPE="text/css">
<?php echo $view['html']['style']; ?>
</head>
<body>
<div id="header">
<h1>> <?php echo $view['details']['title'] ?></h1>
</div>

<div id="reportLog">
<fieldset><legend>Report Log</legend>
<?php foreach( $view['logs'] as $log ): ?>
<?php echo $log ?>
<?php endforeach; ?>
</fieldset>
</div>

<div id="reportView">

<fieldset><legend>Report Preview - <a href="<?php echo $objReport->report->getFileLink(); ?>" target="_self">click here to download</a></legend>

<?php echo $view['html']['body']; ?>

</fieldset>
</div>

<div id="footer">
PHPExcel-ADODB Report Generator by Jeff
</div>
</body>
</html>

