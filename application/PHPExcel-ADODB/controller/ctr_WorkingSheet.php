<?php
class WorkingSheet{ //Class name of report - must be same as the rep parameter
	
	var $TBAcctMap = array();
    
    /*
    TBACCTMAP
    [0] - account name
    [1] - debit - credit
    [2] - previous value
    [3] - last month only value (from current - previous value)
    
    */
    
    var $OpExpenseMap = array();
    var $cell_styles = array();
    var $custom_totals = array();
	
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
		
		$monthName = date("F", mktime(0, 0, 0, $params['month'], 10)); 
		$prevMonthName = date("F", mktime(0, 0, 0, $params['month']-1, 10)); 
		
        $this->load_styles();
		$this->createHeaderFormat($monthName, $prevMonthName, $sheet1, $params);
		$this->createTrialBalance($monthName, $prevMonthName, $sheet1, $params);
		$this->createOperatingExpenses($sheet1, $params);
        $this->createIncomeStatement($sheet1, $params);
        $this->createBalanceSheet($sheet1, $params);
		
		$this->report->end(); // Always call at the end of report

	}
    
    private function load_styles(){
        
        $class["header"] = array(
            'font' => array(
                      'bold' => true
            )
        );

        $this->cell_styles = $class;
        
    }
	
	
	private function createHeaderFormat($month, $prevMonth, $sheet, $params){
		
		$sheet1 = $sheet;
		$monthName = $month;
		$prevMonthName = $prevMonth;
		
		$sheet1->SetCellValue('A1', 'Penta Insurance Broker Services Inc.');
		$sheet1->SetCellValue('A2', 'Trial Balance');
		$sheet1->SetCellValue('A3', 'As of '.$monthName.' ,'.$params['day'].' '.$params['year']);
		$sheet1->SetCellValue('C6', 'DR');
		$sheet1->SetCellValue('D6', 'CR');
		
		$sheet1->SetCellValue('F1', '=A1');
		$sheet1->SetCellValue('F2', 'Operating Expenses');
		$sheet1->SetCellValue('F3', '=A3');
		
		$sheet1->SetCellValue('G5', 'This Month');
		$sheet1->SetCellValue('H5', 'Last Month');
		$sheet1->SetCellValue('I5', 'Todate');
		$sheet1->SetCellValue('J5', 'Previous');
		$sheet1->SetCellValue('K5', 'ToDate');
		
		
		$sheet1->SetCellValue('M1', '=A1');
		$sheet1->SetCellValue('M2', 'Profit and Loss Statement');
		$sheet1->SetCellValue('M3', '=A3');
		
		$sheet1->SetCellValue('N5', 'This Month');
		$sheet1->SetCellValue('O5', 'Last Month');
		$sheet1->SetCellValue('P5', 'Todate');
		$sheet1->SetCellValue('Q5', 'Previous');
		$sheet1->SetCellValue('R5', 'ToDate');
		
		
		$sheet1->SetCellValue('T1', '=A1');
		$sheet1->SetCellValue('T2', 'Balance Sheet Working Paper');
		$sheet1->SetCellValue('T3', '=A3');
		
		
		$sheet1->SetCellValue('AB1', '=A1');
		$sheet1->SetCellValue('AB2', 'Balance Sheet');
		$sheet1->SetCellValue('AB3', '=A3');
		
		$sheet1->SetCellValue('AC5', $monthName);
		$sheet1->SetCellValue('AD5', $prevMonthName);
		$sheet1->SetCellValue('AE5', 'As of Date');
		
        $sheet1->getColumnDimension('T')->setVisible(false);
        $sheet1->getColumnDimension('U')->setVisible(false);
        $sheet1->getColumnDimension('V')->setVisible(false);
        $sheet1->getColumnDimension('W')->setVisible(false);
        $sheet1->getColumnDimension('X')->setVisible(false);
        $sheet1->getColumnDimension('Y')->setVisible(false);
		

		//Formatting
		foreach(range('A','Z') as $i){
			$sheet1->getColumnDimension($i)->setAutoSize(true); // Format the column to autosize
		}
		
		foreach(range('A','Z') as $i){
			$sheet1->getColumnDimension('A'.$i)->setAutoSize(true); // Format the column to autosize
		}
		
		$sheet1->getStyle('A1:A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet1->getStyle('F1:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet1->getStyle('M1:M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet1->getStyle('T1:T3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet1->getStyle('AB1:AB3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		
	}
    
    private function createBalanceSheet($sheet, $params){
    
        $assets["DEPOSIT"] = array("Deposit in Banks", array("10001", "10002"),array(),""); 
        $assets["INVESTMENT"] = array("Short Term Investment", array("10003", "10004"),array(),""); 
        $assets["COMMISSION"] = array("Commission Receivable", array("10005"),array(),""); 
        $assets["ACCT_RECEIVABLE"] = array("Accounts Receivable", array("10007"),array(),""); 
        $assets["ACCR_INTEREST"] = array("Accrued Interest Receivable", array("10008"),array(),""); 
        $assets["LOANS"] = array("Loans Receivable", array("10009"),array(),""); 
        $assets["FUR_FIX_EQUIP"] = array("Furniture, Fixtures & Equipt.-net", array("10010","10012","10014","10016"), array("10011","10013","10015","10017")); 
        $assets["OTHER_INCOME"] = array("Other Income", array("10019", "10020", "10021", "10022", "10023", "10024", "10025", "10026", "10027"));
       
        $liabilities["DUE_INSURANCE"] = array("Due to Insurance Company", array("20001"),array(),"");
        $liabilities["DUE_AFFILIATES"] = array("Due To Affiliates", array("20002"),array(),"");
        $liabilities["ACCT_PAYABLE"] = array("Accounts Payable", array("20009"),array(),"");
        $liabilities["ACCR_INCTAX_PAYABLE"] = array("Accounts Payable", array("20006"),array(),"");
        $liabilities["ACCR_EXPENSES"] = array("Accrued Expenses", array("20005"),array(),"");
        $liabilities["OTHER_LIABILITIES"] = array("Other Liabilities", array("20003"), array("20010", "20011", "20012"),"");
        
        $equity["CAPITAL"] = array("Capital Stock", array("30001"),array(),"");
        $equity["RETAINED"] = array("Retained Earnings", array("30003"),array(),"");
        $equity["NET_INCOME"] = array("Net Income", array(), array(), $this->TBAcctMap["SUMINCOME"]."-(".$this->TBAcctMap["SUMEXPENSE"].")");
        
        
        $balance_sheet_info = array("Assets" => $assets, 'Liabilities' => $liabilities, 'Stockholders Equity' => $equity);
        
        //column AB , row 6
        
        $curRow = 6;
		$titleCol = 'AB';
		$thisMonthCol = 'AC';
		$lastMonthCol = 'AD';
        $todateCol = 'AE';
        
        foreach($balance_sheet_info as $title => $info):
            $sheet->SetCellValue($titleCol.$curRow,$title);
            $sheet->getStyle($titleCol.$curRow)->applyFromArray($this->cell_styles["header"]);
            $curRow += 1;
                foreach($info as $account_info):
                    
                    $sheet->SetCellValue($titleCol.$curRow,$account_info[0]);
                    $cur_sum_str = "";
                    $past_sum_str = "";
                    
                    if(!empty($account_info[1])){
                        
                        $cur_sum_str .= "=SUM(";
                        $past_sum_str .= "=SUM(";
                        
                        foreach($account_info[1] as $account):
                            
                            $cur_sum_str .= $this->TBAcctMap[$account][1].",";
                            $past_sum_str .= $this->TBAcctMap[$account][2].","; 
     
                        endforeach;
                        
                        if(count($account_info[1]) <= 0){
                            $cur_sum_str .= "0";
                            $past_sum_str .= "0";
                        }
                        
                        $cur_sum_str .= ")";
                        $past_sum_str .= ")";
                        
                    }
                    
                    if(!empty($account_info[2])){
                        
                        $cur_sum_str .= "-(";
                        $past_sum_str .= "-(";
                        
                        foreach($account_info[2] as $account):
                            
                            $cur_sum_str .= $this->TBAcctMap[$account][1]."-";
                            $past_sum_str .= $this->TBAcctMap[$account][2]."-"; 
     
                        endforeach;
                        
                        $cur_sum_str .= "(-0))";
                        $past_sum_str .= "(-0))";
                        
                    }
                    
                    if(!empty($account_info[3])){
                        
                        $cur_sum_str .= "=".$account_info[3];
                        $past_sum_str .= "=".$account_info[3];
                        
                    }
                    
                    $sheet->SetCellValue($todateCol.$curRow,$cur_sum_str);
                    $sheet->SetCellValue($thisMonthCol.$curRow, "=".$todateCol.$curRow);
                    $sheet->SetCellValue($lastMonthCol.$curRow, $past_sum_str);
                    
                    $curRow += 1;
                    
                endforeach;
                
            $curRow+=1;
        endforeach;
        
        
   }
    
    private function _working_incomestatement($sheet, $params){ //for gross income
        //column M, row 50
        
        $curRow = 50;
        $titleCol = 'M';
        $valueCol = 'N';
        $totalCol = 'O';
        
        $income["GROSS"] = array("Gross Income", array( "40001" ));
        $income["INTEREST"] = array("Interest", array( "40002", "40003", "40004", "40005" ));
        //$income["FOREIGN"] = array("Foreign Exchange Profit/Loss", array(""));
        
        $income["OTHER_INCOME"] = array("Other Income", array("40006"));
        
        $subtotals = array();
        
        foreach($income as $key => $content_array):
			$sheet->setCellValue($titleCol.$curRow, $content_array[0]);
            $sheet->getStyle($titleCol.$curRow)->applyFromArray($this->cell_styles["header"]);
            
            
            if($key == "GROSS" || $key == "INEREST"){ $curRow += 1; }
			$start = $curRow;
            
                foreach($content_array[1] as $content):

                    $sheet->setCellValue($titleCol.$curRow, $this->TBAcctMap[$content][0]);
                    $sheet->setCellValue($valueCol.$curRow, "=".$this->TBAcctMap[$content][1]);
                    
                    $curRow += 1;
                    
                endforeach;
                
                $curRow -= 1;
                           
                $end = $curRow;
                
                $subtotals['sub'.$curRow] = $curRow;
                
                $sheet->setCellValue($totalCol.$curRow, '=SUM('.$valueCol.$start.':'.$valueCol.$end.')');
                $this->custom_totals[$key] = $totalCol.$curRow;
                $curRow += 1;
                
        endforeach;
        
        $sheet->setCellValue($totalCol.$curRow, $this->getSumString($subtotals,$totalCol));
        
        
        
    }
    
    private function createIncomeStatement($sheet, $params){
        
        $this->_working_incomestatement($sheet, $params);
        
        //column M , row 6
        
        $curRow = 6;
		$titleCol = 'M';
		$thisMonthCol = 'N';
		$lastMonthCol = 'O';
		$todateColF = 'P';
		$previous = 'Q';
		$todateCol = 'R';

        $data['INCOME'] = array("INCOME", array(
            
            "GROSS" => array("Commission", array("40001")),
            "INTEREST" => array("Interest", array( "40002", "40003", "40004", "40005" )),
            "OTHER_INCOME" => array("Others", array("40006"))
        
        ));

        $data['EXPENSES'] = array("EXPENSES", array("MANPOWER", "MARKETING", "OCCUPANCY", "GENERAL", "TAXES"));
        $data['PROVISIONS'] = array("PROVISIONS", array("50006", "50032", "50029", "50030", "50028", "50031"));
        
        
        foreach($data as $title => $content_array):
			$sheet->setCellValue($titleCol.$curRow, $title);
            $sheet->getStyle($titleCol.$curRow)->applyFromArray($this->cell_styles["header"]);
			$curRow += 1;
            
            $start = $curRow;
            
            if($title == "PROVISIONS"): //Repeat process for provisions
               
                foreach($content_array[1] as $content):
                    /*Title*/$sheet->setCellValue($titleCol.$curRow, $this->TBAcctMap[$content][0]);
                    /*1-This Month*/$sheet->setCellValue($thisMonthCol.$curRow, '='.$todateColF.$curRow.'-'.$previous.$curRow);
                    /*2-Last Month*/$sheet->setCellValue($lastMonthCol.$curRow, $this->TBAcctMap[$content][3]);
                    /*3-To Date First*/$sheet->setCellValue($todateColF.$curRow, '='.$todateCol.$curRow);
                    /*4-Previous*/$sheet->setCellValue($previous.$curRow, $this->TBAcctMap[$content][2]);
                    /*5-To Date Last*/$sheet->setCellValue($todateCol.$curRow, '=('.$this->TBAcctMap[$content][1].')');
                    $curRow += 1;
                endforeach;

                
            elseif($title == "EXPENSES"):
            
                foreach($content_array[1] as $key):
                    /*1-This Month*/$sheet->setCellValue($titleCol.$curRow, $this->OpExpenseMap[$key]["Title"]);
                    /*1-This Month*/$sheet->setCellValue($thisMonthCol.$curRow, "=".$this->OpExpenseMap[$key]["This Month"]);
                    /*2-Last Month*/$sheet->setCellValue($lastMonthCol.$curRow, "=".$this->OpExpenseMap[$key]["Last Month"]);
                    /*3-To Date First*/$sheet->setCellValue($todateColF.$curRow, "=".$this->OpExpenseMap[$key]["To Date"]);
                    /*4-Previous*/$sheet->setCellValue($previous.$curRow, "=".$this->OpExpenseMap[$key]["Previous"]);
                    /*5-To Date Last*/$sheet->setCellValue($todateCol.$curRow, "=".$this->OpExpenseMap[$key]["To Date 2"]);
                    $curRow += 1;
                endforeach;
                
            
            else:

                foreach($content_array[1] as $key => $items):
                
                    $last_month = 0;
                    $previous_amt = 0;
                
                    foreach($items[1] as $item):
                    
                        $last_month += $this->TBAcctMap[$item][3];
                        $previous_amt += $this->TBAcctMap[$item][2];
                    
                    endforeach;
                    
                    /*1-This Month*/$sheet->setCellValue($titleCol.$curRow, $items[0]);
                    /*1-This Month*/$sheet->setCellValue($thisMonthCol.$curRow, "=".$todateColF.$curRow."-".$previous.$curRow);
                    /*2-Last Month*/$sheet->setCellValue($lastMonthCol.$curRow, $last_month);
                    /*3-To Date First*/$sheet->setCellValue($todateColF.$curRow, "=".$todateCol.$curRow);
                    /*4-Previous*/$sheet->setCellValue($previous.$curRow, $previous_amt);
                    /*5-To Date Last*/$sheet->setCellValue($todateCol.$curRow,  "=".$this->custom_totals[$key]);
                    $curRow += 1;
                    
                endforeach;
            
            endif;
			
                            
            $end = $curRow - 1;
                
            /*Title*/$sheet->setCellValue($titleCol.$curRow, "Total");
                     $sheet->getStyle($titleCol.$curRow)->applyFromArray($this->cell_styles["header"]);
            /*1-This Month*/$sheet->setCellValue($thisMonthCol.$curRow, '=SUM('.$thisMonthCol.$start.':'.$thisMonthCol.$end.')');
            /*2-Last Month*/$sheet->setCellValue($lastMonthCol.$curRow, '=SUM('.$lastMonthCol.$start.':'.$lastMonthCol.$end.')');
            /*3-To Date First*/$sheet->setCellValue($todateColF.$curRow, '=SUM('.$todateColF.$start.':'.$todateColF.$end.')');
            /*4-Previous*/$sheet->setCellValue($previous.$curRow, '=SUM('.$previous.$start.':'.$previous.$end.')');
            /*5-To Date Last*/$sheet->setCellValue($todateCol.$curRow, '=SUM('.$todateCol.$start.':'.$todateCol.$end.')');
            
            $curRow += 1;
            
        endforeach;
        
    }
	
	private function createTrialBalance($month, $prevMonth, $sheet, $params){
		
		$worksheet = $sheet;
		$DB = new WorkingSheetModel();
		$result = $DB->show_balances($params['year'], $params['month']);
		
		$ctr = 8; // Starting row
		$balStart = $ctr; // Used for getting the totals cells
		$acct_type = ""; // Initial account type
		$acct_col = "A";
		$debit_col = "C";
		$credit_col = "D";
		
		$totalsC = array();
		$totalsD = array();
		
		while($row = $result->FetchRow()){

				//if($row['Debit'] > 0 || $row['Credit'] > 0){//Show to display rows with values
					
					if($acct_type <> $row['Account Type']){
					
						if($acct_type <> "" /*Processing just started)*/){
						
							$totStart = $balStart + 1;
							$totEnd = $ctr - 1;
							
                            $worksheet->getStyle($acct_col.$ctr)->applyFromArray($this->cell_styles["header"]);
                    
							$worksheet->SetCellValue($acct_col.$ctr, "Total ".$acct_type);
							
							$getSumOfC = 'SUM('.$debit_col.$totStart.':'.$debit_col.$totEnd.')';
							$getSumOfD = 'SUM('.$credit_col.$totStart.':'.$credit_col.$totEnd.')';
							$worksheet->SetCellValue($debit_col.$ctr, '='.$getSumOfC);	
							$worksheet->SetCellValue($credit_col.$ctr, '='.$getSumOfD);
							
							$totalsC[] = $debit_col.$ctr;
							$totalsD[] = $credit_col.$ctr;
                            
                            $this->TBAcctMap["SUM".strtoupper ($acct_type)] = $debit_col.$ctr."-".$credit_col.$ctr;
                            
							$ctr++;
							
						}
					
						$acct_type = $row['Account Type'];
						$worksheet->SetCellValue($acct_col.$ctr, $acct_type);
                        $worksheet->getStyle($acct_col.$ctr)->applyFromArray($this->cell_styles["header"]);
						$balStart = $ctr;
						
						$ctr++;
					}
		
					$balance = $row['Debit'] - $row['Credit'];
					$worksheet->SetCellValue($acct_col.$ctr, $row['Account']);
					$worksheet->SetCellValue($debit_col.$ctr, $row['Debit']);
					$worksheet->SetCellValue($credit_col.$ctr, $row['Credit']);
					
					$this->TBAcctMap[$row['Account Num']][0] = $row['Account'];
					$this->TBAcctMap[$row['Account Num']][1] = $debit_col.$ctr.'-'.$credit_col.$ctr;

					//$col = $this->checkAccountType($row['Account Type']);
					//$col = $this->checkNegativeBalance($balance,$col,$row['Account Type']);
					//$worksheet->SetCellValue($col.$ctr, abs($balance));
					$ctr++;
					
				/*}*///Show to display rows with values
				if($ctr == 45){//Page Break every 50 rows
					$worksheet->setBreak( $acct_col . $ctr, PHPExcel_Worksheet::BREAK_ROW );
				}
		
		}
		
		
		$totStart = $balStart + 1;
		$totEnd = $ctr - 1;
		
		$worksheet->getStyle($acct_col.$ctr)->getFont()->setBold(true);
		$worksheet->SetCellValue($acct_col.$ctr, "Total ".$acct_type);
        $this->TBAcctMap["SUM".strtoupper ($acct_type)] = $debit_col.$ctr."-".$credit_col.$ctr;
		
		$getSumOfC = 'SUM('.$debit_col.$totStart.':'.$debit_col.$totEnd.')';
		$getSumOfD = 'SUM('.$credit_col.$totStart.':'.$credit_col.$totEnd.')';
		$worksheet->SetCellValue($debit_col.$ctr, '='.$getSumOfC);	
		$worksheet->SetCellValue($credit_col.$ctr, '='.$getSumOfD);
		
		$totalsC[] = $debit_col.$ctr;
		$totalsD[] = $credit_col.$ctr;		
		$ctr++;
		
		
		
		
		$worksheet->SetCellValue($acct_col.$ctr,'Total');
	
		$sumFormulaC = "=";
		foreach($totalsC as $index):
				$sumFormulaC .= $index;
				if(end($totalsC) <> $index){
					$sumFormulaC .= "+";
				}
		endforeach;
		
		$sumFormulaD = "=";
		foreach($totalsD as $index):
				$sumFormulaD .= $index;
				if(end($totalsD) <> $index){
					$sumFormulaD .= "+";
				}
		endforeach;
		
		
		$worksheet->SetCellValue($debit_col.$ctr,$sumFormulaC);
		$worksheet->SetCellValue($credit_col.$ctr,$sumFormulaD);
		
		
		$worksheet->getColumnDimension($acct_col)->setAutoSize(true);

	}

	private function createOperatingExpenses($sheet, $params){
		
		$this->getOtherAmounts($params['year'], $params['month']);
		
		$expenses['MANPOWER'] = array("Manpower Expenses",array("50002", "50003", "50006", "50005"));
		$expenses['MARKETING'] = array("Marketing Expenses",array("50012", "50013", "50014"));
		$expenses['OCCUPANCY'] = array("Occupancy Expenses",array("50008","50018", "50009", "50010", "50015" ));
		$expenses['GENERAL'] = array("General & Administrative",array("50020", "50021", "50022", "50023", "50016", "50024", "50025", "50026"));
		$expenses['TAXES'] = array("Taxes & Licenses",array("50007" ));
		
		$adjustments['PROVISIONS'] = array("Provisions", array("50006", "50032", "50029", "50030", "50028", "50031"));
		
		$subtotals = array();
		
		$curRow = 7;
		$titleCol = 'F';
		$thisMonthCol = 'G';
		$lastMonthCol = 'H';
		$todateColF = 'I';
		$previous = 'J';
		$todateCol = 'K';


		foreach($expenses as $key => $expenseArr):
			$sheet->setCellValue($titleCol.$curRow, $expenseArr[0]);
            $sheet->getStyle($titleCol.$curRow)->applyFromArray($this->cell_styles["header"]);
                    
			$curRow += 1;
			foreach($expenseArr[1] as $expense){
				
				/*Title*/$sheet->setCellValue($titleCol.$curRow, $this->TBAcctMap[$expense][0]);
				/*1-This Month*/$sheet->setCellValue($thisMonthCol.$curRow, '='.$todateColF.$curRow.'-'.$previous.$curRow);
				/*2-Last Month*/$sheet->setCellValue($lastMonthCol.$curRow, $this->TBAcctMap[$expense][3]);
				/*3-To Date First*/$sheet->setCellValue($todateColF.$curRow, '='.$todateCol.$curRow);
				/*4-Previous*/$sheet->setCellValue($previous.$curRow, $this->TBAcctMap[$expense][2]);
				/*5-To Date Last*/$sheet->setCellValue($todateCol.$curRow, '=('.$this->TBAcctMap[$expense][1].')');

				$curRow += 1;
			}
			$start = $curRow - count($expenseArr[1]) - 1;
			$end = $curRow - 1;
			$subtotals['sub'.$curRow] = $curRow;
			$sheet->setCellValue($thisMonthCol.$curRow , '=SUM('.$thisMonthCol.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($lastMonthCol.$curRow , '=SUM('.$lastMonthCol.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($todateColF.$curRow , '=SUM('.$todateColF.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($previous.$curRow , '=SUM('.$previous.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($todateCol.$curRow , '=SUM('.$todateCol.$start.':'.$thisMonthCol.$end.')');
			
            $this->OpExpenseMap[$key]["Title"] = $expenseArr[0];
            $this->OpExpenseMap[$key]["This Month"] = $thisMonthCol.$curRow;
            $this->OpExpenseMap[$key]["Last Month"] = $lastMonthCol.$curRow;
            $this->OpExpenseMap[$key]["To Date"] = $todateColF.$curRow;
            $this->OpExpenseMap[$key]["Previous"] = $previous.$curRow;
            $this->OpExpenseMap[$key]["To Date 2"] = $todateCol.$curRow;
            
			$curRow += 1;
			
		endforeach;
		
		$sheet->setCellValue($titleCol.$curRow, "Total Operating Expenses");
			$sheet->setCellValue($thisMonthCol.$curRow , $this->getSumString($subtotals, $thisMonthCol));
			$sheet->setCellValue($lastMonthCol.$curRow , $this->getSumString($subtotals, $lastMonthCol));
			$sheet->setCellValue($todateColF.$curRow , $this->getSumString($subtotals, $todateColF));
			$sheet->setCellValue($previous.$curRow , $this->getSumString($subtotals, $previous));
			$sheet->setCellValue($todateCol.$curRow , $this->getSumString($subtotals, $todateCol));
		
		$grandTotal[] = $curRow;
		
		$curRow += 2;
		
		foreach($adjustments as $adjArr):
			$sheet->setCellValue($titleCol.$curRow, $adjArr[0]);
			$curRow += 1;
			foreach($adjArr[1] as $adj){
				
				/*Title*/$sheet->setCellValue($titleCol.$curRow, $this->TBAcctMap[$adj][0]);
				/*1-This Month*/$sheet->setCellValue($thisMonthCol.$curRow, '='.$todateColF.$curRow.'-'.$previous.$curRow);
				/*2-Last Month*/$sheet->setCellValue($lastMonthCol.$curRow, $this->TBAcctMap[$adj][3]);
				/*3-To Date First*/$sheet->setCellValue($todateColF.$curRow, '='.$todateCol.$curRow);
				/*4-Previous*/$sheet->setCellValue($previous.$curRow, $this->TBAcctMap[$adj][2]);
				/*5-To Date Last*/$sheet->setCellValue($todateCol.$curRow, '=('.$this->TBAcctMap[$adj][1].')');

				$curRow += 1;
			}
			$start = $curRow - count($adjArr[1]) - 1;
			$end = $curRow - 1;
			$grandTotal['sub'.$curRow] = $curRow;
			$sheet->setCellValue($titleCol.$curRow, "TOTAL PROVISIONS");
			$sheet->setCellValue($thisMonthCol.$curRow , '=SUM('.$thisMonthCol.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($lastMonthCol.$curRow , '=SUM('.$lastMonthCol.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($todateColF.$curRow , '=SUM('.$todateColF.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($previous.$curRow , '=SUM('.$previous.$start.':'.$thisMonthCol.$end.')');
			$sheet->setCellValue($todateCol.$curRow , '=SUM('.$todateCol.$start.':'.$thisMonthCol.$end.')');
			
			$curRow += 2;
			
			$sheet->setCellValue($titleCol.$curRow, "TOTAL EXPENSES");
			$sheet->setCellValue($thisMonthCol.$curRow , $this->getSumString($grandTotal, $thisMonthCol));
			$sheet->setCellValue($lastMonthCol.$curRow , $this->getSumString($grandTotal, $lastMonthCol));
			$sheet->setCellValue($todateColF.$curRow , $this->getSumString($grandTotal, $todateColF));
			$sheet->setCellValue($previous.$curRow , $this->getSumString($grandTotal, $previous));
			$sheet->setCellValue($todateCol.$curRow , $this->getSumString($grandTotal, $todateCol));
			
		endforeach;

		
		
		
		
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

	private function getOtherAmounts($yr, $mth){
		$DB = new WorkingSheetModel();
		
		$result = $DB->get_accounts();
		while($row = $result->FetchRow()){
			$this->TBAcctMap[$row['account_num']][2] = 0;
			$this->TBAcctMap[$row['account_num']][3] = 0;
		}
		
		/*Last month balance*/
		$prev = $this->getPreviousDate($yr, $mth);
		$result = $DB->show_balances($prev['yr'], $prev['mth']);
		
		while($row = $result->FetchRow()){
			$this->TBAcctMap[$row['Account Num']][2] += $row['Debit'] - $row['Credit'];
		}
		
		$result = $DB->show_balances_monthly($prev['yr'], $prev['mth']);
		
		while($row = $result->FetchRow()){
			$this->TBAcctMap[$row['Account Num']][3] += $row['Debit'] - $row['Credit'];
		}
		
	}
	
	protected function getPreviousDate($yr,$mth){
	if($mth == 1){
		$mth = 12;
		$yr -= 1;
	}else{
		$mth -= 1;
	}
	$prev['yr'] = $yr;
	$prev['mth'] = $mth;
	return $prev;
	}
  
		
	/*PLS DONT EDIT THIS CODE*/
	public function view(){
		$view['html'] = $this->report->view(); //load html components
		$view['logs'] = $this->report->getLogs(); //load logs
		$view['details'] = $this->report->getDetails(); //load logs
		return $view;
	}

}

?>