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
<?php
function LOCAL_getEntityName($id, $crmType = NULL) {
  if (is_null($crmType)) {
    $crmType = getSalesEntityType($id);
  }
  $entityName = getEntityName($crmType, $id);
  if (!empty($entityName)) {
    return $entityName[$id];
  }
}
function LOCAL_getEntityLink($id, $crmType = NULL) {
  if (is_null($crmType)) {
    $crmType = getSalesEntityType($id);
  }
  $entityName = LOCAL_getEntityName($id, $crmType);
  return "<a href=\"index.php?module={$crmType}&action=DetailView&record={$id}\">{$entityName}</a>";
}
?>
<?php require_once('modules/MarketingDashboard/BatchManager.php'); ?>
<?php $data = BatchManager::getInstance()->summary($_REQUEST['job_id']); ?>
<span style="float:right"><a href="javascript:showListOfJobs();"><?php echo getTranslatedString('LBL_BACK'); ?></a></span>
<table class="grid">
<tr>
<th><?php echo getTranslatedString('Entity'); ?></th>
<th><?php echo getTranslatedString('Title'); ?></th>
<th><?php echo getTranslatedString('Account'); ?></th>
<th><?php echo getTranslatedString('Email'); ?></th>
</tr>
<?php foreach ($data as $itemData): ?>
<?php if (!isset($itemData['data']['id'])) { continue; } ?>
<tr>
<td><?php echo $itemData['crmtype']; ?></td>
<td><?php echo LOCAL_getEntityLink($itemData['data']['id']); ?></td>
<td><?php echo LOCAL_getEntityName($itemData['data']['accountId'], 'Accounts'); ?></td>
<td><?php echo $itemData['data']['email']; ?></td>
</tr>
<?php endforeach; ?>
</table>
<span style="float:right"><a href="javascript:showListOfJobs();"><?php echo getTranslatedString('LBL_BACK'); ?></a></span>
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
function showListOfJobs() {
  $("#ui-tabs-1").load("index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=batch_status");
}
</script>
