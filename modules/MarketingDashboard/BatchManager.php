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

require_once('modules/MarketingDashboard/BatchJob.php');
require_once('modules/MarketingDashboard/CampaignBatchJob.php');
require_once('modules/MarketingDashboard/AssignmentBatchJob.php');
require_once('modules/MarketingDashboard/CreateContactsBatchJob.php');
require_once('modules/MarketingDashboard/RelateBatchJob.php');

class BatchManager {

  private static $singleton = NULL;

  private $adb = NULL;

  private function __construct() {
    $this->adb = PearDatabase::getInstance();
  }

  public static function getInstance() {
    if (is_null(self::$singleton)) {
      self::$singleton = new BatchManager();
    }
    return self::$singleton;
  }

  public function getJob($jobId = NULL) {
    if (is_null($jobId)) {
      $query = "select * from batch_jobs where done=0 order by created limit 1";
    } else {
      $query = "select * from batch_jobs where id={$jobId}";
    }
    $res = $this->adb->query($query);
    $row = $this->adb->getNextRow($res, false);
    if (!$row) {
      return NULL;
    }
    $jobId = $row['id'];
    $userId = $row['user_id'];
    $jobClassName = $row['class_name'];
    $jobData = unserialize($row['data']);
    $job = new $jobClassName($jobId, $userId, $jobData);
    return $job;
  }

  public function addJob1($jobClassName, $data = null,$delay,$datestarting,$timestarting,$threshold,$nomeutente) {
    global $current_user;
    $serializedData = addslashes(serialize($data));
    $query = "insert into batch_jobs (created, user_id, class_name, data, done,delay,datestarting,timestarting,threshold,username) values (NOW(), {$current_user->id}, '{$jobClassName}', '{$serializedData}', 0,'{$delay}','{$datestarting}','{$timestarting}','{$threshold}','{$nomeutente}')";
    $this->adb->query($query);
    $jobId = $this->adb->getLastInsertID();
    $job = new $jobClassName($jobId, $current_user->id, $data);
    $job->create();
  }
 public function addJob($jobClassName, $data = null,$delay,$datestarting,$timestarting,$threshold,$nomeutente,$selectednr) {
    global $current_user,$log;
    $log->debug("ketojaneparametrat"."j".$jobClassName."j".$delay.$datestarting.$timestarting.$threshold.$nomeutente);
    $serializedData = addslashes(serialize($data));
    $nrofqueues=ceil($selectednr/$threshold);
  $log->debug("queuenumero".$nrofqueues);
     //  var_dump($nrofqueues);exit;
    for($i=1;$i<=$nrofqueues;$i++){
    $query = "insert into batch_jobs (created, user_id, class_name, data, done,delay,datestarting,timestarting,threshold,username) values (NOW(), {$current_user->id}, '{$jobClassName}', '{$serializedData}',0,'{$delay}','{$datestarting}','{$timestarting}','{$threshold}','{$nomeutente}')";
    $this->adb->query($query);
    $jobId = $this->adb->getLastInsertID();
    $job = new $jobClassName($jobId, $current_user->id, $data);
    $job->create1($i,$threshold,$selectednr,$nrofqueues);}
 
    
  }
  public function jobDone($job) {
    $query = "update batch_jobs set done=1 where id={$job->id}";
    $this->adb->query($query);
  }

  public function run($num = NULL) {
    if ($num === 0) {
      return;
    }
    $remaining = $num;
    do {
      $job = $this->getJob();
      if ($job) {
        $count = $job->requestItems($remaining);
        $job->preRun();
        $job->run();
        $job->postRun();
        if ($job->isFinished()) {
          $this->jobDone($job);
        }
        if (!is_null($remaining)) {
          $remaining -= $count;
        }
      }
    } while ($job && $remaining);
  }

  public function runJob($jobId) {
    if (empty($jobId)) {
      return;
    }
    $res = $this->adb->pquery('select * from batch_jobs where id=?',array($jobId));
    $row = $this->adb->getNextRow($res, false);
    if (!$row) {
    	return NULL;
    }
    $jobId = $row['id'];
    $userId = $row['user_id'];
    $jobClassName = $row['class_name'];
    $jobData = unserialize($row['data']);
    $job = new $jobClassName($jobId, $userId, $jobData);
    if ($job) {
      $count = $job->requestItems();
      $job->preRun();
      $job->run();
      $job->postRun();
      if ($job->isFinished()) {
        $this->jobDone($job);
      }
    }
  }

  public function summary($jobId = NULL) {
    if (is_null($jobId)) {
      $query = "select j.*, i.done as item_done, u.user_name, count(i.id) as c
      from batch_jobs j
      join vtiger_users u on u.id=j.user_id
      left join batch_items i on i.batch_job_id=j.id
      group by j.id, i.done
      order by j.created desc";
      $res = $this->adb->query($query);
      $jobsData = array();
      while ($row = $this->adb->getNextRow($res, false)) {
        if (!isset($jobsData[$row['id']])) {
          $jobsData[$row['id']] = array(
            'created' => $row['created'],
            'user_id' => $row['user_id'],
            'user_name' => $row['user_name'],
            'class_name' => $row['class_name'],
            'done' => $row['done'],
            'items_done' => 0,
            'items_pending' => 0,
            'total_items' => 0,
            'delay'=>$row['delay'],
            'datestarting'=>$row['datestarting'],
            'timestarting'=>$row['timestarting'],
            'threshold'=>$row['threshold'],
            'launchmassively'=>$row['launchmassively'],
            );
        }
        $jobData = &$jobsData[$row['id']];
        if ($row['item_done']) {
          $jobData['items_done'] = intval($row['c']);
        } else {
          $jobData['items_pending'] = intval($row['c']);
        }
        $jobData['total_items'] = $jobData['items_done'] + $jobData['items_pending'];
      }
      return $jobsData;
    } else {
      $job = $this->getJob($jobId);
      return $job->summary();
    }
  }

  public function purge($days) {
    if (!is_int($days)) {
      return;
    }
    $this->adb->query("delete from batch_jobs where created<date_sub(now(), interval {$days} day)");
    $this->adb->query("delete i from batch_items i left join batch_jobs j on j.id=i.batch_job_id where j.id is NULL");
  }

}

