 <?php
  global $adb,$log;  
    $kaction=$_REQUEST['kaction'];
    $module_val= $_REQUEST['mod_value'];

    if($kaction=='retrieve'){
        $q="
          SELECT DISTINCT blockid,blocklabel,vtiger_blocks.sequence as seq  ,tablabel ,vtiger_tab.tabid as t_id
          FROM vtiger_field
          join vtiger_blocks on vtiger_blocks.blockid = vtiger_field.block
          join vtiger_tab on vtiger_blocks.tabid = vtiger_tab.tabid
          ";
      if($module_val!='')
      $q.=" where vtiger_tab.tablabel= '$module_val'"; 
      $log->debug('testing2 '.$q);
      $query=$adb->query($q);
      $count=$adb->num_rows($query);
    
      for($i=0;$i<$count;$i++){
      $content[$i]['id']=$adb->query_result($query,$i,'blockid');
      $content[$i]['label2']=getTranslatedString($adb->query_result($query,$i,'blocklabel'),$adb->query_result($query,$i,'tablabel'));
      $content[$i]['label']=$adb->query_result($query,$i,'blocklabel');
      $content[$i]['label3']=getTranslatedString($adb->query_result($query,$i,'blocklabel'),$adb->query_result($query,$i,'tablabel'));
      $content[$i]['sequence']=$adb->query_result($query,$i,'seq');
      $content[$i]['tabid']=$adb->query_result($query,$i,'t_id');
      $content[$i]['tablabel']=$adb->query_result($query,$i,'tablabel');
      }
    echo json_encode($content);
     
    }
 ?>

