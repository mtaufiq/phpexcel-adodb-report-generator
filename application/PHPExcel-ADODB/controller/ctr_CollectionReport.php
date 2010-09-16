<?php
class CollectionReport{ //Class name of report - must be same as the rep parameter

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
		$sheet1->SetCellValue('A2', 'Collection Report');
		
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$sheet1->SetCellValue('A3', 'As of '.$monthName.' '.$params['day'].' , '.$params['year']);
		
		
		/*SET HERE ACCOUNTS TO INCLUDE*/
		$accounts = " '10005', '10007', '10008', '10009' ";
		$DB = new CollectionReportModel();
		
		$result = $DB->get_entries($accounts, $params['year'], $params['month']);
		
		$curRow = 6; //Start row
		$start = true;
		$acctTitle = "";
		$curAcctTitle ="";
		$subRows = array();
		
		/*Start Print Data*/

		while($row = $result->FetchRow()){
			
			$curAcctTitle = $row['account_desc'];
			
			if($start == true || $curAcctTitle <> $acctTitle):
				if($start <> true){ $curRow+=1; }
				$acctTitle = $row['account_desc'];
				$curAcctTitle = $acctTitle;
				$sheet1->SetCellValue('A'.$curRow, $acctTitle);
				$sheet1->getStyle('A'.$curRow)->getFont()->setSize(12); 
				$sheet1->getStyle('A'.$curRow)->getFont()->setBold(TRUE); 
				$start = false;
				$curRow += 2;
				$this->showGenericHeader($curRow, $sheet1);
				$curRow += 1;
			endif;
			
			$sheet1->SetCellValue('A'.$curRow, $row['trans_date']);
			$sheet1->SetCellValue('B'.$curRow, $row['or_num']);
			$sheet1->SetCellValue('C'.$curRow, $row['clientlname'].','.$row['clientfname']);
			$sheet1->SetCellValue('D'.$curRow, $row['particulars']);
			$sheet1->SetCellValue('E'.$curRow, ($row['policynum']=="" ? 'N/A' : $row['policynum']));
			$sheet1->SetCellValue('F'.$curRow, $row['amount']);
			
			$subRows[] = $curRow;
			
			$sheet1->getStyle('A'.$curRow.':F'.$curRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$curRow += 1;
		}
		
		$curRow += 1;
		$sheet1->SetCellValue('E'.$curRow, 'Total Receivables');
		$sheet1->SetCellValue('F'.$curRow, $this->getSumString($subRows, 'F'));
		
		$sheet1->getStyle('E'.$curRow.':'.'F'.$curRow)->getFont()->setSize(12); 
		$sheet1->getStyle('E'.$curRow.':'.'F'.$curRow)->getFont()->setBold(TRUE); 
		
		//Formatting
		foreach(range('A','Z') as $i){
			$sheet1->getColumnDimension($i)->setAutoSize(true); // Format the column to autosize
		}
		$sheet1->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		
		$this->report->end(); // Always call at the end of report

	}
	
	private function showGenericHeader($row, $sheet){
		$sheet->SetCellValue('A'.$row, 'Transaction Date');
		$sheet->SetCellValue('B'.$row, 'OR #');
		$sheet->SetCellValue('C'.$row, 'Payor');
		$sheet->SetCellValue('D'.$row, 'Particulars');
		$sheet->SetCellValue('E'.$row, 'Policy (N/A if none)');
		$sheet->SetCellValue('F'.$row, 'Amount');
		
		$sheet->getStyle('A'.$row.':F'.$row)->getFont()->getColor()->setRGB('FFFFFF');
		$sheet->getStyle('A'.$row.':F'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle('A'.$row.':F'.$row)->getFill()->getStartColor()->setRGB('666666');
		
		$sheet->getStyle('A'.$row.':F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
	
	private function getSumString($subRows, $colToSum){
	 $str = "=";
		foreach($subRows as $sub):
		  if($sub == end($subRows)) {
			$str .= $colToSum.$sub;
		  } else {
			$str .= $colToSum.$sub.'+';
		  }
		endforeach;
	  return $str;
	}
	
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		$view['details'] = $this->report->getDetails(); //load logs
		return $view;
	}

}

?>