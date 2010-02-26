<?php
class IncomeStatement{

	public function __construct($details , $parameters){
		
		$logger = new sfGuardLogger($parameters['user'],$parameters['pass']);
		
		$this->report =  new Report();
		//Insert details to initialize method -> filename, format, creator, lastModifiedBy, title, subject, description
		$this->report->initialize($details);// Get PHPExcel object 
		$this->param = $parameters;// Store parameters to report controller object
		
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
		
		$worksheet->SetCellValue('B2', 'Income Statement');
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$worksheet->SetCellValue('B3', 'For the end of '.$monthName);
		$worksheet->SetCellValue('B5', 'Revenues');
		$worksheet->SetCellValue('B6', 'Landscaping Fees');
		$worksheet->SetCellValue('B7', 'Finance Charge Income');
		$worksheet->SetCellValue('B9', 'Total');
		$worksheet->SetCellValue('B11', 'Expenses');
		$worksheet->SetCellValue('B12', 'Landscaping Fees');
		$worksheet->SetCellValue('B13', 'Finance Charge Income');
		$worksheet->SetCellValue('B15', 'Total');
		$worksheet->SetCellValue('B18','Net Income');	
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$this->report->end($this->report->getFormat());

	}
	
	private function showSumFormula($totals){
		
		$formula = "=";
		foreach($totals as $index):
				$formula .= $index;
				if(end($totals) <> $index){
					$formula .= "+";
				}
		endforeach;
		
		return $formula;
	
	}

	private function showAcctTotal($acct_type,$ctr,$totStart,$totEnd,$totalsC,$totalsD,$worksheet){
						
			$worksheet->getStyle('B'.$ctr.':'.'D'.$ctr)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$worksheet->getStyle('B'.$ctr.':'.'D'.$ctr)->getFill()->getStartColor()->setRGB('999000');
			
			$worksheet->getStyle('B'.$ctr)->getFont()->setBold(true);
			$worksheet->SetCellValue('B'.$ctr, "Total ".$acct_type);
			
			$getSumOfC = 'SUM(C'.$totStart.':C'.$totEnd.')';
			$getSumOfD = 'SUM(D'.$totStart.':D'.$totEnd.')';
			$worksheet->SetCellValue('C'.$ctr, '='.$getSumOfC);	
			$worksheet->SetCellValue('D'.$ctr, '='.$getSumOfD);
			
	}						
	
	private function checkAccountType($account){
		if ($account == 'Assets' || $account == 'Income' || $account == 'Expense'){
			return 'C'; //column for Debit
		}else{
			return 'D';
		}
	}
	private function checkNegativeBalance($balance,$column,$account){
		if(($account == 'Assets' || $account == 'Income') && $balance < 0){
			return $this->reverseColumn($column);
		}else{
			return $column;
		}
	}
	
	private function reverseColumn($column){
		if($column == 'C'){ return 'D'; }
		else{ return 'C'; }
	}
	
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		$view['details'] = $this->report->getDetails(); //load logs
		return $view;
	}

}

?>