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
 *  Author       : OpenCubed
 *************************************************************************************************/
require_once('include/utils/CommonUtils.php');
global $default_charset,$adb;

if(isset($_REQUEST['templateid']) && $_REQUEST['templateid'] !='')
{
	$res=$adb->pquery("Select description,subject from vtiger_emailstemplates"
                . " join vtiger_crmentity on crmid=emailstemplatesid"
                . " where  emailstemplatesid= ?",array($_REQUEST['templateid']));                                                                
        $description=$adb->query_result($res,0,'description');
        $subject=$adb->query_result($res,0,'subject');
}
?>
<form name="frmrepstr" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="subject" value="<?php echo $subject;?>"></input>
<textarea name="repstr" style="visibility:hidden">
<?php echo htmlentities($description, ENT_NOQUOTES, $default_charset); ?>
</textarea>
</form>
<script type="text/javascript">
//my changes
if(typeof window.opener.document.getElementById('subject') != 'undefined' &&
	window.opener.document.getElementById('subject') != null){
	window.opener.document.getElementById('subject').value = window.document.frmrepstr.subject.value;
	window.opener.document.getElementById('description').value = window.document.frmrepstr.repstr.value;
	window.opener.oCKeditor.setData(window.document.frmrepstr.repstr.value);
}
window.close();
</script>
