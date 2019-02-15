<?php
/*************************************************************************************************
 * Copyright 2011-2013 TSolucio  --  This file is a part of vtMktDashboard.
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
*  Module       : PALaunch
*  Version      : 1.9
*  Author       : TSolucio
*************************************************************************************************/
require_once('include/utils/utils.php');
require_once('modules/Emails/Emails.php');
require_once('modules/Emails/mail.php');
require_once('modules/Messages/Messages.php');
require_once('modules/Task/Task.php');
require_once('modules/PALaunch/sendEmail.php');
include_once('modules/SMSNotifier/SMSNotifier.php');
include_once('modules/PALaunch/vendor/mtdowling/cron-expression/src/Cron/CronExpression.php');
include_once('modules/PALaunch/vendor/mtdowling/cron-expression/src/Cron/FieldFactory.php');
include_once('modules/PALaunch/vendor/mtdowling/cron-expression/src/Cron/MinutesField.php');
require_once('modules/PALaunch/vendor/mtdowling/cron-expression/src/Cron/AbstractField.php');
global $adb, $current_user,$log;

// Set current user to admin
if (!$current_user) {
	$current_user = CRMEntity::getInstance('Users');
	$current_user->retrieveCurrentUserInfoFromFile(1);
}

// Set database zone if needed
if (!empty($DATABASE_TIMEZONE)) {
	$adb->query("set time_zone='{$DATABASE_TIMEZONE}';");
}

processQueue('WS');
processQueue('Cron');

//processQueue(array('Email', 'Digital'), $MAILQUEUE_BATCH_SIZE, $MAILQUEUE_BATCH_PERIOD, $MAILQUEUE_THROTTLE, true);

$date = new DateTimeField(NULL);
$now = $date->getDisplayTime();
//if ((!isset($SMS_sendtime_start) && !isset($SMS_sendtime_end)) || ($now >= $SMS_sendtime_start && $now <= $SMS_sendtime_end)) {
//	processQueue('SMS', 0, 0, 0, true);
//}

//processQueue('Task');

function processQueue($actionTypes, $batchSize = 0, $batchPeriod = 0, $throttle = 0, $emailoptout = false) {
	global $adb;
	if (!is_array($actionTypes)) {
		$actionTypes = array($actionTypes);
	}
	$actionTypesAsString = "'" . implode("','", $actionTypes) . "'";
                $query = "select palaunchid,sequencersid,businessactionsid, parameters,actions_type as ac,if(s.sequencersid<>'',s.crontab, act.crontab) as crontab, 
                if(s.sequencersid<>'',s.elementtype, act.elementtype_action) as actions_type
                from vtiger_palaunch pal
		join vtiger_crmentity as crm_pal on crm_pal.crmid=pal.palaunchid and crm_pal.deleted=0
		left join vtiger_businessactions act on (act.businessactionsid=pal.sequencer_id )
		left join vtiger_sequencers s on (s.sequencersid=pal.sequencer_id )
		where (DATE_FORMAT(ADDTIME(CONCAT(pal.scheduled_date,' ',pal.time_start),'00:05:00'),'%Y-%m-%d %H:%i')>=DATE_FORMAT(now(),'%Y-%m-%d %H:%i') 
                and DATE_FORMAT(CONCAT(pal.scheduled_date,' ',pal.time_start),'%Y-%m-%d %H:%i')<=DATE_FORMAT(ADDTIME(now(),'00:05:00'),'%Y-%m-%d %H:%i') 
                or (DATE_FORMAT(ADDTIME(CONCAT(pal.scheduled_date,' ',pal.time_start),'00:05:00'),'%Y-%m-%d %H:%i')<DATE_FORMAT(now(),'%Y-%m-%d %H:%i') 
                and elementtype_action=$actionTypesAsString)) 
                and pal.palaunch_status='Pending' and ((elementtype=$actionTypesAsString and s.sequencersid<>'')"
                . "or (elementtype_action=$actionTypesAsString and act.businessactionsid<>''))
		order by pal.scheduled_date";  
        //}
        if ($batchSize > 0) {
		$query .= " limit {$batchSize}";
        }
        	$res = $adb->query($query);
               
	while ($row=$adb->getNextRow($res, false)) {
		$status = 'Done';
             
		if (($row['actions_type']!='Cron' && $row['actions_type']!='WS') && (empty($row['recipient_id']) || $row['invalid'] || $row['plannedactions_status'] != 'Active' || ($row['sequencersid'] && $row['sequencers_status'] != 'Active' && $row['sequencers_status'] != 'By date'))) {
			$status = 'Skipped';
		}
		elseif (($row['actions_type']!='Cron' && $row['actions_type']!='WS') && $emailoptout && $row['emailoptout']) {
			$status = "Optout";
		} else {
                    if($row['actions_type']=='Cron' || $row['actions_type']=='WS'){
                        if($row['sequencersid']!=0 && $row['sequencersid']!='' && $row['sequencersid']!=null)
                        { $actid=$row['sequencersid']; $typ='sequencer';}
                       else {$actid=$row['businessactionsid'];$typ='action';}
                       $param=$row['parameters'];
                       $palid=$row['palaunchid'];
                       $crontab=$row['crontab'];
                    }
			try {
				switch($row['actions_type']){
				case 'Email':
				case 'Digital':
					$rval = sendEmailTemplate($row['recipient_id'], $row['plannedaction_id'], $row['related_id']);
					break;
				case 'SMS':
					$rval = sendSMSTemplate($row['recipient_id'], $row['plannedaction_id']);
					break;
				case 'Task':
					$rval = createTask($row['recipient_id'], $row['plannedaction_id']);
					break;
                                case 'Cron':
					$rval = execCronWS($actid,$typ,'cron',$param,$palid,$crontab);
                                break;
                                    case 'WS':
					$rval = execCronWS($actid,$typ,'ws',$param,$palid,$crontab);
					break;
				}
			} catch (Exception $e) {
				$rval = addslashes($e);
			}
			if ($rval !== true) {
				$status = 'Error';
				$query = "update vtiger_crmentity set description='{$rval}' where crmid={$row['palaunchid']}";
				$adb->query($query);
			}
		}
                if($row['actions_type']!='Cron' && $row['actions_type']!='WS')
		{$query = "update vtiger_palaunch set palaunch_status='{$status}', processed_date=now() where palaunchid={$row['palaunchid']}";
                $adb->query($query);}
		if ($status == 'Done' && $throttle > 0) {
			usleep($throttle*1000);
		}
	}
}
/*
function sendEmailTemplate($crmId, $plannedActionId, $relatedId) {
  global $adb, $current_user;
  $setype = getSalesEntityType($crmId);
  switch ($setype) {
    case 'Contacts':
  $query = "select act.businessactionsid,act.reference, act.subject, act.template, c.email,CONCAT(c.firstname,c.lastname) as toname,
   pa.emailfromopt, pa.emailfrom, u_pa1.email1 as email1, u_pa2.email1 as email2, u_pa1.first_name as firstname1,
   u_pa2.first_name as firstname2, u_pa1.last_name as lastname1, u_pa2.last_name as lastname2, c.accountid as account_id,
   case when (u_c.email1 not like '') then u_c.email1 else u_g.email1 end as email3,
   case when (u_c.first_name not like '') then u_c.first_name else u_g.first_name end as firstname3,
   case when (u_c.last_name not like '') then u_c.last_name else u_g.last_name end as lastname3,
   case when (u_c.id not like '') then u_c.id else u_g.id end as user_id
  from vtiger_plannedactions pa
  join vtiger_businessactions act on act.businessactionsid=pa.action_id
  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
  left join vtiger_contactdetails c on c.contactid={$crmId}
  join vtiger_crmentity crm_c on crm_c.crmid=c.contactid
  left join vtiger_users u_c on u_c.id=crm_c.smownerid
  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
  where pa.plannedbusinessactionsid={$plannedActionId}
  group by pa.plannedbusinessactionsid,pa.action_id,c.contactid";
      break;
    case 'Accounts':
	  $query = "select act.businessactionsid,act.reference, act.subject, act.template, ac.email1 as email,ac.accountname as toname,
	   pa.emailfromopt, pa.emailfrom, u_pa1.email1 as email1, u_pa2.email1 as email2, u_pa1.first_name as firstname1,
	   u_pa2.first_name as firstname2, u_pa1.last_name as lastname1, u_pa2.last_name as lastname2,
	   case when (u_c.email1 not like '') then u_c.email1 else u_g.email1 end as email3,
	   case when (u_c.first_name not like '') then u_c.first_name else u_g.first_name end as firstname3,
	   case when (u_c.last_name not like '') then u_c.last_name else u_g.last_name end as lastname3,
	   case when (u_c.id not like '') then u_c.id else u_g.id end as user_id
		  from vtiger_plannedactions pa
		  join vtiger_businessactions act on act.businessactionsid=pa.action_id
		  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
		  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
		  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
		  left join vtiger_account ac on ac.accountid={$crmId}
		  join vtiger_crmentity crm_c on crm_c.crmid=ac.accountid
		  left join vtiger_users u_c on u_c.id=crm_c.smownerid
		  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
		  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
		  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
		  where pa.plannedbusinessactionsid={$plannedActionId}
		  group by pa.plannedbusinessactionsid,pa.action_id,ac.accountid";
      break;
    case 'Leads':
	  $query = "select act.businessactionsid,act.reference, act.subject, act.template, ld.email,CONCAT(ld.firstname,ld.lastname) as toname,
	   pa.emailfromopt, pa.emailfrom, u_pa1.email1 as email1, u_pa2.email1 as email2, u_pa1.first_name as firstname1,
	   u_pa2.first_name as firstname2, u_pa1.last_name as lastname1, u_pa2.last_name as lastname2,
	   case when (u_c.email1 not like '') then u_c.email1 else u_g.email1 end as email3,
	   case when (u_c.first_name not like '') then u_c.first_name else u_g.first_name end as firstname3,
	   case when (u_c.last_name not like '') then u_c.last_name else u_g.last_name end as lastname3,
	   case when (u_c.id not like '') then u_c.id else u_g.id end as user_id
		  from vtiger_plannedactions pa
		  join vtiger_businessactions act on act.businessactionsid=pa.action_id
		  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
		  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
		  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
		  left join vtiger_leaddetails ld on ld.leadid={$crmId}
		  join vtiger_crmentity crm_c on crm_c.crmid=ld.leadid
		  left join vtiger_users u_c on u_c.id=crm_c.smownerid
		  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
		  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
		  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
		  where pa.plannedbusinessactionsid={$plannedActionId}
		  group by pa.plannedbusinessactionsid,pa.action_id,ld.leadid";
    	break;
  }
  $res = $adb->query($query);
  $data = $adb->getNextRow($res, false);
	$subject = $data['subject'];
	$body = $data['template'];
	$userId = $data['user_id'];
	$action_id = $data['businessactionsid'];
	$action_reference = $data['reference'];
	switch ($setype) {
		case 'Contacts':
			$accountId = $data['account_id'];
			$body = getMergedDescription($body, $crmId, "Contacts");
			$body = getMergedDescription($body, $accountId, "Accounts");
		break;
		case 'Accounts':
			$body = getMergedDescription($body, $crmId, "Accounts");
			$subject = getMergedDescription($subject, $crmId, "Accounts");
		break;
		case 'Leads':
			$body = getMergedDescription($body, $crmId, "Leads");
			$subject = getMergedDescription($subject, $crmId, "Leads");
		break;
	}
	if ($relatedId != $crmId) {
		$relatedModule = getSalesEntityType($relatedId);
		$body = getMergedDescription($body, $relatedId, $relatedModule);
		$subject = getMergedDescription($subject, $relatedId, $relatedModule);
	}
	$body = getMergedDescription($body, $userId, "Users");
	$subject = getMergedDescription($subject, $userId, "Users");
	$recipient = $data['email'];
	$to_name = $data['toname'];
	$emailfromopt = $data['emailfromopt'];
	$firstnamefrom = '';
	$lastnamefrom = '';
	$namefrom = '';
	switch ($emailfromopt) {
	case 'assigneduser':
	  $emailfrom = $data['email1'];
	  $firstnamefrom = $data['firstname1'];
	  $lastnamefrom = $data['lastname1'];
	  break;
	case 'actionuser':
	  $emailfrom = $data['email2'];
	  $firstnamefrom = $data['firstname2'];
	  $lastnamefrom = $data['lastname2'];
	  break;
	case 'contactuser':
	  $emailfrom = $data['email3'];
	  $firstnamefrom = $data['firstname3'];
	  $lastnamefrom = $data['lastname3'];
	  break;
	case 'emailfrom':
	  $emailfrom = $data['emailfrom'];
		$namefrom = $HELPDESK_SUPPORT_NAME;
	  break;
	}
  if (!empty($lastnamefrom) || !empty($firstnamefrom)) {
	  $namefrom = $lastnamefrom.', '.$firstnamefrom;
  }
  // Use sendgrid?
  $rsinfo = $adb->query("SELECT usesg FROM sendgrid_config");
  if ($adb->num_rows ( $rsinfo ) > 0) {
	$sendgrid = $adb->query_result($rsinfo, 0, 'usesg');
  }
  if ($sendgrid) {
		if (empty($userId)) {
			$userId = 1;
		}
      //Creo un menssage
      switch ($setype) {
            case 'Contacts':
                  $data_mss = array(
                    'messagename' => $action_reference,
                    'contact_message' => $crmId,
                    'account_message' => $accountId,
                    'messagetype' => 'email',
                    'email_tplid' => $action_id,
                    'description' => $action_reference,
                    'assigned_user_id' => $userId,
                    );
                  $noemailsqlwhere = 'WHERE messagename=? and email_tplid=? and no_mail=1 and contact_message=?';
            break;
            case 'Accounts':
                  $data_mss = array(
                    'messagename' => $action_reference,
                    'account_message' => $crmId,
                    'messagetype' => 'email',
                    'email_tplid' => $action_id,
                    'description' => $action_reference,
                    'assigned_user_id' => $userId,
                    );
                  $noemailsqlwhere = 'WHERE messagename=? and email_tplid=? and no_mail=1 and account_message=?';
            break;
            case 'Leads':
                  $data_mss = array(
                    'messagename' => $action_reference,
                    'lead_message' => $crmId,
                    'messagetype' => 'email',
                    'email_tplid' => $action_id,
                    'description' => $action_reference,
                    'assigned_user_id' => $userId,
                    );
                  $noemailsqlwhere = 'WHERE messagename=? and email_tplid=? and no_mail=1 and lead_message=?';
            break;
      }
      if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
      	$rdomsg = $adb->pquery("select count(*) from vtiger_messages $noemailsqlwhere",
      		array($action_reference,$action_id,$crmId));
      	if ($adb->query_result($rdomsg,0,0)>0)
      	return false; // devolvemos error. ya existe un mensaje marcado, pueden arreglarlo en la aplicación
      }
      $focus = new Messages();
      $focus->column_fields = $data_mss + $focus->column_fields;
      $focus->mode = '';
      $focus->save('Messages');
      $messageid = $focus->id;
      if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
      	$adb->pquery("UPDATE vtiger_messages SET no_mail = 1 WHERE messagesid = ?", array($messageid));
      	return false; // devolvemos error. ya hemos creado el mensaje y lo hemos marcado, pueden arreglarlo en la aplicación
      }
      $status = sendGridMail($recipient, $body, $subject, array($emailfrom => $namefrom ),
                   "Planned Actions - ".$to_name,'Marketing', $messageid, array());
  } else {
	if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
		return false; // devolvemos error
	}
	$status = send_mail("PALaunch", $recipient, $namefrom, $emailfrom, $subject, $body, '', '', '', '', '');
	if ($status==1) {
	  // Save Email
	  $email = new Emails();
	  $email->column_fields['subject'] = $subject;
	  $email->column_fields["activitytype"] = "Emails";
	  $ddate = date("Y-m-d");
	  $dtime = date("h:m");
	  $email->column_fields["assigned_user_id"] = $current_user->id;
	  $email->column_fields["date_start"] = $ddate;
	  $email->column_fields["time_start"] = $dtime;
	  $email->column_fields["from_email"] = $emailfrom;
	  //Set the flag as 'Webmails' to show up the sent date
	  $email->column_fields["email_flag"] = "SENT";
	  //Save the To field information in vtiger_emaildetails
	  $email->column_fields["saved_toid"] = $recipient;
	  //store the sent date in 'yyyy-mm-dd' format
	  $current_user->date_format = 'yyyy-mm-dd';
	  $email->column_fields["description"] = $body;
	  switch ($setype) {
	  	case 'Contacts':
	  $fieldid = $adb->query_result($adb->query('select fieldid from vtiger_field where tablename="vtiger_contactdetails" and fieldname="email" and columnname="email" and vtiger_field.presence in (0,2)'), 0, 'fieldid');
	  	break;
	  	case 'Accounts':
	  		$fieldid = $adb->query_result($adb->query('select fieldid from vtiger_field where tablename="vtiger_account" and fieldname="email1" and columnname="email1" and vtiger_field.presence in (0,2)'), 0, 'fieldid');
	  	break;
	  	case 'Leads':
	  		$fieldid = $adb->query_result($adb->query('select fieldid from vtiger_field where tablename="vtiger_leaddetails" and fieldname="email" and columnname="email" and vtiger_field.presence in (0,2)'), 0, 'fieldid');
	  	break;
	  }
	  $email->column_fields['parent_id'] = $crmId.'@'.$fieldid.'|';
	  $email->save("Emails");
	}
  }

	if ($status == 1) {
		return true;
	} elseif ($status == 0) {
		return false;
	} else {
		return $status;
	}
}

function sendSMSTemplate($crmId, $plannedActionId) {
  global $adb,$current_user;
  $setype = getSalesEntityType($crmId);
  switch ($setype) {
    case 'Contacts':
          $query = "select act.subject, act.templateonlytext, c.mobile, pa.emailfromopt, pa.emailfrom, u_pa1.email1 as email1, u_pa2.email1 as email2,
           u_pa1.first_name as firstname1, u_pa2.first_name as firstname2, u_pa1.last_name as lastname1, u_pa2.last_name as lastname2, c.accountid as account_id,
           case when (u_c.email1 not like '') then u_c.email1 else u_g.email1 end as email3,
           case when (u_c.first_name not like '') then u_c.first_name else u_g.first_name end as firstname3,
           case when (u_c.last_name not like '') then u_c.last_name else u_g.last_name end as lastname3,
           case when (u_c.id not like '') then u_c.id else u_g.id end as user_id
                  from vtiger_plannedactions pa
                  join vtiger_businessactions act on act.businessactionsid=pa.action_id
                  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
                  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
                  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
                  left join vtiger_contactdetails c on c.contactid={$crmId}
                  join vtiger_crmentity crm_c on crm_c.crmid=c.contactid
                  left join vtiger_users u_c on u_c.id=crm_c.smownerid
                  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
                  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
                  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
                  where pa.plannedbusinessactionsid={$plannedActionId}
                  group by pa.plannedbusinessactionsid,pa.action_id,c.contactid";
      break;
    case 'Accounts':
	  $query = "select act.subject, act.templateonlytext, ac.otherphone as mobile, pa.emailfromopt, pa.emailfrom, u_pa1.email1 as email1, u_pa2.email1 as email2,
	   u_pa1.first_name as firstname1, u_pa2.first_name as firstname2, u_pa1.last_name as lastname1, u_pa2.last_name as lastname2,
	   case when (u_c.email1 not like '') then u_c.email1 else u_g.email1 end as email3,
	   case when (u_c.first_name not like '') then u_c.first_name else u_g.first_name end as firstname3,
	   case when (u_c.last_name not like '') then u_c.last_name else u_g.last_name end as lastname3,
	   case when (u_c.id not like '') then u_c.id else u_g.id end as user_id
		  from vtiger_plannedactions pa
		  join vtiger_businessactions act on act.businessactionsid=pa.action_id
		  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
		  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
		  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
		  left join vtiger_account ac on ac.accountid={$crmId}
		  left join vtiger_accountscf acf on ac.accountid=acf.accountid
		  join vtiger_crmentity crm_c on crm_c.crmid=ac.accountid
		  left join vtiger_users u_c on u_c.id=crm_c.smownerid
		  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
		  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
		  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
		  where pa.plannedbusinessactionsid={$plannedActionId}
		  group by pa.plannedbusinessactionsid,pa.action_id,ac.accountid";
      break;
    case 'Leads':
	  $query = "select act.subject, act.templateonlytext, ldf.mobile as mobile, pa.emailfromopt, pa.emailfrom, u_pa1.email1 as email1, u_pa2.email1 as email2,
	   u_pa1.first_name as firstname1, u_pa2.first_name as firstname2, u_pa1.last_name as lastname1, u_pa2.last_name as lastname2,
	   case when (u_c.email1 not like '') then u_c.email1 else u_g.email1 end as email3,
	   case when (u_c.first_name not like '') then u_c.first_name else u_g.first_name end as firstname3,
	   case when (u_c.last_name not like '') then u_c.last_name else u_g.last_name end as lastname3,
	   case when (u_c.id not like '') then u_c.id else u_g.id end as user_id
		  from vtiger_plannedactions pa
		  join vtiger_businessactions act on act.businessactionsid=pa.action_id
		  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
		  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
		  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
		  left join vtiger_leaddetails ld on ld.leadid={$crmId}
		  left join vtiger_leadaddress ldf on ld.leadid=ldf.leadaddressid
		  join vtiger_crmentity crm_c on crm_c.crmid=ld.leadid
		  left join vtiger_users u_c on u_c.id=crm_c.smownerid
		  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
		  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
		  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
		  where pa.plannedbusinessactionsid={$plannedActionId}
		  group by pa.plannedbusinessactionsid,pa.action_id,ld.leadid";
      break;
  }
  $res = $adb->query($query);
  $data = $adb->getNextRow($res, false);
	$subject = $data['subject'];
	$body = $data['templateonlytext'];
	$userId = $data['user_id'];
	switch ($setype) {
		case 'Contacts':
			$accountId = $data['account_id'];
			$body = getMergedDescription($body, $crmId, "Contacts");
			$body = getMergedDescription($body, $accountId, "Accounts");
		break;
		case 'Accounts':
			$body = getMergedDescription($body, $crmId, "Accounts");
		break;
		case 'Leads':
			$body = getMergedDescription($body, $crmId, "Leads");
		break;
	}
	$body = getMergedDescription($body, $userId, "Users");

	$recipient = $data['mobile'];

	if(!empty($recipient)) {
		SMSNotifier::sendsms($body, $recipient, $current_user->id, $crmId, $setype);
	}

	return true;
}

function createTask($crmId, $plannedActionId) {
  global $adb,$current_user;
  $setype = getSalesEntityType($crmId);
  switch ($setype) {
    case 'Contacts':
          $query = "select act.businessactionsid,act.reference, act.subject, act.template, act.event_type, c.email,CONCAT(c.firstname,c.lastname) as toname,
           pa.emailfromopt, pa.emailfrom, c.accountid as account_id, u_c.id as user_id, vtiger_groups.groupid, crm_pa.smownerid, crm_pa.smcreatorid
                  from vtiger_plannedactions pa
                  join vtiger_businessactions act on act.businessactionsid=pa.action_id
                  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
                  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
                  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
                  left join vtiger_contactdetails c on c.contactid={$crmId}
                  join vtiger_crmentity crm_c on crm_c.crmid=c.contactid
                  left join vtiger_users u_c on u_c.id=crm_c.smownerid
                  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
                  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
                  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
                  where pa.plannedbusinessactionsid={$plannedActionId}
                  group by pa.plannedbusinessactionsid,pa.action_id,c.contactid";
      break;
    case 'Accounts':
	  $query = "select act.businessactionsid,act.reference, act.subject, act.template, act.event_type, ac.email1 as email,ac.accountname as toname,
	   pa.emailfromopt, pa.emailfrom, u_c.id as user_id, vtiger_groups.groupid, crm_pa.smownerid, crm_pa.smcreatorid
		  from vtiger_plannedactions pa
		  join vtiger_businessactions act on act.businessactionsid=pa.action_id
		  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
		  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
		  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
		  left join vtiger_account ac on ac.accountid={$crmId}
		  join vtiger_crmentity crm_c on crm_c.crmid=ac.accountid
		  left join vtiger_users u_c on u_c.id=crm_c.smownerid
		  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
		  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
		  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
		  where pa.plannedbusinessactionsid={$plannedActionId}
		  group by pa.plannedbusinessactionsid,pa.action_id,ac.accountid";
      break;
    case 'Leads':
	  $query = "select act.businessactionsid,act.reference, act.subject, act.template, act.event_type, ld.email,CONCAT(ld.firstname,ld.lastname) as toname,
	   pa.emailfromopt, pa.emailfrom, u_c.id as user_id, vtiger_groups.groupid, crm_pa.smownerid, crm_pa.smcreatorid
		  from vtiger_plannedactions pa
		  join vtiger_businessactions act on act.businessactionsid=pa.action_id
		  join vtiger_crmentity crm_pa on crm_pa.crmid=pa.plannedbusinessactionsid
		  join vtiger_users u_pa1 on u_pa1.id=crm_pa.smownerid
		  join vtiger_users u_pa2 on u_pa2.id=crm_pa.smcreatorid
		  left join vtiger_leaddetails ld on ld.leadid={$crmId}
		  join vtiger_crmentity crm_c on crm_c.crmid=ld.leadid
		  left join vtiger_users u_c on u_c.id=crm_c.smownerid
		  left join vtiger_groups on vtiger_groups.groupid=crm_c.smownerid
		  left join vtiger_users2group on vtiger_users2group.groupid=vtiger_groups.groupid
		  left join vtiger_users u_g on u_g.id=vtiger_users2group.userid
		  where pa.plannedbusinessactionsid={$plannedActionId}
		  group by pa.plannedbusinessactionsid,pa.action_id,ld.leadid";
    break;
  }
  $res = $adb->query($query);
  $data = $adb->getNextRow($res, false);
  $subject = $data['subject'];
  $body = $data['template'];
  $action_id = $data['businessactionsid'];
  $action_reference = $data['reference'];
  $event_type = $data['event_type'];
  $ddate = date("Y-m-d");
  $dtime = date("h:m");
  $linktoentity = '';
  $linktolead = '';
  //Creo una tarea
  switch ($setype) {
     case 'Contacts':
        $linktoentity = $data['account_id'];
        break;
     case 'Accounts':
        $linktoentity = $crmId;
        break;
     case 'Leads':
        $linktolead = $crmId;
        break;
  }
  $emailfromopt = $data['emailfromopt'];
  switch ($emailfromopt) {
  	case 'assigneduser':  // asignado a la acción programada
  		$userId = $data['smownerid'];
  		$_REQUEST['assigntype'] = 'U';
  		break;
  	case 'emailfrom':
  	case 'actionuser':  // creador de la acción programada
  		$userId = $data['smcreatorid'];
  		$_REQUEST['assigntype'] = 'U';
  		break;
  	case 'contactuser':  // asignado a de la entidad
  	default:
  		$userId = $data['user_id'];
  		$_REQUEST['assigntype'] = 'U';
  		if (empty($userId)) {
  			$userId = $data['groupid'];
  			$_REQUEST['assigntype'] = 'T';
  		}
  		break;
  }
  $task = new Task();
  $task->mode = '';
  $task->column_fields['taskname'] = $action_reference;
  $task->column_fields['date_start'] = $ddate;
  $task->column_fields['time_start'] = $dtime;
  $task->column_fields['date_end'] = $ddate;
  $task->column_fields['time_end'] = $dtime;
  $task->column_fields['event_type'] = $event_type;
  $task->column_fields['linktoentity'] = $linktoentity;
  $task->column_fields['linktolead'] = $linktolead;
  $task->column_fields['followup_date'] = '';
  $task->column_fields['followup_time'] = '';
  $task->column_fields['followup_type'] = '--Seleccionar--';
  $task->column_fields['followup_create'] = '0';
  $task->column_fields['taskstate'] = 'Planned';
  $task->column_fields['esitovendita'] = '--None--';
  $task->column_fields['esitochiamata'] = '--Seleccionar--';
  $task->column_fields['contacted'] = '0';
  $task->column_fields['fecha_cita'] = '';
  $task->column_fields["assigned_user_id"] = $userId;
  $task->save('Task');
  return !empty($task->id);
}
 * 
 */
function execCronWS($actid, $typ, $eltyp, $param, $palid, $crontab) {
    //launch action
    global $adb, $log;
    include_once('data/CRMEntity.php');
    $palq = $adb->pquery("select count_execution from vtiger_palaunch where palaunchid=? ", array($palid));
    $count = $adb->query_result($palq, 0, 'count_execution');
    if ($count == '' || $count == 0 || $eltyp=='cron') {
        $update = $adb->pquery("update vtiger_palaunch set count_execution='1' where palaunchid=? and (count_execution='' or count_execution='0' ) ", array($palid));
        if ($adb->database->Affected_Rows($update) != 0 || $eltyp=='cron') {
                include_once('modules/BusinessActions/runJSONAction.php');
                $response = runJSONAction($actid, str_replace("'","\"",$param));
        }
    }
    if ($eltyp == 'cron') {
        require_once 'vendor/autoload.php';
        // Works with complex expressions
 date_default_timezone_set('Europe/Rome');
        $cron = Cron\CronExpression::factory("$crontab");
        $nextd = $cron->getNextRunDate(date("Y-m-d H:i"))->format('Y-m-d');
        $nexty = $cron->getNextRunDate(date("Y-m-d H:i"))->format('H:i');
        $dt = date("Y-m-d");
        $te = date("H:i");
        $st = 'Pending';
        $adb->pquery("update vtiger_palaunch set scheduled_date='$nextd',time_start='$nexty',palaunch_status='$st', processed_date='$dt',time_end='$te' where palaunchid=?", array($palid));
    } else if ($eltyp == 'ws') {
        $st = 'Done';
        $dt = date("Y-m-d");
        $te = date("H:i");
        $adb->pquery("update vtiger_palaunch set palaunch_status='$st', processed_date='$dt',time_end='$te' where palaunchid=?", array($palid));
    }
}

?>
