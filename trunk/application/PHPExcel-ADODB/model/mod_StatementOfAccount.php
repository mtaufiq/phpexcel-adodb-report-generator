<?php

class StatementOfAccountModel{ //name of class must be <class name>Model


//Add ere functions that will query the db

	public function get_client($id){
		$db = new dbControl(); // create db controller
		
		$query = "
                
                Select * from mktg_client
                
                Where mktg_client.id = '".$id."'
                
                Limit 1
 
                ";
		
		return $db->query($query);//query it then return the result set

	}
    
    public function get_policies($client){
        
        $db = new dbControl();
        
        $query = "
        
         SELECT

            mktg_policy.id, 
            mktg_policy.policynum,
            mktg_policy.riskinsured,
            mktg_typeofcoverage.type,    
            mktg_policy.inceptiondate, 
            mktg_policy.expirydate, 
            mktg_policy.premium, 
            sum(acct_official_receipt_entries.credit+acct_official_receipt_entries.debit) as 'payment', 
           
            mktg_policy.instype,
            mktg_policy.dateofbooking, 
            acct_account.account_num 

         FROM 

            mktg_policy LEFT JOIN acct_official_receipt_reference ON (mktg_policy.ID=acct_official_receipt_reference.MKTG_POLICY_ID)

            LEFT JOIN acct_official_receipt_entries ON (acct_official_receipt_reference.ID=acct_official_receipt_entries.ACCT_OFFICIAL_RECEIPT_REFERENCE_ID) 

            LEFT JOIN acct_account ON (acct_official_receipt_entries.ACCT_ACCOUNT_ID=acct_account.ID AND acct_account.ACCOUNT_NUM='10001'  ), mktg_typeofcoverage, mktg_client


         WHERE
            (acct_account.account_num is not null OR ( (acct_official_receipt_entries.credit+acct_official_receipt_entries.debit) is null AND acct_account.account_num is null ))
            AND mktg_policy.mktg_typeofcoverage_id = mktg_typeofcoverage.id
            AND mktg_policy.mktg_client_id = mktg_client.id
            AND mktg_client.id = ".$client."

         GROUP BY mktg_policy.policynum

         Order BY mktg_policy.id
        
        
        ";
        
        return $db->query($query);//query it then return the result set
    
    }
}

?>