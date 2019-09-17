<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class multiple_datepicker extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$module = 'ContactRole';
			$wasActive = vtlib_isModuleActive($module);
			if (!$wasActive) {
				vtlib_toggleModuleAccess($module, true);
			}
			$modobj = Vtiger_Module::getInstance($module);
			if ($modobj) {
				$block = new Vtiger_Block();
				$block->label = 'LBL_ContactRole_Vacation_Block';
				$block->sequence = 1;
				$modobj->addBlock($block);
            }

            $fields = array(
				'ContactRole' => array(
					'LBL_ContactRole_Vacation_Block' => array(
						'contactrole_vacations' => array(
							'columntype'=>'varchar(1000)',
							'typeofdata'=>'V~M~LE~1000',
							'uitype'=>'1',
							'displaytype'=>'3',
							'label'=>'LBL_ContactRole_Dates',
							'massedit' => 0,
                        ),
					)
				),
			);            
            $this->massCreateFields($fields);
            
			if (!$wasActive) {
				vtlib_toggleModuleAccess($module, false);
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
