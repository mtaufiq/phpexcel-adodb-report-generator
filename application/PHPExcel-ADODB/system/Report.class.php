<?php
class Report{

	public $fileName;
	public $fileFormat;
	public $format;
	public $PHPExcelObj;
	public $logs;
	
	public function initialize($details){
	
		$this->log("Loading PHPExcel");

		/** PHPExcel_Writer_Excel2007 */
		include 'PHPExcel/PHPExcel/Writer/Excel2007.php';
		
		/** PHPExcel_Writer_Excel5 - for 2003 and below */
		include 'PHPExcel/PHPExcel/Writer/Excel5.php';
		
		/** PHPExcel_HTML Writer*/
		include 'PHPExcel/PHPExcel/Writer/HTML.php';
		
		/** PHPExcel_PDF Writer */
		include 'PHPExcel/PHPExcel/IOFactory.php';
		
		
		$this->log("Loading details:".
				   "<ul>".
		           "<li>Creator: ".$details['creator']."</li>".
		           "<li>Title: ".$details['title']."</li>".
		           "<li>Format: ".$details['format']."</li>".
		           "<li>Filename: ".$details['fileName']."</li>".
				   "</ul>"			   
				  );

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		$this->log("Creating report - ".$details['title']);

		// Set properties
		$this->log("Setting properties of report");
		$objPHPExcel->getProperties()->setCreator($details['creator']);
		$objPHPExcel->getProperties()->setLastModifiedBy($details['creator']);
		$objPHPExcel->getProperties()->setTitle($details['title']);
		$objPHPExcel->getProperties()->setSubject($details['title']." report");
		$objPHPExcel->getProperties()->setDescription($details['title']." report generated using PHPExcel and ADODB");
		
		$this->fileName = $details['fileName'];
		$this->format = $details['format'];

		$this->setPHPExcelObj($objPHPExcel);
		
	}
	
	public function end($format){
	
		$objWriter;
	
		switch ($format) {
			case '2007':
				$this->log("Saving in Excel 2007 format (.xlsx)");
				$this->setFileFormat(".xlsx");
				$objWriter = new PHPExcel_Writer_Excel2007($this->getPHPExcelObj());
				break;
			case '2003':
				$this->log("Saving in Excel 2003 format (.xls)");
				$this->setFileFormat(".xls");
				$objWriter = new PHPExcel_Writer_Excel5($this->getPHPExcelObj());
				break;
			case 'pdf':
				$this->log("Saving in Adobe PDF format (.pdf)");
				$this->setFileFormat(".pdf");
				$objWriter = PHPExcel_IOFactory::createWriter($this->getPHPExcelObj(), 'PDF');
			default:
				$this->log("File not saved! - Format invalid");
		}
		
		$this->save($objWriter);
				
	}
	
	public function view(){
		$objWriter = new PHPExcel_Writer_HTML($this->getPHPExcelObj());
		
		$html['header'] = $objWriter->generateHTMLHeader();
		
		$html['style'] = $objWriter->generateStyles(true); // do not write <style> and </style>

		$html['body'] = $objWriter->generateSheetData();
		
		$html['footer'] = $objWriter->generateHTMLFooter();
	
		return $html;
	}
	
	private function save($objWriter){
		$objWriter->save('reports/generated/'.$this->getFileName());
		$this->log("Done File Writing - ".$this->getFileName());
	}
	
	private function setPHPExcelObj($obj){
		$this->PHPExcelObj = $obj;
	}
	
	public function getPHPExcelObj(){
		return $this->PHPExcelObj;
	}
	
	
	public function setFileFormat($fileFormat){
		$this->fileFormat = $fileFormat;
	}
	
	public function getFileLink(){
		return 'reports/generated/'.$this->fileName.$this->fileFormat;
	}
	
	public function getFileName(){
		return $this->fileName.$this->fileFormat;
	}
	
	public function getFormat(){
		return $this->format;
	}
	
	public function log($log){
		// Add some data
		$this->logs[] = date('H:i:s') . " - " . $log."<br />";
	}
	
	public function getLogs(){
		return $this->logs;
	}

}

?>