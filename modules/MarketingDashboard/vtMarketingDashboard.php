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

require_once('data/CRMEntity.php');
require_once('include/utils/CommonUtils.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('Smarty_setup.php');
require_once('modules/MarketingDashboard/utils.php');
require_once('include/fields/DateTimeField.php');
ini_set('max_execution_time',500);
$smarty = new vtigerCRM_Smarty;
global $adb,$log,$app_strings,$current_user,$theme,$currentModule,$mod_strings,$image_path,$category,$default_charset;
$thementity=$theme;
$mytab=$_REQUEST['mytab'];

$params=$adb->query('select * from entityconverter');
$title=$adb->query_result($params,0,'title');
$due_date=$adb->query_result($params,0,'duedate');
$visit=$adb->query_result($params,0,'status');
$event=$adb->query_result($params,0,'event');
$assignto=$adb->query_result($params,0,'assignto');
$due_time=$adb->query_result($params,0,'duetime');
$desc_name=html_entity_decode($adb->query_result($params,0,'description'), ENT_QUOTES, $default_charset);
$taskstate=$adb->query_result($params,0,'taskstate');
$descr=html_entity_decode($adb->query_result($params,0,'descrname'), ENT_QUOTES, $default_charset);
$assigntocontacts=$adb->query_result($params,0,'assigncontact');
$assigntoaccounts=$adb->query_result($params,0,'assignaccount');
$selectedtemplate=$adb->query_result($params,0,'emailtemplate');
$relatedmodules=explode(":::",$adb->query_result($params,0,'relatedmodules'));
$smarty->assign('selentityname', ($title==0 ? 'selected' : ''));
$smarty->assign('selfixname', ($title==1 ? 'selected' : ''));
$smarty->assign('seldescname', ($title==2 ? 'selected' : ''));
$smarty->assign('seldescentity', ($title==3 ? 'selected' : ''));
$smarty->assign('seldescaccount', ($title==4 ? 'selected' : ''));

if($due_date!='') {
    $due_dateobj=new DateTimeField($due_date);
    $dat=$due_dateobj->getDisplayDate();
} else {
    $due_dateobj=new DateTimeField(date("Y-m-d"));
    $dat=$due_dateobj->getDisplayDate();
}
//$due_dateobj2=new DateTimeField(date("Y-m-d"));
//$dat2=$due_dateobj->getDisplayDate();
$dat2=date('d-m-Y');
$smarty->assign('due_date_val2',$dat2);
$smarty->assign('due_date_val', $dat);
$smarty->assign('selvisitval', $visit);
$smarty->assign('desc_name_val',$desc_name);
$smarty->assign('descrname_val',$descr);
if($due_time=='' || is_null($due_time)) {
	$due_time=date('H:i');
}
$smarty->assign('due_time_val', $due_time);
$due_time2=date('H:i');
$smarty->assign('due_time_val2',$due_time2);

include_once('modules/MarketingDashboard/campaign_management.php');
include_once('modules/MarketingDashboard/create_contacts.php');
include_once('modules/MarketingDashboard/calculation.php');
include_once('modules/MarketingDashboard/massive_assign.php');

$smarty->assign('mytab', $mytab);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('CATEGORY', $category);
$smarty->assign('THEME', $thementity);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$dat_fmt = $current_user->date_format;
$smarty->assign("dateStr", $dat_fmt);
$dat_fmt2=$current_user->date_format;
$smarty->assign("dateStr2",$dat_fmt);
$smarty->assign("dateFormat", (($dat_fmt == 'dd-mm-yyyy')?'dd-mm-yy':(($dat_fmt == 'mm-dd-yyyy')?'mm-dd-yy':(($dat_fmt == 'yyyy-mm-dd')?'yy-mm-dd':''))));
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);

$smarty->assign('campaignid',$_REQUEST['campaignid']);
$smarty->assign('stcampaignid',$_REQUEST['stcampaignid']);
$smarty->display('modules/MarketingDashboard/index.tpl');

?>
