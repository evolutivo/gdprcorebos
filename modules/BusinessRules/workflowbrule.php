<?php
/**
 *************************************************************************************************
 * Copyright 2015 OpenCubed -- This file is a part of OpenCubed coreBOS customizations.
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
 *  Module       : BusinessRules
 *  Version      : 5.5.0
 *  Author       : OpenCubed.
 *************************************************************************************************/
function updateaction($entity){
global $adb;    
$orig_pot = $entity->getId();
$orig_id = explode("x",$orig_pot);
$orig_id = $orig_id[1];
$q=$adb->pquery("select linktomap from vtiger_businessrules where businessrulesid=$orig_id");
$mapid=$adb->query_result($q,0,0);
if(!empty($mapid)){
$mapfocus=  CRMEntity::getInstance("cbMap");
$mapfocus->retrieve_entity_info($mapid,"cbMap");
$condition=$mapfocus->getMapSQL(); 

$adb->pquery('Update vtiger_businessactions'
        . ' set businessrules_action=?'
        . ' where linktobrules =?',array($condition,$orig_id));
} 
}
?>
