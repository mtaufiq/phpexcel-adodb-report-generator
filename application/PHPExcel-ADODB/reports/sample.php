<?php

//basic function to download chart
function charting($title,$val1,$val2,$color){
$url = file_get_contents("http://chart.apis.google.com/chart?chs=340x200&cht=p3&chl=$title&chd=t:$val1,$val2&chtt=Example+Chart&chco=$color");
$img = fopen("chart.png", 'w');
fwrite($img, $url);
fclose($img); 
}

//create excel file
$objPHPExcel = new PHPExcel();
charting("PHP|Java",50,50,"00ff00,0000ff");
$objPHPExcel->setActiveSheetIndex(0);
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Example Chart');
$objDrawing->setDescription('Example Chart');
$objDrawing->setPath('chart.png');
$objDrawing->setCoordinates('H6');
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

//Write xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

?>