<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'config.inc.php';
require_once 'vtlib/Vtiger/Cron.php';
require_once 'config.inc.php';
global $mod_strings, $app_strings, $current_language, $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path.'images/';

$smarty = new vtigerCRM_Smarty;
if (isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$id = $_REQUEST['record'];
	$cronTask = Vtiger_cron::getInstanceById($id);
	$label = getTranslatedString($cronTask->getName(), $cronTask->getModule());
	$cron_status = $cronTask->getStatus();
	$cron_freq =  $cronTask->getFrequency();
	$cron_daily =  $cronTask->getdaily();
	$cron_desc = $cronTask->getDescription();
	$lastend = explode(" ", $cronTask->getLastEndDateTime());
	if (!isset($lastend[1])) {
		$lastend[1] = '00:00';
	}
	$hourminsec = explode(":", $lastend[1]);
	$hourmin = $hourminsec[0].':'.$hourminsec[1];
	$cron = array();
	$cron['label'] = $label;
	if ($cron_daily==1) {
		$cron['frequency']=(int)($cron_freq/(60*60));
		$cron['time'] = 'daily';
	} elseif ($cron_freq/(60*60)>1) {
		$cron['frequency']=(int)($cron_freq/(60*60));
		$cron['time'] = 'hour';
	} else {
		$cron['frequency']=(int)($cron_freq/60);
		$cron['time'] = 'min';
	}
	$cron['status'] = $cron_status;
	$cron['description'] = $cron_desc;
	$cron['id']=$id;
	$cron['hourmin']=$hourmin;
	$smarty->assign('CRON_DETAILS', $cron);
	$smarty->assign('CRON_MODULE', $cronTask->getModule());
	$smarty->assign('MOD', return_module_language($current_language, 'CronTasks'));
	$smarty->assign('THEME', $theme);
	$smarty->assign('IMAGE_PATH', $image_path);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('CMOD', $mod_strings);
	$smarty->assign('MIN_CRON_FREQUENCY', getMinimumCronFrequency());
	if (in_array($cronTask->getName(), $cronTask->fixedTasks) || (!empty($coreBOSOnDemandActive) && in_array($cronTask->getName(), $cbodFixedCronTasks))) {
		$smarty->display('modules/CronTasks/ShowCron.tpl');
	} else {
		$smarty->display('modules/CronTasks/EditCron.tpl');
	}
} else {
	header('Location:index.php?module=CronTasks&action=ListCronJobs&directmode=ajax');
}
?>
