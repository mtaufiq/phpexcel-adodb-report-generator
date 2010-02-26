<?php
class CashDisbursementSummary{

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
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Penta Insurance Broker Services Inc.');
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'CASH DISBURSEMENT');
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B4', 'For the month ended '.$monthName.', '.$params['year']);

		$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Summary');
		$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Debit');
		$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Credit');

		$mod = new CashDisbursementSummaryModel();
		$result = $mod->get_summary($params['year'],$params['month']);
		
		$ctr = 9; //starting cell B9
		
		while($row = $result->FetchRow())
		  {
		  $objPHPExcel->getActiveSheet()->SetCellValue('B'.$ctr, $row['account_desc'] );
		  $debit = ( $row['debit'] > 0 ?  $row['debit'] : '');
		  $objPHPExcel->getActiveSheet()->SetCellValue('D'.$ctr, $debit);
		  $credit = ( $row['credit'] > 0 ?  $row['credit'] : '');
		  $objPHPExcel->getActiveSheet()->SetCellValue('E'.$ctr, $credit );
		  $ctr++;
		  }
		
		if($ctr > 9){ //If counter has moved
		$ctr++;
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.($ctr), '=SUM(D8:D'.($ctr-2).')');
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.($ctr), '=SUM(E8:E'.($ctr-2).')');
		}
		
		/*Formatting of Cells*/
		$objPHPExcel->getActiveSheet()->getStyle('B'.($ctr).':'.'E'.($ctr))->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$objPHPExcel->getActiveSheet()->getStyle('B'.($ctr).':'.'E'.($ctr))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setSize(15); 
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setBold(TRUE); 
		$objPHPExcel->getActiveSheet()->getStyle('B7:E7')->getFont()->setBold(TRUE); 
		  
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