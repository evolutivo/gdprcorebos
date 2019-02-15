<?php
/*************************************************************************************************
* Copyright 2013 JPL TSolucio, S.L. -- This file is a part of vtMktDashboard
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : vtMktDashboard
*  Version      : 1.9
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

class CreateContactsBatchJob extends BatchJob {

  public function create() {
    $rsprocess = $this->adb->query("insert into batch_items (batch_job_id, crmid, crmtype) select {$this->id}, vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_contactresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_contactresults.crmid where mktdb_contactresults.selected=1 and mktdb_contactresults.userid={$this->userId}");
  }

  public function run() {
    while ($item = $this->nextItem()) {
      $contactId = NULL;
      switch ($item['crmtype']) {
      case 'Accounts':
        $contactId = $this->fromAccounts($item);
        break;
      case 'Contacts':
        $contactId = $this->fromContacts($item);
        break;
      }
      if (!is_null($contactId)) {
        $this->markItemDone(array('id' => $contactId));
      }
    }
  }

  public function fromAccounts($item) {
    $assigntocontacts = $this->get('assigntocontacts');
    $focus= new Contacts();
    $acct_focus = new Accounts();
    $acct_focus->retrieve_entity_info($item['crmid'], "Accounts");
    $focus->column_fields['lastname'] =$acct_focus->column_fields['accountname'];
    $focus->column_fields['account_id']=$item['crmid'];
    $focus->column_fields['email']=$acct_focus->column_fields['email1'];
    $focus->column_fields['fax'] = $acct_focus->column_fields['fax'];
    $focus->column_fields['otherphone'] = $acct_focus->column_fields['phone'];
    $focus->column_fields['mailingcity'] = $acct_focus->column_fields['bill_city'];
    $focus->column_fields['othercity'] = $acct_focus->column_fields['ship_city'];
    $focus->column_fields['mailingstreet'] = $acct_focus->column_fields['bill_street'];
    $focus->column_fields['otherstreet'] = $acct_focus->column_fields['ship_street'];
    $focus->column_fields['mailingstate'] = $acct_focus->column_fields['bill_state'];
    $focus->column_fields['otherstate'] = $acct_focus->column_fields['ship_state'];
    $focus->column_fields['mailingzip'] = $acct_focus->column_fields['bill_code'];
    $focus->column_fields['otherzip'] = $acct_focus->column_fields['ship_code'];
    $focus->column_fields['mailingcountry'] = $acct_focus->column_fields['bill_country'];
    $focus->column_fields['othercountry'] = $acct_focus->column_fields['ship_country'];
    $focus->column_fields['mailingpobox'] = $acct_focus->column_fields['bill_pobox'];
    $focus->column_fields['otherpobox'] = $acct_focus->column_fields['ship_pobox'];

    if($assigntocontacts!=0) {
      $focus->column_fields['assigned_user_id'] = $assigntocontacts;
    } else {
      $findaccuser=$this->adb->query("Select smownerid from vtiger_crmentity where crmid=".$item['crmid']);
      $accuser=$this->adb->query_result($findaccuser,0,'smownerid');
      $focus->column_fields['assigned_user_id'] = $accuser;
    }
    $focus->mode = '';
    $focus->save('Contacts');

    return $focus->id;
  }

  public function fromContacts($item) {
    $assigntocontacts = $this->get('assigntocontacts');
    $qcond="Select lastname,email1,smownerid,accountname,email,vtiger_contactdetails.accountid
    from vtiger_contactdetails
    left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
    inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
    where vtiger_contactdetails.contactid = ".$item['crmid'];
    $contactquery=$this->adb->query($qcond);
    $row=$this->adb->fetch_array($contactquery);
    $accid=$row['accountid'];
    $email=$row['email1'];
    if(!empty($email)){
      $focus= new Contacts();
      $focus->column_fields['lastname'] =$row['lastname'];
      $focus->column_fields['email']=$email;
      $focus->column_fields['title']=getTranslatedString('ContactTitle','MarketingDashboard');
      $focus->column_fields['account_id']=$accid;
      $focus->column_fields['posizione_']='GENERICO';
      if($assigntocontacts!=0) {
        $focus->column_fields['assigned_user_id'] = $assigntocontacts;
      } else {
        $focus->column_fields['assigned_user_id'] = $row['smownerid'];
      }
      $focus->mode = '';
      $focus->save('Contacts');

      return $focus->id;
    }
  }
}
