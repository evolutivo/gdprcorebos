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

//Manage Assignments
$account_display1=isset($_REQUEST['show31']) ? $_REQUEST['show31'] : 'none';
$smarty->assign('show31',$account_display1);
$contact_display1=isset($_REQUEST['show32']) ? $_REQUEST['show32'] : 'none';
$smarty->assign('show32',$contact_display1);
$smarty->assign('indexaccfilter2',$_REQUEST['selfilteracc2']);
$smarty->assign('indexconfilter2',$_REQUEST['selfiltercon2']);
$result = $adb->pquery("Select * from vtiger_users where deleted=0 ORDER BY user_name ASC ", array());
$num_rows=$adb->num_rows($result);
$assignedto_accounts=array(0=>array(getTranslatedString('EntityUser')=>''));
for($i=0;$i<$num_rows;$i++){
    $user1=$adb->query_result($result,$i,'id');
    $username1=getUserFullName($user1).' ('.$adb->query_result($result,$i,'user_name').')';
    $assignedto_accounts[$user1]=array($username1=>($assigntoaccounts==$user1 ? 'selected' : ''));
}
$relatedlists=isPresentRelatedLists("Accounts");
$allmodules=array();
$allmodules[0]=array(getTranslatedString('Accounts')=>(in_array(0,$relatedmodules)? 'selected':''));
foreach ($relatedlists as $key=>$value){
    $allmodules[$key]=array($value=>(in_array($key,$relatedmodules)? 'selected':''));
}
$allmodules[10000000]=array('ModComments'=>''); // hard coded modcomments
$smarty->assign("RELATED_MODULES",$allmodules);

// delete previous search results
$adb->query('delete from mktdb_assignresults where userid='.$current_user->id);

// Search on Accounts
if($account_display1=='block') {
  $query  = "SELECT {$current_user->id} as userid, 0 as selected, ce.crmid, 'a'
           FROM vtiger_account acc
           INNER JOIN vtiger_crmentity ce ON acc.accountid = ce.crmid ";
  $cond=" WHERE ce.deleted = 0 ";

  if($_REQUEST['selfilteracc2']!=''){
    $account2_ids=  findRecords('Accounts', $_REQUEST['selfilteracc2']);
    $cond.= " AND acc.accountid IN(".implode(',',$account2_ids).") ";
  }

  $ins = 'insert into mktdb_assignresults (userid, selected, crmid, entity) ';
  $result = $adb->query($ins.' ( '.$query.$cond.' )');
}

// Search on Contacts
if($contact_display1=='block') {
  $query  = "SELECT {$current_user->id} as userid, 0 as selected, ce.crmid, 'c'
           FROM vtiger_contactdetails cd
           INNER JOIN vtiger_crmentity ce ON cd.contactid = ce.crmid 
           LEFT JOIN vtiger_account acc ON acc.accountid=cd.accountid ";
  $cond=" WHERE ce.deleted = 0  ";

  if($_REQUEST['selfiltercon2']!=''){
    $contact_ids=  findRecords('Contacts', $_REQUEST['selfiltercon2']);
    $cond.= " AND cd.contactid IN(".implode(',',$contact_ids).") ";
  }
  $ins = 'insert into mktdb_assignresults (userid, selected, crmid, entity) ';
  $result = $adb->query($ins.' ( '.$query.$cond.' )');
}

 $arr=array(
    array('field'=>'selected_id3','title'=>'','width'=>'50px',
    	'template'=>'<input name="selected_id3" type="checkbox" # if(selected_id3==="1") {# checked #}# onclick="check_object(this,\'3\');" value="#=recordid#">',
    	'headerTemplate'=>'<input type="checkbox" name="selectall3" id="selectall3" onclick="toggleSelectAllGrid(this.checked,\'selected_id3\',\'3\')">'),
    array('field'=>'modrel','title'=>$mod_strings['Entity']),
    array('field'=>'recordid','title'=>$app_strings['Title'],'template'=>'<a href="index.php?module=#=vtmodule#&action=DetailView&record=#=recordid#" target="_new">#=recordname#</a>'),
    array('field'=>'accountid','title'=>$app_strings['Accounts'],'template'=>'<a href="index.php?module=Accounts&action=DetailView&record=#=accountid#" target="_new">#=accountname#</a>'),
    array('field'=>'email','title'=>$mod_strings['Email']),
    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
    array('field'=>'vtmodule','hidden'=>true),
);
$columns3=json_encode($arr);
$arr=array(
    'selected_id3'=>array('type'=>'string'),
    'modrel'=>array('type'=>'string'),
    'recordid'=>array('type'=>'string'),
    'accountid'=>array('type'=>'string'),
    'email'=>array('type'=>'string'),
    'user'=>array('type'=>'string')
);
$fields3=json_encode($arr);
$arr=array();
$groups3=json_encode($arr);

$modulearray=array("6"=>getTranslatedString("Accounts"),"4"=>getTranslatedString("Contacts"));
$firstfields=  findFields('6');
$relatedfields=  findFields('6');

$qtot='select count(*) from mktdb_assignresults where userid='.$current_user->id;
$total=$adb->query_result($adb->query($qtot),0,0);
$smarty->assign('hideSelectAll3', ($total>$list_max_entries_per_page ? 'false' : 'true'));
$smarty->assign('fields3', $fields3);
$smarty->assign('columns3', $columns3);
$smarty->assign('groups3', $groups3);
$smarty->assign('relatedlists',$relatedlists);
$smarty->assign('modulearray',$modulearray);
$smarty->assign('allfields',$firstfields);
$smarty->assign('relatedfields',$relatedfields);
$smarty->assign("ASSIGNEDTO_ACCOUNTS", $assignedto_accounts);
?>
