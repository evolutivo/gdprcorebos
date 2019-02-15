<?php
/*
 * Copyright 2012 JPL TSolucio, S.L.   --   This file is a part of vtiger CRM.
 * Licensed under the GNU General Public License (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. vtiger CRM distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://www.gnu.org/licenses/>
*/
require_once('include/utils/utils.php');
require_once('config.inc.php');
require_once('modules/Users/Users.php');
require_once 'modules/evvtgendoc/OpenDocument.php'; // open document class
require_once('include/logging.php');
$log =& LoggerManager::getLogger('index');
global $currentModule,$current_language,$current_user,$adb,$mod_strings;

function attach_document($id,$module,$focus,$fileid)
{
    global $currentModule,$current_language,$current_user,$adb,$mod_strings,$root_directory;
    $focus->id;
    
    $current_user = new Users();
    //getting the current user info from flat file
    $result = $current_user->retrieveCurrentUserInfoFromFile($focus->column_fields['assigned_user_id']);
    
    $orgfile=$adb->pquery("Select CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name 
                                              from vtiger_notes n
                                              join vtiger_seattachmentsrel sa on sa.crmid=n.notesid
                                              join vtiger_attachments a on a.attachmentsid=sa.attachmentsid
                                              where n.notesid=? ",array($fileid));
    $docuPath=$adb->query_result($orgfile,0,'filepath');
    
    $docuInfo = array(
                      'fname'=>$docuPath,
                      'fpath'=>$root_directory.$docuPath,
                      );
    
    if($fileid != 0)
    {
        $relatedquery = "insert into vtiger_senotesrel values(?,?)";
        $relatedresult = $adb->pquery($relatedquery, array($id, $fileid));
        
        $relatedquery = "insert into vtiger_senotesrel values(?,?)";
        $relatedresult = $adb->pquery($relatedquery, array($focus->id, $fileid));
    }
        
    return $docuInfo;
   
    
}

function gen_odt_document($id,$module,$focus,$gendoctemplate)
{
    global $currentModule,$current_language,$current_user,$adb,$mod_strings;
    $fileid=$gendoctemplate;
    $record=$focus->id;
    
    $current_user = new Users();
    //getting the current user info from flat file
    $result = $current_user->retrieveCurrentUserInfoFromFile($focus->column_fields['assigned_user_id']);
    
     $orgfile=$adb->pquery("Select CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name 
                                              from vtiger_notes n
                                              join vtiger_seattachmentsrel sa on sa.crmid=n.notesid
                                              join vtiger_attachments a on a.attachmentsid=sa.attachmentsid
                                              where n.notesid=? ",array($fileid));
    $mergeTemplatePath=$root_directory.$adb->query_result($orgfile,0,'filepath');
    $mergeTemplateName=$adb->query_result($orgfile,0,'name');
    
    if(!empty($record)) 
    {
        $odtout = new OpenDocument;
        if(!is_dir('gendocoutput/'.$module)) mkdir('gendocoutput/'.$module);
        if (file_exists('gendocoutput/'.$module.'/odtout'.$record.'.odt')) unlink('gendocoutput/'.$module.'/odtout'.$record.'.odt');
        $odtout->GenDoc($mergeTemplatePath,$record,$module);
        $odtout->save('gendocoutput/'.$module.'/odtout'.$record.'.odt');
        ZipWrapper::copyPictures($mergeTemplatePath, 'gendocoutput/'.$module.'/odtout'.$record.'.odt');
        $pathfilename = $root_directory.'gendocoutput/'.$module.'/odtout'.$record.'.odt';
        $filename = 'odtout'.$record.'.odt';
        
        return $filename;
    }    
    
}

function evvt_createdocument($related_to,$moduleName,$focus_message,$filepath,$filename)
{
    global $adb,$log,$mod_strings,$current_user;
    global $root_directory;
    $log->debug("INPUT ARRAY for the function add_expgen_attachment");
    $log->debug($input_array);

    $filename = $filename;
    $filetype = 'application/vnd.oasi';
    $filedirectory = $filepath;

    //decide the file path where we should upload the file in the server
    $upload_filepath = decideFilePath();

    $attachmentid = $adb->getUniqueID("vtiger_crmentity");

    $new_filename = $attachmentid.'_'.$filename;

    //write a file with the passed content
    @copy($filedirectory,$upload_filepath.$new_filename);
    

    
    $date_var = $adb->formatDate(date('YmdHis'), true);
    $description = $focus_message->column_fields['description'];
  
    $crmquery = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
    $crmresult = $adb->pquery($crmquery, array($attachmentid, $current_user->id,$current_user->id,'Documents Attachment', $description, $date_var, $date_var));

    $attachmentquery = "insert into vtiger_attachments(attachmentsid,name,description,type,path,subject) values(?,?,?,?,?,?)";
    $attachmentresult = $adb->pquery($attachmentquery, array($attachmentid, $filename, $description, $filetype, $upload_filepath,$focus_message->column_fields['messagesname']));

    $notesid = $adb->getUniqueID("vtiger_crmentity");
    $crmquery = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
    $crmresult = $adb->pquery($crmquery, array($notesid, $current_user->id,$current_user->id,'Documents', $description, $date_var, $date_var));

    $notesquery = "INSERT INTO vtiger_notes (notesid, note_no, title, filename, notecontent, folderid, filetype, filelocationtype, filedownloadcount, filestatus, filesize, fileversion,template) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $notesresult = $adb->pquery($notesquery, array($notesid,'MKT'.$notesid ,$focus_message->column_fields['messagesname'],$filename, $description, 1, $filetype, 'I', 0, 1, filesize($upload_filepath.$new_filename) ,1,0));
    $notesquery = "INSERT INTO vtiger_notescf (notesid) VALUES (?)";
    $notesresult = $adb->pquery($notesquery, array($notesid));
    
    $relatedquery = "insert into vtiger_seattachmentsrel values(?,?)";
    $relatedresult = $adb->pquery($relatedquery, array($notesid, $attachmentid));        
    
    if($notesid != 0)
    {
        $relatedquery = "insert into vtiger_senotesrel values(?,?)";
        $relatedresult = $adb->pquery($relatedquery, array($related_to, $notesid));
        
        $relatedquery = "insert into vtiger_senotesrel values(?,?)";
        $relatedresult = $adb->pquery($relatedquery, array($focus_message->id, $notesid));
    }     

}



?>
