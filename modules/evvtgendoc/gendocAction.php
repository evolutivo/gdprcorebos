<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : evvtgendoc
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
$Vtiger_Utils_Log = false;
include_once('vtlib/Vtiger/Module.php');
require_once 'modules/evvtgendoc/OpenDocument.php';
require_once('modules/Documents/Documents.php');
global $currentModule,$current_language,$current_user,$adb,$mod_strings,$log;
$fileid=vtlib_purify($_REQUEST['gdtemplate']);
$record=vtlib_purify($_REQUEST['gdcrmid']);
$record = preg_replace('/[^0-9]/', '', $record);
$module=vtlib_purify($_REQUEST['gdmodule']);
$module=preg_replace('/[[:^alnum:]]/', '', $module);
$modulei18n = getTranslatedString('SINGLE_'.$module,$module);
$format=vtlib_purify($_REQUEST['gdformat']);
$action=vtlib_purify($_REQUEST['gdaction']);

$holdUser = $current_user;
$current_user = Users::getActiveAdminUser();

$orgfile=$adb->pquery("Select CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name
		from vtiger_notes n
		join vtiger_seattachmentsrel sa on sa.crmid=n.notesid
		join vtiger_attachments a on a.attachmentsid=sa.attachmentsid
		where n.notesid=?",array($fileid));
$mergeTemplatePath=$adb->query_result($orgfile,0,'filepath');
$mergeTemplateName=basename($adb->query_result($orgfile,0,'name'),'.odt');
if(!empty($record)) {
	$out = '';
	$fullfilename = $root_directory .  OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
	$fullpdfname = $root_directory . OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
	$filename = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
	$pdfname = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
	$odtout = new OpenDocument();
	if (!is_dir(OpenDocument::GENDOCCACHE . '/' . $module)) {
		mkdir( OpenDocument::GENDOCCACHE . '/' . $module);
	}
	if (file_exists($fullfilename)) unlink($fullfilename);
	if (file_exists($fullpdfname)) unlink($fullpdfname);
	$odtout->GenDoc($mergeTemplatePath,$record,$module);
	$odtout->save($filename);
	ZipWrapper::copyPictures($mergeTemplatePath, $filename, $odtout->changedImages, $odtout->newImages);
	$odtout->postprocessing($fullfilename);
	if ($format=='pdf') {
		$odtout->convert($filename,$pdfname);
	}
	$einfo = getEntityName($module, $record);
	$name = str_replace(' ', '_', $modulei18n.'_'.$einfo[$record]);
	switch ($action) {
		case 'export':
			header("Pragma: public");
			header('Expires: 0');
			header("Cache-Control: private must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: PHP OpenOffice Generated Data");
			if ($format=='doc') {
				header("Content-type: application/vnd.oasis.opendocument.text");
				header('Content-Disposition: attachment; filename="'.$name.'.odt"');
				readfile($filename);
			} else {
				header("Content-type: application/pdf");
				header('Content-Disposition: attachment; filename="'.$name.'.pdf"');
				readfile($pdfname);
			}
			break;
		case 'email':
			$sname = 'storage/'.$modulei18n.'_'.$record.($format=='doc'?'.odt':'.pdf');
			if (file_exists($sname)) unlink($sname);
			rename(($format=='doc'?$filename:$pdfname),$sname);
			break;
		case 'save':
			$doc = new Documents();
			$doc->column_fields["notes_title"] = $modulei18n.' '.$einfo[$record].' '.$mergeTemplateName;
			$doc->column_fields["notecontent"] = '';
			$doc->column_fields["fileversion"] = 1;
			$doc->column_fields["docyear"] = date('Y');
			$doc->column_fields["filelocationtype"] = 'I';
			$gdfolder = GlobalVariable::getVariable('GenDoc_Save_Document_Folder','');
			if ($gdfolder!='') {
				$sql = 'select folderid from vtiger_attachmentsfolder where foldername=?';
				$res = $adb->pquery($sql, array($gdfolder));
				if ($adb->num_rows($res)==0) {
					$sql = "select folderid from vtiger_attachmentsfolder order by foldername";
					$res = $adb->pquery($sql, array());
				}
			} else {
				$sql = "select folderid from vtiger_attachmentsfolder order by foldername";
				$res = $adb->pquery($sql, array());
			}
			$doc->column_fields["folderid"]=$adb->query_result($res, 0, "folderid");
			$doc->column_fields["filestatus"] = '1';
			$doc->date_due_flag = 'off';
			$_REQUEST['assigntype'] = 'U';
			$doc->column_fields['assigned_user_id'] = $current_user->id;
			unset($_FILES);
			$name .= '_'.str_replace(' ', '_', $mergeTemplateName);
			$f=array(
				'name'=>$name.($format=='pdf' ? '.pdf' : '.odt'),
				'type'=> ($format=='pdf' ? 'application/pdf' : 'application/vnd.oasis.opendocument.text'),
				'tmp_name'=> ($format=='pdf' ? $fullpdfname : $fullfilename),
				'error'=>0,
				'size'=>filesize(($format=='pdf' ? $fullpdfname : $fullfilename))
			);
			$_FILES["file0"] = $f;
			$doc->column_fields["filename"] = $f['name'];
			$doc->column_fields["filesize"] = $f['size'];
			$doc->column_fields["filetype"] = $f['type'];
			$_REQUEST['createmode'] = 'link';
			$_REQUEST['return_module'] = $module;
			$_REQUEST['return_id'] = $record;
			$doc->save("Documents");
			break;
	}
	$out.= '<a href="' . OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt">' . $app_strings['DownloadMergeFile'] . '</a></td></tr></table><br/>';
}
$current_user = $holdUser;
?>
