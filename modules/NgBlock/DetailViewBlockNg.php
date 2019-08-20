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

class NgBlock_DetailViewBlockNgWidget {
	private $_name = 'DetailViewBlockNgWidget';
        private $id ;
	
	private $defaultCriteria = 'All';
	
	protected $context = false;
	protected $criteria= false;
	
	function __construct($id_ng) {
            $this->id = $id_ng;
	}
	
	function getFromContext($key, $purify=false) {
		if ($this->context) {
			$value = $this->context[$key];
			if ($purify && !empty($value)) {
				$value = vtlib_purify($value);
			}
			return $value;
		}
		return false;
	}
	
	function title() {
		return getTranslatedString('LBL_NGBLOCK_INFORMATION', 'NgBlock');
	}
	
	function name() {
		return $this->_name;
	}
	
	function setCriteria($newCriteria) {
		$this->criteria = $newCriteria;
	}
	
	function getViewer() {
		global $theme, $app_strings, $current_language;
		
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', return_module_language($current_language,'NgBlock'));
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
				
		return $smarty;
	}
	
	protected function getModels($parentRecordId, $criteria) {
		global $adb, $current_user;

		$moduleName = 'ModComments';
		if(vtlib_isModuleActive($moduleName)) {
			$entityInstance = CRMEntity::getInstance($moduleName);
			
			$queryCriteria  = '';
			switch($criteria) {
				case 'All': $queryCriteria = sprintf(" ORDER BY %s.%s DESC ", $entityInstance->table_name, $entityInstance->table_index); break;
				case 'Last5': $queryCriteria =  sprintf(" ORDER BY %s.%s DESC LIMIT 5", $entityInstance->table_name, $entityInstance->table_index) ;break;
				case 'Mine': $queryCriteria = ' AND vtiger_crmentity.smownerid=' . $current_user->id.sprintf(" ORDER BY %s.%s DESC ", $entityInstance->table_name, $entityInstance->table_index); break;
			}
			
			$query = $entityInstance->getListQuery($moduleName, sprintf(" AND %s.related_to=?", $entityInstance->table_name));
			$query .= $queryCriteria;
			$result = $adb->pquery($query, array($parentRecordId));
		
			$instances = array();
			if($adb->num_rows($result)) {
				while($resultrow = $adb->fetch_array($result)) {
					$instances[] = new ModComments_CommentsModel($resultrow);
				}
			}
		}
		return $instances;
	}
	
	function processItem($model) {
		$viewer = $this->getViewer();
		$viewer->assign('COMMENTMODEL', $model);
		return $viewer->fetch(vtlib_getModuleTemplate("ModComments","widgets/DetailViewBlockCommentItem.tpl"));
	}
	
	function process($context = false) {
            require_once('modules/NgBlock/NgBlock.php');
            global $adb,$mod_strings, $app_strings,$default_language,$current_user;
		$this->context = $context;
		$sourceRecordId =  $this->getFromContext('ID', true);
		
                $result=$adb->pquery("Select * from vtiger_ng_block where id=?", array($this->id));
                $pointing_module_name	=$adb->query_result($result,0,'pointing_module_name');
                $tabid=  getTabid($pointing_module_name);
                $pointing_field_name=$adb->query_result($result,0,'pointing_field_name');
                $name=$adb->query_result($result,0,'name');
                $module_name=$adb->query_result($result,0,'module_name');
                $nr_page=$adb->query_result($result,0,'nr_page');
                $paginate=$adb->query_result($result,0,'paginate');
                $edit_record=$adb->query_result($result,0,'edit_record');
                $delete_record=$adb->query_result($result,0,'delete_record');
                $add_record=$adb->query_result($result,0,'add_record');
                $opened=$adb->query_result($result,0,'opened');
                $columns=$adb->query_result($result,0,'columns');
                $type=$adb->query_result($result,0,'type');
                $custom_widget_path=$adb->query_result($result,0,'custom_widget_path');
                $col= explode(",",$columns);
                $listcol= explode(",",$columns);
                $id_sub_ng=$adb->query_result($result,0,'sub_ng');
                $sub_ng=array();
                if(!empty($id_sub_ng))
                $sub_ng= explode(",",$id_sub_ng);
                $createcol=$adb->query_result($result,0,'createcol');
                $type=$adb->query_result($result,0,'type');
                $custom_widget_path=$adb->query_result($result,0,'custom_widget_path');
                $instanceNgBlock = new NgBlock();
                $editColBR = $instanceNgBlock->getEditCol($createcol);
                $edit_fld=array();$default_val=array();
                if(sizeof($editColBR)>0)
                {
                    foreach($editColBR as $k=>$v){
                        $edit_fld[]=$k;
                        $default_val[$k]=$v['value'];
                    }
                    $col= array_unique(array_merge($col,$edit_fld));
                }
                $options= Array();
                foreach($col as $key=>$fldname_val)
                {
                    if(empty($fldname_val)) continue;
                    $re=$adb->pquery("Select fieldlabel,uitype,fieldname,typeofdata,relmodule "
                            . " from vtiger_field "
                            . " left join vtiger_fieldmodulerel on vtiger_fieldmodulerel.fieldid=vtiger_field.fieldid"
                            . " where columnname = ? OR fieldname=?"
                            . " and tabid = '$tabid' ",array($fldname_val,$fldname_val));  
                    $tmp1= getTranslatedString($adb->query_result($re,0,'fieldlabel'),$pointing_module_name);
                    $uitype = $adb->query_result($re,0,'uitype');
                    $fieldname = $adb->query_result($re,0,'fieldname');
                    $columnName[] = $fldname_val;
                    $coList=false;$coEdit=false;$defValue='';
                    if(in_array($fldname_val, $edit_fld) || sizeof($editColBR)==0){
                        $coEdit=true;
                        $defValue=$default_val[$fldname_val];
                    }
                    if(in_array($fldname_val, $listcol)){
                        $coList=true;
                    }
                    $columnNameEdit[]=$coEdit;
                    $columnNameList[]=$coList;
                    $defaultValue[]=$defValue;
                    $fieldLabel[] = $tmp1;
                    $fieldUitype [] = $uitype;
                    if(in_array($uitype, array('15','16','55','33'))){
                        $res1=$adb->pquery("Select * from vtiger_$fieldname ",array());
                        for($count_options=0;$count_options<$adb->num_rows($res1);$count_options++)
                        {
                            $options[$fldname_val][$count_options]=$adb->query_result($res1,$count_options,$fieldname);
                        }
                    
                    }
                    elseif(in_array($uitype, array('26'))){
                        $res1=$adb->pquery("Select * from vtiger_attachmentsfolder ",array());
                        for($count_options=0;$count_options<$adb->num_rows($res1);$count_options++)
                        {
                            $options[$fldname_val][$count_options]=array(
                                'id'=>$adb->query_result($res1,$count_options,'folderid'),
                                'name'=>$adb->query_result($res1,$count_options,'foldername'));
                        }
                    
                    }
                    elseif($uitype=='53'){
                        $res1=$adb->pquery("Select id,concat(first_name,' ',last_name) as crmname,'User' as type from vtiger_users "
                                . " Union"
                                . " Select groupid as id,groupname as crmname,'Group' as type from vtiger_groups",array());
                        while ($emp=$adb->fetch_array($res1))  {
                               $options[$fldname_val][]=array('crmid'=>$emp['id'],
                                                          'crmname'=>$emp['crmname'],
                                                          'crmtype'=>$emp['type']);
                        }
                    
                    }
                    elseif($uitype=='10'){
                        $relmodule[$fieldname] = $adb->query_result($re,0,'relmodule');                   
                    }
                    elseif($uitype=='57'){
                        $relmodule[$fieldname] = 'Contacts';                   
                    }
                    elseif($uitype=='51'){
                        $relmodule[$fieldname] = 'Accounts';                   
                    }
                }
                $blockURL="module=Utilities&action=UtilitiesAjax";
                $blockURL.="&file=ng_block_actions&id=".$sourceRecordId;
                $blockURL.="&ng_block_id=".$this->id;                

                $viewer = $this->getViewer();
		$viewer->assign('RECORD_ID', $sourceRecordId);
                $viewer->assign('NG_BLOCK_NAME', $name);
                $viewer->assign('NG_BLOCK_ID', $this->id);
                $viewer->assign('MODULE_NAME', $module_name);
                $viewer->assign('APP', $app_strings);
                if(file_exists("modules/$pointing_module_name/language/$default_language.lang.php")){
                    include("modules/$pointing_module_name/language/$default_language.lang.php");
                }
                if(file_exists("modules/$module_name/language/$default_language.lang.php")){
                    include("modules/$module_name/language/$default_language.lang.php");
                }
//                include_once("include/Webservices/GetRelatedActions.php");
//                $fldDep=vtws_getFieldDep($pointing_module_name,'FieldDependency');                
                $viewer->assign('MOD', $mod_strings);
                $viewer->assign('MOD_NG', $mod_strings);
                $viewer->assign('POINTING_MODULE', $pointing_module_name);
                $viewer->assign('POINTING_FIELDNAME', $pointing_field_name);
                $viewer->assign('NR_PAGE', $nr_page);
                $viewer->assign('PAGINATE', $paginate);
                $viewer->assign('EDIT_RECORD', $edit_record);
                $viewer->assign('DELETE_RECORD', $delete_record);
                $viewer->assign('ADD_RECORD', $add_record);
                $viewer->assign('OPENED', $opened);
                $viewer->assign("COLUMN_NAME", $columnName);
                $viewer->assign("COLUMN_NAME_EDIT", $columnNameEdit);
                $viewer->assign("COLUMN_NAME_LIST", $columnNameList);
                $viewer->assign("DEFAULT_VALUE", $defaultValue);
                $viewer->assign("FIELD_LABEL", $fieldLabel);
                $viewer->assign("FIELD_UITYPE", $fieldUitype);
                $viewer->assign("SUB_NG", $sub_ng);
                $viewer->assign("Date_format", $current_user->date_format);
                $viewer->assign("COLUMN_NAME_JSON", json_encode($columnName));
                $viewer->assign("FIELD_UITYPE_JSON", json_encode($fieldUitype));
                $viewer->assign("DEFAULT_VALUE_JSON", json_encode($defaultValue));
                $viewer->assign("OPTIONS", json_encode($options));
                $viewer->assign('REL_MODULE', $relmodule);
                $viewer->assign("blockURL",$blockURL);
//                $viewer->assign('FLDDEP', json_encode($fldDep['all_field_dep']));
//                $viewer->assign('MAP_PCKLIST_TARGET', json_encode($fldDep['MAP_PCKLIST_TARGET']));
                
                if($type=='Graph'){
                    $ret_temp=$viewer->fetch(vtlib_getModuleTemplate("NgBlock","DetailViewBlockNgGraph.tpl"));                    
                }
                elseif($type=='Text'){
                    $ret_temp=$viewer->fetch(vtlib_getModuleTemplate("NgBlock","DetailViewBlockNgText.tpl"));                    
                }
                elseif($type=='Elastic'){
                    $ret_temp=$viewer->fetch(vtlib_getModuleTemplate("NgBlock","DetailViewBlockNgJson.tpl"));                    
                }
                elseif($type=='Custom'){
                    if(file_exists("Smarty/templates/modules/$module_name/$custom_widget_path")){
                        $ret_temp=$viewer->fetch(vtlib_getModuleTemplate($module_name,$custom_widget_path)); 
                    }
                    else{
                        $ret_temp=$viewer->fetch(vtlib_getModuleTemplate('NgBlock',$custom_widget_path)); 
                    }
                }
                else{
                    $ret_temp=$viewer->fetch(vtlib_getModuleTemplate("NgBlock","DetailViewBlockNg.tpl"));
                }
                return $ret_temp;
	}
	
}
