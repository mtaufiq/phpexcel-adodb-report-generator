<?php
class TestTemplate{ //Class name of report - must be same as the rep parameter

	/*PLS DONT EDIT THIS CODE*/
	public function __construct($details , $parameters){
		
		$this->report =  new Report();
		$this->report->initialize($details);
		$this->param = $parameters;
		
	}

	/*Creates the report*/
	public function create(){
	
		$objPHPExcel = $this->report->getPHPExcelObj(); // PHP Excel Object - used to create anything in the report
		$params = $this->param; // Parameters given :)
	
		// Rename sheet
		$this->report->log("Created Worksheet"); // logs used to debug report creation
		$objPHPExcel->getActiveSheet()->setTitle($this->report->fileName);
		
		// Add some data on the first workseet
		$this->report->log("Adding Report Content");
		
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet1 = $objPHPExcel->getActiveSheet();
		
		$sheet1->SetCellValue('A1', 'Penta Insurance Broker Services Inc.');
		$sheet1->SetCellValue('A2', 'Trial Balance');
		
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$sheet1->SetCellValue('A1', 'As of '.$monthName.' ,'.$params['day'].' '.$params['year']);
		$sheet1->SetCellValue('C6', 'DR');
		$sheet1->SetCellValue('D6', 'CR');

		
		$this->report->end(); // Always call at the end of report

	}
	
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		$view['details'] = $this->report->getDetails(); //load logs
		return $view;
	}

}

?>