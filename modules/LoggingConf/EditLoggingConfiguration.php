<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : LoggingConf
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/

require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
require_once('modules/LoggingConf/LoggingUtils.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";


$field_module=getLoggingModules();
$allfields=Array();
foreach($field_module as $fld_module)
{
	
        $fieldListResult = getDefOrgFieldList($fld_module);
	$noofrows = $adb->num_rows($fieldListResult);
	$language_strings = return_module_language($current_language,$fld_module);
	$allfields[$fld_module] = getStdOutput($fieldListResult, $noofrows, $language_strings,$profileid);
}

if($_REQUEST['fld_module'] != '')
{$smarty->assign("DEF_MODULE",vtlib_purify($_REQUEST['fld_module']));
$type=getEntitylogtype(getTabId(vtlib_purify($_REQUEST['fld_module'])));
$indextype=getEntitylogindextype(getTabId(vtlib_purify($_REQUEST['fld_module'])));
$brelastic=getEntitylogbr(getTabId(vtlib_purify($_REQUEST['fld_module'])));
$type=explode(",",$type);
if(in_array('denormalized',$type))
        $denorm='checked';
if(in_array('normalized',$type))
        $norm='checked';
if(in_array('entitylog',$type))
        $elog='checked';
$smarty->assign("denorm",$denorm);
$smarty->assign("norm",$norm);
$smarty->assign("elog",$elog);
$smarty->assign("fldvalues",$brelastic);
$smarty->assign("indextype",$indextype);
}
else
	$smarty->assign("DEF_MODULE",'Movement');

/** Function to get the field label/permission array to construct the default orgnization field UI for the specified profile 
  * @param $fieldListResult -- mysql query result that contains the field label and uitype:: Type array
  * @param $mod_strings -- i18n language mod strings array:: Type array
  * @param $profileid -- profile id:: Type integer
  * @returns $standCustFld -- field label/permission array :: Type varchar
  *
 */	
function getStdOutput($fieldListResult, $noofrows, $lang_strings,$profileid)
{
	global $adb;
        require_once('modules/LoggingConf/LoggingUtils.php');
        require_once('include/utils/utils.php');
	$standCustFld = Array();
	if(!isset($focus->mandatory_fields)) $focus->mandatory_fields = Array();
        $loop=0;
	for($i=0; $i<$noofrows; $i++)
	{ 
		$fieldname = $adb->query_result($fieldListResult,$i,"fieldname");
		$uitype = $adb->query_result($fieldListResult,$i,"uitype");

                if($uitype=='10'){
                $modulerel=  $adb->query_result($fieldListResult,$i,"relmodule");  
                }
                else if($uitype==51 || $uitype==50 || $uitype==73 || $uitype==68)
                {$modulerel="Accounts";
              
                }
                else if($uitype==57){
                    $modulerel="Contacts";
                                  }
                    else if($uitype==59){
                    $modulerel="Products";
  
                    }
                        else if($uitype==58){
                    $modulerel="Campaigns";
    
                        }
                        else if($uitype==76){
                    $modulerel="Potentials";

                        }
                        else if($uitype==75 || $uitype==81){
                    $modulerel="Vendors";

                    }
                        else if($uitype==78){
                    $modulerel="Quotes";

                    }
                        else if($uitype==80){
                    $modulerel="SalesOrder";

                        }
  
                else $modulerel='';
            
		$displaytype = $adb->query_result($fieldListResult,$i,"displaytype");
		$fieldlabel = $adb->query_result($fieldListResult,$i,"fieldlabel");
                $tabid=$adb->query_result($fieldListResult,$i,"tabid");
                $moduleName=getTabModuleName($tabid);
              
		if($lang_strings[$fieldlabel] !='')
			$standCustFld []= $mandatory.' '.$lang_strings[$fieldlabel];
		else
			$standCustFld []= $mandatory.' '.$fieldlabel;
                $logged=isLogged($adb->query_result($fieldListResult,$i,"fieldid") ,$tabid);
		if($logged[0])
		{
			$visible = "checked";
		}		
		else
		{
			$visible = "";
		}
                if($logged[1])
		{
			$visible1 = "checked";
		}		
		else
		{
			$visible1 = "";
		}
                
		$standCustFld []= '<input type="checkbox" value="'.$adb->query_result($fieldListResult,$i,"fieldid").'" name="fieldstobelogged'.$moduleName.'[]"'.$visible .'>';
	$standCustFld []= '<input type="checkbox" value="'.$adb->query_result($fieldListResult,$i,"fieldid").'" name="fieldselastic'.$moduleName.$loop.'"'.$visible1 .'>';
    $fieldid=$adb->query_result($fieldListResult,$i,"fieldid");
        if($uitype==68 && $u68!=1) {$u68=1;$i=$i-1;}
                else if ($uitype==68 && $u68==1) $modulerel='Contacts';
                else $u68='';
        if($uitype=='10' || $uitype=='51' || $uitype=='50' || $uitype=='73' || $uitype=='68' || $uitype=='57' || $uitype=='59'  || $uitype=='58'  || $uitype=='76'  || $uitype=='75'  || $uitype=='80' || $uitype=='78' || $uitype=='81') 
{
    $relmodule=explode(";",getEntitylogrelmodule(getTabId($moduleName)));

 if(in_array($modulerel.':'.$uitype,$relmodule)) $selected='selected';
 else $selected='';
$modname="modulerel".$moduleName.$fieldid.$loop.'[]';
//if($uitype=='68') {
// if(in_array('Contacts:'.$uitype,$relmodule)) $selected2='selected';
// else $selected2='';
//    $opt2='<option value="Contacts:'.$uitype.'" '.$selected2.'>Contacts</option>';
//}
//else $opt2='';
$standCustFld []='<select id="'.$modname.'" ><option value="Nessuno">Nessuno</option><option value='.$modulerel.':'.$uitype.' '.$selected.'>'.$modulerel.'</option>'.$opt2.'</select>';
}
else $standCustFld []='';
$loop++;	}
	$standCustFld=array_chunk($standCustFld,4);	
	$standCustFld=array_chunk($standCustFld,4);	
	return $standCustFld;
}

$smarty->assign("FIELD_INFO",$field_module);
$smarty->assign("FIELD_LISTS",$allfields);
$smarty->assign("MOD", return_module_language($current_language,'LoggingConf'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MODE",'edit');                    
$smarty->display("LoggConfContents.tpl");

?>