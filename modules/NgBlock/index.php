<?php
/*************************************************************************************************
 * Copyright 2012-2013 OpenCubed  --  
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
 *  Module       : NgBlock
 *  Version      : 1.8
 *  Author       : OpenCubed
 *************************************************************************************************/

require_once('Smarty_setup.php');
include_once 'modules/NgBlock/NgBlock.php';

global $root_directory,$adb,$theme,$current_user;
$smarty=new vtigerCRM_Smarty;
$kaction=$_REQUEST['kaction'];
$content=Array();
if($kaction=='retrieve'){
      $query=$adb->query(" 
          SELECT DISTINCT *
              from  vtiger_ng_block
              left join vtiger_ng_block_tab_rl on related_tab = vtiger_ng_block_tab_rl.tab_rl_id
          ");
      $count=$adb->num_rows($query);
      
      for($i=0;$i<$count;$i++){
      $content[$i]['id']=$adb->query_result($query,$i,'id');
      $content[$i]['id_hidden']=$adb->query_result($query,$i,'id');
      $content[$i]['name']=$adb->query_result($query,$i,'name');
      $content[$i]['module_name']=$adb->query_result($query,$i,'module_name');
      $content[$i]['module_id']=getTabid($adb->query_result($query,$i,'module_name'));
      $content[$i]['module_name_trans']=getTranslatedString($adb->query_result($query,$i,'module_name'),$adb->query_result($query,$i,'module_name'));
      $content[$i]['pointing_block_name']=  $adb->query_result($query,$i,'pointing_block_name');
      $content[$i]['pointing_block_name_trans']=  getTranslatedString($adb->query_result($query,$i,'pointing_block_name'),$adb->query_result($query,$i,'module_name'));
      $content[$i]['pointing_module_name']=$adb->query_result($query,$i,'pointing_module_name');
      $content[$i]['pointing_module_name_trans']=getTranslatedString($adb->query_result($query,$i,'pointing_module_name'),$adb->query_result($query,$i,'pointing_module_name'));
      $content[$i]['pointing_field_name']=$adb->query_result($query,$i,'pointing_field_name');
      $content[$i]['pointing_field_name_trans']=getTranslatedString($adb->query_result($query,$i,'pointing_field_name'),$adb->query_result($query,$i,'pointing_module_name'));
      $content[$i]['columns']=$adb->query_result($query,$i,'columns');
      $content[$i]['cond']=$adb->query_result($query,$i,'cond');
      $content[$i]['paginate']=($adb->query_result($query,$i,'paginate')==1 ? true : false );
      $content[$i]['nr_page']=$adb->query_result($query,$i,'nr_page');
      $content[$i]['add_record']=($adb->query_result($query,$i,'add_record') ==1 ? true : false );
      $content[$i]['sort']=$adb->query_result($query,$i,'sort');
      $content[$i]['edit_record']=($adb->query_result($query,$i,'edit_record') ==1 ? true : false );
      $content[$i]['delete_record']=($adb->query_result($query,$i,'delete_record') ==1 ? true : false );
      $content[$i]['sequence_ngblock']=$adb->query_result($query,$i,'sequence_ngblock');
      $content[$i]['destination']=$adb->query_result($query,$i,'destination');
      $content[$i]['type']=$adb->query_result($query,$i,'type');
      $content[$i]['related_tab']=$adb->query_result($query,$i,'related_tab');
      $content[$i]['related_tab_name']=$adb->query_result($query,$i,'tab_name');
      $content[$i]['custom_widget_path']=$adb->query_result($query,$i,'custom_widget_path');
      }
      echo json_encode($content);
     
    }
elseif($kaction=='edit'){
     global $log;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Update vtiger_ng_block "
             . " set name=?,"
             . " module_name=?,"
             . " pointing_block_name=? ,"
             . " columns=?,"
             . " cond=?,"
             . " sort=?,"
             . " paginate=?,"
             . " add_record=?,"
             . " edit_record=?,"
             . " delete_record=?,"
             . " nr_page=?,"
             . " sequence_ngblock=?,"
             . " destination=?,"
             . " type=?,"
             . " related_tab=?,"
             . " custom_widget_path=?";
     $query.= "  where id=? ";
             
     $result=$adb->pquery($query,array($mv->name,$mv->module_name,$mv->pointing_block_name,$mv->columns,
                                         $mv->cond,$mv->sort,$mv->paginate,$mv->add_record,
                                         $mv->edit_record,$mv->delete_record,$mv->nr_page,$mv->sequence_ngblock,
                                         $mv->destination,$mv->type,$mv->related_tab,$mv->custom_widget_path,
                                         $mv->id)
             ); 
     $actionsid=$adb->query_result($adb->pquery("select actionsid from vtiger_actions "
            . " where reference=?",array($mv->id)),0,'actionsid');
     require_once('modules/Actions/Actions.php');
     $action=CRMEntity::getInstance('Actions');
     $action->retrieve_entity_info($actionsid,"Actions");
     $action->id=$actionsid;
     $action->mode = 'edit';
     $action->column_fields['assigned_user_id'] = $action->column_fields['assigned_user_id'];
     $action->column_fields['elementtype_action'] = $mv->destination;
     $action->column_fields['actions_status'] = 'Active';
     $action->save("Actions"); 
     
    }
elseif($kaction=='delete'){
     global $log;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Delete from vtiger_ng_block where id=?";
             
     $result=$adb->pquery($query,array($mv->id)); 
     NgBlock::removeWidgetFrom(array($mv->module_name),'DETAILVIEWWIDGET',$mv->id);
    }
elseif($kaction=='addtab'){
     global $log;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Insert into vtiger_ng_block_tab_rl "
             . " (moduleid,tab_name,sequence)"
             . " values(?,?,?)";            
     $result=$adb->pquery($query,array($mv->moduleid,$mv->name,$mv->sequence)); 
}
elseif($kaction=='edittab'){
     global $log;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Update vtiger_ng_block_tab_rl "
             . " set tab_name=?,"
             . " moduleid=?,"
             . " sequence=? "
             ;
     $query.= "  where tab_rl_id=? ";
     $result=$adb->pquery($query,array($mv->name,$mv->moduleid,$mv->sequence,
                                         $mv->id)
             ); 
}
elseif($kaction=='deletetab'){
     global $log;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Delete from vtiger_ng_block_tab_rl where tab_rl_id=? ";
     $result=$adb->pquery($query,array($mv->id)
             ); 
}
elseif($kaction=='get_tab'){
   $qu1="Select * from vtiger_ng_block_tab_rl";
   $query=$adb->query($qu1);
   $count=$adb->num_rows($query);
   for($i=0;$i<$count;$i++){
      $content[$i]['id']=$adb->query_result($query,$i,'tab_rl_id');
      $content[$i]['name']=$adb->query_result($query,$i,'tab_name');
      $content[$i]['moduleid']=$adb->query_result($query,$i,'moduleid');
      $content[$i]['modulename']=getTranslatedString($adb->query_result($query,$i,'moduleid'),$adb->query_result($query,$i,'moduleid'));
      $content[$i]['sequence']=$adb->query_result($query,$i,'sequence');
    }
    echo json_encode($content);
}
elseif($kaction=='get_elastic_indexes'){
    global $adb;
    $content=array();
    $ip=GlobalVariable::getVariable('ip_elastic_server', '');//;//$dbconfig['ip_server'];
    $endpointUrl = "http://$ip:9200/_cat/indices?v";
    $channel1 = curl_init();
    curl_setopt($channel1, CURLOPT_URL, $endpointUrl);
    curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($channel1, CURLOPT_POST, false);
    //curl_setopt($channel1, CURLOPT_CUSTOMREQUEST, "PUT");
//    curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fields1));
    curl_setopt($channel1, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($channel1, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($channel1, CURLOPT_TIMEOUT, 1000);
    $response1 = curl_exec($channel1);
    $arr=explode('
',$response1);
    $arr_el=array();
    for($i_c=1;$i_c<sizeof($arr);$i_c++) {
        $specific_arr=explode(' ',$arr[$i_c]);
        if(!empty($specific_arr[5])){
            $res=$adb->pquery("Select  * from
                vtiger_elastic_indexes 
                where elasticname=?",array($specific_arr[5]));
            $arr_el[]=$specific_arr[5];
            if($adb->num_rows($res)==0){
                $adb->pquery("Insert into 
                          vtiger_elastic_indexes (elasticid,elasticname,status)
                          values(?,?,?)
                          ",array($i_c,$specific_arr[5],'open'));
            }           
        }
    }
    $adb->pquery("Delete from
                vtiger_elastic_indexes
                where elasticname not in (".  generateQuestionMarks($arr_el).")"
            ,array($arr_el));
    $res=$adb->pquery("Select  * 
                from vtiger_elastic_indexes",array());
    for($i_c=0;$i_c<$adb->num_rows($res);$i_c++) {
        $elasticname=$adb->query_result($res,$i_c,'elasticname');
        $content[]=$elasticname;
    }
    echo json_encode($arr_el);
}
elseif($kaction=='add'){
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Insert into vtiger_ng_block "
             . " (name,module_name,pointing_block_name,pointing_module_name,"
             . " pointing_field_name,columns,cond,sort,"
             . " paginate,add_record,edit_record,"
             . " delete_record,nr_page,sequence_ngblock,destination,type,"
             . " custom_widget_path)"
             . " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";            
     $result=$adb->pquery($query,array($mv->name,$mv->module_name,$mv->pointing_block_name,$mv->pointing_module_name,
                                         $mv->pointing_field_name,$mv->columns,$mv->cond,$mv->sort,
                                         $mv->paginate,$mv->add_record,$mv->edit_record,
                                         $mv->delete_record,$mv->nr_page,$mv->sequence_ngblock,
                                         $mv->destination,$mv->type,$mv->custom_widget_path)); 
    $last_id=$adb->query_result($adb->query("select id from vtiger_ng_block order by id desc"),0,'id');
    $seq=$adb->query_result($adb->pquery("select sequence from vtiger_blocks "
            . " where blocklabel=? and tabid=?",array($mv->pointing_block_name,getTabid($mv->module_name))),0,'sequence');
    NgBlock::addWidgetTo(array($mv->module_name),$mv->destination,$last_id,intval($seq));
    
     }
else{
       global $app_strings, $mod_strings, $log, $current_language;
       $temp_mod_strings = ($module != '' ) ? return_module_language($current_language, 'NgBlock') : $mod_strings;
       $smarty->assign('translations', json_encode($temp_mod_strings));
       $smarty->display("modules/NgBlock/index.tpl");
 
    }

?>

