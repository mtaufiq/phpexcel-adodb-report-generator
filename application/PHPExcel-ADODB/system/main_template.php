<?php if ( !isset($GLOBALS['base_url'])) exit('No direct script access allowed');?>

<!-- HTML PART - EDIT HTML HERE -->
<!-- ALL VARIABLES CREATED ON THE REPORT FILE WILL BE VISIBLE HERE -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $view['details']['title'] ?>-PHPExcel-ADODB Report</title>
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

<ul class="tabs">
    <li><a href="#reportView">View Report</a></li>
    <li><a href="#reportLog">Report Log</a></li>
    <li><a href="#reportOptions">Options</a></li>
</ul>

<div class="tab_container">

<div id="reportView" class="tab_content">
<fieldset><legend>Report Preview</legend>

<?php echo $view['html']['body']; ?>

</fieldset>
</div>


<div id="reportLog" class="tab_content">
<fieldset><legend>Report Log</legend>
<?php foreach( $view['logs'] as $log ): ?>
<?php echo $log ?>
<?php endforeach; ?>
</fieldset>
</div>


<div id="reportOptions" class="tab_content">
<fieldset><legend>Options:</legend>
<form method="POST" action="system/report_option.php">
<table>
<tbody>
<tr><td>Report</td><td><input type="text" name="rep" value="<?php echo $parameters[rep] ?>" /></td></tr>
<tr><td>Format</td><td><input type="text" name="format" value="<?php echo $parameters[format] ?>" /></td></tr>
</tbody>
</table>
<fieldset><legend>Custom Parameters</legend>
<table>
<tbody>

<?php foreach($parameters as $par_key => $par_val): ?>

<!--Add in here parameters that you dont want to include in the report options for-->
<?php if($par_key != "rep" && $par_key != "format" && $par_key != "Format" && $par_key != "real_user" && $par_key != "user" && $par_key != "pass" ): ?>
<tr><td><?php echo $par_key ?></td><td><input type="text" name="<?php echo $par_key ?>" value="<?php echo $par_val ?>" /></td></tr>
<?php endif; ?>

<!--Add in here parameters that you want to use, but you want it to be hidden-->
<?php if($par_key == "user" || $par_key == "pass"): ?>
<tr><td></td><td><input type="hidden" name="<?php echo $par_key ?>" value="<?php echo $par_val ?>" /></td></tr>
<?php endif; ?>

<?php endforeach; ?>

</tbody>
</table>
</fieldset>
<tr><td colspan="2"><input type="submit" value="Show Report" /></td></tr>
</form>
</fieldset>	
</div>


</div>

<div id="footer">
P.A.L (PHPExcel-ADODB-LibChart) Report Generator by Jeff
</div>
</body>

<script src="web/js/jquery.min.js" type="text/javascript"></script>
<LINK REL="SHORTCUT ICON" HREF="web/images/favicon.ico">
<LINK REL=StyleSheet HREF="web/css/main.css" TYPE="text/css">
<LINK REL=StyleSheet HREF="web/css/tabs.css" TYPE="text/css">

<script type="text/javascript">
	$(document).ready(function() {

	$(".tab_content").hide();
	$("ul.tabs li:first").addClass("active").show();
	$(".tab_content:first").show();

	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tab_content").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

});
</script>


</html>

