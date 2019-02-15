<?php
/*
$moduleTitle="TSolucio::GENDOC post Install operations";

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head><title>vtlib $moduleTitle</title>";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>';
echo '</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">';
echo '<table width=100% border=0><tr><td align=left>';
echo '<a href="index.php"><img src="themes/softed/images/vtiger-crm.gif" alt="vtiger CRM" title="vtiger CRM" border=0></a>';
echo '</td><td align=center style="background-image: url(\'vtlogowmg.png\'); background-repeat: no-repeat; background-position: center;">';
echo "<b><H1>$moduleTitle</H1></b>";
echo '</td><td align=right>';
echo '<a href="www.vtiger-spain.com"><img src="vtspain.gif" alt="vtiger-spain" title="vtiger-spain" border=0 height=100></a>';
echo '</td></tr></table>';
echo '<hr style="height: 1px">';

$Vtiger_Utils_Log=true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
*/
$module = Vtiger_Module::getInstance('evvtgendoc');
if ($module) {
	@mkdir('gendocoutput');
	include_once('modules/Accounts/Accounts.php');
	include_once('config.inc.php');
	include_once("include/language/$default_language.lang.php");

	$module->addLink('HEADERSCRIPT','evvtgendoc.js',"modules/evvtgendoc/evvtgendoc.js");

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
	
	$adb->query("ALTER table vtiger_organizationdetails ADD COLUMN account_id INT(19)");
	
	$sql="select * from vtiger_organizationdetails";
	$result = $adb->pquery($sql, array());
	
	$organization_name = $adb->query_result($result,0,'organizationname');
	$organization_address= $adb->query_result($result,0,'address');
	$organization_city = $adb->query_result($result,0,'city');
	$organization_state = $adb->query_result($result,0,'state');
	$organization_code = $adb->query_result($result,0,'code');
	$organization_country = $adb->query_result($result,0,'country');
	$organization_phone = $adb->query_result($result,0,'phone');
	$organization_fax = $adb->query_result($result,0,'fax');
	$organization_website = $adb->query_result($result,0,'website');
	
	putMsg("<br/>An Account with the data of this organization is being created...");
	$organization=new Accounts();
	$organization->column_fields['accountname']=$organization_name;
	$organization->column_fields['phone']=$organization_phone;
	$organization->column_fields['fax']=$organization_fax;
	$organization->column_fields['website']=$organization_website;
	$organization->column_fields['ship_street']=$organization_address;
	$organization->column_fields['ship_city']=$organization_city;
	$organization->column_fields['ship_code']=$organization_code;
	$organization->column_fields['ship_country']=$organization_country;
	$organization->column_fields['ship_state']=$organization_state;
	$organization->column_fields['assigned_user_id']=1;
	$organization->save("Accounts");
	putMsg("with ID: $organization->id ... DONE");
	putMsg("<br/>Organization is being related to this Account...");
	$adb->query("UPDATE vtiger_organizationdetails set account_id=$organization->id");
	putMsg("... DONE");

	putMsg("<br><b>Added Action to evvtgendoc module.</b>");
} else {
	putMsg("<b>Failed to find evvtgendoc module.</b><br>");
}

?>
