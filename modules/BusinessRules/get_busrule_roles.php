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

require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $log,$adb;
$id=$this->get_template_vars('ID');
$sel=$this->get_template_vars('fldvalue');
$options=array();

$arr_evo=explode(',',$sel);
$sql="Select rolename,roleid
    from vtiger_role";
$result=$adb->pquery($sql,$par);
$options[] = array('','','' );
for($i=0;$i<$adb->num_rows($result);$i++)
{
   $currentValId=$adb->query_result($result,$i,'roleid');
   $currentValName=$adb->query_result($result,$i,'rolename');
   if(in_array(trim($currentValId),$arr_evo)){
           $chk_val = "selected";
   }else{
           $chk_val = '';
   }
   $options[] = array($currentValName,$currentValId,$chk_val );
}
$this->assign("fldvalue",$options);