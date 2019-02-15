<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source in cooperation with a-g-c (Andreas Goebel)
 * The Initial Developer of the Original Code is vtiger and a-g-c (Andreas Goebel)
 * Portions created by vtiger are Copyright (C) vtiger, portions created by a-g-c are Copyright (C) a-g-c.
 * All Rights Reserved.
 * www.a-g-c.de
 ************************************************************************************/
require_once('Smarty_setup.php');
require_once("modules/SQLReports/ReportRunSQL.php");
require_once("modules/SQLReports/ReportsSQL.php");

global $app_strings;
global $mod_strings;

$reportid = vtlib_purify($_REQUEST["record"]);

$oPrint_smarty=new vtigerCRM_Smarty;
$oReport = new ReportsSQL($reportid);
$oReportRun = new ReportRunSQL($reportid);


$arr_values = $oReportRun->GenerateReport("PRINT",$filterlist);
$total_report = $oReportRun->GenerateReport("PRINT_TOTAL",$filterlist);

$oPrint_smarty->assign("COUNT",$arr_values[1]);
$oPrint_smarty->assign("APP",$app_strings);
$oPrint_smarty->assign("MOD",$mod_strings);
$oPrint_smarty->assign("REPORT_NAME",$oReport->reportname);
$oPrint_smarty->assign("PRINT_CONTENTS",$arr_values[0]);
$oPrint_smarty->assign("TOTAL_HTML",$total_report);
$oPrint_smarty->display("PrintReport.tpl");

?>
