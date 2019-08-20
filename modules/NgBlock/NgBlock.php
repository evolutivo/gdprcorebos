<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'include/utils/VtlibUtils.php';


class NgBlock {

    public $header = '';
	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
	
	}

	/**
	 * Transfer the comment records from one parent record to another.
	 * @param CRMID Source parent record id
	 * @param CRMID Target parent record id
	 */
	static function transferRecords($currentParentId, $targetParentId) {
		global $adb;
		$adb->pquery("UPDATE vtiger_modcomments SET related_to=? WHERE related_to=?", array($targetParentId, $currentParentId));
	}

	/**
	 * Get widget instance by name
	 */
	static function getWidget($id,$context) {
                global $adb;
		$result=$adb->pquery("Select id,pointing_block_name,top_widget from vtiger_ng_block where id=?", array($id));
                $nr=$adb->num_rows($result);
                if ($nr>0) {
                    $top_widget=$adb->query_result($result,0,'top_widget');
                    $pointing_block_name=$adb->query_result($result,0,'pointing_block_name');
                    $lbl_block_name=array_search($context['header'],$context['MOD']);

                    if($lbl_block_name==$pointing_block_name || $context['header']==$pointing_block_name  
                            ||($context['CUSTOM_LINKS']['RELATEDVIEWWIDGET'] && $context['CUSTOM_LINKS']['RELATEDVIEWWIDGET'][0]->linktype=='RELATEDVIEWWIDGET') 
                            || $top_widget=='1'){
			require_once dirname(__FILE__) . '/DetailViewBlockNg.php';
			return (new NgBlock_DetailViewBlockNgWidget($id));
	}
		}
		return false;
	}

	/**
	 * Add widget to other module.
	 * @param unknown_type $moduleNames
	 * @return unknown_type
	 */
	static function addWidgetTo($moduleNames, $widgetType='DETAILVIEWWIDGET', $widgetName='DetailViewBlockNgWidget',$sequence) {
		if (empty($moduleNames)) return;

		include_once 'vtlib/Vtiger/Module.php';

		if (is_string($moduleNames)) $moduleNames = array($moduleNames);

		$commentWidgetModules = array();
		foreach($moduleNames as $moduleName) {
			$module = Vtiger_Module::getInstance($moduleName);
			if($module) {
				$module->addLink($widgetType, $widgetName, "block://NgBlock:modules/NgBlock/NgBlock.php","",$sequence);
				$commentWidgetModules[] = $moduleName;
			}
		}
		if (count($commentWidgetModules) > 0) {
			$modCommentsModule = Vtiger_Module::getInstance('NgBlock');
			$modCommentsModule->addLink('HEADERSCRIPT', 'NgBlockHeaderScript', 'modules/NgBlock/NgBlock.js');
//			$modCommentsRelatedToField = Vtiger_Field::getInstance('related_to', $modCommentsModule);
//			$modCommentsRelatedToField->setRelatedModules($commentWidgetModules);
		}
	}

	/**
	 * Remove widget from other modules.
	 * @param unknown_type $moduleNames
	 * @param unknown_type $widgetType
	 * @param unknown_type $widgetName
	 * @return unknown_type
	 */
	static function removeWidgetFrom($moduleNames, $widgetType='DETAILVIEWWIDGET', $widgetName='DetailViewBlockNgWidget') {
		if (empty($moduleNames)) return;

		include_once 'vtlib/Vtiger/Module.php';

		if (is_string($moduleNames)) $moduleNames = array($moduleNames);

		$commentWidgetModules = array();
		foreach($moduleNames as $moduleName) {
			$module = Vtiger_Module::getInstance($moduleName);
			if($module) {
				$module->deleteLink($widgetType, $widgetName, "block://NgBlock:modules/NgBlock/NgBlock.php");
				$commentWidgetModules[] = $moduleName;
			}
		}
		
	}

	/**
	 * Wrap this instance as a model
	 */
	function getAsCommentModel() {
		return new ModComments_CommentsModel($this->column_fields);
	}

	function getListButtons($app_strings) {
		$list_buttons = Array();
		return $list_buttons;
	}
        
        function createElastic($indextype,$typ){
    
            global $adb;

            $entries=Array();
            $tabid=  getTabid('Adocdetail');
            global $dbconfig;
            $ip=GlobalVariable::getVariable('ip_elastic_server', '');
            //$endpointUrl = "http://$ip:9200/$indextype/$typ/_search?pretty";
        //    $fields1 =array('query'=>array("term"=>array("adocdetailid"=>$id)),'sort'=>array('modifiedtime'=>array('order'=>'asc')));
//            $channel1 = curl_init();
//            curl_setopt($channel1, CURLOPT_URL, $endpointUrl);
//            curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($channel1, CURLOPT_POST, true);
//            //curl_setopt($channel1, CURLOPT_CUSTOMREQUEST, "PUT");
//            //curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fields1));
//            curl_setopt($channel1, CURLOPT_CONNECTTIMEOUT, 100);
//            curl_setopt($channel1, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($channel1, CURLOPT_TIMEOUT, 1000);
//            $response1 = json_decode(curl_exec($channel1));
//            $tot=$response1->hits->total;
            // if ($ip != '193.182.16.34') {
            //     $indextype = 'eprice_messageindex';
            // }
            $endpointUrl = "http://$ip:9200/$indextype/$typ/_search?pretty&size=10000";
        //    $fields1 =array('query'=>array("term"=>array("adocdetailid"=>$id)),'sort'=>array('modifiedtime'=>array('order'=>'asc')));
            $channel1 = curl_init();
            curl_setopt($channel1, CURLOPT_URL, $endpointUrl);
            curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($channel1, CURLOPT_POST, true);
            //curl_setopt($channel1, CURLOPT_CUSTOMREQUEST, "PUT");
            $id = $_REQUEST['id'];
            if ($indextype == 'eprice_messageslog') {
                $fields1 = array('query'=>array("term"=>array("project"=>$id)),'sort'=>array('createdtimeMessages'=>array('order'=>'desc')));
            } else {
                $fields1 = array('query'=>array("term"=>array("projectid"=>$id)),'sort'=>array('createdtimeProjectTask'=>array('order'=>'desc')));
            }
            curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fields1));
            curl_setopt($channel1, CURLOPT_CONNECTTIMEOUT, 100);
            curl_setopt($channel1, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($channel1, CURLOPT_TIMEOUT, 1000);
            $response1 = json_decode(curl_exec($channel1));
            foreach ($response1->hits->hits as $row) {
                $user = getUserName($row->_source->userchange);
                $update_log = explode(";",$row->_source->changedvalues);
                $name = '';
                if (!empty(getUserName($row->_source->smownerid)))
                    $name = getUserName($row->_source->smownerid);
                else {
                    $name_arr = getGroupName($row->_source->smownerid);
                    $name = $name_arr[0];
                }
                $row->_source->assigned_user_id_display = $name;
                if ($indextype == 'eprice_messageslog') {
                    $row->_source->messagio = strip_tags(html_entity_decode($row->_source->messagio));
                    $row->_source->createdtime = $row->_source->createdtimeMessages;
                    $row->_source->href = "index.php?module=Messages&action=DetailView&record=".$row->_source->messagesid;
                    $row->_source->id = $row->_source->messagesid;
                } else {
                    $row->_source->createdtime = $row->_source->createdtimeProjectTask;
                    $row->_source->href = "index.php?module=ProjectTask&action=DetailView&record=".$row->_source->projecttaskid;
                    $row->_source->id = $row->_source->projecttaskid;
                    $row->_source->startdate_display = $row->_source->startdate;
                }
                $source[] = $row->_source;
            }
            return (array)$source;
        }
        
        function getEditCol($createcol) {
                require_once('modules/Map/Map.php');
                require_once('modules/BusinessRules/BusinessRules.php');
                global $current_user,$adb;
                $userProfileArr = getUserProfile($current_user->id);
                $arr=explode(',',$createcol);
                $columns=array();
                for($i=0;$i<sizeof($arr);$i++){
                    if(empty($arr[$i])) continue;
                    $brId=$arr[$i];
                    $focusBR = CRMEntity::getInstance("BusinessRules");
                    $focusBR->retrieve_entity_info($brId, "BusinessRules");
                    $mapid = $focusBR->column_fields['linktomap'];
                    $focusMap = CRMEntity::getInstance("Map");
                    $focusMap->retrieve_entity_info($mapid, "Map");
                    $profile = $focusMap->getMapProfile();
                    $target_fields = $focusMap->getMapTargetFields();
                    if(count(array_intersect($profile ,$userProfileArr)) != 0 
                            || in_array('', $profile)){
                        $columns=$target_fields;
                        break;
                    }
                }
		return $columns;
	}

}
?>
