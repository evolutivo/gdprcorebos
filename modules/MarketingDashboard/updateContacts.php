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

require_once('modules/MarketingDashboard/BatchManager.php');
require_once('include/freetag/freetag.class.php');

global $adb,$log,$current_user;

// We assign all selected accounts, contacts and leads to the given campaign
$new_campaign=vtlib_purify($_REQUEST['campid']);
if (!empty($new_campaign)) {
	$sql = "insert ignore into vtiger_campaigncontrel (campaignid,contactid,campaignrelstatusid)
		select $new_campaign,crmid,1
		from mktdb_campaignresults
		where selected = 1 and userid={$current_user->id} and entity='c'";
	$adb->query($sql);  // Contactos
	$sql = "insert ignore into vtiger_campaignaccountrel (campaignid,accountid,campaignrelstatusid)
		select $new_campaign,crmid,1
		from mktdb_campaignresults
		where selected = 1 and userid={$current_user->id} and entity='a'";
	$adb->query($sql);  // Accounts
	$sql = "insert ignore into vtiger_campaignleadrel (campaignid,leadid,campaignrelstatusid)
		select $new_campaign,crmid,1
		from mktdb_campaignresults
		where selected = 1 and userid={$current_user->id} and entity='l'";
	$adb->query($sql);  // Leads
	$sql = "insert ignore into vtiger_campaignleadrel (campaignid,leadid,campaignrelstatusid)
		select distinct $new_campaign,lead_message,1
		from mktdb_campaignresults
		inner join vtiger_messages on crmid=messagesid
		where selected = 1 and userid={$current_user->id} and entity='m' and lead_message is not null and lead_message != 0";
	$adb->query($sql);  // Leads/Message
	$sql = "insert ignore into vtiger_campaigncontrel (campaignid,contactid,campaignrelstatusid)
		select distinct $new_campaign,contact_message,1
		from mktdb_campaignresults
		inner join vtiger_messages on crmid=messagesid
		where selected = 1 and userid={$current_user->id} and entity='m' and contact_message is not null and contact_message != 0";
	$adb->query($sql);  // Contacts/Message
	$sql = "insert ignore into vtiger_campaignaccountrel (campaignid,accountid,campaignrelstatusid)
		select distinct $new_campaign,account_message,1
		from mktdb_campaignresults
		inner join vtiger_messages on crmid=messagesid
		where selected = 1 and userid={$current_user->id} and entity='m' and account_message is not null and account_message != 0";
	$adb->query($sql);  // Accounts/Message
}

// Tags
$addTag = $_REQUEST['add_tag'];
$removeTag = $_REQUEST['remove_tag'];
if (!empty($addTag) || !empty($removeTag)) {
	$freetag = new freetag();
	$query = "select vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_campaignresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_campaignresults.crmid where mktdb_campaignresults.selected=1 and mktdb_campaignresults.userid={$current_user->id}";
	$res = $adb->query($query);
	while ($row = $adb->getNextRow($res, false)) {
		$id = $row['crmid'];
		$modulename = $row['setype'];
		if (!empty($addTag)) {
			$freetag->tag_object($current_user->id, $id, $addTag, $modulename);
		}
		if (!empty($removeTag)) {
			$freetag->delete_object_tag($current_user->id, $id, $removeTag);
		}
	}
}

$sequencerId = $_REQUEST['sequencer_id'];
$plannedactionId = $_REQUEST['plannedaction_id'];
$subslistId = $_REQUEST['subslist_id'];

$BatchManager = BatchManager::getInstance();

if (is_numeric($sequencerId)) {
	$data = array(
		'module' => 'Sequencers',
		'id' => $sequencerId,
	);
	$BatchManager->addJob('RelateBatchJob', $data);
}

if (is_numeric($plannedactionId)) {
	$data = array(
		'module' => 'PlannedActions',
		'id' => $plannedactionId,
	);
	$BatchManager->addJob('RelateBatchJob', $data);
}

if (is_numeric($subslistId)) {
	$data = array(
		'module' => 'SubsList',
		'id' => $subslistId,
	);
	$BatchManager->addJob('RelateBatchJob', $data);
}

echo "ok";
?>
