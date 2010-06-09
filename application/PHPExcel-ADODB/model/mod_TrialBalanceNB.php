<?php

class TrialBalanceNBModel{

	public function show_balances($year,$month){
		$db = new dbControl();
		
		$query = "SELECT
					acct_account.account_desc AS 'Account',
					acct_account.account_type AS 'Account Type',
					SUM(acct_general_ledger.crb_debit + acct_general_ledger.cdb_debit + acct_general_ledger.jv_debit) AS 'Debit',
					SUM(acct_general_ledger.crb_credit + acct_general_ledger.cdb_credit + acct_general_ledger.jv_credit) AS 'Credit'
				  FROM
					acct_account, acct_general_ledger
				  WHERE
					acct_general_ledger.acct_account_id = acct_account.id
					AND month <= ".$month."
					AND year <= ".$year."
				  GROUP BY
					acct_account.id";
		
		return $db->query($query);

	}
}

?>