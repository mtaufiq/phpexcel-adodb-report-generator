<?php include '../system/dbControl.class.php' ?>

<?php

class CashReceiptSummaryModel{
	

	public function get_summary($year,$month){
		$db = new dbControl();
		
		$query = "Select account.account_type, account.account_desc , SUM(official_receipt_entries.debit) as debit , 
						SUM(official_receipt_entries.credit) as credit
				  From account, official_receipt_entries, official_receipt_reference	
				  Where account.id = official_receipt_entries.account_id
						AND official_receipt_entries.official_receipt_reference_id = official_receipt_reference.id
						AND MONTH(official_receipt_reference.trans_date) = '".$month."'
						AND YEAR(official_receipt_reference.trans_date) = '".$year."'
				  GROUP BY account.account_desc";
		
		return $db->query($query);

	}
}

?>