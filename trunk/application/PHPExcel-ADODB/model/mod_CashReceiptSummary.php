<?php

class CashReceiptSummaryModel{

	public function get_summary($year,$month){
		$db = new dbControl();
		
		$query = "Select acct_account.account_type, acct_account.account_desc , SUM(acct_official_receipt_entries.debit) as debit , 
						SUM(acct_official_receipt_entries.credit) as credit
				  From acct_account, acct_official_receipt_entries, acct_official_receipt_reference	
				  Where acct_account.id = acct_official_receipt_entries.acct_account_id
						AND acct_official_receipt_entries.acct_official_receipt_reference_id = acct_official_receipt_reference.id
						AND MONTH(acct_official_receipt_reference.trans_date) = '".$month."'
						AND YEAR(acct_official_receipt_reference.trans_date) = '".$year."'
				  GROUP BY acct_account.account_desc";
		
		return $db->query($query);

	}
}

?>