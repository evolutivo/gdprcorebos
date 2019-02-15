<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source in cooperation with a-g-c (Andreas Goebel)
 * The Initial Developer of the Original Code is vtiger and a-g-c (Andreas Goebel)
 * Portions created by vtiger are Copyright (C) vtiger, portions created by a-g-c are Copyright (C) a-g-c.
 * www.a-g-c.de
 * All Rights Reserved.
 ************************************************************************************/
global $calpath;
global $app_strings,$mod_strings;
global $theme;
global $log;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/database/PearDatabase.php');
require_once('data/CRMEntity.php');
require_once("modules/Reports/Reports.php");

class ReportRunSQL extends CRMEntity
{
	var $reporttype;
	var $reportname;
        var $sqlcode;
        var $reportdesc;
		
	var $convert_currency = array('Potentials_Amount', 'Accounts_Annual_Revenue', 'Leads_Annual_Revenue', 'Campaigns_Budget_Cost', 
									'Campaigns_Actual_Cost', 'Campaigns_Expected_Revenue', 'Campaigns_Actual_ROI', 'Campaigns_Expected_ROI');
	//var $add_currency_sym_in_headers = array('Amount', 'Unit_Price', 'Total', 'Sub_Total', 'S&H_Amount', 'Discount_Amount', 'Adjustment');
	var $append_currency_symbol_to_value = array('Products_Unit_Price','Services_Price', 
						'Invoice_Total', 'Invoice_Sub_Total', 'Invoice_S&H_Amount', 'Invoice_Discount_Amount', 'Invoice_Adjustment', 
						'Quotes_Total', 'Quotes_Sub_Total', 'Quotes_S&H_Amount', 'Quotes_Discount_Amount', 'Quotes_Adjustment', 
						'SalesOrder_Total', 'SalesOrder_Sub_Total', 'SalesOrder_S&H_Amount', 'SalesOrder_Discount_Amount', 'SalesOrder_Adjustment', 
						'PurchaseOrder_Total', 'PurchaseOrder_Sub_Total', 'PurchaseOrder_S&H_Amount', 'PurchaseOrder_Discount_Amount', 'PurchaseOrder_Adjustment'
						);
	var $ui10_fields = array();
	
	/** Function to set reportid,primarymodule,secondarymodule,reporttype,reportname, for given reportid
	 *  This function accepts the $reportid as argument
	 *  It sets reportid,primarymodule,secondarymodule,reporttype,reportname for the given reportid
	 */
	function ReportRunSQL($reportid)
	{
		$oReport = new ReportsSQL($reportid);
		$this->reportid = $reportid;
		$this->reportname = $oReport->reportname;
                $this->sqlcode = $oReport->sqlcode;
                //$this->reportdesc = $oReport->reportdesc;

	}


	function getReportQuery()
	{
		return $this->sqlcode;
	}
 

	/** function to get query for the given reportid,filterlist,type    
	 *  @ param $reportid : Type integer
	 *  @ param $filterlist : Type Array
	 *  @ param $module : Type String 
	 *  this returns join query for the report 
	 */

	function getSQLforReport($reportid)
	{
		//get sqlcode form agc_reportsql and return
		return $reportquery;

	}

	/** function to get the report output in HTML,PDF,TOTAL,PRINT,PRINTTOTAL formats depends on the argument $outputformat    
	 *  @ param $outputformat : Type String (valid parameters HTML,PDF,TOTAL,PRINT,PRINT_TOTAL)
	 *  @ param $filterlist : Type Array
	 *  This returns HTML Report if $outputformat is HTML
         *  		Array for PDF if  $outputformat is PDF
	 *		HTML strings for TOTAL if $outputformat is TOTAL
	 *		Array for PRINT if $outputformat is PRINT
	 *		HTML strings for TOTAL fields  if $outputformat is PRINTTOTAL
	 *		HTML strings for 
	 */

	// Performance Optimization: Added parameter directOutput to avoid building big-string!
	function GenerateReport($outputformat,$filterlist, $directOutput=false)
	{

		global $adb,$current_user,$php_max_execution_time;
		global $modules,$app_strings;
		global $mod_strings,$current_language;

		if($outputformat == "HTML")
		{
			$sSQL = $this->sqlcode;
			$result = $adb->query($sSQL);
			$error_msg = $adb->database->ErrorMsg();
			if(!$result && $error_msg!=''){
				// Performance Optimization: If direct output is requried
				if($directOutput) {
					echo getTranslatedString('LBL_REPORT_GENERATION_FAILED', "SQLReports") . "<br>" . $error_msg;
					$error_msg = false; 
				}				
				// END
				return $error_msg;
			}
			
			// Performance Optimization: If direct output is required
			if($directOutput) {
				echo '<table cellpadding="5" cellspacing="0" align="center" class="rptTable"><tr>';
			}
			// END
			
			//if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1)
			//	$picklistarray = $this->getAccessPickListValues();
			if($result)
			{
				$y=$adb->num_fields($result);
				$arrayHeaders = Array();
				for ($x=0; $x<$y; $x++)
				{
					$fld = $adb->field_name($result, $x);
					if(in_array($this->getLstringforReportHeaders($fld->name), $arrayHeaders))
					{
						$headerLabel = str_replace("_"," ",$fld->name);
						$arrayHeaders[] = $headerLabel;
					}
					else
					{
						//$headerLabel = str_replace($modules," ",$this->getLstringforReportHeaders($fld->name));
						//$headerLabel = str_replace("_"," ",$this->getLstringforReportHeaders($fld->name));
						//$arrayHeaders[] = $headerLabel;
                                                $arrayHeaders[] = $fld->name;
                                                $headerLabel = $fld->name;
					}
					//STRING TRANSLATION starts
					$mod_name = split(' ',$headerLabel,2);
					$module ='';
					$module = getTranslatedString($mod_name[0],$mod_name[0]);

					
					/*
                                        if($module!=''){
                                                $headerLabel_tmp = getTranslatedString($mod_name[1],$mod_name[0]);
                                        } else {
                                                $headerLabel_tmp = getTranslatedString($mod_name[0]." ".$mod_name[1]);
                                        }

					if($headerLabel == $headerLabel_tmp) $headerLabel = getTranslatedString($headerLabel_tmp);
					else $headerLabel = $headerLabel_tmp;
                                         
                                         */
					//STRING TRANSLATION ends
					$header .= "<td class='rptCellLabel'>".$headerLabel."</td>";
					
					// Performance Optimization: If direct output is required
					if($directOutput) {
						echo $header;
						$header = '';
					}
					// END
				}
				
				// Performance Optimization: If direct output is required
				if($directOutput) {
					echo '</tr><tr>';
				}
				// END

				$noofrows = $adb->num_rows($result);
				$custom_field_values = $adb->fetch_array($result);
				//$groupslist = $this->getGroupingList($this->reportid);

				$column_definitions = $adb->getFieldsDefinition($result);
					
				do
				{
					$arraylists = Array();
					if(count($groupslist) == 1)
					{
						$newvalue = $custom_field_values[0];
					}elseif(count($groupslist) == 2)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					}elseif(count($groupslist) == 3)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}
					if($newvalue == "") $newvalue = "-";

					if($snewvalue == "") $snewvalue = "-";

					if($tnewvalue == "") $tnewvalue = "-";

					$valtemplate .= "<tr>";
					
					// Performance Optimization
					if($directOutput) {
						echo $valtemplate;
						$valtemplate = '';
					}
					// END

					for ($i=0; $i<$y; $i++)
					{
						$fld = $adb->field_name($result, $i);
						$fld_type = $column_definitions[$i]->type;
						if (in_array($fld->name, $this->convert_currency)) {
								if($custom_field_values[$i]!='')
									$fieldvalue = convertFromMasterCurrency($custom_field_values[$i],$current_user->conv_rate);
								else
									$fieldvalue = getTranslatedString($custom_field_values[$i]);
							} elseif(in_array($fld->name, $this->append_currency_symbol_to_value)) {
								$curid_value = explode("::", $custom_field_values[$i]);
								$currency_id = $curid_value[0];
								$currency_value = $curid_value[1];
								$cur_sym_rate = getCurrencySymbolandCRate($currency_id);
								if($custom_field_values[$i]!='')
									$fieldvalue = $cur_sym_rate['symbol']." ".$currency_value;
								else
									$fieldvalue = getTranslatedString($custom_field_values[$i]);
							}elseif ($fld->name == "PurchaseOrder_Currency" || $fld->name == "SalesOrder_Currency" 
										|| $fld->name == "Invoice_Currency" || $fld->name == "Quotes_Currency") {
								if($custom_field_values[$i]!='')
									$fieldvalue = getCurrencyName($custom_field_values[$i]);
								else
									$fieldvalue =getTranslatedString($custom_field_values[$i]);
							}elseif (in_array($fld->name,$this->ui10_fields) && !empty($custom_field_values[$i])) {
								$type = getSalesEntityType($custom_field_values[$i]);
								$tmp =getEntityName($type,$custom_field_values[$i]);
                                                                if($tmp == '')
                                                                {
                                                                    $fieldvalue = '';
                                                                }
                                                                else
                                                                {
                                                                    foreach($tmp as $key=>$val){
                                                                            $fieldvalue = $val;
                                                                            break;
                                                                    }
                                                                }
								
							}
							else {
								if($custom_field_values[$i]!='')
									$fieldvalue = getTranslatedString($custom_field_values[$i]);
								else
									$fieldvalue = getTranslatedString($custom_field_values[$i]);
							}
						$fieldvalue = str_replace("<", "&lt;", $fieldvalue);
						$fieldvalue = str_replace(">", "&gt;", $fieldvalue);

					//check for Roll based pick list
						$temp_val= $fld->name;
						if(is_array($picklistarray))
							if(array_key_exists($temp_val,$picklistarray))
							{
								if(!in_array($custom_field_values[$i],$picklistarray[$fld->name]) && $custom_field_values[$i] != '')
									$fieldvalue =$app_strings['LBL_NOT_ACCESSIBLE'];

							}
						if(is_array($picklistarray[1]))
							if(array_key_exists($temp_val,$picklistarray[1]))
							{
								$temp =explode(",",str_ireplace(' |##| ',',',$fieldvalue));
								$temp_val = Array();
								foreach($temp as $key =>$val)
								{
										if(!in_array(trim($val),$picklistarray[1][$fld->name]) && trim($val) != '')
										{
											$temp_val[]=$app_strings['LBL_NOT_ACCESSIBLE'];
										}
										else
											$temp_val[]=$val;
								}
								$fieldvalue =(is_array($temp_val))?implode(", ",$temp_val):'';
							}

						if($fieldvalue == "" )
						{
							$fieldvalue = "-";
						}
						else if(stristr($fieldvalue,"|##|"))
						{
							$fieldvalue = str_ireplace(' |##| ',', ',$fieldvalue);
						}
						else if($fld_type == "date" || $fld_type == "datetime") {
                                                        $mydatetime = new DateTimeField($fieldvalue);
							$fieldvalue = $mydatetime->getDisplayDate();
						}
																				
						if(($lastvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($this->reporttype == "summary")
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";									
							}else
							{
								$valtemplate .= "<td class='rptData'>".$fieldvalue."</td>";
							}
						}else if(($secondvalue === $fieldvalue) && $this->reporttype == "summary")
						{
							if($lastvalue === $newvalue)
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";	
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						else if(($thirdvalue === $fieldvalue) && $this->reporttype == "summary")
						{
							if($secondvalue === $snewvalue)
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						else
						{
							if($this->reporttype == "tabular")
							{
								$valtemplate .= "<td class='rptData'>".$fieldvalue."</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						
						// Performance Optimization: If direct output is required
						if($directOutput) {
							echo $valtemplate;
							$valtemplate = '';
						}
						// END
					}
					
					$valtemplate .= "</tr>";
					
					// Performance Optimization: If direct output is required
					if($directOutput) {
						echo $valtemplate;
						$valtemplate = '';
					}
					// END
					
					$lastvalue = $newvalue;
					$secondvalue = $snewvalue;
					$thirdvalue = $tnewvalue;
					$arr_val[] = $arraylists;
					set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));

				// Performance Optimization
				if($directOutput) {
					echo "</tr></table>";
					echo "<script type='text/javascript' id='__reportrun_directoutput_recordcount_script'>
						if(jQuery('_reportrun_total')) jQuery('_reportrun_total').innerHTML=$noofrows;</script>";
				} else {

					$sHTML ='<table cellpadding="5" cellspacing="0" align="center" class="rptTable">
					<tr>'. 
					$header
					.'<!-- BEGIN values -->
					<tr>'. 
					$valtemplate
					.'</tr>
					</table>';
				}
				//<<<<<<<<construct HTML>>>>>>>>>>>>
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				$return_data[] = $sSQL;
				return $return_data;
			}
                        else
                        {
                            return array();
                        }
		}
                elseif($outputformat == "PDF")
		{

			$sSQL = $this->sqlcode;
			$result = $adb->query($sSQL);

			if($result)
			{
				$y=$adb->num_fields($result);
				$noofrows = $adb->num_rows($result);
				$custom_field_values = $adb->fetch_array($result);
				$column_definitions = $adb->getFieldsDefinition($result);

				do
				{
					$arraylists = Array();
					for ($i=0; $i<$y; $i++)
					{
						$fld = $adb->field_name($result, $i);
						if (in_array($fld->name, $this->convert_currency)) {
							$fieldvalue = convertFromMasterCurrency($custom_field_values[$i],$current_user->conv_rate);
						} elseif(in_array($fld->name, $this->append_currency_symbol_to_value)) {
							$curid_value = explode("::", $custom_field_values[$i]);
							$currency_id = $curid_value[0];
							$currency_value = $curid_value[1];
							$cur_sym_rate = getCurrencySymbolandCRate($currency_id);
							$fieldvalue = $cur_sym_rate['symbol']." ".$currency_value;
						}elseif ($fld->name == "PurchaseOrder_Currency" || $fld->name == "SalesOrder_Currency" 
									|| $fld->name == "Invoice_Currency" || $fld->name == "Quotes_Currency") {
							$fieldvalue = getCurrencyName($custom_field_values[$i]);
						}elseif (in_array($fld->name,$this->ui10_fields) && !empty($custom_field_values[$i])) {
								$type = getSalesEntityType($custom_field_values[$i]);
								$tmp =getEntityName($type,$custom_field_values[$i]);
								foreach($tmp as $key=>$val){
									$fieldvalue = $val;
									break;
								}
						}else {
							$fieldvalue = getTranslatedString($custom_field_values[$i]);
						}
						//$append_cur = str_replace($fld->name,"",decode_html($this->getLstringforReportHeaders($fld->name)));
						$headerLabel = str_replace("_"," ",$fld->name);
						//STRING TRANSLATION starts
						$mod_name = split(' ',$headerLabel,2);
						$module ='';
                                                $module = getTranslatedString($mod_name[0],$mod_name[0]);
						
						/*
							if($module!=''){
								$headerLabel_tmp = getTranslatedString($mod_name[1],$mod_name[0]);
							} else {
								$headerLabel_tmp = getTranslatedString($mod_name[0]." ".$mod_name[1]);
                                                        }
						if($headerLabel == $headerLabel_tmp) $headerLabel = getTranslatedString($headerLabel_tmp);
						else $headerLabel = $headerLabel_tmp;
                                                 * 
                                                 */
						//STRING TRANSLATION starts 
						if(trim($append_cur)!="") $headerLabel .= $append_cur;
					
						$fieldvalue = str_replace("<", "&lt;", $fieldvalue);
						$fieldvalue = str_replace(">", "&gt;", $fieldvalue);

						// Check for role based pick list
						$temp_val= $fld->name;
						if(is_array($picklistarray))
							if(array_key_exists($temp_val,$picklistarray))
							{
								if(!in_array($custom_field_values[$i],$picklistarray[$fld->name]) && $custom_field_values[$i] != '')
								{
									$fieldvalue =$app_strings['LBL_NOT_ACCESSIBLE'];
								}
							}
						if(is_array($picklistarray[1]))
							if(array_key_exists($temp_val,$picklistarray[1]))
							{
								$temp =explode(",",str_ireplace(' |##| ',',',$fieldvalue));
								$temp_val = Array();
								foreach($temp as $key =>$val)
								{
										if(!in_array(trim($val),$picklistarray[1][$fld->name]) && trim($val) != '')
										{
											$temp_val[]=$app_strings['LBL_NOT_ACCESSIBLE'];
										}
										else
											$temp_val[]=$val;
								}
								$fieldvalue =(is_array($temp_val))?implode(", ",$temp_val):'';
							}

						if($fieldvalue == "" )
						{
							$fieldvalue = "-";
						}
						else if(stristr($fieldvalue,"|##|"))
						{
							$fieldvalue = str_ireplace(' |##| ',', ',$fieldvalue);
						}
						else if($fld_type == "date" || $fld_type == "datetime") {
							$mydatetime = new DateTimeField($fieldvalue);
							$fieldvalue = $mydatetime->getDisplayDate();
						}
						if(array_key_exists($this->getLstringforReportHeaders($fld->name), $arraylists))
							$arraylists[$headerLabel] = $fieldvalue;
						else	
							$arraylists[$headerLabel] = $fieldvalue;
					}
					$arr_val[] = $arraylists;
					set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));

				return $arr_val;
			}
		}
                elseif($outputformat == "TOTALHTML")
		{
			$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sqlcode;
			if(isset($this->totallist))
			{
				if($sSQL != "")
				{
					$result = $adb->query($sSQL);
					$y=$adb->num_fields($result);
					$custom_field_values = $adb->fetch_array($result);
					$coltotalhtml .= "<table align='center' width='60%' cellpadding='3' cellspacing='0' border='0' class='rptTable'><tr><td class='rptCellLabel'>".$mod_strings[Totals]."</td><td class='rptCellLabel'>".$mod_strings[SUM]."</td><td class='rptCellLabel'>".$mod_strings[AVG]."</td><td class='rptCellLabel'>".$mod_strings[MIN]."</td><td class='rptCellLabel'>".$mod_strings[MAX]."</td></tr>";
					
					// Performation Optimization: If Direct output is desired
					if($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}
					// END
					
					foreach($this->totallist as $key=>$value)
					{
						$fieldlist = explode(":",$key);
						$mod_query = $adb->pquery("SELECT distinct(tabid) as tabid, uitype as uitype from vtiger_field where tablename = ? and columnname=?",array($fieldlist[1],$fieldlist[2]));
						if($adb->num_rows($mod_query)>0){
							$module_name = getTabName($adb->query_result($mod_query,0,'tabid'));
							$fieldlabel = trim(str_replace($escapedchars," ",$fieldlist[3]));
							$fieldlabel = str_replace("_", " ", $fieldlabel);
							if($module_name){
								$field = getTranslatedString($module_name)." ".getTranslatedString($fieldlabel,$module_name);
							} else {
								$field = getTranslatedString($module_name)." ".getTranslatedString($fieldlabel);
							}							
						}
						$uitype_arr[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $adb->query_result($mod_query,0,"uitype");
						$totclmnflds[str_replace($escapedchars," ",$module_name."_".$fieldlist[3])] = $field;
					}
					for($i =0;$i<$y;$i++)
					{
						$fld = $adb->field_name($result, $i);
						$keyhdr[$fld->name] = $custom_field_values[$i];
					}

					foreach($totclmnflds as $key=>$value)
					{
						$coltotalhtml .= '<tr class="rptGrpHead" valign=top>'; 
						$col_header = trim(str_replace($modules," ",$value));
						$fld_name_1 = $this->primarymodule . "_" . trim($value);
						$fld_name_2 = $this->secondarymodule . "_" . trim($value);
						if($uitype_arr[$value]==71 || in_array($fld_name_1,$this->convert_currency) || in_array($fld_name_1,$this->append_currency_symbol_to_value)
								|| in_array($fld_name_2,$this->convert_currency) || in_array($fld_name_2,$this->append_currency_symbol_to_value)) {
							$col_header .= " (".$app_strings['LBL_IN']." ".$current_user->currency_symbol.")";
							$convert_price = true;
						} else{
							$convert_price = false;
						}
						$coltotalhtml .= '<td class="rptData">'. $col_header .'</td>';
						$value = trim($key);
						$arraykey = $value.'_SUM';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = $value.'_AVG';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = $value.'_MIN';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = $value.'_MAX';
						if(isset($keyhdr[$arraykey]))
						{							
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td class="rptTotal">'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$coltotalhtml .= '<tr>';
						
						// Performation Optimization: If Direct output is desired
						if($directOutput) {
							echo $coltotalhtml;
							$coltotalhtml = '';
						}
						// END
					}

					$coltotalhtml .= "</table>";
					
					// Performation Optimization: If Direct output is desired
					if($directOutput) {
						echo $coltotalhtml;
						$coltotalhtml = '';
					}
					// END
				}
			}			
			return $coltotalhtml;
		}elseif($outputformat == "PRINT")
		{
			$sSQL = $this->sqlcode;
			$result = $adb->query($sSQL);

			if($result)
			{
				$y=$adb->num_fields($result);
				$arrayHeaders = Array();
				for ($x=0; $x<$y; $x++)
				{
					$fld = $adb->field_name($result, $x);
                                        /*
					if(in_array($this->getLstringforReportHeaders($fld->name), $arrayHeaders))
					{
						$headerLabel = str_replace("_"," ",$fld->name);
						$arrayHeaders[] = $headerLabel;
					}
					else
					{
						$headerLabel = str_replace($modules," ",$this->getLstringforReportHeaders($fld->name));
						$arrayHeaders[] = $headerLabel;	
					}
                                         *
                                         */
                                        $headerLabel = $fld->name;
					$arrayHeaders[] = $headerLabel;
					//STRING TRANSLATION starts
					$mod_name = split(' ',$headerLabel,2);
					$module ='';
					$module = getTranslatedString($mod_name[0],$mod_name[0]);

                                        /*
					if(!empty($this->secondarymodule)){
						if($module!=''){
							$headerLabel_tmp = $module." ".getTranslatedString($mod_name[1],$mod_name[0]);
						} else {
							$headerLabel_tmp = getTranslatedString($mod_name[0]." ".$mod_name[1]);
						}
					} else {
						if($module!=''){
							$headerLabel_tmp = getTranslatedString($mod_name[1],$mod_name[0]);
						} else {
							$headerLabel_tmp = getTranslatedString($mod_name[0]." ".$mod_name[1]);
						}
					}
                                         * */
                                        /*
					if($headerLabel == $headerLabel_tmp) $headerLabel = getTranslatedString($headerLabel_tmp);
					else $headerLabel = $headerLabel_tmp;
                                         * 
                                         */
					//STRING TRANSLATION ends
					$header .= "<th>".$headerLabel."</th>";
				}
				$noofrows = $adb->num_rows($result);
				$custom_field_values = $adb->fetch_array($result);
				//$groupslist = $this->getGroupingList($this->reportid);

				$column_definitions = $adb->getFieldsDefinition($result);

				do
				{
					$arraylists = Array();
					if(count($groupslist) == 1)
					{
						$newvalue = $custom_field_values[0];
					}elseif(count($groupslist) == 2)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					}elseif(count($groupslist) == 3)
					{
						$newvalue = $custom_field_values[0];
                                                $snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}
					
					if($newvalue == "") $newvalue = "-";

					if($snewvalue == "") $snewvalue = "-";

					if($tnewvalue == "") $tnewvalue = "-";
 
					$valtemplate .= "<tr>";
					
					for ($i=0; $i<$y; $i++)
					{
						$fld = $adb->field_name($result, $i);
						if (in_array($fld->name, $this->convert_currency)) {
							$fieldvalue = convertFromMasterCurrency($custom_field_values[$i],$current_user->conv_rate);
						} elseif(in_array($fld->name, $this->append_currency_symbol_to_value)) {
							$curid_value = explode("::", $custom_field_values[$i]);
							$currency_id = $curid_value[0];
							$currency_value = $curid_value[1];
							$cur_sym_rate = getCurrencySymbolandCRate($currency_id);
							$fieldvalue = $cur_sym_rate['symbol']." ".$currency_value;
						}elseif ($fld->name == "PurchaseOrder_Currency" || $fld->name == "SalesOrder_Currency" 
									|| $fld->name == "Invoice_Currency" || $fld->name == "Quotes_Currency") {
							$fieldvalue = getCurrencyName($custom_field_values[$i]);
						}elseif (in_array($fld->name,$this->ui10_fields) && !empty($custom_field_values[$i])) {
								$type = getSalesEntityType($custom_field_values[$i]);
								$tmp =getEntityName($type,$custom_field_values[$i]);
								foreach($tmp as $key=>$val){
									$fieldvalue = $val;
									break;
								}
						}else {
							$fieldvalue = getTranslatedString($custom_field_values[$i]);
						}
					
						$fieldvalue = str_replace("<", "&lt;", $fieldvalue);
						$fieldvalue = str_replace(">", "&gt;", $fieldvalue);	

						//Check For Role based pick list 
						$temp_val= $fld->name;
						if(is_array($picklistarray))
							if(array_key_exists($temp_val,$picklistarray))
							{
								if(!in_array($custom_field_values[$i],$picklistarray[$fld->name]) && $custom_field_values[$i] != '')
								{
									$fieldvalue =$app_strings['LBL_NOT_ACCESSIBLE'];
								}
							}
						if(is_array($picklistarray[1]))
							if(array_key_exists($temp_val,$picklistarray[1]))
							{

								$temp =explode(",",str_ireplace(' |##| ',',',$fieldvalue));
								$temp_val = Array();
								foreach($temp as $key =>$val)
								{
										if(!in_array(trim($val),$picklistarray[1][$fld->name]) && trim($val) != '')
										{
											$temp_val[]=$app_strings['LBL_NOT_ACCESSIBLE'];
										}
										else
											$temp_val[]=$val;
								}
								$fieldvalue =(is_array($temp_val))?implode(", ",$temp_val):'';
							}


						if($fieldvalue == "" )
						{
							$fieldvalue = "-";
						}
						else if(stristr($fieldvalue,"|##|"))
						{
							$fieldvalue = str_ireplace(' |##| ',', ',$fieldvalue);
						}
						else if($fld_type == "date" || $fld_type == "datetime") {
							$mydatetime = new DateTimeField($fieldvalue);
							$fieldvalue = $mydatetime->getDisplayDate();
						}
						if(($lastvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($this->reporttype == "summary")
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";									
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}else if(($secondvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($lastvalue == $newvalue)
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";	
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
						else if(($thirdvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($secondvalue == $snewvalue)
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
						else
						{
							if($this->reporttype == "tabular")
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
					  }
					 $valtemplate .= "</tr>";
					 $lastvalue = $newvalue;
					 $secondvalue = $snewvalue;
					 $thirdvalue = $tnewvalue;
					 $arr_val[] = $arraylists;
					 set_time_limit($php_max_execution_time);
				}while($custom_field_values = $adb->fetch_array($result));
				
				$sHTML = '<tr>'.$header.'</tr>'.$valtemplate;	
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				return $return_data;
			}
		}elseif($outputformat == "PRINT_TOTAL")
		{
			$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sqlcode;
			if(isset($this->totallist))
			{
				if($sSQL != "")
				{
					$result = $adb->query($sSQL);
					$y=$adb->num_fields($result);
					$custom_field_values = $adb->fetch_array($result);

					$coltotalhtml .= '<table width="100%" border="0" cellpadding="5" cellspacing="0" align="center" class="printReport" ><tr><th>'.$mod_strings[Totals].'</th><th>'.$mod_strings[SUM].'</th><th>'.$mod_strings[AVG].'</th><th>'.$mod_strings[MIN].'</th><th>'.$mod_strings[MAX].'</th></tr>';

					foreach($this->totallist as $key=>$value)
					{
						$fieldlist = explode(":",$key);
						$totclmnflds[str_replace($escapedchars," ",$fieldlist[3])] = str_replace($escapedchars," ",$fieldlist[3]);
					}

					for($i =0;$i<$y;$i++)
					{
						$fld = $adb->field_name($result, $i);
						$keyhdr[$fld->name] = $custom_field_values[$i];

					}
					foreach($totclmnflds as $key=>$value)
					{
						$coltotalhtml .= '<tr valign=top>'; 
						$col_header = getTranslatedString(trim(str_replace($modules," ",$value)));
						$fld_name_1 = $this->primarymodule . "_" . trim($value);
						$fld_name_2 = $this->secondarymodule . "_" . trim($value);
						if(in_array($fld_name_1,$this->convert_currency) || in_array($fld_name_1,$this->append_currency_symbol_to_value)
								|| in_array($fld_name_2,$this->convert_currency) || in_array($fld_name_2,$this->append_currency_symbol_to_value)) {
							$col_header .= " (".$app_strings['LBL_IN']." ".$current_user->currency_symbol.")";
							$convert_price = true;
						} else {
							$convert_price = false;
						}
						$coltotalhtml .= '<td>'. $col_header .'</td>';
						
						$arraykey = trim($value).'_SUM';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td>'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$arraykey = trim($value).'_AVG';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td>'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$arraykey = trim($value).'_MIN';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td>'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$arraykey = trim($value).'_MAX';
						if(isset($keyhdr[$arraykey]))
						{
							if($convert_price)
								$conv_value = convertFromMasterCurrency($keyhdr[$arraykey],$current_user->conv_rate);
							else 
								$conv_value = $keyhdr[$arraykey];
							$coltotalhtml .= '<td>'.$conv_value.'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$coltotalhtml .= '<tr>';
					}

					$coltotalhtml .= "</table>";
				}
			}			
			return $coltotalhtml;
		}
	}


        	/** Function to convert the Report Header Names into i18n
	 *  @param $fldname: Type Varchar
	 *  Returns Language Converted Header Strings
	 **/
	function getLstringforReportHeaders($fldname)
	{
            global $modules,$current_language,$current_user,$app_strings;
            $rep_header = ltrim(str_replace($modules," ",$fldname));
            $rep_header_temp = ereg_replace(" ","_",$rep_header);
            $rep_module = ereg_replace('_'.$rep_header_temp,"",$fldname);
            $temp_mod_strings = return_module_language($current_language,$rep_module);
            // htmlentities should be decoded in field names (eg. &). Noticed for fields like 'Terms & Conditions', 'S&H Amount'
            $rep_header = decode_html($rep_header);
            $curr_symb = "";
            if(in_array($fldname, $this->convert_currency)) {
            $curr_symb = " (".$app_strings['LBL_IN']." ".$current_user->currency_symbol.")";
            }
            if($temp_mod_strings[$rep_header] != '')
            {
                $rep_header = $temp_mod_strings[$rep_header];
            }
            $rep_header .=$curr_symb;

            return $rep_header;
	}
}
?>