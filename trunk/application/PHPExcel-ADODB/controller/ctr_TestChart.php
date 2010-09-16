<?php
class TestChart{

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
		
		$worksheet->SetCellValue('B2', 'Penta Insurance Broker Services Inc.');
		$worksheet->SetCellValue('B3', 'Sample Graph - Vertical Bar');
		
		$dataSample['January'] = array("January",200);
		$dataSample['February'] = array("February",156);
		$dataSample['March'] = array("March",100);
		$dataSample['April'] = array("April",120);
				$dataSample['Jeff'] = array("Jeff",190);
		
		
		$chartSampleGen = new ChartControl("Sample","Sample Graph/Chart Title");
		$chartSampleGen->createVerticalBar(600,250);
		$chartSampleGen->setData($dataSample);
		$chartSamplePath = $chartSampleGen->render($worksheet, 'B5');
		
		$worksheet->SetCellValue('B7', 'Graph caption, explanations');
		
		$worksheet->getColumnDimension('B')->setAutoSize(true);
		$worksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
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