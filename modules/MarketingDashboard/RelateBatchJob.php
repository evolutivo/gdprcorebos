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

class RelateBatchJob extends BatchJob {

	public function create() {
		$rsprocess = $this->adb->query("insert into batch_items (batch_job_id, crmid, crmtype) select {$this->id}, vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_campaignresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_campaignresults.crmid where mktdb_campaignresults.selected=1 and mktdb_campaignresults.userid={$this->userId}");
	}

	public function run() {
		$module = $this->get('module');
		$id = $this->get('id');
		$entity = CRMEntity::getInstance($module);
		$entity->retrieve_entity_info($id, $module);
		while ($item = $this->nextItem()) {
			$entity->save_related_module($module, $id, $item['crmtype'], $item['crmid']);
			$this->markItemDone();
		}
	}
}
