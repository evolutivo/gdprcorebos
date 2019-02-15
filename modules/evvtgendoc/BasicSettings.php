<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('Smarty_setup.php');
require_once 'vtlib/Vtiger/Module.php';
global $app_strings, $mod_strings, $current_language,$currentModule, $theme,$current_user,$log;

function gendoc_changeModuleVisibility($tabid, $status) {
	$moduleInstance = Vtiger_Module::getInstance($tabid);
	if ($status == 'module_disable') {
		$moduleInstance->deleteLink('DETAILVIEWWIDGET', 'Generate Document');
		$moduleInstance->deleteLink('LISTVIEWBASIC', 'Generate Document');
	} else {
		$moduleInstance->addLink('DETAILVIEWWIDGET', 'Generate Document','module=evvtgendoc&action=evvtgendocAjax&file=DetailViewWidget&formodule=$MODULE$&forrecord=$RECORD$');
		$moduleInstance->addLink('LISTVIEWBASIC','Generate Document',"javascript:showgendoctemplates('\$MODULE\$');");
	}
}
function gendoc_getModuleinfo() {
	global $adb;
	$allEntities = array();
	$entityQuery = "SELECT tabid,name FROM vtiger_tab WHERE isentitytype=1 and name NOT IN ('Rss','Webmails','Recyclebin','Events')";
	$result = $adb->pquery($entityQuery, array());
	while($result && $row = $adb->fetch_array($result)){
		$allEntities[$row['tabid']] = getTranslatedString($row['name'],$row['name']);
	}
	asort($allEntities);
	$mlist = array();
	foreach ($allEntities as $tabid => $mname) {
		$checkres = $adb->pquery('SELECT linkid FROM vtiger_links WHERE tabid=? AND linktype=? AND linklabel=?', Array($tabid, 'DETAILVIEWWIDGET', 'Generate Document'));
		$mlist[$tabid] = array(
			'name' => $mname,
			'active' => $adb->num_rows($checkres),
		);
	}
	return $mlist;
}

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$category = getParentTab();

$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign('CATEGORY',$category);
if(!is_admin($current_user)) {
	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));
} else {
	$tabid = isset($_REQUEST['tabid']) ? vtlib_purify($_REQUEST['tabid']) : '';
	$status = isset($_REQUEST['status']) ? vtlib_purify($_REQUEST['status']) : '';
	if($status != '' && $tabid != ''){
		gendoc_changeModuleVisibility($tabid, $status);
	}
	$infomodules = gendoc_getModuleinfo();
	$smarty->assign('INFOMODULES',$infomodules);
	$smarty->assign('MODULE',$module);
	if (empty($_REQUEST['ajax']) || $_REQUEST['ajax'] != true) {
		$smarty->display(vtlib_getModuleTemplate($currentModule,'BasicSettings.tpl'));
	} else {
		$smarty->display(vtlib_getModuleTemplate($currentModule,'BasicSettingsContents.tpl'));
	}
}
?>