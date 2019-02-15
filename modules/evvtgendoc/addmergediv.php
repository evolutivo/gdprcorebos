<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

class AddGenDocMergeDIV extends VTEventHandler {

	/**
	 * @param $handlerType
	 * @param $entityData VTEntityData
	 */
	public function handleEvent($handlerType, $entityData) {
		global $adb, $log, $currentModule;
		if ($handlerType == 'corebos.header') {
			$mname = (isset($currentModule) ? $currentModule : '');
			$tabid = getTabid($mname);
			$checkres = $adb->pquery('SELECT linkid FROM vtiger_links WHERE tabid=? AND linktype=? AND linklabel=?', Array($tabid, 'DETAILVIEWWIDGET', 'Generate Document'));
			if ($adb->num_rows($checkres)) {
				require_once('Smarty_setup.php');
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign('MODULE', $mname);
				$smarty->assign('ID', (isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : ''));
				$smarty->display('modules/evvtgendoc/gendocdiv.tpl');
			}
		}
	}

	public function handleFilter($handlerType, $parameter) {
		return $parameter;
	}
}
