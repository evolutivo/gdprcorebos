<?php
/*************************************************************************************************
* Copyright 2013 JPL TSolucio, S.L. -- This file is a part of vtMktDashboard
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
*  Module       : vtMktDashboard
*  Version      : 1.9
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
?>
<?php require_once('modules/MarketingDashboard/BatchManager.php'); ?>
<?php $data = BatchManager::getInstance()->summary(); ?>
<table class="grid">
<tr>
<th><?php echo getTranslatedString('CRONJOB_Created'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_Name'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_User'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_Done'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_TotalItems'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_PendingItems'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_ProcessedItems'); ?></th>
<th><?php echo getTranslatedString('CRONJOB_Progress'); ?></th>
<th><?php echo getTranslatedString('LBL_ACTION'); ?></th>
<th><?php echo 'Expected Run'; ?></th>
<th><?php echo 'Delete'; ?></th>
</tr>
<?php foreach ($data as $jobId => $jobData): ?>
<tr>
<td><?php echo $jobData['created']; ?></td>
<td><?php echo $jobData['class_name']; ?></td>
<td><?php echo $jobData['user_name']; ?></td>
<td><?php echo $jobData['done']? getTranslatedString('LBL_YES') : getTranslatedString('LBL_NO'); ?></td>
<td><?php echo $jobData['total_items']; ?></td>
<td><?php echo $jobData['items_pending']; ?></td>
<td><?php echo $jobData['items_done']; ?></td>
<td><?php echo $jobData['total_items'] != 0? round($jobData['items_done'] / $jobData['total_items'] * 100, 2) : 100; ?>%</td>
<td>
<?php
 $actions = '';
 if ($jobData['total_items']<5000 and $jobData['items_pending']>0) {
    $actions = '<a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=runjob&jobId='.$jobId.'">'.getTranslatedString('Run').'</a>';
 }
 if ($jobData['items_done']>0) {
    $actions .= (empty($actions) ? '' : ' | ').'<a href="javascript:showJob('.$jobId.');">'.getTranslatedString('View').'</a>';
 }
 echo $actions;
?>
</td>
<?php if($jobData['launchmassively']==1) {?>
<td><?php echo $jobData['datestarting'].' '.$jobData['timestarting'].' '.'<a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=runallbatch&jobId='.$jobId.'">'."Launched".'</a>'.'<a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=runallbatch2&jobId='.$jobId.'">'.'  '."Launched".'</a>';?></td>
<?php } ?>
<?php if($jobData['launchmassively']==0){ ?>
<td><?php echo $jobData['datestarting'].' '.$jobData['timestarting'].' '.'<a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=runallbatch&jobId='.$jobId.'">'."Launch This".'</a>'.'<a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=runallbatch2&jobId='.$jobId.'">'.'  '."Launch All".'</a>';?></td>
<?php } ?>
<td>  <?php echo '<a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=deletejob&jobId='.$jobId.'">'."X".'</a>';?></td>
</tr>
<?php endforeach; ?>
</table>
<style type="text/css" scoped>
#ui-tabs-1 .grid {
  width: 100%;
  border: 1px solid lightgray;
  border-collapse: collapse;
}
#ui-tabs-1 .grid td, #ui-tabs-1 .grid th {
  padding: 0.1em 0.5em;
  border: 1px solid lightgray;
  text-align: center;
}
#ui-tabs-1 .grid a {
  color: blue;
  text-decoration: underline;
}
</style>
<script>
function showJob(jobId) {
  $( "#ui-tabs-1" ).load( "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=batch_job_status&job_id=" + jobId );
}
</script>
