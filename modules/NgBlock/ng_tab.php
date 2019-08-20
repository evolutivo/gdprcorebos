<?php
global $adb,$log; 
$content=array();
$module=$this->get_template_vars('MODULE');
if(vtlib_isModuleActive('NgBlock')){
$q="Select * "
        . " from vtiger_ng_block_tab_rl"
        . " join vtiger_ng_block on tab_rl_id=related_tab"
        . " where moduleid=?"
        . " and destination='RELATEDVIEWWIDGET'"
        . " order by sequence";
$query=$adb->pquery($q,array($module));
$count=$adb->num_rows($query);
for($i=0;$i<$count;$i++){
    $tab_id=$adb->query_result($query,$i,'tab_rl_id');
    $tab_name=$adb->query_result($query,$i,'tab_name');
    $ng_block_name=$adb->query_result($query,$i,'name');
    $ng_block_id=$adb->query_result($query,$i,'id');
    $relatedmodule=$adb->query_result($query,$i,'pointing_module_name');
    $content[$tab_id]['tab_name']=$tab_name;
    $content[$tab_id]['items'][]=array('name'=>$ng_block_name,'id'=>$ng_block_id,
        'relatedmodule'=>$relatedmodule);
}
}
$this->assign('ng_tabs',$content);