<?php
/*************************************************************************************************
 * Copyright 2013 JPL TSolucio, S.L. -- This file is a part of vtMktDashboard
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
require_once('Smarty_setup.php');
ini_set('max_execution_time',500);
$smarty = new vtigerCRM_Smarty;

require_once 'modules/Adocmaster/Adocmaster.php';
require_once 'modules/Adocdetail/Adocdetail.php';
global $default_charset,$current_user,$adb;
$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
$machine_factor=$_REQUEST['machine_factor'];
$hr_factor=$_REQUEST['hr_factor'];
$expired_factor=$_REQUEST['expired_factor'];
$warehouse_factor=$_REQUEST['warehouse_factor'];
$transport_factor=$_REQUEST['transport_factor'];
$marketing_factor=$_REQUEST['marketing_factor'];
$params = array($machine_factor,$hr_factor,$expired_factor,$warehouse_factor,$transport_factor,$marketing_factor);
$adb->pquery('update mktdb_calculationparams set machine_factor=?,hr_factor=?,expired_factor=?,warehouse_factor=?,transport_factor=?,marketing_factor=?',$params);
$allcalc=$adb->query("SELECT * FROM mktdb_calculationresults WHERE selected=1 AND qty>0");
$nr_res=$adb->num_rows($allcalc);
if($nr_res>0){
$adocmaster=new Adocmaster();
$adocmaster->column_fields['adocmastername']="Calculations";
$adocmaster->column_fields['assigned_user_id']=1;
$adocmaster->mode='';
$adocmaster->id='';
$adocmaster->save("Adocmaster");
for($i=0;$i<$nr_res;$i++){
$res="Calculating line ".$i." ... ";
$productid=$adb->query_result($allcalc,$i,'crmid');
$iiqty=$adb->query_result($allcalc,$i,'qty');
$productQuery=$adb->pquery("SELECT * FROM vtiger_products WHERE productid=?",array($productid));
$productname=$adb->query_result($productQuery,0,'productname');
$dircostprice=$adb->query_result($productQuery,0,'dircostprice');
$funcostprice=$adb->query_result($productQuery,0,'funcostprice');
$dirpercost=$adb->query_result($productQuery,0,'dirpercost');
$res.=$productname;
$price_formula=($dircostprice+$funcostprice*$machine_factor+$dirpercost*$hr_factor+$expired_factor/$qty + $warehouse_factor/$qty + $transport_factor/$qty)*(1-$marketing_factor);
$res.=" price calculated:".$price_formula."<br>";
//echo $res;
$adocdetail=new Adocdetail();
$adocdetail->column_fields['adocdetailname']="Calculate ".$productname;
$adocdetail->column_fields['nrline']=$i+1;
$adocdetail->column_fields['adoc_quantity']=$qty;
$adocdetail->column_fields['adoc_product']=$productid;
$adocdetail->column_fields['adoc_price']=$price_formula;
$adocdetail->column_fields['adoctomaster']=$adocmaster->id;
$adocdetail->column_fields['assigned_user_id']=1;
$adocdetail->mode='';
$adocdetail->id='';
$adocdetail->save("Adocdetail");
$data = array(
                'adocdetailname' => $adocdetail->column_fields['adocdetailname'],
                'adoc_quantity'=>$adocdetail->column_fields['adoc_quantity'],
                'adoc_product'=>$adocdetail->column_fields['adoc_product'],
                'productname'=>$productname,
		'adoc_price'=>$adocdetail->column_fields['adoc_price'],
                'adoctomasterid'=>$adocmaster->id,
                'adoctomastername' => $adocmaster->column_fields['adocmastername'],
               );
  $info[] = $data;
}
}
$allinfo=json_encode($info);			
 $arr=array(
    array('field'=>'adocdetailname','title'=>'Dettagli contabili'),
    array('field'=>'adoc_quantity','title'=>'Quantità'),
    array('field'=>'adoc_product','title'=>'Prodotto','template'=>'<a href="index.php?module=Products&action=DetailView&record=#=adoc_product#" target="_new">#=productname#</a>'),
    array('field'=>'adoc_price','title'=>'Prezzo'),
    array('field'=>'adoctomasterid','title'=>'Documenti contabili','template'=>'<a href="index.php?module=Adocmaster&action=DetailView&record=#=adoctomasterid#" target="_new">#=adoctomastername#</a>'),

     );
$columns=json_encode($arr);
$arr=array(
    'adocdetailname'=>array('type'=>'string'),
    'adoc_quantity'=>array('type'=>'string'),
    'adoc_product'=>array('type'=>'string'),
    'adoc_price'=>array('type'=>'string'),
    'adoctomasterid'=>array('type'=>'string'),
     );
$fields=json_encode($arr);
$arr=array();
$groups=json_encode($arr);
$smarty->assign('info', $allinfo);
$smarty->assign('fields', $fields);
$smarty->assign('columns', $columns);
$smarty->assign('groups', $groups);
$smarty->display("modules/MarketingDashboard/showResults.tpl");

?>
