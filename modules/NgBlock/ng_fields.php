<?php  
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once('include/utils/utils.php');
require_once('modules/Map/Map.php');
require_once('modules/BusinessRules/BusinessRules.php');
require_once('modules/NgBlock/NgBlock.php');
require_once('modules/NgBlock/NgBlock.php');



global $root_directory,$adb,$theme,$current_user;
$kaction=$_REQUEST['kaction'];
$content=Array();

if($kaction=='retrieve'){
      $query=$adb->query(" 
          SELECT DISTINCT *
              from  vtiger_ng_fields
              left join vtiger_field on vtiger_field.fieldid = vtiger_ng_fields.field_id
              left join vtiger_blocks on vtiger_field.block = vtiger_blocks.blockid
          ");
      $count=$adb->num_rows($query);
      for($i=0;$i<$count;$i++){
          $content[$i]['id']=$adb->query_result($query,$i,'evo_field_id');
          $content[$i]['fieldid']=$adb->query_result($query,$i,'fieldid');
          $content[$i]['fieldname']=$adb->query_result($query,$i,'fieldname');
          $content[$i]['module_name']=  getTabname($adb->query_result($query,$i,'tabid'));
          $content[$i]['module_id']=$adb->query_result($query,$i,'tabid');
          $content[$i]['module_name_trans']=getTranslatedString(getTabname($adb->query_result($query,$i,'tabid')));
          $content[$i]['pointing_block_name']=  $adb->query_result($query,$i,'block');
          $content[$i]['pointing_block_name_trans']=  getTranslatedString($adb->query_result($query,$i,'blocklabel'),getTabname($adb->query_result($query,$i,'tabid')));
          $content[$i]['pointing_module_name']=getTabname($adb->query_result($query,$i,'moduleid'));
          $content[$i]['pointing_module_name_trans']=getTranslatedString(getTabname($adb->query_result($query,$i,'moduleid')));
          $content[$i]['columns_search']=$adb->query_result($query,$i,'fld_search');
          $content[$i]['columns_shown']=$adb->query_result($query,$i,'fld_shown');
          $sourceflds  =   explode(',', $adb->query_result($query,$i,'fld_source'));
          $destflds  =   explode(',', $adb->query_result($query,$i,'fld_destination'));
          $source=array();
          for($j=0;$j<sizeof($sourceflds);$j++)
            {
                $source[]=array("source_field_name"=>array('columnname'=>$sourceflds[$j]),
                                "dest_field_name"=>array('columnname'=>$destflds[$j]));
            }
          $content[$i]['source']=$source;
          $content[$i]['br_id']=$adb->query_result($query,$i,'br_id');
          $content[$i]['type']=$adb->query_result($query,$i,'uitype');
          $content[$i]['existing']=true;
          
      }
    echo json_encode($content);
}
elseif($kaction=='add_ng_field'){
     global $log,$adb;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $modname=$mv->module_name;
     $pointing_module_name=$mv->pointing_module_name;
     $blockid=$mv->pointing_block_name;
     if($mv->existing==true){
        $query="Update vtiger_field "
             . " set uitype=?"
             ;
        $query.= "  where fieldname=? "
                 . " and tabid=?";
        $result=$adb->pquery($query,array($mv->type,$mv->pointing_field_name,getTabid($modname)));
     }
     else{
        $block = Vtiger_Block::getInstance($blockid);
        $field1 = new Vtiger_Field();
        $field1->name = preg_replace('/[^a-z0-9]/', '',strtolower($mv->fieldname));
        $field1->label= $mv->fieldname;
        $field1->column = preg_replace('/[^a-z0-9]/', '',strtolower($mv->fieldname));
        $field1->columntype = 'VARCHAR(100)';
        $field1->uitype = $mv->type;
        $field1->typeofdata = 'V~O';
        $field1->displaytype = 1;
        $field1->presence = 0;
        $block->addField($field1);
     }
     $query2="Select fieldid "
             . " from vtiger_field "
             . " where (fieldname=? OR fieldname=?) and tabid=?"
             ;
     $result2=$adb->pquery($query2,array($mv->pointing_field_name,preg_replace('/[^a-z0-9]/', '',strtolower($mv->fieldname)),getTabid($modname)));
     $fldid=$adb->query_result($result2,0,'fieldid');
     $query="Insert into vtiger_ng_fields "
             . " (field_id,moduleid,br_id,fld_shown,fld_search,fld_source,fld_destination)"
             . " values(?,?,?,?,?,?,?)";            
     $result=$adb->pquery($query,array($fldid,getTabid($pointing_module_name),$mv->br_id,
                                         $mv->columns_shown,$mv->columns_search,
                                         $mv->fld_source,$mv->fld_destination)); 
}
elseif($kaction=='edit_ng_field'){
     global $log,$adb;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $modname=$mv->module_name;
     $pointing_module_name=$mv->pointing_module_name;
     $blockname=$mv->pointing_block_name;
     if($mv->existing==true){
        $query="Update vtiger_field "
             . " set uitype=?"
             ;
        $query.= "  where fieldname=? "
                 . " and tabid=?";
        $result=$adb->pquery($query,array($mv->type,$mv->pointing_field_name,getTabid($modname)));
     }
     
     $query2="Select fieldid "
             . " from vtiger_field "
             . " where (fieldname=?) and tabid=?"
             ;
     $result2=$adb->pquery($query2,array($mv->pointing_field_name,getTabid($modname)));
     $fldid=$adb->query_result($result2,0,'fieldid');
     $query="Update vtiger_ng_fields "
             . " set moduleid=?,"
             . " fld_shown=?,"
             . " fld_search=?,"
             . " br_id=?,"
             . " fld_source=?,"
             . " fld_destination=?"
             . " where evo_field_id=?";            
     $result=$adb->pquery($query,array(getTabid($mv->pointing_module_name),$mv->columns_shown
                                          ,$mv->columns_search,$mv->br_id,
                                           $mv->fld_source,$mv->fld_destination,
                                             $mv->id)); 
}
elseif($kaction=='delete_ng_field'){
     global $log;
     $models=$_REQUEST['models'];
     $mv=json_decode($models);
     $query="Delete from vtiger_ng_block_tab_rl where tab_rl_id=? ";
     $result=$adb->pquery($query,array($mv->id)
             ); 
}

?> 