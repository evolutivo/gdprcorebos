<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *  Module       : LoggingConf
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/

require_once('include/utils/CommonUtils.php');
require_once('include/utils/utils.php');
require_once('modules/LoggingConf/LoggingUtils.php');
global $adb,$log;
$tab_id= getTabid(vtlib_purify($_REQUEST['Screen']));
$fieldsarray=$_REQUEST['fieldstobeloggedModule'];
$fieldselarray=$_REQUEST['fieldselasticModule'];
$elog=$_REQUEST['elog'];
$relmodule=$_REQUEST['relmodule'];
$denorm=$_REQUEST['denorm'];
//$indextype=$_REQUEST['indextype'];
//$brelastic=$_REQUEST['brelastic'];
$norm=$_REQUEST['norm'];
if($elog=='true')
$type[]='entitylog';
if($denorm=='true')
$type[]='denormalized';
if($norm=='true')
$type[]='normalized';
$type1=implode(",",$type);
//changed to BI logging query
////if($indextype=='' || $indextype==null)
////{
//     $pref=GlobalVariable::getVariable('ip_elastic_indexprefix', '');
////    $random = strtolower(vtlib_purify($_REQUEST['Screen'])).substr( md5(rand()), 0, 7);
//     if($brelastic=='undefined')
//     $ind=strtolower($pref.vtlib_purify($_REQUEST['Screen']));
//     else $ind=strtolower($pref);
////}
////else $ind=$indextype;
// $ip=GlobalVariable::getVariable('ip_elastic_server', '');
//$create=1;
// if(in_array("denormalized",$type) || in_array("normalized",$type))  {
//
// if($brelastic=='undefined'){
//     $sqlFields=Array();
//     $join=Array();
//     $where=Array();
//$endpointUrl2 = "http://$ip:9200/$ind/_mapping/denorm";
//$channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($channel11, CURLOPT_POST, false);
////curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
////curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields1->fields));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//
//$fields1=unserialize($fieldselarray);
//foreach($fields1 as $field)
//{
//    if(is_numeric($field)){
//    $col=getColumnname($field);
//    if(substr($col[1],0,1)=='N')
//    {$coltype='double';
//        $loggingFields[$col[0].$_REQUEST['Screen']]=array("type"=>$coltype,"index"=>"not_analyzed");
//
//    }
//    else if(substr($col[1],0,1)=='D')
//    { $coltype='date';
//    if(substr($col[1],0,2)=='DT') $format='yyyy-MM-dd HH:mm:ss';
//    else $format='yyyy-MM-dd';
//    $loggingFields[$col[0].$_REQUEST['Screen']]=array("type"=>$coltype,"format"=>"$format","index"=>"not_analyzed");
//    }
//    else {$coltype='string';
//    $loggingFields[$col[0].$_REQUEST['Screen']]=array("type"=>$coltype,"index"=>"not_analyzed");}
//    $table=$col[2];
//    $sqlFields[$k]=$table.'.'.$col[0].' as '.$col[0].$_REQUEST['Screen'];
//    $k++;
//     }
//}
//$relmodule1=explode(";",$relmodule);
//if ($relmodule!='' && $relmodule!=null){
//for($j=0;$j<count($relmodule1);$j++){
//  if($relmodule1[$j]!='Nessuno'){
//  $relmodule2=explode(":",$relmodule1[$j]);
// $mod1=getTabid($relmodule2[0]);
//     $ent1=getEntityField($relmodule2[0]);
//    $tbl1=$ent1['tablename'];
//    $id1=$ent1['entityid'];
//    $uitype=$relmodule2[1];
//    $relfield=getFieldFK($_REQUEST['Screen'],$relmodule2[0],$uitype);
//
//    $join[$j1]=" left join $tbl1 $tbl1$j on $tbl1$j.$id1=$relfield[1].$relfield[0] left join vtiger_crmentity crm$relmodule2[0]$j on (crm$relmodule2[0]$j.crmid=$tbl1$j.$id1 and crm$relmodule2[0]$j.deleted=0) ";
//    $where[$j1]="";
//    $j1++;
//
// $fields2=array();
//$query=$adb->pquery("Select fields from vtiger_loggingconfiguration where tabid=? and fields!=''",array($mod1));
//
//$fieldserialized=$adb->query_result($query,0);
//$fields2=unserialize($fieldserialized);
//foreach($fields2 as $field2)
//{
//    if(is_numeric($field2)){
//    $col=getColumnname($field2);
//    if(substr($col[1],0,1)=='N')
//    {$coltype='double';
//    $loggingFields[$col[0].$relmodule2[0]]=array("type"=>$coltype,"index"=>"not_analyzed");
//    }
//    else if(substr($col[1],0,1)=='D')
//    {$coltype='date';
//    if(substr($col[1],0,2)=='DT') $format='yyyy-MM-dd HH:mm:ss';
//    else $format='yyyy-MM-dd';
//    $loggingFields[$col[0].$relmodule2[0]]=array("type"=>$coltype,"format"=>"$format","index"=>"not_analyzed");}
//    else {$coltype='string';
//    $loggingFields[$col[0].$relmodule2[0]]=array("type"=>$coltype,"index"=>"not_analyzed");}
//    if($col[2]=='vtiger_crmentity') $table="crm$relmodule2[0]$j";
//    else $table=$col[2].$j;
//    $sqlFields[$k]=$table.'.'.$col[0].' as '.$col[0].$relmodule2[0].$j;
//    $k++;
//    }
//}
//}
//}
// }
//$ent=getEntityField(vtlib_purify($_REQUEST['Screen']));
//$tbl=$ent['tablename'];
//$id=$ent['entityid'];
//$sqlFields[$k]=$tbl.'.'.$id.' as '.$id.$_REQUEST['Screen'];
//$sqlfld=implode(",",$sqlFields);
//$join=implode(" ",$join);
//$where=implode(" ",$where);
// $loggingFields[$id.$_REQUEST['Screen']]=array("type"=>"string");
//$query1="select $sqlfld from $tbl join vtiger_crmentity on crmid=$id $join where vtiger_crmentity.deleted=0 $where ## $tbl.$id";
//
//if(count($response2->$ind->mappings->denorm)!=0)
//{
//
//$fields1=array("denorm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']'));
//$endpointUrl2 = "http://$ip:9200/$ind/denorm/_mapping?ignore_conflicts=true";
// $channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
////curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
//curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields1));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//$fields111=array("norm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']'));
//$endpointUrl21 = "http://$ip:9200/$ind/norm/_mapping?ignore_conflicts=true";
// $channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl21);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
////curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
//curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields111));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//}
//else{
//$fields1=array("mappings"=>array("denorm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']'),"norm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']')));
//$endpointUrl2 = "http://$ip:9200/$ind";
// $channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
////curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
//curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields1));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//}
//if($response2->acknowledged==true)
//$create=1;
//else $create=0;
////}
// }
// else {
// $br=explode(",",$brelastic);
// for($i=0;$i<count($br);$i++){
// $brid=$br[$i];
// $map=$adb->query("select content,mapname,cbmapid,selected_fields from vtiger_businessrules join vtiger_crmentity c on c.crmid=businessrulesid
//   join vtiger_cbmap on cbmapid=linktomap join vtiger_crmentity c1 on c1.crmid=cbmapid
//   where c.deleted=0 and businessrulesid=$brid");
// $query=str_replace('"','',$adb->query_result($map,0,0));
// $fields31=$adb->query_result($map,0,3);
// $fields32=explode("FROM",$query);
// $fields322=explode(",",str_replace("SELECT","",$fields32[0]));
// $mapname=$adb->query_result($map,0,1);
// $mapid=$adb->query_result($map,0,2);
// $ind1[$i]=strtolower($ind.'_'.preg_replace('/[^A-Za-z0-9\-]/', '', $mapname));
// $fields3=explode(",",str_replace(Array('"','&quot;'),Array("",""),$fields31));
// $sqlFields=array();
// $k1=0;
// foreach($fields3 as $field)
//{
//    $f=explode(".",$field);
//    $f2=explode(" AS ",$f[1]);
//    $fselect=explode(" AS ",$fields322[$k1]);
//    $fselect2=explode(" as ",$fselect[0]);
//    $tabname=trim(str_replace(range(0,9),'',$f[0]));
//    $clname=preg_replace("/\s+/", "",$f2[0]);
//    $col=getColumnname('',$clname,$tabname);
//    if(substr($col[1],0,1)=='N')
//    {$coltype='double';
//    $loggingFields[trim($f[0]).'_'.$clname]=array("type"=>$coltype,"index"=>"not_analyzed");
//    }
//    else if(substr($col[1],0,1)=='D')
//    { $coltype='date';
//    if(substr($col[1],0,2)=='DT') $format='yyyy-MM-dd HH:mm:ss';
//    else $format='yyyy-MM-dd';
//    $loggingFields[trim($f[0]).'_'.$clname]=array("type"=>$coltype,"format"=>"$format","index"=>"not_analyzed");
//    }
//    else {$coltype='string';
//    $loggingFields[trim($f[0]).'_'.$clname]=array("type"=>$coltype,"index"=>"not_analyzed");}
//    $sqlFields[$k1]=trim($fselect2[0]).' AS '.trim($f[0]).'_'.$clname;
//    $k1++;
//}
// $sqlfld=implode(",",$sqlFields);
// $query2="SELECT ".$sqlfld.' FROM '.$fields32[1];
// $adb->pquery("update vtiger_cbmap set content=? where cbmapid=?",array($query2,$mapid));
// //check if index exists for each br
// $endpointUrl2 = "http://$ip:9200/$ind1[$i]/_mapping/denorm";
//$channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($channel11, CURLOPT_POST, false);
////curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
////curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields1->fields));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//
//if(count($response2->$ind->mappings->denorm)!=0)
//{
//
//$fields1=array("denorm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']'));
//$endpointUrl2 = "http://$ip:9200/$ind1[$i]/denorm/_mapping?ignore_conflicts=true";
// $channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
////curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
//curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields1));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//$fields111=array("norm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']'));
//$endpointUrl21 = "http://$ip:9200/$ind1[$i]/norm/_mapping?ignore_conflicts=true";
//$channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl21);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
////curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
//curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields111));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//}
//else{
//$fields1=array("mappings"=>array("denorm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']'),"norm"=>array("properties"=>$loggingFields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\', \'dd-MM-yyyy\', \'date_optional_time\']')));
//$endpointUrl2 = "http://$ip:9200/$ind1[$i]";
// $channel11 = curl_init();
////curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
//curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
////curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
//curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fields1));
//curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
//curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
//$response2 = json_decode(curl_exec($channel11));
//}
//if($response2->acknowledged==true)
//$create=1;
//else $create=0;
// }
// $ind=implode(",",$ind1);
// }
// }
// if(($create)<1)
// $ind='';

//Updating the database
if($elog=='undefined' && $denorm=='undefined' && $norm=='undefined')
{//break;
}
else{
$update_params[]=$fieldsarray;
$update_params[]=$type1;
$q=$adb->query("show columns from vtiger_loggingconfiguration where Field='fieldselastic'");
if($adb->num_rows($q)==1){
$setfields=',fieldselastic=?';
$update_params[]=$fieldselarray;
}
$q2=$adb->query("show columns from vtiger_loggingconfiguration where Field='relmodules'");
if($adb->num_rows($q2)==1){
$setfields.=',relmodules=?';
if($relmodule=="")
$update_params[]=0;
else
$update_params[]=$relmodule;
}
$update_params[]=$tab_id;
$update_query = "update vtiger_loggingconfiguration set fields=?,type=? $setfields  where tabid=?";
}
$query=$adb->pquery($update_query, $update_params);
echo $query;
?>
