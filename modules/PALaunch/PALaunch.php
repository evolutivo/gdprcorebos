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

class PALaunch extends CRMEntity {
	public $db;
	public $log;
	public $disableVtCallbacks = true;

	public $table_name = 'vtiger_palaunch';
	public $table_index= 'palaunchid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_palaunchcf', 'palaunchid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE', 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_palaunch', 'vtiger_palaunchcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_palaunch'   => 'palaunchid',
		'vtiger_palaunchcf' => 'palaunchid'
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'PALaunch Id'=> array('palaunch'=> 'palaunchid'),
		'Planned Action' => array('palaunch'=>'plannedaction_id'),
		'Related To' => array('palaunch'=>'related_id'),
		'Recipient' => array('palaunch'=>'recipient_id'),
		'Sequencer' => array('palaunch'=>'sequencer_id'),
		'Scheduled Date' => array('palaunch'=>'scheduled_date'),
		'Processed Date' => array('palaunch'=>'processed_date'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'PALaunch Id'=> 'palaunchid',
		'Planned Action' => 'plannedaction_id',
		'Related To' => 'related_id',
		'Recipient' => 'recipient_id',
		'Sequencer' => 'sequencer_id',
		'Scheduled Date' => 'scheduled_date',
		'Processed Date' => 'processed_date',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'palaunchid';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'PALaunch Id'=> array('palaunch'=> 'palaunchid'),
		'Planned Action' => array('palaunch'=>'plannedaction_id'),
		'Related To' => array('palaunch'=>'related_id'),
		'Recipient' => array('palaunch'=>'recipient_id'),
		'Sequencer' => array('palaunch'=>'sequencer_id'),
		'Scheduled Date' => array('palaunch'=>'scheduled_date'),
		'Processed Date' => array('palaunch'=>'processed_date'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'PALaunch Id'=> 'palaunchid',
		'Planned Action' => 'plannedaction_id',
		'Related To' => 'related_id',
		'Recipient' => 'recipient_id',
		'Sequencer' => 'sequencer_id',
		'Scheduled Date' => 'scheduled_date',
		'Processed Date' => 'processed_date',
	);

	// For Popup window record selection
	public $popup_fields = array('paulaunchid');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'related_id';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'palaunchid';

	// Required Information for enabling Import feature
	public $required_fields = array('MODULE_REFERENCE_FIELD'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'scheduled_date';
	public $default_sort_order='DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime',);

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	public function getRecipientId($relatedId, $relatedModule = NULL) {
		global $adb;
		if (is_null($relatedModule)) {
			$relatedModule = getSalesEntityType($relatedId);
		}
		$recipientId = null;
		switch ($relatedModule) {
		case 'Potentials':
			$rspot = $adb->query("select related_to from vtiger_potential where potentialid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'related_to');
			break;
		case 'HelpDesk':
			$rspot = $adb->query("select parent_id from vtiger_troubletickets where ticketid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'parent_id');
			break;
		case 'Quotes':
			$rspot = $adb->query("select accountid from vtiger_quotes where quoteid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'accountid');
			break;
		case 'SalesOrder':
			$rspot = $adb->query("select accountid from vtiger_salesorder where salesorderid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'accountid');
			break;
		case 'Invoice':
			$rspot = $adb->query("select accountid from vtiger_invoice where invoiceid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'accountid');
			break;
		case 'ServiceContracts':
			$rspot = $adb->query("select sc_related_to from vtiger_servicecontracts where servicecontractsid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'sc_related_to');
			break;
		case 'Assets':
			$rspot = $adb->query("select account from vtiger_assets where assetsid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'account');
			break;
		case 'ProjectMilestone':
			$rspot = $adb->query("select linktoaccountscontacts
				from vtiger_project
				inner join vtiger_projectmilestone on vtiger_project.projectid = vtiger_projectmilestone.projectid
				where projectmilestoneid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'linktoaccountscontacts');
			break;
		case 'ProjectTask':
			$rspot = $adb->query("select linktoaccountscontacts
				from vtiger_project
				inner join vtiger_projecttask on vtiger_project.projectid = vtiger_projecttask.projectid
				where projecttaskid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'linktoaccountscontacts');
			break;
		case 'Project':
			$rspot = $adb->query("select linktoaccountscontacts from vtiger_project where projectid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'linktoaccountscontacts');
			break;
		case 'CobroPago':
			$rspot = $adb->query("select parent_id from vtiger_cobropago where cobropagoid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'parent_id');
			break;
		case 'Messages':
			$rspot = $adb->query("select account_message,contact_message,lead_message from vtiger_messages where messagesid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'contact_message');
			if (empty($recipientId)) {
				$recipientId = $adb->query_result($rspot,0,'lead_message');
			}
			if (empty($relatedId)) {
				$recipientId = $adb->query_result($rspot,0,'account_message');
			}
			break;
		case 'cbSurveyDone':
			$rspot = $adb->query("select relatewith from vtiger_cbsurveydone where cbsurveydoneid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'relatewith');
			break;
		case 'cbSurveyAnswer':
			$rspot = $adb->query("select relatedwith from vtiger_cbsurveyanswer where cbsurveyanswerid=$relatedId");
			$recipientId = $adb->query_result($rspot,0,'relatedwith');
			break;
		case 'Contacts':
		case 'Accounts':
		case 'Leads':
			$recipientId = $relatedId;
			break;
		}
		return $recipientId;
	}


	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
        function create_palaunch ($actionid,$delay,$param,$parampos=null,$delaybetween=null,$recordid){
         global $adb,$log;
         $date_time = date("Y-m-d H:i", strtotime("+ $delay minutes"));  
         $date = date("Y-m-d",  strtotime($date_time));
         $time=date('H:i',strtotime($date_time ));
         $action=$adb->query("select threshold from vtiger_actions where actionsid=$actionid");
         if($adb->num_rows($action)==0){
         $action=$adb->query("select threshold from vtiger_sequencers where sequencersid=$actionid");
         }
         //$param1=explode(":::",$param);
         $threshold=$adb->query_result($action,0,"threshold");
         if($threshold=='' || $threshold==null) $threshold=1;
         //$paramdec=json_decode(str_replace(array("'[","]'"),array("[","]"),$param[($parampos-1)]));$log->debug($paramdec);
         $ths=ceil(sizeof($param)/$threshold);
         for($i=0;$i<$ths;$i++){
         if($delaybetween!=''&& $delaybetween!=0 && $i>0)
             $time = date("H:i", strtotime("+ $delaybetween minutes",strtotime($time)));
         
         unset($arr); 
         $arr=array();
         for($j=1;$j<=$threshold;$j++){
         if($param[($j-1)+$threshold*$i]!='')
         $arr[]=$param[($j-1)+$threshold*$i];
         else break;
         } 
         $param[$parampos-1]=json_encode(array("parameters"=>$arr));
         $focus=new PALaunch();
         $focus->column_fields['sequencer_id']=$actionid;
         $focus->column_fields['scheduled_date']=$date;
         $focus->column_fields['time_start']=$time;
         $focus->column_fields['processed_date']=$date;
         $focus->column_fields['parameters']=json_encode(array("parameters"=>$arr));
         $focus->column_fields['time_end']=$time;
         $focus->column_fields['palaunch_status']='Pending';
         $focus->column_fields['assigned_user_id']=1;
         if($recordid!=null && $recordid!='')
         $focus->column_fields['records']=$recordid;
         $focus->saveentity("PALaunch");}
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
