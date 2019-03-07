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
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/
class HistoryLogHandler extends VTEventHandler {
  
  private $modulesRegistered;

  function setModulesRegistered($map)
  {
  $this->modulesRegistered=$map;
  }
  function handleEvent($eventName, $entityData) {
    global $log, $adb,$current_user,$app_strings;
    $userid=$current_user->id;
    $moduleName = $entityData->getModuleName(); 
    $tabid=getTabid($moduleName);
    $this->setModulesRegistered($this->getModulesFieldMap($moduleName));
    if (!isset($this->modulesRegistered[$moduleName])) {
      return;
    }
    $type=explode(",",$this->getEntitylogtype($moduleName));
    $queryel=getqueryelastic($tabid);
    $indextype=getEntitylogindextype($tabid);
    //This block of code needs to be adapted : table_name, tableid, name, and fields you wish to be considered for logging
    $table = $this->modulesRegistered[$moduleName]['tablename'];
    $tableid = $table.'.'.$this->modulesRegistered[$moduleName]['primarykey'];
    $fields = $this->modulesRegistered[$moduleName]['fields'];
    //end of block code to be adapted
    $Id = $entityData->getId();
    //getting the roles
    $log->debug("Enter Handler for beforesave event...");    
    if($eventName == 'vtiger.entity.beforesave')
    {
      $log->debug("Enter Handler for beforesave event...");
      if(!empty($Id)) {
      $listquery = getListQuery($moduleName,"and ".$tableid."=?",1);   
      $query=$adb->pquery($listquery,array($Id));
        if($adb->num_rows($query) > 0) {
          for  ($i=0;$i<count($fields);$i++)
          {
            $entityData->old[$i]=$adb->query_result($query,0,$fields[$i]);
            $log->debug('old fields '.$entityData->old[$i].' '.$fields[$i]);
          }
        }
      }
      $log->debug("Exit Handler for beforesave event...");
    }
    if($eventName == 'vtiger.entity.aftersave') {   
      $log->debug("Enter Handler for aftersave event...");
      require_once("modules/Users/CreateUserPrivilegeFile.php");
      require_once("include/utils/GetUserGroups.php");
   //if($defaultOrgSharingPermission[getTabid("$moduleName")] == 3){
      $q=$adb->pquery("select smownerid from vtiger_crmentity  where crmid=?",array($entityData->getId()));
      $owner=$adb->query_result($q,0,"smownerid");
      $role=$adb->query("select parentrole,vtiger_role.roleid from vtiger_user2role join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid  where vtiger_user2role.userid=$owner");
      $current_user_roles=$adb->query_result($role,0,"roleid");
    //$roleid=$adb->query_result($role,0,"parentrole");
        $parrol=getParentRole($current_user_roles);
        $roleid=implode("::",$parrol);
        $userGroupFocus=new GetUserGroups();
        $userGroupFocus->getAllUserGroups($owner);
        $current_user_groups= $userGroupFocus->user_groups;
      if(count($current_user_groups)!=0)
      $grpid='::'.implode("::",$current_user_groups);
      $def_org_share=getAllDefaultSharingAction();
      $arr=getUserModuleSharingObjects($moduleName,$owner,$def_org_share ,$current_user_roles,$parrol,$current_user_groups);
      $gr=$adb->pquery("select * from vtiger_groups where groupid=?",array($owner));
      if($adb->num_rows($gr)==0){
      if(count(array_keys($arr['read']['ROLE']))!=0)
      $roleid.='::'.implode('::',array_keys($arr['read']['ROLE']));}
      else $roleid.=implode('::',array_keys($arr['read']['GROUP']));
      $roleid.='::'.$owner;
      $listquery = getListQuery($moduleName,"and ".$tableid."=".$Id,1)  ;
      $query=$adb->query($listquery); 
      if($adb->num_rows($query) > 0) {
        for  ($i=0;$i<count($fields);$i++)
        {
          $news[$i]=$adb->query_result($query,0,$fields[$i]);         
        }
      }
     $act = "";
     $act1='';
     $cr=false;
     if($queryel!='' && $queryel!=null)
     $fields1=$adb->pquery("$queryel and $tableid=?",array($entityData->getId()));
     else $fields1=array();
     for ($i=0;$i<count($fields);$i++)
      {  if($news[$i]!=$entityData->old[$i]) {         
      $act2='fieldname='. $fields[$i]. ';oldvalue='. $entityData->old[$i].';newvalue='. $news[$i].";";
      $act=array();
          $act[] = array(
            'fieldname' => $fields[$i],
            'oldvalue' => $entityData->old[$i],
            'newvalue' => $news[$i],
            );       
    $dt=date("Y-m-d H:i:s"); 
    if(!empty($act)) { 
    if(in_array('entitylog',$type)){
    require_once('modules/Entitylog/Entitylog.php');
    require_once("data/CRMEntity.php" );
    $focus=new Entitylog();
    $focus->column_fields['entitylogname']=$app_strings['LBL_CHANGES_RECORD'].' '.$Id.' '.$app_strings['LBL_OF_MODULE'].' '.$moduleName.' '.$app_strings['LBL_AT'].' '.$dt;
    $focus->column_fields['assigned_user_id']=$userid;
    $focus->column_fields['user']=$userid;
    $focus->column_fields['relatedto']=$entityData->getId();
    $focus->column_fields['tabid']=$tabid;
    $focus->column_fields['finalstate']=$act2;
    $focus->saveentity("Entitylog");}
    $ip=GlobalVariable::getVariable('ip_elastic_server', '');
    $fl=$adb->pquery("select fieldlabel from vtiger_elastic_indexes where elasticname='$indextype'",array());
    if($adb->num_rows($fl)!=0)
    $fldlabel1=explode(",", $adb->query_result($fl,0,0));
    if(in_array('normalized',$type)) {
    $endpointUrl2 = "http://$ip:9200/$indextype/norm";
    $eid=$entityData->getId();
    $fld=array();
    $fld['roles']=$roleid;
    $fld['changedvalues']=$act2;
    $fld['userchange']=$userid;
    $fld['urlrecord']="<a href='index.php?module=$moduleName&action=DetailView&record=$eid'>Details</a>";
    unset($fields1->fields[0]);
    $in=0;
    foreach($fields1->fields as $key => $value) {
        if( floatval($key)) {
             unset($fields1->fields[$key]);
        }
        else {
        $fldlabel=$fldlabel1[$in];
        $fld["$fldlabel"]=$value;
        $in++;}
    }
    $channel11 = curl_init();
    //curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
    curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($channel11, CURLOPT_POST, true);
    //curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fld));
    curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
    $response2 = curl_exec($channel11);
  }
  if(in_array('denormalized',$type)) {
    $getid=$entityData->getId();
    $endpointUrl12 = "http://$ip:9200/$indextype/denorm/_search?pretty";
    $pk=$this->modulesRegistered[$moduleName]['primarykey'];
    $fields11 =array('query'=>array("term"=>array("$pk"=>"$getid")));
    $channel1 = curl_init();
    curl_setopt($channel1, CURLOPT_URL, $endpointUrl12);
    curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($channel1, CURLOPT_POST, true);
    //curl_setopt($channel1, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fields11));
    curl_setopt($channel1, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($channel1, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($channel1, CURLOPT_TIMEOUT, 1000);
    $response1 = json_decode(curl_exec($channel1));
    //if(strstr($response1->error,'IndexMissingException'))
    //{$ij=1;
    //} 
    $ij=$response1->hits->hits[0]->_id;
    if($ij!='' && $ij!=null && $response1->hits->total!=0 ){
    $endpointUrl2 = "http://$ip:9200/$indextype/denorm/$ij";
    $eid=$entityData->getId();
    $fld=array();
    $fld['roles']=$roleid;
    $fld['changedvalues']=$act2;
    $fld['userchange']=$userid;
    $fld['urlrecord']="<a href='index.php?module=$moduleName&action=DetailView&record=$eid'>Details</a>";
    unset($fields1->fields[0]);
    $in=0;
    foreach($fields1->fields as $key => $value) {
        if( floatval($key)) {
             unset($fields1->fields[$key]);
        }
        else {
        $fldlabel=$fldlabel1[$in];
        $fld["$fldlabel"]=$value;
        $in++;}
    }
$channel11 = curl_init();
//curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($channel11, CURLOPT_POST, true);
curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fld));
curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
$response2 = curl_exec($channel11);

}
else {
 if($cr !=true){
 $cr=true;
 $endpointUrl2 = "http://$ip:9200/$indextype/denorm";
    $eid=$entityData->getId();
    $fld=array();
    $fld['roles']=$roleid;
    $fld['changedvalues']=$act2;
    $fld['userchange']=$userid;
    $fld['urlrecord']="<a href='index.php?module=$moduleName&action=DetailView&record=$eid'>Details</a>";
    unset($fields1->fields[0]);
    $in=0;
    foreach($fields1->fields as $key => $value) {
        if( floatval($key)) {
             unset($fields1->fields[$key]);
        }
        else {
        $fldlabel=$fldlabel1[$in];
        $fld["$fldlabel"]=$value;
        $in++;}
    }
$channel11 = curl_init();
//curl_setopt($channel1, CURLOPT_HTTPHEADER, $headers);
curl_setopt($channel11, CURLOPT_URL, $endpointUrl2);
curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
curl_setopt($channel11, CURLOPT_POST, true);
//curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($fld));
curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
$response23 = curl_exec($channel11);  
}
}
}       
}
    }
    }
    $log->debug("Exit aftersave event...");
    }
    $log->debug("Exiting Handler for module...".$moduleName);
}
function getEntitylogtype($module=''){
    include_once('modules/LoggingConf/LoggingUtils.php');
   require_once('include/utils/UserInfoUtil.php');
   require_once('include/utils/utils.php');
   global $log;
   $tabid=getTabid($module);
   $type=getEntitylogtype($tabid);
           
  return $type;
}

  function getModulesFieldMap($module='')
  {
   include_once('modules/LoggingConf/LoggingUtils.php');
   require_once('include/utils/UserInfoUtil.php');
   require_once('include/utils/utils.php');
   global $log;

   $tabid=getTabid($module);
   $isModule=isModuleLog($tabid);
   if($module=='')
   {
   $allLoggingModules=array_values(getLoggingModules());
   
   foreach($allLoggingModules as $module)
   {
       $moduleInstance=Vtiger_Module::getClassInstance($module);
       $table=$moduleInstance->table_name;
       $primary_key=$moduleInstance->table_index;
       $tabid=getTabid($module);
       $map=array();
       $fields=array();
       $fields=array_values(getModuleLogFieldList($tabid));

       $map[$module]=array(
           'tablename'=>$table,
           'primarykey'=>$primary_key,
           'fields'=>$fields,
       );  
   }   
   }
   elseif($isModule>0)
   {
       $moduleInstance=Vtiger_Module::getClassInstance($module);
       $table=$moduleInstance->table_name;
       $primary_key=$moduleInstance->table_index;     
       $map=array();
       $fields=array();
       $fields=array_values(getModuleLogFieldListNames($tabid));

       $map[$module]=array(
           'tablename'=>$table,
           'primarykey'=>$primary_key,
           'fields'=>$fields,
       );
   }
   return $map;
  }
}

?>
