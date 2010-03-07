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
		
		$worksheet->SetCellValue('B2', 'Penta Insurance Company');
		$worksheet->SetCellValue('B3', 'Income Statement');
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$worksheet->SetCellValue('B4', 'For the end of '.$monthName);
		
		$ctr = 6;
		$incomeStart = $ctr;
		while($row = $incomeRows->FetchRow()){
		
			$worksheet->SetCellValue('B'.$ctr, $row['Account']);
			$worksheet->SetCellValue('C'.$ctr, $row['Debit']);
			$worksheet->SetCellValue('D'.$ctr, $row['Credit']);
		
			$ctr++;
			
		}
		
		$incomeEnd = $ctr - 1;//dpat "=Sum(C.$incomeStart.":C".$incomeEnd."")
		$worksheet->SetCellValue('B'.($ctr), 'Total Income');
		$worksheet->SetCellValue('C'.($ctr), "=SUM(C".$incomeStart.":C".$incomeEnd.")");
		$worksheet->SetCellValue('D'.($ctr), "=SUM(D".$incomeStart.":D".$incomeEnd.")");
		
		$ctr = $ctr + 2;
		$expenseStart = $ctr;
		
		while($row = $expenseRows->FetchRow()){
		
			$worksheet->SetCellValue('B'.$ctr, $row['Account']);
			$worksheet->SetCellValue('C'.$ctr, $row['Debit']);
			$worksheet->SetCellValue('D'.$ctr, $row['Credit']);
		
			$ctr++;
		
		}
		$expenseEnd = $ctr -1;
		$worksheet->SetCellValue('B'.($ctr), 'Total Expenses');
		$worksheet->SetCellValue('C'.($ctr), "=SUM(C".$expenseStart.":C".$expenseEnd.")");
		$worksheet->SetCellValue('D'.($ctr), "=SUM(D".$expenseStart.":D".$expenseEnd.")");
		$ctr = $ctr + 2;
		
		$worksheet->SetCellValue('B'.$ctr, 'Net Income');
		$totalIncome = 'SUM(C'.$incomeStart.':C'.$incomeEnd.')';
		$totalExpense =  'SUM(C'.$expenseStart.':C'.$expenseEnd.')';
		$worksheet->SetCellValue('C'.($ctr), ($totalIncome + $totalExpense));
		
		
		
		
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