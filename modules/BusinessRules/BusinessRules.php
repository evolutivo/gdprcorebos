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

class BusinessRules extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_businessrules';
	public $table_index= 'businessrulesid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_businessrulescf', 'businessrulesid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE', 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_businessrules', 'vtiger_businessrulescf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_businessrules'   => 'businessrulesid',
		'vtiger_businessrulescf' => 'businessrulesid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'BusinessRules Name'=> array('businessrules'=> 'businessrules_name'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'BusinessRules Name'=> 'businessrules_name',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'businessrules_name';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'BusinessRules Name'=> array('businessrules'=> 'businessrules_name')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'BusinessRules Name'=> 'businessrules_name'
	);

	// For Popup window record selection
	public $popup_fields = array('businessrules_name');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'businessrules_name';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'businessrules_name';

	// Required Information for enabling Import feature
	public $required_fields = array('businessrules_name'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'businessrules_name';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'businessrules_name');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
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

        function get_permitted_profiles()
        {
           $roles_array=explode(',',$this->column_fields["busrule_roles"]);
           return $roles_array;
        }
        function isProfilePermitted() {
        global $current_user;
        $roles_array = explode(' |##| ', $this->column_fields["busrule_roles"]);
        $currentprofiles = getUserProfile($current_user->id);
        $allowed = false;
        //while (!$allowed) {
            $role=$current_user->roleid;
            if (in_array($role, $roles_array)) {
                $allowed = true;
                return $allowed;
            }
        //}
        return $allowed;
    }
    function apply_bussinessrules(){
        global $adb,$log;
        $mapid=$this->column_fields['linktomap'];
        if($mapid!='' && $mapid!=0){
            require_once ("modules/cbMap/cbMap.php");
            $map_focus = CRMEntity::getInstance("cbMap");
            $map_focus->retrieve_entity_info($mapid, "cbMap");
            $result=$map_focus->read_map();

            return $result;
          
        }
    }
    function isRolePermitted() {
        global $current_user;
        $roles_array = $this->column_fields["busrule_roles"];
        $currentRole = $current_user->roleid;
        $allowed = false;
        if (!empty($roles_array)) {
            $roles_array = explode(',', $this->column_fields["busrule_roles"]);
            if (in_array($currentRole, $roles_array)) {
                $allowed = true;
            } else {
                $allowed = false;
            }
        } else {
            $allowed = true;
        }
        return $allowed;
    }
     function executeSQLQuery($record) {
        global $adb,$current_user;
        $params = array();
        $allelements = array("CURRENT_USER" => $current_user->id, "CURRENT_RECORD" => $record,"CURRENT_RECORO" => $record);
        $mapid = $this->column_fields['linktomap'];
        if (!empty($mapid)) {
            require_once ("modules/cbMap/cbMap.php");
            $mapfocus = CRMEntity::getInstance("cbMap");
            $mapfocus->retrieve_entity_info($mapid, "cbMap");
            $mapINFO = $mapfocus->getMapSQLCondition();
            $sqlQuery = $mapINFO['sqlString'];
            $sqlCondition = $mapINFO['sqlCondition'];
            $this->log->debug("condition");
            $this->log->debug($sqlCondition);
            foreach ($allelements as $elem => $value) {
                $pos_el = strpos($sqlQuery, $elem);
                if ($pos_el) {
                    $sqlQuery = str_replace($elem, " ? ", $sqlQuery);
                    array_push($params, $value);
                }
            }
            $res_logic = $adb->pquery($sqlQuery, $params);
            if (isset($sqlCondition)) {
                $condRes = $adb->query_result($res_logic, 0, 0);
                $this->log->debug("This is the map condition");
                $this->log->debug($condRes);
                if ($condRes == $sqlCondition) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($adb->num_rows($res_logic) > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        else{
            return true;
        }
    }

    function executeSQLQuery2($therecid) {
    	//new parameter for put_ actions in sap
        global $adb, $current_user;
    
        $params = array();
        $allelements = array("CURRENT_USER" => $current_user->id, "CURRENT_RECORD" => $therecid,"CURRENT_RECORO" => $therecid);
        $mapid = $this->column_fields['linktomap'];
        if (!empty($mapid)) {
            require_once ("modules/cbMap/cbMap.php");
            $mapfocus = CRMEntity::getInstance("cbMap");
            $mapfocus->retrieve_entity_info($mapid, "cbMap");
            $mapINFO = $mapfocus->getMapSQLCondition();
            $sqlQuery = $mapINFO['sqlString'];
            $sqlCondition = $mapINFO['sqlCondition'];
            $this->log->debug("condition");
            $this->log->debug($sqlCondition);
		
            foreach ($allelements as $elem => $value) {
                $pos_el = strpos($sqlQuery, $elem);
                if ($pos_el) {
                    $sqlQuery = str_replace($elem, " ? ", $sqlQuery);
                    array_push($params, $value);
                }
            }
            $res_logic = $adb->pquery($sqlQuery, $params);
            if (isset($sqlCondition)) {
                $condRes = $adb->query_result($res_logic, 0, 0);
                $this->log->debug("This is the map condition");
                $this->log->debug($condRes);
                if ($condRes == $sqlCondition) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($adb->num_rows($res_logic) > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        else{
            return true;
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
