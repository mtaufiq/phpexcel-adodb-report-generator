<?php
class IncomeStatement{

	public function __construct($details , $parameters){
		
		$this->report =  new Report();
		$this->report->initialize($details);
		$this->param = $parameters;
		
	}

	public function create(){
	
		$objPHPExcel = $this->report->getPHPExcelObj();
		$params = $this->param;
	
		// Rename sheet
		$this->report->log("Created Worksheet");
		$objPHPExcel->getActiveSheet()->setTitle($this->report->fileName);
		
		// Add some data
		$this->report->log("Adding Report Content");
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		$worksheet = $objPHPExcel->getActiveSheet();
		
		$DB = new IncomeStatementModel();
		$incomeRows = $DB->get_Income($params['year'],$params['month']);
		$expenseRows = $DB->get_Expense($params['year'],$params['month']);
		
		$worksheet->SetCellValue('B1', 'Penta Insurance Company');
		$worksheet->SetCellValue('B2', 'Income Statement');
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$worksheet->SetCellValue('B3', 'For the end of '.$monthName);
		
		$ctr = 5;
		
		while($row = $incomeRows->FetchRow()){
		
		$worksheet->SetCellValue('B'.$ctr, $row['Account']);
		$worksheet->SetCellValue('C'.$ctr, $row['Debit']);
		$worksheet->SetCellValue('D'.$ctr, $row['Credit']);
		
		$ctr++;
		
		}
		
		$ctr = $ctr + 1;
		
		while($row = $expenseRows->FetchRow()){
		
		$worksheet->SetCellValue('B'.$ctr, $row['Account']);
		$worksheet->SetCellValue('C'.$ctr, $row['Debit']);
		$worksheet->SetCellValue('D'.$ctr, $row['Credit']);
		
		$ctr++;
		
		}
	
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->report->end($this->report->getFormat());
		
		
	}

	
	
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		$view['details'] = $this->report->getDetails(); //load logs
		return $view;
	}

}

?>