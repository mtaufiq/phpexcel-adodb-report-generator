<?php

class CashDisbursementSummaryModel{

	public function get_summary($year,$month){
		$db = new dbControl();
		
		$query = "Select acct_account.account_type, acct_account.account_desc , SUM(acct_check_voucher_entries.debit) as debit , 
						SUM(acct_check_voucher_entries.credit) as credit
				  From acct_account, acct_check_voucher_entries , acct_check_voucher_reference	
				  Where acct_account.id = acct_check_voucher_entries.acct_account_id
						AND acct_check_voucher_entries.acct_check_voucher_reference_id = acct_check_voucher_reference.id
						AND MONTH(acct_check_voucher_reference.trans_date) = '".$month."'
						AND YEAR(acct_check_voucher_reference.trans_date) = '".$year."'
				  GROUP BY acct_account.account_desc";
		
		return $db->query($query);

	}
}

?>