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

function findFilters($mod){
	global $adb;
	$filterarray=array();
	$queryfilter=$adb->pquery("Select * from vtiger_customview where entitytype=? ORDER BY cvid",array($mod));
	while($queryfilter && $r=$adb->fetch_array($queryfilter)){
	    $filterarray[$r['cvid']]=$r['viewname'];
	}
	return $filterarray;
}

function findRecords($modulename,$filtername){
  global $adb,$current_user;
  $moduleinstance=  CRMEntity::getInstance($modulename);
  $indexid=$moduleinstance->table_index;

  $allrecordids=array();
  $queryGenerator_ent = new QueryGenerator($modulename, $current_user);
  $query=$adb->query($queryGenerator_ent->getCustomViewQueryById($filtername));
  while($query && $f=$adb->fetch_array($query)) {
	array_push($allrecordids,$f[$indexid]);
  }
  if(empty($allrecordids))
    array_push($allrecordids,-1);
  return $allrecordids;
}

function findFields($tab){
	global $adb;
	$modname = getTabModuleName($tab);
	$allfields=array();
	$query=$adb->pquery("SELECT * FROM vtiger_field WHERE tabid=?",array($tab));
	while($query && $row=$adb->fetch_array($query)){
	    $fielname=$row['fieldname'];
	    $allfields[$fielname]=getTranslatedString($row['fieldlabel'],$modname);
	}
	return $allfields;
}
?>
