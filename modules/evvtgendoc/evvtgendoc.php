<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class evvtgendoc extends CRMEntity {

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$module = Vtiger_Module::getInstance('evvtgendoc');
			require_once 'modules/evvtgendoc/OpenDocument.php';
			@mkdir(OpenDocument::GENDOCCACHE);

			global $adb;
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.header', 'modules/evvtgendoc/addmergediv.php', 'AddGenDocMergeDIV');

			$global_variables = array(
				'GenDoc_Save_Document_Folder',
				'GenDoc_Company_Account',
			);
			$moduleInstance = Vtiger_Module::getInstance('GlobalVariable');
			$field = Vtiger_Field::getInstance('gvname',$moduleInstance);
			if ($field) {
				$field->setPicklistValues($global_variables);
			}

			$mod_pr = VTiger_Module::getInstance('Documents');
			$block_pr = VTiger_Block::getInstance('LBL_NOTE_INFORMATION',$mod_pr);
			$field8 = new Vtiger_Field();
			$field8->name = 'template';
			$field8->label= 'Template';
			$field8->column = 'template';
			$field8->columntype = 'INT(1)';
			$field8->uitype = 56;
			$field8->displaytype = 1;
			$field8->presence = 0;
			$field8->sequence = 6;
			$block_pr->addField($field8);

			$field8 = new Vtiger_Field();
			$field8->name = 'template_for';
			$field8->label= 'Template For';
			$field8->column = 'template_for';
			$field8->columntype = 'VARCHAR(150)';
			$field8->uitype = 1613;
			$field8->displaytype = 1;
			$field8->presence = 0;
			$block_pr->addField($field8);
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}
}
?>
