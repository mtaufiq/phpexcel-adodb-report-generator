<?php

class CollectionReportModel{ //name of class must be <class name>Model


//Add ere functions that will query the db

	public function get_entries($accounts, $yr, $month){
		$db = new dbControl(); // create db controller
		
		$query = "SELECT acct_official_receipt_reference.trans_date, 
				acct_official_receipt_reference.or_num, mktg_client.clientlname, 
				mktg_client.clientfname, acct_official_receipt_reference.particulars, 
				mktg_policy.policynum, (acct_official_receipt_entries.debit + 
				acct_official_receipt_entries.credit) as 'amount', acct_account.account_desc, 
				acct_account.account_num 

				FROM  mktg_client, acct_official_receipt_entries, 
				acct_account, acct_official_receipt_reference left join mktg_policy on acct_official_receipt_reference.mktg_policy_id = mktg_policy.id

				WHERE acct_official_receipt_entries.acct_account_id = acct_account.id 
				AND acct_account.account_num IN ( ".$accounts." ) 
				AND acct_official_receipt_entries.acct_official_receipt_reference_id = acct_official_receipt_reference.id 
				AND MONTH(acct_official_receipt_reference.trans_date) = ".$month." 
				AND YEAR(acct_official_receipt_reference.trans_date) = ".$yr."
				AND acct_official_receipt_reference.mktg_client_id = mktg_client.id 
				ORDER BY acct_account.account_desc
				";
		
		//echo $query;
		
		return $db->query($query);//query it then return the result set

	}
}

?>