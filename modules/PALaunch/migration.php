<?php
require_once 'include/utils/CommonUtils.php';

$current_user = CRMEntity::getInstance('Users');
$current_user->retrieveCurrentUserInfoFromFile(1);
$current_user->time_zone = DateTimeField::getDBTimeZone();

$palaunch = CRMEntity::getInstance('PALaunch');
$palaunch->column_fields['assigned_user_id'] = $current_user->id;

// Disable Vtiger callbacks for faster processing
$palaunch->disableVtCallbacks = true;

$statusTr = array('Pending', 'Done', 'Error', 'Optout', 'Skipped');

$adb->query('BEGIN');

$query = "select * from vtiger_plannedactions_contacts";
$res = $adb->query($query);
$count = 0;
$t0 = microtime(true);
while ($row=$adb->getNextRow($res, false)) {
	$palaunch->column_fields['description'] = $row['id'];
	$palaunch->column_fields['plannedaction_id'] = $row['plannedaction_id'];
	$palaunch->column_fields['related_id'] = $row['contact_id'];
	$palaunch->column_fields['recipient_id'] = $row['contact_id'];
	$palaunch->column_fields['sequencer_id'] = $row['sequencer_id'];
	$date = new DateTimeField($row['sched_date']);
	$palaunch->column_fields['scheduled_date'] = $date->getDisplayDateTimeValue();
	if (!empty($row['sent_date'])) {
		$date = new DateTimeField($row['sent_date']);
		$processedDate = $date->getDisplayDateTimeValue();
	} else {
		$processedDate = NULL;
	}
	$palaunch->column_fields['processed_date'] = $processedDate;
	$palaunch->column_fields['palaunch_status'] = $statusTr[$row['status']];
	$palaunch->save('PALaunch');
	if ($count%100 == 0) {
		$t1 = microtime(true);
		if ($count != 0) {
			$tu = ($t1 - $t0) / $count;
			$ut = 1 / $tu;
		} else {
			$tu = 0;
			$ut = 0;
		}
		echo "{$count} {$tu}s/op {$ut}op/s \r";
	}
	$count++;
}

$adb->query('COMMIT');

$t1 = microtime(true);
$t = $t1 - $t0;

echo "\n";
echo "Registros migrados: {$count}\n";
echo "Tiempo: {$t}s\n";

