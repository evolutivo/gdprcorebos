<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source in cooperation with a-g-c (Andreas Goebel)
 * The Initial Developer of the Original Code is vtiger and a-g-c (Andreas Goebel)
 * Portions created by vtiger are Copyright (C) vtiger, portions created by a-g-c are Copyright (C) a-g-c.
 * www.a-g-c.de
 * All Rights Reserved.
 ************************************************************************************/
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/CustomView/CustomView.php');
require_once("config.php");
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/SQLReports/ReportsSQL.php');
require_once("modules/SQLReports/ReportRunSQL.php");
require_once("modules/SQLReports/SQLReports.php");
require_once('modules/SQLReports/SQLReportsUtils.php');
require_once('Smarty_setup.php');

global $adb,$mod_strings,$app_strings;

$reportid = vtlib_purify($_REQUEST["record"]);

$sqlreport = new SQLReports();
$sqlreport->retrieve_entity_info($reportid, "SQLReports");
$sqlreport->id = $reportid;


$ogReport = new ReportsSQL($reportid);
$oReportRun = new ReportRunSQL($reportid);

// Performance Optimization: Direct output of the report result
$list_report_form = new vtigerCRM_Smarty;

$sshtml = array();
$totalhtml = '';
$list_report_form->assign("DIRECT_OUTPUT", true);
$list_report_form->assignByRef("__REPORT_RUN_INSTANCE", $oReportRun);


$list_report_form->assign("MOD", $mod_strings);
$list_report_form->assign("APP", $app_strings);
$list_report_form->assign("IMAGE_PATH", $image_path);
$list_report_form->assign("REPORTID", $reportid);
$list_report_form->assign("IS_EDITABLE", false);

$list_report_form->assign("REPORTNAME", $sqlreport->column_fields['reportname']);
if(is_array($sshtml))
{
    $list_report_form->assign("REPORTHTML", $sshtml);
}
else
{
    $list_report_form->assign("ERROR_MSG", getTranslatedString('LBL_REPORT_GENERATION_FAILED', $currentModule) . "<br>" . $sshtml);
}
$list_report_form->assign("REPORTTOTHTML", $totalhtml);
$list_report_form->assign("FOLDERID", $folderid);
$list_report_form->assign("DATEFORMAT",$current_user->date_format);
$list_report_form->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$list_report_form->assign("EXPORT_PERMITTED","YES");


if($_REQUEST['mode'] != 'ajax')
{
        $list_report_form->assign("REPINFOLDER", $reports_array);
        $list_report_form->display('modules/SQLReports/ReportRunSQL.tpl');
}
else
{
        $list_report_form->display('modules/SQLReports/ReportRunContentsSQL.tpl');
}



?>
