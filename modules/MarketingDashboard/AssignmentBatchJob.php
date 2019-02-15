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

class AssignmentBatchJob extends BatchJob {

  public function create() {
    $type = 'a';
    if ($this->get('myaction') == 'moveon' && $this->get('modulefrom') == 4) {
      $type = 'c';
    }
    $rsprocess = $this->adb->query("insert into batch_items (batch_job_id, crmid, crmtype) select {$this->id}, vtiger_crmentity.crmid, vtiger_crmentity.setype from mktdb_assignresults join vtiger_crmentity on vtiger_crmentity.crmid=mktdb_assignresults.crmid where mktdb_assignresults.selected=1 and mktdb_assignresults.userid={$this->userId} and mktdb_assignresults.entity='{$type}'");
  }

  public function run() {
    $myaction = $this->get('myaction');
    if ($myaction == 'reassign') {
      $fieldfrom='assigned_user_id';
      $fieldto='assigned_user_id';
	  } else {
	    $fieldfrom = $this->get('modulefromfields');
	    $fieldto = $this->get('moduletofields');
	  }
	  while ($item = $this->nextItem()) {
	    $relatedEntitiesAssigned = array();
	    $recordid = $item['crmid'];
	    $convertto = $item['crmtype'];
	    $entity_instance = CRMEntity::getInstance($convertto);
	    $entity_instance->retrieve_entity_info($recordid, $convertto);
	    for ($j=0;$j<count($allrelations);$j++){
	      $relationId=$allrelations[$j];
	      if(($assigntoaccounts!=0 && $relationId==0 && $myaction=="reassign") || ( $myaction=="moveon" && $relationId==0) ){
	        if($myaction=="reassign") {
	          $entity_instance->column_fields[$fieldto]=$assigntoaccounts;
	        } else {
	          $entity_instance->column_fields[$fieldto]=$entity_instance->column_fields[$fieldfrom];
	        }
	        $entity_instance->mode="edit";
	        $entity_instance->id=$recordid;
	        $entity_instance->save($convertto);
	      } elseif($relationId==10000000){ // hard coded modcomments
	        // we can only reassign
	        if ($assigntoaccounts!=0 && $myaction=="reassign") {
	          $r = $this->adb->pquery("SELECT modcommentsid FROM vtiger_modcomments WHERE related_to=?",array($recordid));
	          while ($d = $this->adb->getNextRow($r, false)) {
	            $relatedEntitiesAssigned[] = $d['modcommentsid'];
	          }
	          $this->adb->pquery("update vtiger_crmentity set smownerid=? where setype=\'ModComments\' and crmid in
	            (SELECT modcommentsid FROM vtiger_modcomments WHERE related_to=?)",array($assigntoaccounts,$recordid));
	        }
	      } elseif($relationId!=0){
	        $relationInfo = getRelatedListInfoById($relationId);
	        $relatedModule = getTabModuleName($relationInfo["relatedTabId"]);
	        $focus=CRMEntity::getInstance($relatedModule);
	        $function_name = $relationInfo["functionName"];
	        $vlera= $entity_instance->$function_name($recordid, getTabid($convertto),$relationInfo["relatedTabId"]);
	        foreach($vlera['entries'] as $a=>$b){
	          $focus=CRMEntity::getInstance($relatedModule);
	          $focus->retrieve_entity_info($a,$relatedModule);
	          if($assigntoaccounts!=0 && $myaction=="reassign") {
	            $focus->column_fields[$fieldto]=$assigntoaccounts;
	          } else {
	            $focus->column_fields[$fieldto]=$entity_instance->column_fields[$fieldfrom];
	          }
	          $focus->mode="edit";
	          $focus->id=$a;
	          $focus->save($relatedModule);
	          $relatedEntitiesAssigned[] = $focus->id;
	        }
	      }
	    }
	    $this->markItemDone(array(
	      'email' => $entity_instance->column_fields['email1'],
	      'related_entities' => $relatedEntitiesAssigned,
	      ));
	  }
  }
}
