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
global $adb,$currentModule,$current_user,$log;

$respuesta=array();

$mktdbtab = vtlib_purify($_REQUEST['mktdbtab']);
switch ($mktdbtab) {
	case 2:
		$tblname = 'mktdb_contactresults';
		break;
	case 3:
		$tblname = 'mktdb_assignresults';
		break;
        case 4:
		$tblname = 'mktdb_calculationresults';
		break;
	default:
		$tblname = 'mktdb_campaignresults';
		$mktdbtab = '';
		break;
}

switch ($_REQUEST['exec']) {
	case 'List':
		$conditions = '';
		if (!empty($_REQUEST['filter']['filters']) and is_array($_REQUEST['filter']['filters'])) {
			foreach ($_REQUEST['filter']['filters'] as $ftr) {
				if (empty($ftr['value'])) continue;
				$fval = addslashes($ftr['value']);
				$fval = $fval == 'true' ? 1 : 0;
				$conditions.=' and selected ';  // .$ftr['field']
				switch ($ftr['operator']) {
					case 'eq':
						$op='=';
						break;
					case 'neq':
						$op='!=';
						break;
					case 'startswith':
						$fval=$fval.'%';
						$op='like';
						break;
					case 'endswith':
						$fval='%'.$fval;
						$op='like';
						break;
					case 'contains':
						$op='like';
						$fval='%'.$fval.'%';
						break;
					default: $op='=';
					break;
				}
				$conditions.=" $op '$fval'";
			}
		}
		$qemp="select selected, crmid, entity
		 from $tblname
		 where userid=".$current_user->id.$conditions;
		$qtot="select count(*)
		from $tblname
		where userid=".$current_user->id.$conditions;
		$total=$adb->query_result($adb->query($qtot),0,0);
/* 		if (isset($_REQUEST['sort'])) {
			$qemp.=' order by ';
			$ob='';
			foreach ($_REQUEST['sort'] as $sf) {
				if ($sf['field']=='apellidos') {
					$ob.='lastname1 '.$sf['dir'].',lastname2 '.$sf['dir'].',';
				} else {
					$ob.=$sf['field'].' '.$sf['dir'].',';
				}
			}
			$qemp.=trim($ob,',');
		} */
		$qemp.= ' order by crmid ';
		if (isset($_REQUEST['page']) and isset($_REQUEST['pageSize'])) {
			$qemp.=' limit '.(($_REQUEST['page']-1)*$_REQUEST['pageSize']).', '.$_REQUEST['pageSize'];
		}
		$rssr=$adb->query($qemp);
		while ($sr=$adb->fetch_array($rssr)) {
			switch ($sr['entity']) {
				case 'c':
					$mod = 'Contacts';
					$sql = "select smownerid,vtiger_contactdetails.accountid,concat(firstname,' ',lastname) as ename,accountname,mailingcity as ecity,mailingstate as estate, email
					from vtiger_contactdetails
					inner join vtiger_crmentity on crmid=contactid
					inner join vtiger_contactaddress on contactaddressid=contactid
					left join vtiger_account on vtiger_contactdetails.accountid=vtiger_account.accountid
					where contactid=".$sr['crmid'];
					break;
				case 'l':
					$mod = 'Leads';
					$sql = "select smownerid,0 as accountid,concat(firstname,' ',lastname) as ename,company as accountname,city as ecity,state as estate, email
					from vtiger_leaddetails
					inner join vtiger_crmentity on crmid=leadid
					inner join vtiger_leadaddress on leadaddressid=leadid
					where leadid=".$sr['crmid'];
					break;
				case 'm':
					$mod = 'Messages';
					$sql = "select smownerid,account_message as accountid,messagesname as ename,accountname,'' as ecity,'' as estate, '' as email
					from vtiger_messages
					inner join vtiger_crmentity on crmid=messagesid
					left join vtiger_account on vtiger_account.accountid = account_message
					where messagesid=".$sr['crmid'];
					break;
				case 'p':
					$mod = 'Potentials';
					$sql = "select smownerid,potentialname as ename,'' as ecity,'' as estate, '' as email,
							case when (setype = 'Accounts') then related_to else '' end as accountid,
							case when (setype = 'Accounts') then accountname else '' end as accountname
					from vtiger_potential
					inner join vtiger_crmentity on crmid=potentialid
					left join vtiger_account on vtiger_account.accountid = related_to
					where potentialid=".$sr['crmid'];
					break;
				case 't':
					$mod = 'Task';
					$sql = "select smownerid,linktoentity as accountid,taskname as ename,accountname,'' as ecity,'' as estate, '' as email
					from vtiger_task
					inner join vtiger_crmentity on crmid=taskid
					left join vtiger_account on vtiger_account.accountid = linktoentity
					where taskid=".$sr['crmid'];
					break;
				case 'a':
					$mod = 'Accounts';
					$sql = "select smownerid,accountid,accountname as ename,accountname,bill_city as ecity,bill_state as estate, email1 as email
					from vtiger_account
					inner join vtiger_crmentity on crmid=accountid
					inner join vtiger_accountbillads on vtiger_account.accountid = accountaddressid
					where accountid=".$sr['crmid'];
					break;
                               case 'prod':
					$mod = 'Products';
					$sql = "select smownerid,productid,productname as ename
					from vtiger_products
					inner join vtiger_crmentity on crmid=productid
					where productid=".$sr['crmid'];
					break;
			}
			//echo $sql;
			$rsent = $adb->query($sql);
			if ($rsent) {
				$ent = $adb->fetch_array($rsent);
			} else {
				$ent = array();
			}
                        if($sr['entity']=='prod')
			$respuesta['results'][]=array(
					"selected_id$mktdbtab"=>$sr['selected'],
					'modrel'=>getTranslatedString($mod,$mod),
					'vtmodule'=>$mod,
					'recordid'=>$sr['crmid'],
					'recordname'=>$ent['ename'],
					'user'=>html_entity_decode(getUserFullName($ent['smownerid']),ENT_QUOTES,$default_charset),
					);
                        else
                        $respuesta['results'][]=array(
					"selected_id$mktdbtab"=>$sr['selected'],
					'modrel'=>getTranslatedString($mod,$mod),
					'vtmodule'=>$mod,
					'recordid'=>$sr['crmid'],
					'recordname'=>$ent['ename'],
					'accountid'=>$ent['accountid'],
					'accountname'=>html_entity_decode($ent['accountname'],ENT_QUOTES,$default_charset),
					'cityname'=>html_entity_decode($ent['ecity'],ENT_QUOTES,$default_charset),
					'provincename'=>html_entity_decode($ent['estate'],ENT_QUOTES,$default_charset),
					'email' => $ent['email'],
					'user'=>html_entity_decode(getUserFullName($ent['smownerid']),ENT_QUOTES,$default_charset),
					);
		}
		$respuesta['total']=$total;
	break;
	case 'Destroy':
	break;
	case 'Update':
		$adb->pquery("update $tblname set selected=? where crmid in (".$_REQUEST['crmid'].') and userid=?',
			array(($_REQUEST['selid']=='true' || $_REQUEST['selid']=="1") ? 1 : 0,$current_user->id));
	break;
	case 'UpdateAll':
		$adb->pquery("update $tblname set selected=? where userid=?",
		array(($_REQUEST['selid']=='true' || $_REQUEST['selid']=="1") ? 1 : 0,$current_user->id));
	break;
	case 'AreAllSelected':
		$rssel = $adb->pquery("select count(*) from $tblname where selected=0 and userid=?",array($current_user->id));
		$cnt = $adb->query_result($rssel,0,0);
		$respuesta = array('allselected'=>($cnt==0 ? 1 : 0));
	break;
	case 'OneSelected':
		$rssel = $adb->pquery("select 1 from $tblname where selected=1 and userid=? limit 1",array($current_user->id));
		$respuesta = array('oneselected'=>($adb->num_rows($rssel)>0 ? 1 : 0));
	break;
	case 'AtLeastOneEntity':
		$ent = $_REQUEST['entities'];
		if (!empty($ent)) {
			$rssel = $adb->pquery("select 1 from $tblname where selected=1 and entity in (".$ent.') and userid=? limit 1',array($current_user->id));
			$respuesta = array('oneselected'=>($adb->num_rows($rssel)>0 ? 1 : 0));
		} else {
			$respuesta = array('oneselected'=> 0);
		}
                break;
        case 'UpdateQTY':
                if($_REQUEST['crmid']!='')
		$adb->pquery("update $tblname set qty=? where crmid in (".$_REQUEST['crmid'].') and userid=?',
			array($_REQUEST['qty'],$current_user->id));
        case 'oneSelectedMandatory':
                  
                $rssel = $adb->pquery("select 1 from $tblname where selected=1 and qty>0 and userid=?  limit 1",array($current_user->id));
		$respuesta = array('oneselected'=>($adb->num_rows($rssel)>0 ? 1 : 0));
	break;
}
echo json_encode($respuesta);
