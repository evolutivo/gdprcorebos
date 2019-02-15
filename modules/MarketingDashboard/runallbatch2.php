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
//$querydata=$adb->query("select * from batch_jobs where done=0 ");

//$nrresults=$adb->num_rows($querydata);
$checkbatch=$adb->query("select randomnr from batch_jobs where id=$jobId ");
$batchnr=$adb->query_result($checkbatch,0,0);
$querydata=$adb->query("select * from batch_jobs where done=0 and launchmassively!=1 and id!=$jobId and randomnr=$batchnr order by created");
 $upquery=$adb->query("update batch_jobs set launchmassively=1 where id=$jobId");
$nrresults=$adb->num_rows($querydata);
for($i=0;$i<$nrresults;$i++){
global $log;
$log->debug("herehere");
    $jID=$adb->query_result($querydata,$i,0);
    $querydelay=$adb->query("select * from batch_jobs where done=0 and id=$jobId");
   // $delaybetweenjobs=$adb->query_result($querydelay,0,'delay');
 $delay1=$adb->query_result($querydelay,0,'delay');
    if($i==0)
    {$delaybetweenjobs=$delay1;}
else {
     $delaybetweenjobs=$delay1*($i+1);
}
    $initialtime=$adb->query_result($querydelay,0,'datestarting').$adb->query_result($querydelay,0,'timestarting');
    $timeconverted=strtotime($initialtime);
    $minutes='+'.$delaybetweenjobs.' minutes';       
    $dateofexecution=date('d-m-Y',strtotime($minutes,strtotime($initialtime)));
    $log->debug("dateofexecution".$dateofexecution.$minutes);
    $timeofexecution= date('H:i', strtotime($minutes, strtotime($initialtime)));
    if($jID!=$jobId)
    $upquery=$adb->query("update batch_jobs set datestarting='$dateofexecution',timestarting='$timeofexecution',launchmassively=1 where id=$jID");
   // else
     //   $upquery=$adb->query("update batch_jobs set launchmassively=1 where id=$jID");
   
    
}


header("Location: index.php?module=MarketingDashboard&action=index&mytab=4");
?>
