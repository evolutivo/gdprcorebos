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

require_once("modules/Potentials/Potentials.php");
require_once("modules/Task/Task.php");
require_once("modules/Messages/Messages.php");
require_once("modules/MarketingDashboard/sendEmail.php");

class CampaignBatchJob extends BatchJob {

  public $sendgrid = NULL;

  public function create() {
    $rsprocess = $this->adb->query("insert into batch_items (batch_job_id, crmid, crmtype) select {$this->id}, vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_campaignresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_campaignresults.crmid where mktdb_campaignresults.selected=1 and mktdb_campaignresults.userid={$this->userId}");
  }

      public function create1($i,$threshold,$selectednr,$nrofqueues){
                      global $log;
                      $threshold2=$threshold;
                      $selectednr2=$selectednr;
    $query3=$this->adb->query("select count(*) as 'nr2' from mktdb_campaignresults where mktdb_campaignresults.selected=1 limit ".$threshold);
    $nr2=$this->adb->query_result($query3,0,'nr2');
if($i==0)
{$randomnr=$this->adb->query("select count(*) as 'nrjobs' from batch_jobs");
$nrjob=$this->adb->query_result($randomnr,0,'nrjobs');
$nrjob+=1;}
else
{
    $randomnr=$this->adb->query("select count(*) as 'nrjobs' from batch_jobs");
$nrjob=$this->adb->query_result($randomnr,0,'nrjobs');
$nrjob=$nrjob+1-$i;
}
             switch($i){
                 case $i:
                     if($i==1){
                     $limit=($i-1)*$threshold+($i-1);
                     $checknumber=$selectednr/$threshold;
                     if(is_float($checknumber)){
                     $selectednr=$selectednr-($selectednr-$threshold);}
                     else $selectednr=$selectednr-(($nrofqueues-1)*$threshold-1)-1;
                      $query22=$this->adb->query("select mktdb_campaignresults.crmid as 'mkcampid' from mktdb_campaignresults where mktdb_campaignresults.selected=1 limit ".$limit.",".$selectednr);
                     for($i=0;$i<$nr2;$i++){
                    $idfirst=$this->adb->query_result($query22,$i,'mkcampid');
                    $log->debug("ketojaneidte".$idfirst);
                    if($idfirst!='')
                    {  $uprandom=$this->adb->query("update batch_jobs set randomnr=$nrjob where id={$this->id}");
                    $rsprocess = $this->adb->query("insert into batch_items (batch_job_id, crmid, crmtype) select {$this->id}, vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_campaignresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_campaignresults.crmid where mktdb_campaignresults.crmid=$idfirst and mktdb_campaignresults.selected=1 and mktdb_campaignresults.userid={$this->userId}");
                    }   if($i==$threshold)   break; } 
                    
                     }
                     else{
                      
                         $limit=($i-1)*$threshold2;
            $selectednr=$selectednr2-(($nrofqueues-$i)*$threshold2);
                      $query22=$this->adb->query("select mktdb_campaignresults.crmid as 'mkcampid' from mktdb_campaignresults where mktdb_campaignresults.selected=1 limit ".$limit.",".$selectednr);
                     for($i=0;$i<$nr2;$i++){
                    $idfirst=$this->adb->query_result($query22,$i,'mkcampid');
                    $log->debug("ketojaneidte".$idfirst);
                    if($idfirst!='')
                    { $uprandom=$this->adb->query("update batch_jobs set randomnr=$nrjob where id={$this->id}");
                    $rsprocess = $this->adb->query("insert into batch_items (batch_job_id, crmid, crmtype) select {$this->id}, vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_campaignresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_campaignresults.crmid where mktdb_campaignresults.crmid=$idfirst and mktdb_campaignresults.selected=1 and mktdb_campaignresults.userid={$this->userId}");
                    }  if($i==$threshold-1)    break;
                     }
                     
                    }
                 default:
                     break;
                 
                     
                     
    
  }
             }

  public function run() {
    // Use sendgrid?
    $rsinfo = $this->adb->query("SELECT usesg FROM sendgrid_config");
    if ($this->adb->num_rows($rsinfo) > 0) {
      $this->sendgrid = $this->adb->query_result($rsinfo, 0, 'usesg');
    }
    while ($rec = $this->nextItem()) {
      $id = NULL;
      switch ($rec['crmtype']) {
      case 'Contacts':
        $id = $this->convertFromContacts($rec['crmid']);
        break;
      case 'Leads':
        $id = $this->convertFromLeads($rec['crmid']);
        break;
      case 'Messages':
        $id = $this->convertFromMessages($rec['crmid']);
        break;
      case 'Potentials':
        $id = $this->convertFromPotentials($rec['crmid']);
        break;
      case 'Task':
        $id = $this->convertFromTask($rec['crmid']);
        break;
      case 'Accounts':
        $id = $this->convertFromAccount($rec['crmid']);
        break;
      }
      if (!is_null($id)) {
        $this->markItemDone(compact('id'));
      }
    }
  }

  public function getTitle($entityName, $accountId = NULL) {
    $type = $this->get('title');
    $module = $this->get('convertto');
    $description = $this->get('desc_name');
    $moduleName = getTranslatedString($module, $module);
    if ($accountId) {
      $accountName = getEntityName('Accounts', $accountId);
      $accountName = $accountName[$accountId];
    } else {
      $accountName = '';
    }
    switch ($type) {
    case 0:
      return $entityName;
    case 1:
      return $moduleName . ": " . $entityName;
    case 2:
      return $description;
    case 3:
      return $description . " " . $entityName;
    case 4:
      return $description . " " . $accountName;
    }
  }

  public function createEntity($crmType, $data) {
    if ($crmType === 'Task') {
      if (isset($data['contactidlist'])) {
        $_REQUEST['contactidlist'] = $data['contactidlist'];
        unset($data['contactidlist']);
      } else {
        $_REQUEST['contactidlist'] = '';
      }
    }
    $focus = new $crmType();
    $focus->column_fields = $data + $focus->column_fields;
    if ($this->get('assignto') != 0) {
			$focus->column_fields['assigned_user_id'] = $this->get('assignto');
		}
		$focus->mode = '';
		$focus->save($crmType);
		return $focus->id;
  }

  public function relateEntities($module1, $id1, $module2, $id2) {
    $focus = CRMEntity::getInstance($module1);
    $focus->save_related_module($module1, $id1, $module2, $id2);
	}

  public function sendMessage($crmId, $crmType, $email, $messageId, $accountId = NULL) {
    global $current_user;
    $template = $this->get('emailtemplateid');
    $campaignconvert = $this->get('campaignconvert');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $this->adb->pquery("UPDATE vtiger_messages SET no_mail = 1 WHERE messagesid = ?", array($messageId));
      return;
    }
    $etrs = $this->adb->pquery('select subject,template from vtiger_emailstemplates where emailstemplatesid=?', array($template));
    $row = $this->adb->getNextRow($etrs, false);
    $subject = $row['subject'];
    $contents = $row['template'];
    $subject = getMergedDescription($subject, $crmId, $crmType);
    $contents = getMergedDescription($contents, $crmId, $crmType);
    if (!is_null($accountId)) {
      $module = 'Accounts';
      $subject = getMergedDescription($subject, $accountId, $module);
      $contents = getMergedDescription($contents, $accountId, $module);
    }
    $subject = getMergedDescription($subject, $current_user->id, 'Users');
    $contents = getMergedDescription($contents, $current_user->id, 'Users');
 $queryutente=$this->adb->query("select username from batch_jobs where id=$this->id");
    $utenteresult=$this->adb->query_result($queryutente,0,0);
    //if ($this->sendgrid) {
//global $log;
//$log->debug("enteredsendgridmail");
     // sendGridMail($email, $contents, $subject, array($current_user->email1 => getUserFullName($current_user->id)), $campaignconvert . '-' . $template,'Marketing', $messageId, array());
sendGridMail($email, $contents, $subject, array($current_user->email1 => $utenteresult), $campaignconvert . '-' . $template,'Marketing', $messageId, array());
    //} 
//else {
//$log->debug("enteredsendgridmail");
    //  send_mail($crmType, $email, getUserFullName($current_user->id), $current_user->email1, $subject, $contents, '', '', '', '', '');
  //  }
	}

  public function convertFromContacts($crmId) {
    $qcond = "Select * from vtiger_contactdetails join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid where vtiger_contactdetails.contactid = ".$crmId;
    $res = $this->adb->query($qcond);
    $row = $this->adb->getNextRow($res, false);
    $entityName = $row['firstname'] . " " . $row['lastname'];
    $accountId = $row['accountid'];
    $assignedUserId = $row['smownerid'];
    switch ($this->get('convertto')) {
    case 'Potentials':
      $potentialId = $returnId = $this->createEntity('Potentials', array(
        'potentialname' => $this->getTitle($entityName, $accountId),
        'related_to' => $accountId,
        'closingdate' => $this->get('due_date'),
        'end_hr' => $this->get('due_time'),
        'campaignid' => $this->get('campaignconvert'),
        'sales_stage' => $this->get('selvisit'),
        'description' => $this->get('descr'),
        'assigned_user_id' => $assignedUserId,
        ));
      if ($this->get('campaignconvert') != '') {
        $this->relateEntities('Campaigns', $this->get('campaignconvert'), 'Potentials', $potentialId);
      }
      break;
    case 'Task':
      $findpotential = $this->adb->pquery("Select * from vtiger_potential where related_to=? and campaignid=?", array($accountId, $this->get('campaignconvert')));
			$potentialId = $this->adb->query_result($findpotential, 0, 'potentialid');
      $returnId = $this->createEntity('Task', array(
        'taskname' => $this->getTitle($entityName, $accountId),
        'linktoentity' => $accountId,
        'event_type' => $this->get('event'),
        'date_end' => $this->get('due_date'),
        'date_start' => $this->get('due_date'),
        'time_start' => $this->get('due_time'),
        'time_end' => $this->get('due_time'),
        'taskstate' => $this->get('taskstate'),
        'campaign' => $this->get('campaignconvert'),
        'taskdescription' => $this->get('descr'),
        'linktopotential' => $potentialId,
        'assigned_user_id' => $assignedUserId,
        'contactidlist' => $crmId,
        ));
      break;
    case 'Messages':
      $etinfo = getEntityName('EmailsTemplates', array($this->get('emailtemplateid')));
      $returnId = $messageId = $this->createEntity('Messages', array(
        'messagesname' => $this->getTitle($entityName, $accountId),
        'campaign_message' => $this->get('campaignconvert'),
        'contact_message' => $crmId,
        'messagestype' => 'Email',
        'email_tplid' => $this->get('emailtemplateid'),
        'description' => $etinfo[$this->get('emailtemplateid')],
        'assigned_user_id' => $assignedUserId,
        ));
      $emailfields=$this->get('emailselected');
      if($emailfields!='' && $emailfields!=null){
      $ef=explode(",",$emailfields);
      $q=$this->adb->query("select * from vtiger_field where fieldid in ($emailfields)");
      for($e=0;$e<count($ef);$e++){
      $emailfld=$this->adb->query_result($q,$e,'columnname');
      $this->sendMessage($crmId, 'Contacts', $row["$emailfld"], $messageId, $accountId);
      }
      }
      else 
      $this->sendMessage($crmId, 'Contacts', $row["email"], $messageId, $accountId);
      break;
    }
    $this->saveResultData(compact('accountId'));
    return $returnId;
  }

  public function convertFromLeads($crmId) {
    $qcond = "Select * from vtiger_leaddetails join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid where vtiger_leaddetails.leadid = ".$crmId;
    $res = $this->adb->query($qcond);
    $row = $this->adb->getNextRow($res, false);
    $entityName = $row['firstname'] . " " . $row['lastname'];
    $assignedUserId = $row['smownerid'];
    switch ($this->get('convertto')) {
    case 'Potentials':
      $returnId = $this->createEntity('Potentials', array(
        'potentialname' => $this->getTitle($entityName),
        'related_to' => 0,
        'closingdate' => $this->get('due_date'),
        'end_hr' => $this->get('due_time'),
        'campaignid' => $this->get('campaignconvert'),
        'sales_stage' => $this->get('selvisit'),
        'description' => $this->get('descr'),
        'assigned_user_id' => $assignedUserId,
        ));
      break;
    case 'Task':
      $returnId = $this->createEntity('Task', array(
        'taskname' => $this->getTitle($entityName),
        'linktoentity' => 0,
        'event_type' => $this->get('event'),
        'date_end' => $this->get('due_date'),
        'date_start' => $this->get('due_date'),
        'time_start' => $this->get('due_time'),
        'time_end' => $this->get('due_time'),
        'taskstate' => $this->get('taskstate'),
        'campaign' => $this->get('campaignconvert'),
        'taskdescription' => $this->get('descr'),
        'linktolead' => $crmId,
        'assigned_user_id' => $assignedUserId,
        ));
      break;
    case 'Messages':
      $etinfo = getEntityName('EmailsTemplates', array($this->get('emailtemplateid')));
      $returnId = $messageId = $this->createEntity('Messages', array(
        'messagesname' => $this->getTitle($entityName),
        'campaign_message' => $this->get('campaignconvert'),
        'lead_message' => $crmId,
        'messagestype' => 'Email',
        'email_tplid' => $this->get('emailtemplateid'),
        'description' => $etinfo[$this->get('emailtemplateid')],
        'assigned_user_id' => $assignedUserId,
        ));
      $emailfields=$this->get('emailselected');
      if($emailfields!='' && $emailfields!=null){
      $ef=explode(",",$emailfields);
      $q=$this->adb->query("select * from vtiger_field where fieldid in ($emailfields)");
      for($e=0;$e<count($ef);$e++){
      $emailfld=$this->adb->query_result($q,$e,'columnname');
      $this->sendMessage($crmId, 'Leads', $row["$emailfld"], $messageId);
      }
      }
      else $this->sendMessage($crmId, 'Leads', $row["email"], $messageId);
      break;
    }
    return $returnId;
  }

  public function convertFromMessages($crmId) {
    $qcond = "Select * from vtiger_messages join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_messages.messagesid where vtiger_messages.messagesid = ".$crmId;
    $res = $this->adb->query($qcond);
    $row = $this->adb->getNextRow($res, false);
    $assignedUserId = $row['smownerid'];
    if($this->get('campaignconvert') != NULL && $this->get('campaignconvert') != '')
    	$campaignid = $this->get('campaignconvert');
    else
    	$campaignid = $row['campaignid'];    
    switch ($this->get('convertto')) {
    case 'Potentials':
    	if($row['contact_message'] != '' && $row['contact_message'] != NULL && $row['contact_message'] != 0)
    		$related_to = $row['contact_message'];
    	elseif($row['account_message'] != '' && $row['account_message'] != NULL && $row['account_message'] != 0)
    		$related_to = $row['account_message'];
    	else
    		$related_to = '';
    	$result = $this->adb->pquery("SELECT COUNT(*) as existe from vtiger_potential WHERE campaignid = ? AND related_to = ?",
    									array($campaignid,$related_to));
    	$existe = $this->adb->query_result($result,0,'existe');
    	if($existe == 0)
    	{	
	      $returnId = $potentialId = $this->createEntity('Potentials', array(
	        'related_to' => $related_to,
	        'closingdate' => $this->get('due_date'),
	        'campaignid' => $campaignid,
	        'sales_stage' => $this->get('selvisit'),
	        'description' => $this->get('descr'),
	        'potentialname' => $this->getTitle($row['messagesname'], ''),
	        'assigned_user_id' => $assignedUserId,
	        ));
	      if ($row['campaign_message'] != '') {
	        $this->relateEntities('Campaigns', $row['campaign_message'], 'Potentials', $potentialId);
	      }
    	}
    	else
    		$returnId = 0;
      break;
    case 'Task':
      $returnId = $taskId = $this->createEntity('Task', array(
        'taskname' => $row['messagesname'],
        'campaign' => $campaignid,
        'linktoentity' => $row['account_message'],
        'linktolead' => $row['lead_message'],
        'event_type' => $this->get('event'),
        'date_end' => $this->get('due_date'),
        'date_start' => $this->get('due_date'),
        'time_start' => $this->get('due_time'),
        'time_end' => $this->get('due_time'),
        'taskstate' => $this->get('taskstate'),
        'taskdescription' => $this->get('descr'),
        'assigned_user_id' => $assignedUserId,
        ));
      if ($row['contact_message'] != '') {
        $this->relateEntities('Contacts', $row['contact_message'], 'Task', $taskId);
      }
      break;
    }
    return $returnId;
  }

  public function convertFromPotentials($crmId) {
    $qcond = "Select * from vtiger_potential join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid where vtiger_potential.potentialid = ".$crmId;
    $res = $this->adb->query($qcond);
    $row = $this->adb->getNextRow($res, false);
    $entityName = $row['potentialname'];
    $accountId = $row['related_to'];
    $assignedUserId = $row['smownerid'];
    switch ($this->get('convertto')) {
    case 'Potentials':
      $returnId = $potentialId = $this->createEntity('Potentials', array(
        'potentialname' => $this->getTitle($entityName, $accountId),
        'related_to' => $accountId,
        'closingdate' => $row['closingdate'],
        'end_hr' => $row['end_hr'],
        'campaignid' => $row['campaignid'],
        'sales_stage' => $this->get('selvisit'),
        'description' => $row['description'],
        'assigned_user_id' => $assignedUserId,
        ));
      if ($row['campaignid'] != '') {
        $this->relateEntities('Campaigns', $row['campaignid'], 'Potentials', $potentialId);
      }
      break;
    case 'Task':
      $returnId = $this->createEntity('Task', array(
        'taskname' => $this->getTitle($entityName, $accountId),
        'linktoentity' => $row['related_to'],
        'event_type' => $this->get('event'),
        'date_end' => $row['closingdate'],
        'time_end' => $row['end_hr'],
        'campaign' => $row['campaignid'],
        'taskdescription' => $this->get('descr'),
        'assigned_user_id' => $assignedUserId,
        'contactidlist' => $row['contactid'],
        ));
      break;
      case 'Messages':
      	if(getSalesEntityType($accountId) == 'Contacts')
      		$contactId = $accountId;
      	else
      		$contactId = '';

      	$etinfo = getEntityName('EmailsTemplates', array($this->get('emailtemplateid')));
      	$returnId = $messageId = $this->createEntity('Messages', array(
      			'messagesname' => $this->getTitle($entityName, $accountId),
      			'campaign_message' => $row['campaignid'],
      			'account_message' => $accountId,
      			'contact_message' => $contactId,
      			'messagestype' => 'Email',
      			'email_tplid' => $this->get('emailtemplateid'),
      			'description' => $etinfo[$this->get('emailtemplateid')],
      			'assigned_user_id' => $assignedUserId,
      	));
      	$this->sendMessage($crmId, 'Potentials', $row['email'], $messageId, $accountId);
      	break;      
    }
    $this->saveResultData(compact('accountId'));
    return $returnId;
  }

  public function convertFromTask($crmId) {
    $qcond = "Select * from vtiger_task join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid where vtiger_task.taskid = ".$crmId;
    $res = $this->adb->query($qcond);
    $row = $this->adb->getNextRow($res, false);
    $entityName = $row['taskname'];
    $accountId = $row['linktoentity'];
    $q=$this->adb->query("select * from vtiger_account join vtiger_crmentity on crmid=accountid where accountid=$accountId and deleted=0");
    if($this->adb->num_rows($q)>0)
    {
    $email=$this->adb->query_result($q,0,"email1");
    $setype='Accounts';
    }
    else{
    $q1=$this->adb->query("select * from vtiger_contactdetails join vtiger_crmentity on crmid=contactid where contactid=$accountId and deleted=0");
    if($this->adb->num_rows($q1)>0)
    {
    $email=$this->adb->query_result($q,0,"email");
    $setype='Contacts';
    }
    }
    $assignedUserId = $row['smownerid'];
    if($this->get('campaignconvert') != NULL && $this->get('campaignconvert') != '')
    	$campaignid = $this->get('campaignconvert');
    else
    	$campaignid = $row['campaignid'];    
    switch ($this->get('convertto')) {
    case 'Potentials':
      $returnId = $potentialId = $this->createEntity('Potentials', array(
        'potentialname' => $this->getTitle($entityName, $accountId),
        'related_to' => $accountId,
        'closingdate' => $row['date_end'],
        'end_hr' => $row['time_end'],
        'campaignid' => $campaignid,
        'description' => $row['description'],
        'assigned_user_id' => $assignedUserId,
        ));
      if ($row['campaign'] != '') {
        $this->relateEntities('Campaigns', $row['campaign'], 'Potentials', $potentialId);
      }
      break;
     case 'Task':
      $returnId = $this->createEntity('Task', array(
        'taskname' => $row['taskname'],
        'linktoentity' => $accountId,
        'event_type' => $row['event_type'],
        'date_end' => $row['date_end'],
        'date_start' => $row['date_start'],
        'time_start' => $row['time_start'],
        'time_end' => $row['time_end'],
        'taskstate' => $row['taskstate'],
        'campaign' => $this->get('campaign'),
        'taskdescription' => $row['taskdescription'],
        'linktolead' => $row['linktolead'],
        'assigned_user_id' => $row['smownerid'],
        ));
        break;
      case 'Messages':
      $etinfo = getEntityName('EmailsTemplates', array($this->get('emailtemplateid')));
      $returnId = $messageId = $this->createEntity('Messages', array(
        'messagesname' => $entityName,
        'campaign_message' => $campaignid,
        'messagesrelatedto'=>$accountId,
     //   'contact_message' => $crmId,
        'messagestype' => 'Email',
        'email_tplid' => $this->get('emailtemplateid'),
        'description' => $etinfo[$this->get('emailtemplateid')],
        'assigned_user_id' => $assignedUserId,
        ));
      if($email!='')
      $this->sendMessage($crmId, $setype, $email, $messageId, $accountId);
      break;
    }
    $this->saveResultData(compact('accountId'));
    return $returnId;
  }

  public function convertFromAccount($crmId) {
    $qcond = "Select * from vtiger_account join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid where vtiger_account.accountid = ".$crmId;
    $res = $this->adb->query($qcond);
    $row = $this->adb->getNextRow($res, false);
    $entityName = $row['accountname'];
    $assignedUserId = $row['smownerid'];
    switch ($this->get('convertto')) {
    case 'Potentials':
      $contactrelquery = $this->adb->query("SELECT contactid FROM vtiger_contactdetails cd INNER JOIN vtiger_crmentity ce ON ce.crmid=cd.contactid WHERE ce.deleted=0 AND cd.accountid=$crmId");
      $conarraypot = array();
      while ( $contactrelquery && $r = $this->adb->fetch_array( $contactrelquery ) ) {
        $conarraypot [] = $r['contactid'];
      }
      $result = $this->adb->pquery("SELECT COUNT(*) as existe from vtiger_potential WHERE campaignid = ? AND related_to = ?",
      		array($this->get('campaignconvert'),$crmId));
      $existe = $this->adb->query_result($result,0,'existe');
      if($existe == 0)
      {      
	      $returnId = $this->createEntity('Potentials', array(
	        'potentialname' => $this->getTitle($entityName, $crmId),
	        'related_to' => $crmId,
	        'closingdate' => $this->get('due_date'),
	        'end_hr' => $this->get('due_time'),
	        'campaignid' => $this->get('campaignconvert'),
	        'sales_stage' => $this->get('selvisit'),
	        'description' => $this->get('descr'),
	        'assigned_user_id' => $assignedUserId,
	        'contactidlist' => implode (",", $conarraypot),
	        ));
      }
      else
      	$returnId = 0; 
      break;
    case 'Task':
      $contactrelquery = $this->adb->query("SELECT contactid FROM vtiger_contactdetails cd INNER JOIN vtiger_crmentity ce ON ce.crmid=cd.contactid WHERE ce.deleted=0 AND cd.accountid=$crmId");
      $conarray = array();
      while ($contactrelquery && $r = $this->adb->fetch_array( $contactrelquery ) ) {
        $conarray [] = $r ['contactid'];
      }
      $findpotential = $this->adb->pquery("Select * from vtiger_potential where related_to=? and campaignid=?", array($crmId, $focus->column_fields['campaign']));
      $potentialId = $this->adb->query_result($findpotential, 0, 'potentialid');
      $returnId = $this->createEntity('Task', array(
        'taskname' => $this->getTitle($entityName, $crmId),
        'linktoentity' => $crmId,
        'event_type' => $this->get('event'),
        'date_end' => $this->get('due_date'),
        'date_start' => $this->get('due_date'),
        'time_start' => $this->get('due_time'),
        'time_end' => $this->get('due_time'),
        'taskstate' => $this->get('taskstate'),
        'campaign' => $this->get('campaignconvert'),
        'taskdescription' => $this->get('descr'),
        'linktolead' => $crmId,
        'linktopotential' => $potentialId,
        'assigned_user_id' => $assignedUserId,
        'contactidlist' => implode (",", $conarraypot),
        ));
      break;
     case 'SMS':
      $etinfo = getEntityName('EmailsTemplates', array($this->get('emailtemplateid')));
      $returnId = $messageId = $this->createEntity('Messages', array(
        'messagesname' => $this->getTitle($entityName, $crmId),
        'campaign_message' => $this->get('campaignconvert'),
        'account_message' => $crmId,
        'messagestype' => 'SMS',
        'email_tplid' => $this->get('emailtemplateid'),
        'description' => $etinfo[$this->get('emailtemplateid')],
        'assigned_user_id' => $assignedUserId,
        ));  
      global $log;
      $this->sendSMS($crmId, 'Accounts', $row['celullare'], $messageId);
      break;
    case 'Messages':
      $etinfo = getEntityName('EmailsTemplates', array($this->get('emailtemplateid')));
      $returnId = $messageId = $this->createEntity('Messages', array(
        'messagesname' => $this->getTitle($entityName, $crmId),
        'campaign_message' => $this->get('campaignconvert'),
        'account_message' => $crmId,
        'messagestype' => 'Email',
        'email_tplid' => $this->get('emailtemplateid'),
        'description' => $etinfo[$this->get('emailtemplateid')],
        'assigned_user_id' => $assignedUserId,
        ));
      $emailfields=$this->get('emailselected');
      if($emailfields!='' && $emailfields!=null){
      $ef=explode(",",$emailfields);
      $q=$this->adb->query("select * from vtiger_field where fieldid in ($emailfields)");
      for($e=0;$e<count($ef);$e++){
      $emailfld=$this->adb->query_result($q,$e,'columnname');
      $this->sendMessage($crmId, 'Accounts', $row["$emailfld"], $messageId);
      }
      }
      else       
      $this->sendMessage($crmId, 'Accounts', $row['email1'], $messageId);
      break;
    }
    $this->saveResultData(compact('accountId'));
    return $returnId;
  }
  
  public function sendSMS($crmId, $crmType, $phone, $messageId, $accountId = NULL) {
    global $current_user,$log;
    $log->debug('hyn tek sms'.$phone);
    $template = $this->get('emailtemplateid');
    $campaignconvert = $this->get('campaignconvert');
    $etrs = $this->adb->pquery('select subject,template from vtiger_businessactions where businessactionsid=?', array($template));
    $row = $this->adb->getNextRow($etrs, false);
    $subject = $row['subject'];
    $contents = $row['template'];
    $subject = getMergedDescription($subject, $crmId, $crmType);
    $contents = getMergedDescription($contents, $crmId, $crmType);
    if (!is_null($accountId)) {
      $module = 'Accounts';
      $subject = getMergedDescription($subject, $accountId, $module);
      $contents = getMergedDescription($contents, $accountId, $module);
    }
    $subject = getMergedDescription($subject, $current_user->id, 'Users');
    $contents = getMergedDescription($contents, $current_user->id, 'Users');
    $contents=strip_tags($contents);
     //$value= htmlentities($contents);
     $contents=html_entity_decode($contents);
     //$contents=html_entity_decode($contents,ENT_QUOTES | ENT_IGNORE, "UTF-8");
    //$contents= addslashes($contents);
    require_once 'include/plivo/plivo.php';
    $auth_id = "MAODI2MTEXMTQ3YTMZYZ";
    $auth_token = "NmIxODJkMDUyOWU3MjA5NDEwMzViOTM2YmRiZGY0";
    $p = new RestAPI($auth_id, $auth_token);
    // Send a message
    $params = array(
            'src' => 'Corebos',
            'dst' =>  $phone,
            'text' => $contents,
            'type' => 'sms',
        );
    $response = $p->send_message($params);
}      
}
