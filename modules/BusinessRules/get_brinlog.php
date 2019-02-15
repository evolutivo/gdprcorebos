<?php

require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $log,$adb;
$sel=$_REQUEST['sel_values'];
$query=$_REQUEST['query'];
$ticked=false; 
$content=array();

if(!isset($sel))
{
$sql="Select businessrulesid,businessrules_name
        from vtiger_businessrules
        join vtiger_crmentity on crmid=businessrulesid
        where deleted=0  
        and  businessrules_name like '$query%'
        ";
        
    
    $result=$adb->pquery($sql,array());
    for($i=0;$i<$adb->num_rows($result);$i++)
    {
       $act_id=$adb->query_result($result,$i,'businessrulesid');
       $content[]=array('id'=>"$act_id",
           'name'=>$adb->query_result($result,$i,'businessrules_name'));
    }
    echo json_encode($content);
}
else{
 
$evo_actions=$sel;
if($evo_actions!='')
{ 
    $arr_evo_actions=explode(',',$evo_actions);
if($evo_actions!='undefined'){
    $sql="Select businessrulesid,businessrules_name
        from vtiger_businessrules
        join vtiger_crmentity on crmid=businessrulesid
        where deleted=0  and businessrulesid in (".  generateQuestionMarks($arr_evo_actions).")
            ORDER BY FIELD( businessrulesid, $evo_actions) 
        ";  

        $content=array();
        $result=$adb->pquery($sql,array($arr_evo_actions));
        for($i=0;$i<$adb->num_rows($result);$i++)
            {
               $act_id=$adb->query_result($result,$i,'businessrulesid');
                   if($evo_actions!=''){
                   $content[$i]['id']=$act_id;
                   $content[$i]['name']=$adb->query_result($result,$i,'businessrules_name');
                }
}}
}
echo json_encode($content);
}
