<?php
class StatementOfAccount{ //Class name of report - must be same as the rep parameter

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
		
        $formatted_date = strtotime($params["date"]);
        $formatted_date = date('F d , Y', $formatted_date);
        
		$sheet1->SetCellValue('A1', 'Statement of Account');
        $sheet1->mergeCells('A1:E1');

        $sheet1->SetCellValue('A3', $formatted_date);
        
        $DB = new StatementOfAccountModel();
        $client_query = $DB->get_client($params["client"]);
		$client_info = array();
        
		while($row = $client_query->FetchRow()){
        
            $client_info["id"] = $row["id"];
            
            $client_info["name"] = $row["businessname"]." - ".$row["clientlname"]." , ".$row['clientfname'];
        
        }
        
        $sheet1->SetCellValue('A5', $client_info["name"]);
        
        
        $sheet1->SetCellValue('A7', "We would like to follow up payment for the following:");
        $sheet1->mergeCells('A7:E7');

        
        $policies_of_client = $DB->get_policies($params["client"]);    

        $rowCtr = 9; // Starting Cell to iterate;
        
        $this->showGenericHeader($rowCtr, $sheet1);
        
        $rowCtr += 2;
        
        $startSum = $rowCtr; //Start getting row sums
        
        $aging = array();
        
        $aging["< 30"] = 0;
        $aging["31-60"] = 0;
        $aging["61-90"] = 0;     
        $aging["91-120"] = 0;
        $aging["0ver 120"] = 0;
        
        while($row = $policies_of_client->FetchRow()){
            
            $due = $row["premium"]-$row["payment"]; //due amount
            
            //if($due > 0): uncomment to not include paid amounts
            
            $sheet1->SetCellValue('A'.$rowCtr, $row["policynum"]);
            $sheet1->SetCellValue('B'.$rowCtr, $row["riskinsured"]);
            $sheet1->SetCellValue('C'.$rowCtr, $row["type"]);
            $sheet1->SetCellValue('D'.$rowCtr, $row["inceptiondate"]." - ".$row["expirydate"]);
            
            
            $sheet1->SetCellValue('E'.$rowCtr, $due);
    
            
            $days_passed = $this->getAgingInDays($row["instype"], $row["dateofbooking"], $row["payment"], $row["premium"]);
            
            if($days_passed < 30){
                $aging["< 30"] += $due;
            }else if($days_passed < 60){
                $aging["31-60"] += $due;
            }else if($days_passed < 90){
                $aging["61-90"] += $due;
            }else if($days_passed < 120){
                $aging["91-120"] += $due;
            }else{
                $aging["0ver 120"] += $due;
            }
            
            $rowCtr += 1;
            
            //endif;
            
        }
        
        $sheet1->SetCellValue('E'.$rowCtr, '=SUM(E'.$startSum.':E'.($rowCtr-1).')');
        $sheet1->getStyle('E'.$rowCtr)->getFont()->setBold(TRUE);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCtr)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);        
        
        $rowCtr +=2;
        
        $sheet1->SetCellValue('A'.$rowCtr, "Important: Please be reminded that non-payment of premium may prejudice a claim under your insurance policy.");
        $sheet1->mergeCells('A'.$rowCtr.':E'.$rowCtr);
		$sheet1->getStyle('A'.$rowCtr)->getFont()->setBold(TRUE); 
        
        $rowCtr +=2;
        
        $this->showAgingHeader($rowCtr, $sheet1);
        
        $rowCtr += 1;
        
        $sheet1->SetCellValue('A'.$rowCtr, $aging["< 30"]);
		$sheet1->SetCellValue('B'.$rowCtr, $aging["31-60"]);
		$sheet1->SetCellValue('C'.$rowCtr, $aging["61-90"]);
		$sheet1->SetCellValue('D'.$rowCtr, $aging["91-120"]);
		$sheet1->SetCellValue('E'.$rowCtr, $aging["0ver 120"]);
        $sheet1->getStyle('A'.$rowCtr.':E'.$rowCtr)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $rowCtr += 2;
        
        $sheet1->SetCellValue('A'.$rowCtr, "Please make a check payable to PENTA INSURANCE BROKER SERVICES, INC.");
        $sheet1->mergeCells('A'.$rowCtr.':E'.$rowCtr);
		$sheet1->getStyle('A'.$rowCtr)->getFont()->setBold(TRUE); 
        
        
        $rowCtr +=2;
        
        $sheet1->SetCellValue('A'.$rowCtr, "Thank you,");
        $sheet1->mergeCells('A'.$rowCtr.':E'.$rowCtr);
		$sheet1->getStyle('A'.$rowCtr)->getFont()->setBold(TRUE); 
        
        
        
        $rowCtr +=3;
        
        $sheet1->SetCellValue('A'.$rowCtr, "Reynaldo E. Basuel");
        $sheet1->mergeCells('A'.$rowCtr.':E'.$rowCtr);
        $sheet1->getStyle('A'.$rowCtr)->getFont()->setSize(13); 
		$sheet1->getStyle('A'.$rowCtr)->getFont()->setBold(TRUE); 
        
        $rowCtr +=1;
        
        $sheet1->SetCellValue('A'.$rowCtr, "GM & Managing Director");
        $sheet1->mergeCells('A'.$rowCtr.':E'.$rowCtr);
        $sheet1->getStyle('A'.$rowCtr)->getFont()->setSize(11); 
		$sheet1->getStyle('A'.$rowCtr)->getFont()->setBold(TRUE); 
        
        
        
        
        //Formatting
        
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10); 

        //$sheet1->getPageSetup()->setPrintArea('A1:E'.$rowCtr);
        
        //$objPHPExcel->getActiveSheet()->setBreak( 'F5' , PHPExcel_Worksheet::BREAK_COLUMN );

        
        $sheet1->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $sheet1->getStyle('A1')->getFont()->setSize(20); 
		$sheet1->getStyle('A1')->getFont()->setBold(TRUE); 
        $sheet1->getStyle('A1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE); 
        
		$sheet1->getStyle('A5')->getFont()->setSize(13); 
		$sheet1->getStyle('A5')->getFont()->setBold(TRUE); 
        
        foreach(range('A','Z') as $i){
			$sheet1->getColumnDimension($i)->setAutoSize(true); // Format the column to autosize
		}

		
		$this->report->end(); // Always call at the end of report

	}
    
    private function showGenericHeader($row, $sheet){
		$sheet->SetCellValue('A'.$row, 'Policy No.');
		$sheet->SetCellValue('B'.$row, 'Propety Insured');
		$sheet->SetCellValue('C'.$row, 'Line of Insurance');
		$sheet->SetCellValue('D'.$row, 'Period of Insurance');
		$sheet->SetCellValue('E'.$row, 'Premium including taxes');
		
		//$sheet->getStyle('A'.$row.':E'.$row)->getFont()->getColor()->setRGB('FFFFFF');
		//$sheet->getStyle('A'.$row.':E'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		//$sheet->getStyle('A'.$row.':E'.$row)->getFill()->getStartColor()->setRGB('666666');
		
		$sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
    
    private function showAgingHeader($row, $sheet){
		$sheet->SetCellValue('A'.$row, 'Current');
		$sheet->SetCellValue('B'.$row, '31-60 Days');
		$sheet->SetCellValue('C'.$row, '61-90 Days');
		$sheet->SetCellValue('D'.$row, '91-120 Days');
		$sheet->SetCellValue('E'.$row, 'Over 120 days');
		
		//$sheet->getStyle('A'.$row.':E'.$row)->getFont()->getColor()->setRGB('FFFFFF');
		//$sheet->getStyle('A'.$row.':E'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		//$sheet->getStyle('A'.$row.':E'.$row)->getFill()->getStartColor()->setRGB('666666');
		
		$sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
    
    function getAgingInDays($maxdays, $bookingdate, $payment, $due){
    
    if($payment >= $due){
      return "<span class='affirmative'> Fully Paid </span>";
    }

    $currentDate = mktime(0,0,0);     // for the second date we are going to use the current Unix system time
                                // be advised that this is not necessarily the date of your computer but the date of the Unix server on which the .php file resides

     
    $previousDate = strtotime($bookingdate); // we will now create the timestamp for this date
     
    $nrSeconds = $currentDate - $previousDate; // subtract the previousDate from the currentDate to see how many seconds have passed between these two dates
     
    $nrSeconds = abs($nrSeconds); // in some cases, because of a user input error, the second date which should be smaller then the current one
                                // will give a negative number of seconds. So we use abs() to get the absolute value of nrSeconds
     
    $nrDaysPassed = floor($nrSeconds / 86400); // see explanations below to see what this does
    $nrWeeksPassed = floor($nrSeconds / 604800); // same as above
    $nrYearsPassed = floor($nrSeconds / 31536000); // same as above

    return $nrDaysPassed;
    
}
	
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		$view['details'] = $this->report->getDetails(); //load logs
		return $view;
	}

}

?>