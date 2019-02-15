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

//Contacts creation
$account_display=isset($_REQUEST['showacc']) ? $_REQUEST['showacc'] : 'none';
$smarty->assign('showdispacc',$account_display);
$contact_display=isset($_REQUEST['showcon1']) ? $_REQUEST['showcon1'] : 'none';
$smarty->assign('showdispcon1',$contact_display);
$result = $adb->pquery("Select * from vtiger_users where deleted=0 ORDER BY user_name ASC ", array());
$num_rows=$adb->num_rows($result);
$assignedto_details1=array(0=>array(getTranslatedString('EntityUser')=>''));
for($i=0;$i<$num_rows;$i++){
    $user1=$adb->query_result($result,$i,'id');
    $username1=getUserFullName($user1).' ('.$adb->query_result($result,$i,'user_name').')';
    $assignedto_details1[$user1]=array($username1=>($assigntocontacts==$user1 ? 'selected' : ''));
}
$nocontacts= empty($_REQUEST["nocontacts"]) ? 0 : 1;
$smarty->assign('nocontacts', ($nocontacts!=0 ? 'checked' : ''));
$withcontacts= empty($_REQUEST["withcontacts"]) ? 0 : 1;
$smarty->assign('withcontacts', ($withcontacts!=0 ? 'checked' : ''));
$smarty->assign('indexaccfilter1',$_REQUEST['selfilteracc1']);
$smarty->assign('indexconfilter1',$_REQUEST['selfiltercon1']);
$smarty->assign('indexaccountconfilter',$_REQUEST['selfilteraccountcon']);
$smarty->assign('indexfilterconacc1',$_REQUEST['selfilteracccon1']);

// delete previous search results
$adb->query('delete from mktdb_contactresults where userid='.$current_user->id);

// Search on Accounts
if($account_display=='block') {
  $query  = "SELECT {$current_user->id} as userid, 0 as selected, ce.crmid, 'a'
           FROM vtiger_account acc
           INNER JOIN vtiger_crmentity ce ON acc.accountid = ce.crmid ";
  $cond=" WHERE ce.deleted = 0 ";

  if($_REQUEST['selfilteracc1']!='') {
    $account_ids=  findRecords('Accounts', $_REQUEST['selfilteracc1']);
    $cond.= " AND acc.accountid IN(".implode(',',$account_ids).") ";
  }
  if($_REQUEST['selfilteraccountcon']!='') {
    $filtername= getCVname($_REQUEST['selfilteraccountcon']);
    if($filtername!="All"){
      $cont_ids=  findRecords('Contacts', $_REQUEST['selfilteraccountcon']);
      $cond.= " AND acc.accountid IN (Select accountid from vtiger_contactdetails WHERE contactid IN(".implode(',',$cont_ids).") ) ";
    }
    // else
    // $cond.= " AND acc.accountid IN (Select accountid from vtiger_contactdetails WHERE contactid IN(".implode(',',$cont_ids).")  OR accountid='' OR accountid=0 )";    
  }

 if($nocontacts==1)
    $cond.= " AND acc.accountid NOT IN(SELECT distinct accountid 
                                     FROM vtiger_contactdetails cd 
                                     INNER JOIN vtiger_crmentity cecon ON cecon.crmid=cd.contactid
                                     WHERE cecon.deleted=0 AND cd.accountid IS NOT NULL)";
  if($withcontacts==1)
    $cond.= " AND acc.accountid IN(SELECT distinct accountid 
                                     FROM vtiger_contactdetails cd 
                                     INNER JOIN vtiger_crmentity cecon ON cecon.crmid=cd.contactid
                                     WHERE cecon.deleted=0 AND cd.accountid IS NOT NULL)";

	$ins = 'insert into mktdb_contactresults (userid, selected, crmid, entity) ';
	$result = $adb->query($ins.' ( '.$query.$cond.' )');
}

// Search on Contacts
if($contact_display=='block') {
  $query  = "SELECT {$current_user->id} as userid, 0 as selected, ce.crmid, 'c'
           FROM vtiger_contactdetails cd
           INNER JOIN vtiger_crmentity ce ON cd.contactid = ce.crmid 
           LEFT JOIN vtiger_account acc ON acc.accountid=cd.accountid ";
  $cond=" WHERE ce.deleted = 0 AND cd.email='' and cd.accountid IS NOT NULL and acc.email1<>'' ";

  if($_REQUEST['selfiltercon1']!='') {
    $contact_ids=  findRecords('Contacts', $_REQUEST['selfiltercon1']);
    $cond.= " AND cd.contactid IN(".implode(',',$contact_ids).") ";
  }

  if($_REQUEST['selfilteracccon1']!=''){
    $account_ids=  findRecords('Accounts', $_REQUEST['selfilteracccon1']);
    $cond.= " AND cd.accountid IN(".implode(',',$account_ids).") ";
  }

  $ins = 'insert into mktdb_contactresults (userid, selected, crmid, entity) ';
  $result = $adb->query($ins.' ( '.$query.$cond.' )');
  $log->fatal($ins.' ( '.$query.$cond.' )');
}

 $arr=array(
    array('field'=>'selected_id2','title'=>'','width'=>'50px',
    	'template'=>'<input name="selected_id2" type="checkbox" # if(selected_id2==="1") {# checked #}# onclick="check_object(this,\'2\');" value="#=recordid#">',
    	'headerTemplate'=>'<input type="checkbox" name="selectall2" id="selectall2" onclick="toggleSelectAllGrid(this.checked,\'selected_id2\',\'2\')">'),
    array('field'=>'modrel','title'=>$mod_strings['Entity']),
    array('field'=>'recordid','title'=>$app_strings['Title'],'template'=>'<a href="index.php?module=#=vtmodule#&action=DetailView&record=#=recordid#" target="_new">#=recordname#</a>'),
    array('field'=>'accountid','title'=>$app_strings['Accounts'],'template'=>'<a href="index.php?module=Accounts&action=DetailView&record=#=accountid#" target="_new">#=accountname#</a>'),
    array('field'=>'email','title'=>$mod_strings['Email']),
    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
    array('field'=>'vtmodule','hidden'=>true),
);
$columns1=json_encode($arr);
$arr=array(
    'selected_id2'=>array('type'=>'string'),
    'modrel'=>array('type'=>'string'),
    'recordid'=>array('type'=>'string'),
    'accountid'=>array('type'=>'string'),
    'email'=>array('type'=>'string'),
    'user'=>array('type'=>'string')
     );
$fields1=json_encode($arr);
$arr=array();
$groups1=json_encode($arr);

$qtot='select count(*) from mktdb_contactresults where userid='.$current_user->id;
$total=$adb->query_result($adb->query($qtot),0,0);
$smarty->assign('hideSelectAll1', ($total>$list_max_entries_per_page ? 'false' : 'true'));
$smarty->assign('fields1', $fields1);
$smarty->assign('columns1', $columns1);
$smarty->assign('groups1', $groups1);
$smarty->assign("ASSIGNEDTO_ARRAY1", $assignedto_details1);
?>
