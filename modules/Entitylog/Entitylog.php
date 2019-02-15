<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class Entitylog extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_entitylog';
	public $table_index= 'entitylogid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_entitylogcf', 'entitylogid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE', 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_entitylog', 'vtiger_entitylogcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_entitylog'   => 'entitylogid',
		'vtiger_entitylogcf' => 'entitylogid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Entitylog Name'=> array('entitylog'=> 'entitylogname'),
                'Related to' => array('entitylog'=> 'relatedto'),                
                'User' => array('entitylog'=> 'user'),
                'Related Module' => array('entitylog'=> 'tabid'),
                'Changes Message' => array('entitylog'=> 'finalstate'),
		'Assigned To' => array('crmentity'=> 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Entitylog Name'=> 'entitylogname',
                'Related to'=>'relatedto',
                'User'=>'user',
                'Related Module'=>'tabid',
                'Changes Message'=>'finalstate',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'entitylogname';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Entitylog Name'=> array('entitylog'=> 'entitylogname')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Entitylog Name'=> 'entitylogname'
	);

	// For Popup window record selection
	public $popup_fields = array('entitylogname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'entitylogname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'entitylogname';

	// Required Information for enabling Import feature
	public $required_fields = array('entitylogname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'entitylogname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'entitylogname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/*
         * function to create norm or denorm index in elastic
         */
        function create_elastic_index($moduleName,$id,$changevalues){
         global $adb,$current_user;
         include_once("modules/LoggingConf/LoggingUtils.php");
         $tabid=getTabid($moduleName); 
         $type=explode(",",getEntitylogtype($tabid));
         $queryel=getqueryelastic($tabid);
         $indextype=getEntitylogindextype($tabid);
         require_once("modules/Users/CreateUserPrivilegeFile.php");
         require_once("include/utils/GetUserGroups.php");
         $q=$adb->pquery("select smownerid from vtiger_crmentity  where crmid=?",array($id));
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
         $arr=getUserModuleSharingRoles($moduleName,$owner,$def_org_share ,$current_user_roles,$parrol,$current_user_groups);
         $gr=$adb->pquery("select * from vtiger_groups where groupid=?",array($owner));
         if($adb->num_rows($gr)==0){
         if(count(array_keys($arr['read']['ROLE']))!=0)
         $roleid.='::'.implode('::',array_keys($arr['read']['ROLE']));}
         else $roleid.=implode('::',array_keys($arr['read']['GROUP']));
         $roleid.='::'.$owner;
         $tab=$adb->query("select * from  vtiger_entityname where tabid=$tabid");
         $tableid=$adb->query_result($tab,0,'tablename').'.'.$adb->query_result($tab,0,'entityidfield');
         $ip=GlobalVariable::getVariable('ip_elastic_server', '',1);
         $fl=$adb->pquery("select fieldlabel from vtiger_elastic_indexes where elasticname='$indextype'");
         $fldlabel1=explode(",", $adb->query_result($fl,0,0));
         $user="1";
         $update_log = unserialize($changevalues);
                  $lines = array();
                  foreach($update_log as $data) {
                    $query = "select fieldlabel,uitype,columnname,fieldid from vtiger_field where tabid={$tabid} and fieldname='{$data['fieldname']}'";
                    $res = $adb->query($query);
                    $fieldlabel = $adb->query_result($res, 0, 0);
                    $uitype = $adb->query_result($res, 0, 1);
                    $columnname = $adb->query_result($res, 0, 2);
                    $fieldid = $adb->query_result($res, 0, 3);
                    if (in_array($uitype,array(10)))
                    {                     
                         $idold=$data['oldvalue'];
                         $relatedModule=$adb->query_result($adb->pquery("Select setype from vtiger_crmentity where crmid=?",array($idold)),0,0);
                         $data['oldvalue']=  getEntityName($relatedModule, $idold);
                         $data['oldvalue']=$data['oldvalue'][$idold];

                         $idnew= $data['newvalue'];
                         $relatedModule=$adb->query_result($adb->pquery("Select setype from vtiger_crmentity where crmid=?",array($idnew)),0,0);
                         $data['newvalue']=getEntityName($relatedModule, $idnew);
                         $data['newvalue']=$data['newvalue'][$idnew]; 
                     
                    }
                   if(is_array($data))
                    {
                    $lines[] ='fieldname='.$fieldlabel.';oldvalue='.$data['oldvalue'].';newvalue='.$data['newvalue'].';';
                    } 
                    else{
                    $lines[] ='fieldname='.$fieldlabel.';oldvalue='.$data.';newvalue='.$data.';';
                    }
                    }
         $lines=implode("",$lines);
     
         if(in_array('normalized',$type)) {
         $endpointUrl2 = "http://$ip:9200/$indextype/norm";
         $fields1=$adb->pquery("$queryel and $tableid=?",array($id));
         $fld=array();
         $fld['roles']=$roleid;
         $fld['changedvalues']=$lines;
         $fld['userchange']=$user;
         $fld['urlrecord']="<a href='index.php?module=$moduleName&action=DetailView&record=$id'>Details</a>";
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
        $getid=$id;
        $endpointUrl12 = "http://$ip:9200/$indextype/denorm/_search?pretty";
        $pk=$adb->query_result($tab,0,'entityidfield');
        $fields1 =array('query'=>array("term"=>array("$pk"=>"$getid")));
        $channel1 = curl_init();
        curl_setopt($channel1, CURLOPT_URL, $endpointUrl12);
        curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel1, CURLOPT_POST, true);
        //curl_setopt($channel1, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fields1));
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
        $fields1=$adb->pquery("$queryel and $tableid=?",array($id));
        $fld=array();
        $fld['roles']=$roleid;
        $fld['changedvalues']=$lines;
        $fld['userchange']=$user;
        $fld['urlrecord']="<a href='index.php?module=$moduleName&action=DetailView&record=$id'>Details</a>";
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
        $endpointUrl2 = "http://$ip:9200/$indextype/denorm";
        $fields1=$adb->pquery("$queryel and $tableid=?",array($id));
        $fld=array();
        $fld['roles']=$roleid;
        $fld['changedvalues']=$lines;
        $fld['userchange']=$user;
        $fld['urlrecord']="<a href='index.php?module=$moduleName&action=DetailView&record=$id'>Details</a>";
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
	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, $modulename.'-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
