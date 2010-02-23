<?php require '../system/dbControl.class.php' ?>
<?php

class CashReceiptSummaryModel{

	public function get_summary($year,$month){
		$db = new dbControl();
		
		$query = "Select account.account_type, account.account_desc , SUM(check_voucher_entries.debit) as debit , 
						SUM(check_voucher_entries.credit) as credit
				  From account, check_voucher_entries, check_voucher_reference	
				  Where account.id = check_voucher_entries.account_id
						AND check_voucher_entries.check_voucher_reference_id = check_voucher_reference.id
						AND MONTH(check_voucher_reference.trans_date) = '".$month."'
						AND YEAR(check_voucher_reference.trans_date) = '".$year."'
				  GROUP BY account.account_desc";
		
		return $db->query($query);

	}
}

?>