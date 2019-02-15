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

require_once('include/freetag/freetag.class.php');

global $default_charset,$current_user;
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
//Contacts
$parentid = isset($_REQUEST["parentid"]) ? $_REQUEST["parentid"] : '';
$parent = isset($_REQUEST["parentid_type"]) ? $_REQUEST["parentid_type"] : '';
$parent_display = isset($_REQUEST["parentid_display"]) ? $_REQUEST["parentid_display"] : '';
$smarty->assign('parentid',$parentid);
$smarty->assign('parentid_display',$parent_display);
$campaignid = isset($_REQUEST["campaignid"]) ? $_REQUEST["campaignid"] : '';
$campaign = isset($_REQUEST["campaignid_type"]) ? $_REQUEST["campaignid_type"] : '';
$campaign_display = isset($_REQUEST["campaignid_display"]) ? $_REQUEST["campaignid_display"] : '';
$smarty->assign('campaignid',$campaignid);
$smarty->assign('campaignid_display',$campaign_display);
$campaigniddel = isset($_REQUEST["campaigniddel"]) ? $_REQUEST["campaigniddel"] : '';
$campaigndel_display = isset($_REQUEST["campaigniddel_display"]) ? $_REQUEST["campaigniddel_display"] : '';
$smarty->assign('campaigniddel',$campaigniddel);
$smarty->assign('campaigniddel_display',$campaigndel_display);
$con_display=isset($_REQUEST['showcon']) ? $_REQUEST['showcon'] : 'none';
$smarty->assign('showdispcon',$con_display);
$conwithtask= empty($_REQUEST["conwithtask"]) ? 0 : 1;
$smarty->assign('conwithtask', ($conwithtask!=0 ? 'checked' : ''));
$connotask= empty($_REQUEST["connotask"]) ? 0 : 1;
$smarty->assign('connotask', ($connotask!=0 ? 'checked' : ''));
$conwithmessage= empty($_REQUEST["conwithmessage"]) ? 0 : 1;
$smarty->assign('conwithmessage', ($conwithmessage!=0 ? 'checked' : ''));
$connomessage= empty($_REQUEST["connomessage"]) ? 0 : 1;
$smarty->assign('connomessage', ($connomessage!=0 ? 'checked' : ''));
//filters of Accounts
$accountfilters=array();
$acc_filter=$adb->query("Select * from vtiger_customview where entitytype='Accounts'");
while($r=$adb->fetch_array($acc_filter)){
    $accountfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('accountfilters',$accountfilters);
$smarty->assign('indexaccfilter',$_REQUEST['selfilteracc']);
//filters of Contacts
$contactfilters=array();
$acc_filter=$adb->query("Select * from vtiger_customview where entitytype='Contacts'");
while($r=$adb->fetch_array($acc_filter)){
    $contactfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('contactfilters',$contactfilters);
$smarty->assign('indexconfilter',$_REQUEST['selfiltercon']);

//Leads
$leadfilters=array();
$lead_filter=$adb->query("Select * from vtiger_customview where entitytype='Leads'");
while($r=$adb->fetch_array($lead_filter)){
    $leadfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('leadfilters',$leadfilters);
$smarty->assign('indexleadfilter',$_REQUEST['selfilterlead']);
$campaignleadid = isset($_REQUEST["campaignleadid"]) ? $_REQUEST["campaignleadid"] : '';
$campaignlead_display = isset($_REQUEST["campaignleadid_display"]) ? $_REQUEST["campaignleadid_display"] : '';
$smarty->assign('campaignleadid',$campaignleadid);
$smarty->assign('campaignleadid_display',$campaignlead_display);
$leadwithtask= empty($_REQUEST["leadwithtask"]) ? 0 : 1;
$smarty->assign('leadwithtask', ($leadwithtask!=0 ? 'checked' : ''));
$leadnotask= empty($_REQUEST["leadnotask"]) ? 0 : 1;
$smarty->assign('leadnotask', ($leadnotask!=0 ? 'checked' : ''));

//parameters
$eventarray=array();
$events=$adb->query("Select * from vtiger_event_type where presence=1");
while($r=$adb->fetch_array($events)){
    $eventarray[$r['event_type']] = getTranslatedString($r['event_type'],'Task');
}
$smarty->assign('eventarray', $eventarray);
$smarty->assign('indexevent', $event);

$visitarray=array();
$visits=$adb->query("Select * from vtiger_sales_stage");
while($r=$adb->fetch_array($visits)){
    $visitarray[$r['sales_stage']]=getTranslatedString($r['sales_stage'],'Potentials');
}
$smarty->assign('visitarray', $visitarray);

$taskstatearray=array();
$states=$adb->query("Select * from vtiger_taskstate where presence=1");
while($r=$adb->fetch_array($states)){
    $taskstatearray[$r['taskstate']]=getTranslatedString($r['taskstate'],'Task');
}
$smarty->assign('taskstatearray', $taskstatearray);
$smarty->assign('selstate', $taskstate);

$selead = empty($_REQUEST["status"]) ? 0 : $_REQUEST["status"];
$statusname=$leadarray[$selead];
$smarty->assign('indexlead', $selead);
$lead_display=isset($_REQUEST['showlead']) ? $_REQUEST['showlead'] : 'none';
$smarty->assign('showdisplead',$lead_display);

$result = $adb->pquery("Select * from vtiger_users where deleted=0 ORDER BY user_name ASC ", array());
$num_rows=$adb->num_rows($result);
$assignedto_details=array(0=>array(getTranslatedString('EntityUser')=>''));
for($i=0;$i<$num_rows;$i++){
    $user=$adb->query_result($result,$i,'id');
    $username=getUserFullName($user).' ('.$adb->query_result($result,$i,'user_name').')';
    $assignedto_details[$user]=array($username=>($assignto==$user ? 'selected' : ''));
}
$smarty->assign('emailtemplateid',$selectedtemplate);
$etinfo = getEntityName('EmailsTemplates', array($selectedtemplate));
$smarty->assign('emailtemplateid_display',$etinfo[$selectedtemplate]);

//convert options
$stcampaignid = isset($_REQUEST["stcampaignid"]) ? $_REQUEST["stcampaignid"] : '';
$stcampaignid_display = isset($_REQUEST["stcampaignid_display"]) ? $_REQUEST["stcampaignid_display"] : '';
$smarty->assign('stcampaignid',$stcampaignid);
$smarty->assign('stcampaignid_display',$stcampaignid_display);

//Messages
$messagefilters=array();
$mess_filter=$adb->query("Select * from vtiger_customview where entitytype='Messages'");
while($r=$adb->fetch_array($mess_filter)){
    $messagefilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('messagefilters',$messagefilters);
$smarty->assign('indexmessfilter',$_REQUEST['selfiltermess']);
$message_display=isset($_REQUEST['showmessage']) ? $_REQUEST['showmessage'] : 'none';
$smarty->assign('showdispmessage',$message_display);
$campaignmessid = isset($_REQUEST["campaignmessid"]) ? $_REQUEST["campaignmessid"] : '';'';
$campaignmess_display = isset($_REQUEST["campaignmessid_display"]) ? $_REQUEST["campaignmessid_display"] : '';
$smarty->assign('campaignmessid',$campaignmessid);
$smarty->assign('campaignmessid_display',$campaignmess_display);

//filters of Contacts
$smarty->assign('indexmessconfilter',$_REQUEST['selfiltermesscon']);

//Potentials
$potentialfilters=array();
$potential_filter=$adb->query("Select * from vtiger_customview where entitytype='Potentials'");
while($r=$adb->fetch_array($potential_filter)){
 $potentialfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('potentialfilters',$potentialfilters);
$smarty->assign('indexpotfilter',$_REQUEST['selfilterpot']);
$potential_display=isset($_REQUEST['showpotential']) ? $_REQUEST['showpotential'] : 'none';
$smarty->assign('showdisppotential',$potential_display);
$campaignpotid = isset($_REQUEST["campaignpotid"]) ? $_REQUEST["campaignpotid"] : '';'';
$campaignpot_display = isset($_REQUEST["campaignpotid_display"]) ? $_REQUEST["campaignpotid_display"] : '';
$smarty->assign('campaignpotid',$campaignpotid);
$smarty->assign('campaignpotid_display',$campaignpot_display);

//Tasks
$taskfilters=array();
$task_filter=$adb->query("Select * from vtiger_customview where entitytype='Task'");
while($r=$adb->fetch_array($task_filter)){
    $taskfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('taskfilters',$taskfilters);
$smarty->assign('indextaskfilter',$_REQUEST['selfiltertask']);
$task_display=isset($_REQUEST['showtask']) ? $_REQUEST['showtask'] : 'none';
$campaigntaskid = isset($_REQUEST["campaigntaskid"]) ? $_REQUEST["campaigntaskid"] : '';'';
$campaigntask_display = isset($_REQUEST["campaigntaskid_display"]) ? $_REQUEST["campaigntaskid_display"] : '';
$smarty->assign('campaigntaskid',$campaigntaskid);
$smarty->assign('campaigntaskid_display',$campaigntask_display);
$smarty->assign('showdisptask',$task_display);

//Accounts
$account2_display=isset($_REQUEST['showaccount']) ? $_REQUEST['showaccount'] : 'none';
$smarty->assign('showdispaccount',$account2_display);
$smarty->assign('accountindexfilteracc',$_REQUEST['accountselfilteracc']);
$smarty->assign('accountindexfiltercon',$_REQUEST['accountselfiltercon']);
$smarty->assign('accountindexfilterpot',$_REQUEST['accountselfilterpot']);
$smarty->assign('accountindexfiltertask',$_REQUEST['accountselfiltertask']);
$campaignaccid = isset($_REQUEST["campaignaccid"]) ? $_REQUEST["campaignaccid"] : '';
$campaignaccid_display = isset($_REQUEST["campaignaccid_display"]) ? $_REQUEST["campaignaccid_display"] : '';
$smarty->assign('campaignaccid',$campaignaccid);
$smarty->assign('campaignaccid_display',$campaignaccid_display);
$campaignacciddel = isset($_REQUEST["campaignacciddel"]) ? $_REQUEST["campaignacciddel"] : '';
$campaignacciddel_display = isset($_REQUEST["campaignacciddel_display"]) ? $_REQUEST["campaignacciddel_display"] : '';
$smarty->assign('campaignacciddel',$campaignacciddel);
$smarty->assign('campaignacciddel_display',$campaignacciddel_display);
$accwithtask= empty($_REQUEST["accwithtask"]) ? 0 : 1;
$smarty->assign('accwithtask', ($accwithtask!=0 ? 'checked' : ''));
$accnotask= empty($_REQUEST["accnotask"]) ? 0 : 1;
$smarty->assign('accnotask', ($accnotask!=0 ? 'checked' : ''));
$accwithmessage= empty($_REQUEST["accwithmessage"]) ? 0 : 1;
$smarty->assign('accwithmessage', ($accwithmessage!=0 ? 'checked' : ''));
$accnomessage= empty($_REQUEST["accnomessage"]) ? 0 : 1;
$smarty->assign('accnomessage', ($accnomessage!=0 ? 'checked' : ''));

//Segments
$segmentfilters=array();
$segment_filter=$adb->query("Select * from vtiger_customview where entitytype='SubsList'");
while($r=$adb->fetch_array($segment_filter)){
 $segmentfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('segmentfilters',$segmentfilters);
$smarty->assign('indexsegfilter',$_REQUEST['selfilterseg']);
$segment_display=isset($_REQUEST['showsegment']) ? $_REQUEST['showsegment'] : 'none';
$smarty->assign('showdispsegment',$segment_display);
$segmentid = isset($_REQUEST["segmentid"]) ? $_REQUEST["segmentid"] : '';'';
$segmentid_display = isset($_REQUEST["segmentid_display"]) ? $_REQUEST["segmentid_display"] : '';
$smarty->assign('segmentid',$segmentid);
$smarty->assign('segmentid_display',$segmentid_display);

// Tags
$tags_display = isset($_REQUEST['showtags']) ? $_REQUEST['showtags'] : 'none';
$hastags = isset($_REQUEST['hastags'])? $_REQUEST['hastags'] : null;
$doesnothavetags = isset($_REQUEST['doesnothavetags'])? $_REQUEST['doesnothavetags'] : null;
$smarty->assign('showdisptags',$tags_display);
$smarty->assign('hastags', $hastags);
$smarty->assign('doesnothavetags', $doesnothavetags);
$taglist = array();
$query = "select * from vtiger_freetags";
$res = $adb->query($query);
while ($row = $adb->getNextRow($res, false)) {
	$taglist[$row['id']] = $row['tag'];
}
$smarty->assign('taglist', $taglist);

// Searching...
// delete previous search results
$adb->query('delete from mktdb_campaignresults where userid='.$current_user->id);
//searching contacts ...
if($con_display=='block'){
$query  = "SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce.crmid, 'c'
           FROM vtiger_contactdetails con
           INNER JOIN vtiger_crmentity ce ON con.contactid = ce.crmid 
           INNER JOIN vtiger_contactaddress ca ON ca.contactaddressid=con.contactid";
$cond=" WHERE ce.deleted = 0 ";

if (!empty($parentid)) {
      $cond.= " AND con.accountid=$parentid ";
}
if (!empty($campaignid)) {
      $query.=" INNER JOIN vtiger_campaigncontrel ccrel ON ccrel.contactid=con.contactid ";
      $cond.= " AND ccrel.campaignid=$campaignid ";
}
if (!empty($campaigniddel)) {
      $cond.= " AND con.contactid not in (select vtiger_contactdetails.contactid from vtiger_contactdetails join vtiger_crmentity on crmid=vtiger_contactdetails.contactid and deleted=0 INNER JOIN vtiger_campaigncontrel ccrel ON ccrel.contactid=vtiger_contactdetails.contactid AND ccrel.campaignid=$campaigniddel )";
}
if($_REQUEST['selfilteracc']!=''){
    $filtername= getCVname($_REQUEST['selfilteracc']);
    if($filtername!="All"){
    $acc_ids=  findRecords('Accounts', $_REQUEST['selfilteracc']);
    $cond.= " AND con.accountid IN(".implode(',',$acc_ids).") ";
    }
//    else
//    $cond.= " AND (con.accountid IN(".implode(',',$acc_ids).") OR con.accountid='' OR con.accountid=0)";
 }
 if($_REQUEST['selfiltercon']!=''){
     $cont_ids=  findRecords('Contacts', $_REQUEST['selfiltercon']);
     $cond.= " AND con.contactid IN(".implode(',',$cont_ids).") ";
 }
 if($conwithtask==1){
     $subquery="SELECT contactid 
                FROM vtiger_cntactivityrel
                INNER JOIN vtiger_task on vtiger_cntactivityrel.activityid=vtiger_task.taskid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignid))
     $subquery.= " AND vtiger_task.campaign=".$campaignid;
    $cond.= " AND con.contactid IN(".$subquery.") ";
 }
 if($connotask==1){
     $subquery="SELECT contactid 
                FROM vtiger_cntactivityrel
                INNER JOIN vtiger_task on vtiger_cntactivityrel.activityid=vtiger_task.taskid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignid))
     $subquery.= " AND vtiger_task.campaign=".$campaignid;
    $cond.= " AND con.contactid NOT IN(".$subquery.") "; 
 }
  if($conwithmessage==1){
     $subquery="SELECT contact_message 
                FROM vtiger_messages 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_messages.messagesid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignid))
     $subquery.= " AND vtiger_messages.campaign_message=".$campaignid;
    $cond.= " AND con.contactid IN(".$subquery.") ";
 }
  if($connomessage==1){
     $subquery="SELECT contact_message 
                FROM vtiger_messages 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_messages.messagesid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignid))
     $subquery.= " AND vtiger_messages.campaign_message=".$campaignid;
    $cond.= " AND con.contactid NOT IN(".$subquery.") ";
 }
$ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
$result = $adb->query($ins.' ( '.$query.$cond.' )');
}

//searching leads...
if($lead_display=='block'){
$query="SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce_lead.crmid, 'l'
        FROM vtiger_leaddetails lead
        INNER JOIN vtiger_crmentity ce_lead ON ce_lead.crmid=lead.leadid 
        INNER JOIN vtiger_leadaddress leadaddr ON lead.leadid=leadaddr.leadaddressid ";
$cond=" WHERE ce_lead.deleted=0 ";

 if($_REQUEST['selfilterlead']!=''){
     $lead_ids=  findRecords('Leads', $_REQUEST['selfilterlead']);
     $query.= " AND lead.leadid IN(".implode(',',$lead_ids).") ";
 }
if(!empty($campaignleadid)){
$query.=" INNER JOIN vtiger_campaignleadrel leadrel ON leadrel.leadid=lead.leadid ";
$cond.= " AND leadrel.campaignid=$campaignleadid ";
}
if($leadwithtask==1){
    $subquery="SELECT linktolead
                FROM vtiger_task
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid
                WHERE vtiger_crmentity.deleted=0 ";
    if (!empty($campaignleadid))
    $subquery.= " AND vtiger_task.campaign=".$campaignleadid  ;
    $cond.= " AND lead.leadid IN(".$subquery.") "; 
 }
 if($leadnotask==1){
   $subquery="SELECT linktolead
                FROM vtiger_task
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid
                WHERE vtiger_crmentity.deleted=0 ";
    if (!empty($campaignleadid))
    $subquery.= " AND vtiger_task.campaign=".$campaignleadid;
    $cond.= " AND lead.leadid NOT IN(".$subquery.") ";
 }
 $ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
 $result = $adb->query($ins.' ( '.$query.$cond.' )');
}

//Seach messages
if($message_display=='block'){
$query  = "SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce.crmid, 'm'
           FROM vtiger_messages mess
           INNER JOIN vtiger_crmentity ce ON mess.messagesid = ce.crmid 
           WHERE ce.deleted = 0 ";
if (!empty($campaignmessid)) {
      $query.= " AND mess.campaign_message=$campaignmessid ";
}
 if($_REQUEST['selfiltermess']!=''){
     $mess_ids=  findRecords('Messages', $_REQUEST['selfiltermess']);
     $query.= " AND mess.messagesid IN(".implode(',',$mess_ids).") ";
 }
 
 if($_REQUEST['selfiltermesscon']!=''){
    $filtername= getCVname($_REQUEST['selfiltermesscon']);
    if($filtername!="All"){
    $cont_ids=  findRecords('Contacts', $_REQUEST['selfiltermesscon']);
    $query.= " AND mess.contact_message IN(".implode(',',$cont_ids).") ";
    }
//    else
//    $query.= " AND (mess.contact_message IN(".implode(',',$cont_ids).") OR mess.contact_message='' OR mess.contact_message=0)";
 }

 $ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
 $result = $adb->query($ins.' ( '.$query.' )');
}

//Seach Potentials
if($potential_display=='block'){
$query  = "SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce.crmid, 'p'
           FROM vtiger_potential pot
           INNER JOIN vtiger_crmentity ce ON pot.potentialid = ce.crmid 
           WHERE ce.deleted = 0 ";
if (!empty($campaignpotid)) {
      $query.= " AND pot.campaignid=$campaignpotid ";
}
 if($_REQUEST['selfilterpot']!=''){
     $pot_ids=  findRecords('Potentials', $_REQUEST['selfilterpot']);
     $query.= " AND pot.potentialid IN(".implode(',',$pot_ids).") ";
 }
 $ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
 $result = $adb->query($ins.' ( '.$query.$cond.' )');
}

//Seach Tasks
if($task_display=='block'){
$query  = "SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce.crmid, 't'
           FROM vtiger_task task
           INNER JOIN vtiger_crmentity ce ON task.taskid = ce.crmid 
           WHERE ce.deleted = 0 ";
if (!empty($campaigntaskid)) {
      $query.= " AND task.campaign=$campaigntaskid ";
}
 if($_REQUEST['selfiltertask']!=''){
     $task_ids=  findRecords('Task', $_REQUEST['selfiltertask']);
     $query.= " AND task.taskid IN(".implode(',',$task_ids).") ";
 }
 $ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
 $result = $adb->query($ins.' ( '.$query.' )');
}

if($account2_display=='block'){
$account2filter=$_REQUEST['accountselfilteracc'];
$contact2filter=$_REQUEST['accountselfiltercon'];
$potential2filter=$_REQUEST['accountselfilterpot'];
$task2filter=$_REQUEST['accountselfiltertask'];
$query  = "SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce.crmid, 'a'
           FROM vtiger_account account
           INNER JOIN vtiger_crmentity ce ON account.accountid = ce.crmid 
           INNER JOIN vtiger_accountbillads accbill on accbill.accountaddressid=account.accountid";
$cond=" WHERE ce.deleted = 0 ";
if (!empty($account2filter)) {
      $accountids=  findRecords('Accounts', $account2filter);
      $cond.= " AND account.accountid IN(".implode(',',$accountids).") ";
}

if (!empty($contact2filter)) {
      $contactids=  findRecords('Contacts', $contact2filter);
      $filtername= getCVname($contact2filter);
      if($filtername!="All" )
      $cond.= " AND account.accountid IN (Select accountid from vtiger_contactdetails WHERE contactid IN(".implode(',',$contactids).") ) ";
}
if (!empty($potential2filter)) {
      $potentialids=  findRecords('Potentials', $potential2filter);
      $filtername= getCVname($potential2filter);
      if($filtername!="All")
      $cond.= " AND account.accountid IN (Select related_to from vtiger_potential WHERE potentialid IN(".implode(',',$potentialids).") ) ";
}
if (!empty($task2filter)) {
      $taskids=  findRecords('Task', $task2filter);
      $filtername= getCVname($task2filter);
      if($filtername!="All")
      $cond.= " AND account.accountid IN (Select linktoentity from vtiger_task WHERE taskid IN(".implode(',',$taskids).") ) ";
}
if (!empty($campaignaccid)) {
      $query.=" INNER JOIN vtiger_campaignaccountrel accrel ON accrel.accountid=account.accountid ";
      $cond.= " AND accrel.campaignid=$campaignaccid ";
}
if (!empty($campaignacciddel)) {
      
     // $query.=" LEFT JOIN vtiger_campaignaccountrel accrel ON accrel.accountid=account.accountid AND accrel.campaignid<>$campaignacciddel ";
      $cond.= " and account.accountid not in (select vtiger_account.accountid from vtiger_account join vtiger_crmentity on crmid=vtiger_account.accountid and deleted=0 INNER JOIN vtiger_campaignaccountrel accrel ON accrel.accountid=vtiger_account.accountid and campaignid=$campaignacciddel)";
}
if($accwithtask==1){
     $subquery="SELECT linktoentity 
                FROM vtiger_task 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignaccid))
     $subquery.= " AND vtiger_task.campaign=".$campaignaccid;
    $cond.= " AND account.accountid IN(".$subquery.") ";
 }
 if($accnotask==1){
      $subquery="SELECT linktoentity 
                FROM vtiger_task 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_task.taskid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignaccid))
     $subquery.= " AND vtiger_task.campaign=".$campaignaccid;
    $cond.= " AND account.accountid NOT IN(".$subquery.") ";
 }
  if($accwithmessage==1){
     $subquery="SELECT account_message 
                FROM vtiger_messages 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_messages.messagesid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignaccid))
     $subquery.= " AND vtiger_messages.campaign_message=".$campaignaccid;
    $cond.= " AND account.accountid IN(".$subquery.") ";
 }
  if($accnomessage==1){
     $subquery="SELECT account_message 
                FROM vtiger_messages 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_messages.messagesid
                WHERE vtiger_crmentity.deleted=0 ";
     if (!empty($campaignaccid))
     $subquery.= " AND vtiger_messages.campaign_message=".$campaignaccid;
    $cond.= " AND account.accountid NOT IN(".$subquery.") ";
 }
 $ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
 $result = $adb->query($ins.' ( '.$query.$cond.' )');
}

//Search Segments
if($segment_display=='block'){
	$query  = "SELECT distinct {$current_user->id} as userid, 0 as selected, crm_rel.crmid, case crm_rel.setype when 'Contacts' then 'c' when 'Leads' then 'l' when 'Accounts' then 'a' end
		FROM vtiger_crmentityrel cer
		join vtiger_crmentity crm_sl on crm_sl.crmid = cer.crmid
		join vtiger_crmentity crm_rel on crm_rel.crmid = cer.relcrmid
		WHERE crm_sl.deleted = 0 and crm_rel.deleted = 0";
	if (!empty($segmentid)) {
		$query.= " AND crm_sl.crmid={$segmentid} ";
	}
	if($_REQUEST['selfilterseg']!=''){
		$seg_ids=  findRecords('SubsList', $_REQUEST['selfilterseg']);
		$query.= " AND crm_sl.crmid IN(".implode(',',$seg_ids).") ";
	}
	$ins = 'insert into mktdb_campaignresults (userid, selected, crmid, entity) ';
	$result = $adb->query($ins.' ( '.$query.$cond.' )');
}

// Search Tags
if ($tags_display == 'block') {
	if (!empty($hastags) && !empty($hastags)) {
		if (!empty($hastags)) {
			$tagIds = implode(',', $hastags);
			$tagNumber = count($hastags);
			$queryHasTags = "select object_id, module, count(distinct t.id) as c
				from vtiger_freetagged_objects o
				join vtiger_freetags t on t.id=o.tag_id
				where t.id in ({$tagIds})
				group by o.object_id
				having c={$tagNumber}";
		}
		if (!empty($doesnothavetags)) {
			$tagIds = implode(',', $doesnothavetags);
			$queryDoesNotHaveTags = "select object_id, module, count(distinct t.id) as c
				from vtiger_freetagged_objects o
				join vtiger_freetags t on t.id=o.tag_id
				where t.id in ({$tagIds})
				group by o.object_id
				having c=0";
		}
		if (empty($hastags)) {
			$query = "select {$current_user->id}, 0, object_id, module from ({$queryDoesNotHaveTags}) q1";
		} elseif (empty($doesnothavetags)) {
			$query = "select {$current_user->id}, 0, object_id, module from ({$queryHasTags}) q1";
		} else {
			$query = "select {$current_user->id}, 0, q1.object_id, q1.module from ({$queryHasTags}) q1 join ({$queryDoesNotHaveTags}) q2 on q1.object_id=q2.object_id";
		}
		$insQuery = "insert into mktdb_campaignresults (userid, selected, crmid, entity) ({$query})";
		$adb->query($insQuery);
	}
}

 $arr=array(
    array('field'=>'selected_id','title'=>'','width'=>'50px',
    	'template'=>'<input name="selected_id" type="checkbox" # if(selected_id==="1") {# checked #}# onclick="check_object(this,\'\');" value="#=recordid#">',
    	'headerTemplate'=>'<input type="checkbox" name="selectall" id="selectall" onclick="toggleSelectAllGrid(this.checked,\'selected_id\',\'1\')">'),
    array('field'=>'modrel','title'=>$mod_strings['Entity']),
    array('field'=>'recordid','title'=>$app_strings['Title'],'template'=>'<a href="index.php?module=#=vtmodule#&action=DetailView&record=#=recordid#" target="_new">#=recordname#</a>'),
    array('field'=>'accountid','title'=>$app_strings['Accounts'],'template'=>'<a href="index.php?module=Accounts&action=DetailView&record=#=accountid#" target="_new">#=accountname#</a>'),
    array('field'=>'cityname','title'=>$mod_strings['City']),
    array('field'=>'provincename','title'=>$mod_strings['Region']),
    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
    array('field'=>'vtmodule','hidden'=>true),
 );
$columns=json_encode($arr);
$arr=array(
    'selected_id'=>array('type'=>'string'),
    'modrel'=>array('type'=>'string'),
    'recordid'=>array('type'=>'string'),
    'accountid'=>array('type'=>'string'),
    'cityname'=>array('type'=>'string'),
    'provincename'=>array('type'=>'string'),
    'user'=>array('type'=>'string')
);
$fields=json_encode($arr);
$arr=array();
$groups=json_encode($arr);

$qtot='select count(*) from mktdb_campaignresults where userid='.$current_user->id;
$total=$adb->query_result($adb->query($qtot),0,0);
$smarty->assign('hideSelectAll', ($total>$list_max_entries_per_page ? 'false' : 'true'));

$smarty->assign('fields', $fields);
$smarty->assign('PAGESIZE', $list_max_entries_per_page);
$smarty->assign('columns', $columns);
$smarty->assign('groups', $groups);
$smarty->assign('ASSIGNEDTO_ARRAY', $assignedto_details);
if (!empty($_REQUEST['campaignleadid'])) {  //Leads
	$smarty->assign('campaignconvert',$_REQUEST['campaignleadid']);
	$smarty->assign('campaignconvert_display',$_REQUEST['campaignleadid_display']);
} elseif (!empty($_REQUEST['campaignid'])) {  // contacts
	$smarty->assign('campaignconvert',$_REQUEST['campaignid']);
	$smarty->assign('campaignconvert_display',$_REQUEST['campaignid_display']);
} elseif (!empty($_REQUEST['campaignaccid'])) {  // accounts
	$smarty->assign('campaignconvert',$_REQUEST['campaignaccid']);
	$smarty->assign('campaignconvert_display',$_REQUEST['campaignaccid_display']);
} elseif (!empty($_REQUEST['campaignmessid'])) {  // message
	$smarty->assign('campaignconvert',$_REQUEST['campaignmessid']);
	$smarty->assign('campaignconvert_display',$_REQUEST['campaignmessid_display']);
} elseif (!empty($_REQUEST['campaignpotid'])) {  // potential
	$smarty->assign('campaignconvert',$_REQUEST['campaignpotid']);
	$smarty->assign('campaignconvert_display',$_REQUEST['campaignpotid_display']);
} elseif (!empty($_REQUEST['campaigntaskid'])) {  // task
	$smarty->assign('campaignconvert',$_REQUEST['campaigntaskid']);
	$smarty->assign('campaignconvert_display',$_REQUEST['campaigntaskid_display']);
}

?>
