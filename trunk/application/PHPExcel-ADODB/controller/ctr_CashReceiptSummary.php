<?php require '../system/Report.class.php' ?>
<?php require '../system/sf_guard_logger/sf_guard_logger.class.php' ?>
<?php
class CashReceiptSummary{

	public $param;

	public function init($details , $parameters){
		
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
		$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Penta Insurance Broker Services Inc.');
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'CASH RECEIPTS');
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B4', 'For the month ended '.$monthName.', '.$params['year']);

		$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Summary');
		$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Debit');
		$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Credit');

		$objPHPExcel->getActiveSheet()->getStyle('B7:D7')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('99999999');

		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		
		$mod = new CashReceiptSummaryModel();
		$result = $mod->get_summary($params['year'],$params['month']);
		
		$ctr = 8;
		while($row = $result->FetchRow())
		  {
		  $objPHPExcel->getActiveSheet()->SetCellValue('B'.$ctr, $row['account_desc'] );
		  $objPHPExcel->getActiveSheet()->SetCellValue('C'.$ctr, $row['debit']);
		  $objPHPExcel->getActiveSheet()->SetCellValue('D'.$ctr, $row['credit'] );
		  $ctr++;
		  }
		
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$ctr, '=SUM(C8:C'.($ctr-1).')');
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$ctr, '=SUM(D8:D'.($ctr-1).')');
		
		  
		$this->report->end($this->report->getFormat());

	}
	
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		return $view;
	}

}

?>