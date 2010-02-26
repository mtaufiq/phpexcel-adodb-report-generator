<?php

class IncomeStatementModel{

	public function show_balances($year,$month){
		$db = new dbControl();
		
		$query = "SELECT
					account.account_desc AS 'Account',
					account.account_type AS 'Account Type',
					SUM(general_ledger.crb_debit + general_ledger.cdb_debit + general_ledger.jv_debit) AS 'Debit',
					SUM(general_ledger.crb_credit + general_ledger.cdb_credit + general_ledger.jv_credit) AS 'Credit'
				  FROM
					account, general_ledger
				  WHERE
					general_ledger.account_id = account.id
					AND month <= ".$month."
					AND year <= ".$year."
					AND account.account_type IN ('Expense','Income')
				  GROUP BY
					account.id";
		
		return $db->query($query);

	}
}

?>