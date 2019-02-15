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
//filters of Products
$productfilters=array();
$prod_filter=$adb->query("Select * from vtiger_customview where entitytype='Products'");
while($r=$adb->fetch_array($prod_filter)){
    $productfilters[$r['cvid']]=$r['viewname'];
}
$smarty->assign('productfilters',$productfilters);
$smarty->assign('indexprodfilter',$_REQUEST['selfilterprod']);
$product_display=isset($_REQUEST['showproduct']) ? $_REQUEST['showproduct'] : 'none';
$smarty->assign('showdispproduct',$product_display);
$calculateprodid = isset($_REQUEST["calculateprodid"]) ? $_REQUEST["calculateprodid"] : '';'';
$calculateprodid_display = isset($_REQUEST["calculateprodid_display"]) ? $_REQUEST["calculateprodid_dsplay"] : '';
$smarty->assign('calculateprodid',$calculateprodid);
$smarty->assign('calculateprodid_display',$calculateprodid_display);

//Get parameters of Calculations
//
$params=$adb->query('select * from mktdb_calculationparams');
$machine_factor=$adb->query_result($params,0,'machine_factor');
$hr_factor=$adb->query_result($params,0,'hr_factor');
$expired_factor=$adb->query_result($params,0,'expired_factor');
$warehouse_factor=$adb->query_result($params,0,'warehouse_factor');
$transport_factor=$adb->query_result($params,0,'transport_factor');
$marketing_factor=$adb->query_result($params,0,'marketing_factor');
$smarty->assign('machine_factor',$machine_factor);
$smarty->assign('hr_factor',$hr_factor);
$smarty->assign('expired_factor',$expired_factor);
$smarty->assign('warehouse_factor',$warehouse_factor);
$smarty->assign('transport_factor',$transport_factor);
$smarty->assign('marketing_factor',$marketing_factor);
// Searching...
// delete previous search results
$adb->query('delete from mktdb_calculationresults where userid='.$current_user->id);
//searching contacts ...
if($product_display=='block'){
$query  = "SELECT DISTINCT {$current_user->id} as userid, 0 as selected, ce.crmid, 'prod'
           FROM vtiger_products prod
           INNER JOIN vtiger_crmentity ce ON prod.productid = ce.crmid ";
$cond=" WHERE ce.deleted = 0 AND prod.productcategory='Analisi' ";

if($_REQUEST['selfilterproduct']!=''){
    $filtername= getCVname($_REQUEST['selfilterproduct']);
    if($filtername!="All"){
    $product_ids=  findRecords('Products', $_REQUEST['selfilterproduct']);
    $cond.= " AND prod.productid IN(".implode(',',$product_ids).") ";
    }
 }
 
$ins = 'insert into mktdb_calculationresults (userid, selected, crmid, entity) ';
$result = $adb->query($ins.' ( '.$query.$cond.' )');
}

 $arr=array(
    array('field'=>'selected_id4','title'=>'','width'=>'50px',
    	'template'=>'<input name="selected_id4" type="checkbox" # if(selected_id4==="1") {# checked #}# onclick="check_object(this,\'4\');" value="#=recordid#">',
    	'headerTemplate'=>'<input type="checkbox" name="selectall4" id="selectall4" onclick="toggleSelectAllGrid(this.checked,\'selected_id4\',\'4\')">'),
    array('field'=>'modrel','title'=>$mod_strings['Entity']),
    array('field'=>'recordid','title'=>$app_strings['Title'],'template'=>'<a href="index.php?module=#=vtmodule#&action=DetailView&record=#=recordid#" target="_new">#=recordname#</a>'),
    array('field'=>'qty','title'=>$app_strings['quantity'],'template'=>'<input name="quantity" id="quantity" type="text" style="width:70px;" onchange="updateInputCalculations(\'4\',this.value,#=recordid#)">'),
//    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
//    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
//    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
    array('field'=>'user','title'=>$app_strings['SINGLE_Users']),
    array('field'=>'vtmodule','hidden'=>true),
 );
$columns=json_encode($arr);
$arr=array(
    'selected_id'=>array('type'=>'string'),
    'modrel'=>array('type'=>'string'),
    'recordid'=>array('type'=>'string'),
    'user'=>array('type'=>'string')
);
$fields=json_encode($arr);
$arr=array();
$groups=json_encode($arr);

$qtot='select count(*) from mktdb_calculationresults where userid='.$current_user->id;
$total=$adb->query_result($adb->query($qtot),0,0);
$smarty->assign('hideSelectAll', ($total>$list_max_entries_per_page ? 'false' : 'true'));

$smarty->assign('fields4', $fields);
$smarty->assign('PAGESIZE', $list_max_entries_per_page);
$smarty->assign('columns4', $columns);
$smarty->assign('groups4', $groups);
$smarty->assign('ASSIGNEDTO_ARRAY', $assignedto_details);
if (!empty($_REQUEST['calculateprodid'])) {  //Product
	$smarty->assign('calculationconvert',$_REQUEST['calculateprodid']);
	$smarty->assign('calculationconvert_display',$_REQUEST['calculateprodid_display']);
} 

?>
