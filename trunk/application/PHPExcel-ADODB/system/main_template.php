<?php if ( !isset($GLOBALS['base_url'])) exit('No direct script access allowed');?>

<!-- HTML PART - EDIT HTML HERE -->
<!-- ALL VARIABLES CREATED ON THE REPORT FILE WILL BE VISIBLE HERE -->
<html>
<title><?php echo $view['details']['title'] ?>-PHPExcel-ADODB Report</title>
<head>
<LINK REL="SHORTCUT ICON" HREF="web/images/favicon.ico">
<LINK REL=StyleSheet HREF="web/css/main.css" TYPE="text/css">
<?php echo $view['html']['style']; ?>
</head>
<body>
<div id="header">
<h1 class="report_name"><?php echo $view['details']['title'] ?></h1>
<div id="sub_header">
Download as: 
<?php foreach($objReport->report->getFormatLinks() as $format => $formatName): ?>
<a href="<?php echo $objReport->report->getFileLink().'.'.$format ?>" target="_self"><img src="web/images/icn_<?php echo $format ?>.png" width="20px" height="20px" title="Download as <?php echo $formatName ?>"></a>&nbsp;
<?php endforeach; ?>
</div>
</div>

<div id="reportView">
<fieldset><legend>Report Preview</legend>

<?php echo $view['html']['body']; ?>

</fieldset>
</div>


<div id="reportLog">
<fieldset><legend>Report Log</legend>
<?php foreach( $view['logs'] as $log ): ?>
<?php echo $log ?>
<?php endforeach; ?>
</fieldset>
</div>

<div id="footer">
P.A.L (PHPExcel-ADODB-LibChart) Report Generator by Jeff
</div>
</body>
</html>

