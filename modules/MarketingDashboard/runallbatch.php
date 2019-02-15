<?php
/*************************************************************************************************
* Copyright 2012-2013 OpenCubed  --  This file is a part of vtMktDashboard.
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
*  Module       : MarketingDashboard
*  Version      : 1.9
*  Author       : OpenCubed
*************************************************************************************************/
require_once('include/utils/utils.php');
require_once('modules/MarketingDashboard/BatchManager.php');

$jobId = '';
if (isset($argv[1])) {
  $jobId = $argv[1];
} else if (isset($_REQUEST['jobId'])) {
	$jobId = $_REQUEST['jobId'];
} else {
  $jobId = NULL;
}
$querydata=$adb->query("select datestarting,timestarting from batch_jobs where id=$jobId");
$executiondate=$adb->query_result($querydata,0,'datestarting');
$executiontime=$adb->query_result($querydata,0,'timestarting');
//echo $jobId;
//echo $executiondate;
//echo $executiontime;
$queryupdate=$adb->query("update batch_jobs set launchmassively=1 where id=$jobId");
//if (!empty($jobId)) {
//	BatchManager::getInstance()->runJob($jobId);
//}
header("Location: index.php?module=MarketingDashboard&action=index&mytab=4");
?>
