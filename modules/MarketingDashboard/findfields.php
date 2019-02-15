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

require_once('include/utils/utils.php');
require_once('modules/MarketingDashboard/utils.php');
require_once('include/utils/CommonUtils.php');
require_once('include/ListView/ListView.php');

$rel=$_POST['related'];

if($rel==0){
 if($_POST['id']==0)
  $moduleid=$_POST['origin']; 
 else{
 $relationInfo = getRelatedListInfoById($_POST['id']);
 $moduleid = $relationInfo['relatedTabId'];
 }
}
else
$moduleid=$_POST['id'];

$allfields=array();
$allmodules=array();
$result=array();

$allfields=findFields($moduleid);
$result['fields']=$allfields;

if($rel==1){
$allmodules[0]=getTranslatedString(getTabModuleName($moduleid));

$relatedlists=isPresentRelatedLists(getTabModuleName($moduleid));
foreach ($relatedlists as $key=>$value){
    $allmodules[$key]=getTranslatedString($value);
}
$result['modules']=$allmodules;
$relatedfields=findFields($moduleid);
//$allKeys = array_keys($relatedlists);
//$relatedfields= findFields(getTabid($relatedlists[$allKeys[0]]));
$result['relatedfields']=$relatedfields;
}
echo json_encode($result);
?>
