<?php
/*************************************************************************************************
* Copyright 2012-2013 OpenCubed  --  This file is a part of vtMktDashboard.
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
*  Module       : MarketingDashboard
*  Version      : 1.9
*  Author       : OpenCubed
*************************************************************************************************/

$Vtiger_Utils_Log = false;
global $adb, $current_user;
include_once('vtlib/Vtiger/Module.php');

function evvtWrite2Log($msg) {
	$writeLog = true;
	if ($writeLog) {
		$logFile='logs/sendgridlogs.log';
		error_log("$msg\n",3,$logFile);
	}
}
$input = @file_get_contents('php://input');
$sendgridevents = json_decode($input);
/* now in the $request variable we have this object:
array (
  0 => 
  stdClass::__set_state(array(
     'email' => 'joe@tsolucio.com',
     'smtp-id' => '<1381941003.525ebf0b54427@erpevolutivo.com>',
     'timestamp' => 1381941015,
     'response' => '250 2.0.0 Ok: queued as 8786E2F0039 ',
     'category' => '-7',
     'event' => 'delivered',
     'crmid' => 11,
  )),
)
*/

$date=date('l jS \of F Y h:i:s A');
$LogContent = "****\nSendGrid Notification $date \n";
if (!is_array($sendgridevents) and !isset($sendgridevents[0])) {
	evvtWrite2Log("$LogContent Error Input Information");
	die();
}
foreach ($sendgridevents as $request) {
	foreach ($request as $key => $value) {
          if(!is_object($value))
	  $LogContent.= "Key: $key; Value: $value \n";
	}
	evvtWrite2Log($LogContent);

	$recipient=trim($request->email);
	$event=$request->event;
	$combid=explode('-',$request->category);
	$category=$combid[0];
	$crmid=$request->crmid;
	$crmtype=getSalesEntityType($crmid);
	if ($crmtype!='Messages' and $crmtype!='Emails') {
		evvtWrite2Log("Error CRM Type: $crmtype - $crmid");
		continue;
	}
	evvtWrite2Log("CRM Type: $crmtype - $crmid");
	if ($crmtype=='Messages') {
		$user = new Users();
		$userid = 1;
		$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
		$em = new VTEventsManager($adb);
		// Initialize Event trigger cache
		$em->initTriggerCache();
		$entityData = VTEntityData::fromEntityId($adb, $crmid);
		//Event triggering code
		$em->triggerEvent("vtiger.entity.beforesave", $entityData);
		//Event triggering code ends
		$updtable = 'vtiger_messages';
		$updindex = 'messagesid';
	} else {
		$updtable = 'vtiger_emaildetails';
		$updindex = 'emailid';
	}
	$msg = '';
	$query = '';
	switch($event) {
	   case 'open':
	       $query="Update $updtable set $event=$event+1 where $updindex=?";
	       break;
	   case 'bounce':
	       $query="Update $updtable set $event=$event+1 where $updindex=?";
	       $msg = $request->type.'('.$request->status.') : '.$request->reason;
	       break;
	   case 'spamreport':
	   case 'dropped':  // when sendgrid drops it, it is because we have been warned somehow, better to stop sending
	       $msg = $request->reason;
	   case 'unsubscribe':
	       $query="Update $updtable set $event=1 where $updindex=?";
	       // mark emailoptout fields in modules
	       if ($crmtype=='Messages') {
	         $msgrs = $adb->query('select contact_message,account_message,lead_message from vtiger_messages where messagesid='.$crmid);
	         $messages = $adb->fetch_array($msgrs);
	         if (!empty($messsages['contact_message'])) {
	         	$adb->query('update vtiger_contactdetails set emailoptout=1 where contactid='.$messages['contact_message']);
	         }
	         if (!empty($messages['account_message'])) {
	         	$adb->query('update vtiger_account set emailoptout=1 where accountid='.$messages['account_message']);
	         }
	         if (!empty($messages['lead_message'])) {
	         	$adb->query('update vtiger_leaddetails set emailoptout=1 where leadid='.$messages['lead_message']);
	         }
	         if (file_exists('modules/MarketingDashboard/processSendNotificationHook.php')) {
	         	include 'modules/MarketingDashboard/processSendNotificationHook.php';
	         }
	       } else {
	         $msgrs = $adb->query('select idlists from vtiger_emaildetails where emailid='.$crmid);
	         $messages = $adb->fetch_array($msgrs);
	         $idlists = explode('|', $messages['idlists']);
	         foreach ($idlists as $eid) {
	         	list($id,$void) = explode('@',$eid);
	         	$cet = getSalesEntityType($id);
	         	switch ($cet) {
	         		case 'Contacts':
	         			$crs = $adb->query('select email from vtiger_contactdetails where contactid='.$id);
	         			$ce = $adb->fetch_row($crs);
	         			if ($ce['email']==$recipient) {
	         				$adb->query('update vtiger_contactdetails set emailoptout=1 where contactid ='.$id);
	         				break 2;
	         			}
	         			break;
	         		case 'Accounts':
	         			$crs = $adb->query('select email1 from vtiger_account where accountid='.$id);
	         			$ce = $adb->fetch_row($crs);
	         			if ($ce['email']==$recipient) {
	         				$adb->query('update vtiger_account set emailoptout=1 where accountid='.$id);
	         				break 2;
	         			}
	         			break;
	         		case 'Leads':
	         			$crs = $adb->query('select email from vtiger_leaddetails where leadid='.$id);
	         			$ce = $adb->fetch_row($crs);
	         			if ($ce['email']==$recipient) {
	         				$adb->query('update vtiger_leaddetails set emailoptout=1 where leadid='.$id);
	         				break 2;
	         			}
	         			break;
	         	}
	         	if (file_exists('modules/MarketingDashboard/processSendNotificationHook.php')) {
	         		include 'modules/MarketingDashboard/processSendNotificationHook.php';
	         	}
	         }
	       }
	       break;
	   case 'delivered':
	       $query="Update $updtable set $event=1 where $updindex=?";
	       $msg = $request->reason;
	       break;
	   case 'click':
	       $query="Update $updtable set clicked=clicked+1 where $updindex=?";
	       if ($crmtype=='Messages') {
		       $rsdesc = $adb->pquery('select description from vtiger_crmentity where crmid=?',array($crmid));
		       $desc = $adb->query_result($rsdesc,0,'description');
		       $msg = $desc.$request->url.';';
		       $adb->pquery('update vtiger_messages set lasturlclicked=? where messagesid=?',array($request->url,$crmid));
	       }
	}
	evvtWrite2Log($query);
	if (!empty($query)) {
		$adb->pquery($query,array($crmid));
		$adb->pquery('update vtiger_crmentity set modifiedtime=now() where crmid=?',array($crmid));
	}
	if ((!empty($query) or !empty($msg)) and $crmtype=='Messages') {
		if (!empty($msg)) $adb->pquery('update vtiger_crmentity set description=? where crmid=?',array($msg,$crmid));
		$adb->pquery('update vtiger_messages set lasteventtime=now() where messagesid=?',array($crmid));
		//Event triggering code
		$em->triggerEvent("vtiger.entity.aftersave", $entityData);
		//Event triggering code ends
	}
} // foreach all events
?>
