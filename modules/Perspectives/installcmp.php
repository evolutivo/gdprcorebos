<?php

/* * ***********************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
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
 * **********************************************************************************************
 *  Module       : Perspectives
 *  Version      : 5.4.0
 *  Author       : axhemshahaj
 * *********************************************************************************************** */

if (isset($_REQUEST['module_name']) && isset($_REQUEST['record_id'])) {

    $recordid = htmlspecialchars($_REQUEST['record_id']);
    $module = htmlspecialchars($_REQUEST['module_name']);
    $filename = getFileNameById($recordid);
    $newfilename = 'composer.json';
    $dir = 'perspectives';
    global $root_directory;
    $source = $root_directory . $dir . '/' . $filename;
    $destination = $root_directory . $newfilename;

    //read and write to file.
    $contents = readaFile($source);
    writeFile($destination, $contents);

    //execute command via shell_exec()
    //$output = shell_exec('composer install 2>&1; echo $?');
    putenv('HOME=/root');
    shell_exec("rm -rf $root_directory.'vendor'");
    shell_exec("rm -rf $root_directory.'composer.lock'");
    shell_exec("composer install 2>&1");
    shell_exec("composer update 2>&1");
    echo json_encode(array('module' => $module, 'record_id' => $recordid));
    exit();
}

/**
 * 
 * @param type $file
 * @return string
 */
function readaFile($file) {
    $contents = "";
    $openfile = fopen($file, 'r') or die("can't open file");
    //check if opened file is empty[No data]
    if (filesize($file) != 0) {
        while (!feof($openfile)) {
            $contents .= fgets($openfile);
        }
        fclose($openfile);
    } else {
        echo 'File has no Data to read';
    }
    return $contents;
}

/**
 *
 * @param string $destination
 * @param string $contents
 * @return string message if file is created or nor.
 */
function writeFile($destination, $contents) {
    $output = "";
    if ($contents != "") {
        $composer = fopen($destination, 'w') or die("can't open file");
        fwrite($composer, $contents);
        fclose($composer);
        $output .= 'Composer.json crated succesfully';
    } else {
        $output .= "Can not crate composer.json file";
    }
    return $output;
}

/**
 *
 * @global type $adb
 * @param int $recordId
 * @return string filename
 */
function getFileNameById($recordId) {
    global $adb;
    $query = 'SELECT perspective_file FROM vtiger_perspectives where perspectivesid=?';
    $result = $adb->pquery($query, array($recordId));
    if ($result and $adb->num_rows($result) > 0) {
        $res = $adb->query_result($result);
        return $res;
    } else {
        return NULL;
    }
}
