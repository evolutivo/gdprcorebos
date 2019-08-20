<?php
global $adb;
$sql=$adb->query("SELECT tabid,tablabel FROM vtiger_tab where isentitytype=1");
                   $nr=$adb->num_rows($sql);
                   for($i=0 ;$i < $nr ;$i++)
                   {
                     $content[$i]['tabid']=$adb->query_result($sql,$i,'tabid');
                     $content[$i]['tablabel']=$adb->query_result($sql,$i,'tablabel');
                     $content[$i]['tabtrans']=  getTranslatedString($adb->query_result($sql,$i,'tablabel'),$adb->query_result($sql,$i,'module'));
                   }
                    $content[$i]['tabid']=0;
                    $content[$i]['tablabel']='Home';
                    $content[$i]['tabtrans']=  'Home';
       echo json_encode($content);
?>