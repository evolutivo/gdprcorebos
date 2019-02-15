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

require_once('include/utils/CommonUtils.php');
require_once('data/CRMEntity.php');
include_once("modules/Emails/mail.php");
include_once("modules/MarketingDashboard/sendEmail.php");
require_once('config.inc.php');
require_once('modules/MarketingDashboard/BatchManager.php');
require_once('include/fields/DateTimeField.php');
global $app_strings,$default_theme,$default_charset,$root_directory;
global $current_user,$currentModule,$adb,$log,$sendgrid_server;
$mytab=vtlib_purify($_REQUEST['mytab']);

switch ($mytab) {
	case 1:
		$objdate=new DateTimeField($_REQUEST['due_date']);
		$mydate=$objdate->getDBInsertDateValue();
		//if (isset($_REQUEST['title']) and  isset($_REQUEST['due_date']) and isset($_REQUEST['selvisit']) and isset($_REQUEST['selevent']) and isset($_REQUEST['assignto']) and isset($_REQUEST['selstate'])) { 
		 $params = array($_REQUEST['title'],$mydate,$_REQUEST['selvisit'],$_REQUEST['selevent'],$_REQUEST['assignto'],$_REQUEST['due_time'],$_REQUEST['desc_name'],$_REQUEST['selstate'],$_REQUEST['descr'],0,$_REQUEST['emailtemplateid'],'','');
		 $adb->pquery('update entityconverter set title=?,duedate=?,status=?,event=?,assignto=?,duetime=?,description=?,taskstate=?,descrname=?,assigncontact=?,emailtemplate=?,assignaccount=?,relatedmodules=?',$params);
		//}
		break;
	case 2:
		$objdate=new DateTimeField(NULL);
		$mydate=$objdate->getDBInsertDateValue();
		$params = array(0,$mydate,'','',0,'','','','',$_REQUEST['assignto_contact'],'','','');
		$adb->pquery('update entityconverter set title=?,duedate=?,status=?,event=?,assignto=?,duetime=?,description=?,taskstate=?,descrname=?,assigncontact=?,emailtemplate=?,assignaccount=?,relatedmodules=?',$params);
		break;
	case 3:
		$objdate=new DateTimeField(NULL);
		$mydate=$objdate->getDBInsertDateValue();
		$selectedmodules=implode(":::",$_REQUEST['relatedmodules']);
		$params = array(0,$mydate,'','',0,'','','','','','',$_REQUEST['assignto_account'],$selectedmodules);
		$adb->pquery('update entityconverter set title=?,duedate=?,status=?,event=?,assignto=?,duetime=?,description=?,taskstate=?,descrname=?,assigncontact=?,emailtemplate=?,assignaccount=?,relatedmodules=?',$params);
		break;
       case 4:
		$params = array($_REQUEST['machine_factor'],$_REQUEST['hr_factor'],$_REQUEST['expired_factor'],$_REQUEST['warehouse_factor'],$_REQUEST['transport_factor'],$_REQUEST['transport_factor']);
		$adb->pquery('update mktdb_calculationparams set machine_factor=?,hr_factor=?,expired_factor=?,warehouse_factor=?,transport_factor=?,transport_factor=?',$params);
		break;
}
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
$emailtemplates=$adb->query_result($params,0,'emailtemplate');
$relatedmodules=explode(":::",$adb->query_result($params,0,'relatedmodules'));
$nrofselected=$adb->query('select count(*) as nrresults from mktdb_campaignresults where selected=1');
$nrbegining=$adb->query('select count(*) as nrresults from mktdb_campaignresults');
$nrbegining1=$adb->query_result($nrbegining,0,'nrresults');
//$selectednr=$nrofselected-$nrbegining/2;
$selectednr=$adb->query_result($nrofselected,0,'nrresults');
$selectednr1=$selectednr-$nrbegining1/2;
//var_dump($selectednr1);exit;
// Form variables
$convertto = $_REQUEST['convertto'];
if ($convertto == "po") {
	$convertto = "Potentials";
} elseif ($convertto == "task") {
	$convertto = "Task";
}
elseif ($convertto == "sendsms") {
	$convertto = "SMS";
}
else {
	$convertto = "Messages";
}
$campaignconvert = $_REQUEST['campaignconvert'];
$selvisit = $_REQUEST['selvisit'];
$contactid = $_REQUEST['contactid'];
$emailtemplateid = $_REQUEST['emailtemplateid'];
$emailselected = $_REQUEST['emailselected'];
//
$delay=$_REQUEST['delaytest'];
$datestarting=$_REQUEST['startdate'];
$timestarting=$_REQUEST['starttime'];
$maxnrofjobs=$_REQUEST['maxnrofjobs'];
$nomeutente=$_REQUEST['nomeutente'];
//$log->debug("nomeutente".$nomeutente);
//$log->debug("maxnrofjobs".$maxnrofjobs);
$data = compact('title', 'due_date', 'visit', 'event', 'assignto', 'due_time', 'desc_name', 'taskstate', 'descr', 'assigntocontacts', 'assigntoaccounts', 'emailtemplates', 'relatedmodules', 'convertto', 'campaignconvert', 'selvisit', 'contactid', 'emailtemplateid','emailselected');

$BatchManager = BatchManager::getInstance();

switch ($mytab) {
	case 1:
	  $BatchManager->addJob('CampaignBatchJob', $data,$delay,$datestarting,$timestarting,$maxnrofjobs,$nomeutente,$selectednr);
		break;
	case 2:
	  $BatchManager->addJob1('CreateContactsBatchJob', $data);
		break;
	case 3:
	  $BatchManager->addJob1('AssignmentBatchJob', $data);
		break;
}
?>
<script>
location.href = "index.php?module=MarketingDashboard&action=index&mytab=4";
</script>

