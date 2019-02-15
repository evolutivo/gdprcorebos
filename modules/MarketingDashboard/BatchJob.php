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

abstract class BatchJob {

  public $id = NULL;

  public $userId = NULL;

  protected $adb = NULL;

  protected $itemResults = NULL;

  protected $data = NULL;

  protected $finished = FALSE;

  protected $currentItemId = NULL;

  protected $resultData = NULL;

  public function __construct($id, $userId, $data) {
    $this->adb = PearDatabase::getInstance();
    $this->id = $id;
    $this->userId = $userId;
    $this->data = $data;
  }

  public function requestItems($count = NULL) {
    if (is_null($count)) {
      $query = "select * from batch_items where batch_job_id={$this->id} and done=0";
    } else {
      $query = "select * from batch_items where batch_job_id={$this->id} and done=0 limit {$count}";
    }
    $this->itemResults = $this->adb->query($query);
    return $this->adb->num_rows($this->itemResults);
  }

  public function nextItem() {
    $this->resultData = array();
    $item = $this->adb->getNextRow($this->itemResults, false);
    $this->currentItemId = $item['id'];
    return $item;
  }

  public function markItemDone($data = NULL) {
    if (!is_null($data)) {
      $this->resultData += $data;
    }
    $serializedData = addslashes(serialize($this->resultData));
    $query = "update batch_items set done=1, data='{$serializedData}' where id={$this->currentItemId}";
    $this->adb->query($query);
  }

  public function get($key) {
    if (isset($this->data[$key])) {
      return $this->data[$key];
    }
  }

  public function isFinished() {
    $query = "select count(1) from batch_items where batch_job_id={$this->id} and done=0";
    $res = $this->adb->query($query);
    return $this->adb->query_result($res, 0, 0) == 0;
  }

  public function saveResultData($data) {
    $this->resultData += $data;
  }

  public function summary() {
    $query = "select * from batch_items where batch_job_id={$this->id}";
    $res = $this->adb->query($query);
    $data = array();
    while ($row = $this->adb->getNextRow($res, false)) {
      $row['data'] = unserialize($row['data']);
      if (!$row['data']) {
        $row['data'] = array();
      }
      $data[] = $row;
    }
    return $data;
  }

  public function preRun() {
    global $current_user;
    $this->savedCurrentUser = $current_user;
    $current_user = new Users();
    $current_user->retrieve_entity_info($this->userId, 'Users');
  }

  public function postRun() {
    global $current_user;
    $current_user = $this->savedCurrentUser;
  }

  public abstract function create();

  public abstract function run();

}

