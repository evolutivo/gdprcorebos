<?php  
                require_once('include/utils/utils.php');
                require_once('modules/Map/Map.php');
                require_once('modules/BusinessRules/BusinessRules.php');
                require_once('modules/NgBlock/NgBlock.php');

                global $adb,$db_use,$log; 
                $content=array();
                $kaction=$_REQUEST['kaction'];
                $id=$_REQUEST['id']; 
                $ng_block_id=$_REQUEST['ng_block_id']; 
                
                $a=$adb->pquery("SELECT *
                                  from vtiger_ng_block where 
                                  id =?",array($ng_block_id));
                $columns=$adb->query_result($a,0,'columns');
                //$columns=str_replace('smownerid', 'assigned_user_id', $columns);
                $cond=$adb->query_result($a,0,'br_id');
                $elastic_id=$adb->query_result($a,0,'elastic_id');
                $elastic_type=$adb->query_result($a,0,'elastic_type');
                $sort=$adb->query_result($a,0,'sort');
                $ng_module=$adb->query_result($a,0,'module_name');
                $pointing_module=$adb->query_result($a,0,'pointing_module_name');
                $tabid=  getTabid($pointing_module);
                $pointing_field_name=$adb->query_result($a,0,'pointing_field_name');
                                    
                $col= explode(",",$columns);
                $pointing_module_field=$pointing_field_name;
                require_once("modules/$ng_module/$ng_module.php");
                $ng=  CRMEntity::getInstance($ng_module);
                $ng_module_table=$ng->table_name;
                $ng_module_id=$ng->table_index;
                if($pointing_module!=''){
                    require_once("modules/$pointing_module/$pointing_module.php");
                    $pointing= CRMEntity::getInstance($pointing_module);
                    $pointing_module_table=$pointing->table_name;
                    $pointing_module_tablecf=$pointing->table_name."cf";
                    $pointing_module_id=$pointing->table_index;
                }

                $query_cond='';  
                if($cond!='')
                 {
                    $query_cond= " and  $cond ";
                 }
                if(!empty($sort) && $sort!= null && $sort!= ' '){
                    $so= explode(",",$sort);    
                    $sort_by=$so[0]; 
                    $order=$so[1];
                    $query_sort= " order by $sort_by  $order";
                } 
                $col2=implode(",",$col);
                 
                // retrieve record data     
                if($kaction=='retrieve'){
                     
                     $entity_field_arr=getEntityFieldNames($pointing_module);
                     $entity_field=$entity_field_arr["fieldname"];//var_dump($entity_field);
                     if (is_array($entity_field)) {
                       $entityname=implode(",$pointing_module_table.",$entity_field);
                     } 
                     else {$entityname=$entity_field;}
                     if($pointing_module=='Documents'){
                        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
                        $queryStr = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name," .
                                    "'Documents' ActivityType,vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,vtiger_crmentity.modifiedtime,
                                    vtiger_seattachmentsrel.attachmentsid attachmentsid, vtiger_crmentity.smownerid smownerid, vtiger_notes.notesid crmid,
                                    vtiger_notes.notecontent description,vtiger_notes.*
                                    from vtiger_notes
                                    inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
                                    left join vtiger_notescf ON vtiger_notescf.notesid= vtiger_notes.notesid
                                    inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
                                    inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_senotesrel.crmid
                                    LEFT JOIN vtiger_groups
                                    ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                                    left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid =vtiger_notes.notesid
                                    left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
                                    left join vtiger_users on vtiger_crmentity.smownerid= vtiger_users.id
                                    where crm2.crmid=" . $id;
                        $query=$adb->query($queryStr);
 
                      }else
                        if($pointing_module_table=='vtiger_adocmaster' && $ng_module_table=='vtiger_project'){
                            $query=$adb->pquery("SELECT vtiger_adocmaster.adocmasterid FROM vtiger_adocmaster 
                                LEFT JOIN vtiger_adocdetail ON vtiger_adocdetail.adoctomaster= vtiger_adocmaster.adocmasterid 
                                 JOIN vtiger_crmentity c1 ON c1.crmid = vtiger_adocmaster.adocmasterid
                                 left Join vtiger_crmentity c2 on c2.crmid=vtiger_adocdetail.adocdetailid
                                 WHERE c1.deleted =0 aND c2.deleted=0 and adocdetail_project=? and codcausale<>'PP' and nrtracking not like 'CAT%'  ",array($id));
                        }else
                    $query=$adb->pquery(" 
                          SELECT $pointing_module_table.$pointing_module_id
                          FROM $ng_module_table t1
                          join $pointing_module_table on t1.$ng_module_id = $pointing_module_table.$pointing_module_field
                          join vtiger_crmentity on crmid = $pointing_module_table.$pointing_module_id
                          where deleted = 0 and t1.$ng_module_id=? "
                          . $query_cond ."  $query_sort",array($id));
                      $count=$adb->num_rows($query);
                       // var_dump($col); 
                      

                      for($i=0;$i<$count;$i++){
                          $content[$i]['id']=$adb->query_result($query,$i,$pointing_module_id);
                          $content[$i]['href']='index.php?module='.$pointing_module.'&action=DetailView&record='.$content[$i]['id'];
                          $focus_pointing= CRMEntity::getInstance($pointing_module);
                          $focus_pointing->id=$adb->query_result($query,$i,$pointing_module_id);
                          $focus_pointing->mode = 'edit';
                          $focus_pointing->retrieve_entity_info($adb->query_result($query,$i,$pointing_module_id), $pointing_module);
                          $entityname_val='';
                          if (is_array($entity_field)) {
                              for($k=0;$k<sizeof($entity_field);$k++){
                                $entityname_val.=' '.$adb->query_result($query,$i,$entity_field[$k]);
                              }
                          } 
                          else{
                              $entityname_val=$adb->query_result($query,$i,$entityname);
                          }
                          $content[$i]['name']=$entityname_val;
                          for($j=0;$j<sizeof($col);$j++)
                          {
                              if($col[$j]=='') continue;
                              $a=$adb->query("SELECT *
                                      from vtiger_field
                                      WHERE ( columnname='$col[$j]' OR fieldname='$col[$j]' )"
                                      . " and tabid = '$tabid' ");
                                  $uitype=$adb->query_result($a,0,'uitype');
                                  $fieldname=$adb->query_result($a,0,'fieldname');
                                  $col_fields[$fieldname]=$focus_pointing->column_fields["$col[$j]"];
                                  $block_info=getDetailViewOutputHtml($uitype,$fieldname,'',$col_fields,'','',$pointing_module);
                                      $ret_val=$block_info[1];
                                      
                                  if(in_array($uitype,array(10,51,50,73,68,57,59,58,76,75,81,78,80)))
                                  {
                                      if(strpos($ret_val,'href')!==false) //if contains link remmove it 
                                      {
                                          $pos1=strpos($ret_val,'>');
                                          $first_sub=substr($ret_val,$pos1+1);
                                          $pos2=strpos($first_sub,'<');
                                          $log->debug('ret_val'.$first_sub.' '.$pos2);
                                          $sec_sub=substr($first_sub,0,$pos2);
                                          $ret_val=$sec_sub;
                                      }
                                      $content[$i][$col[$j]]=$col_fields[$fieldname]; 
                                      $content[$i][$col[$j].'_display']=$ret_val;
                                  }
                                  elseif(in_array($uitype,array(5,6,23)))
                                  {
                                      $content[$i][$col[$j]]=$col_fields[$fieldname]; 
                                      $content[$i][$col[$j].'_display']=$ret_val;
                                  }
                                  elseif(in_array($uitype,array(19,21,22,23)))
                                  {
                                      $content[$i][$col[$j]]=  str_replace('&nbsp;','',strip_tags($col_fields[$fieldname])); 
                                  }
                                  else
                                      $content[$i][$col[$j]]=$ret_val;
                          }
                      }
                        echo json_encode($content);

                }
                 // retrieve graph record data     
                elseif($kaction=='retrieve_graph'){
                     
                     if($cond!='')
                     {$query_cond= " and  $cond ";}
                     
                    $entity_field_arr=getEntityFieldNames($pointing_module);
                      $entity_field=$entity_field_arr["fieldname"];//var_dump();
                      if (is_array($entity_field)) {
                        $entityname=implode(",$pointing_module_table.",$entity_field);
                      } 
                     else {$entityname=$entity_field;}
                        
                    $query=$adb->pquery(" 
                          SELECT $pointing_module_table.$pointing_module_id,
                          $pointing_module_table.$col2,$pointing_module_table.$entityname
                          ,vtiger_crmentity.smownerid,vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime,vtiger_crmentity.description
                          FROM $ng_module_table
                          join $pointing_module_table on $ng_module_table.$ng_module_id = $pointing_module_table.$pointing_module_field
                          join vtiger_crmentity on crmid = $pointing_module_table.$pointing_module_id
                          where deleted = 0 and $ng_module_table.$ng_module_id=? "
                          . $query_cond ."  $query_sort",array($id));
                          
                      $count=$adb->num_rows($query);
                      
                      if(strpos($columns,'smownerid')!==false)
                       {   array_push($col,'smownerid');}
                      if(strpos($columns,'createdtime')!==false)
                       {   array_push($col,'createdtime');}
                       if(strpos($columns,'modifiedtime')!==false)
                       {   array_push($col,'modifiedtime');}
                       if(strpos($columns,'description')!==false)
                       {   array_push($col,'description');}

                       // var_dump($col); 

                    for($i=0;$i<$count;$i++){
                          $entityname_val='';
                          if (is_array($entity_field)) {
                              for($k=0;$k<sizeof($entity_field);$k++){
                                $entityname_val.=' '.$adb->query_result($query,$i,$entity_field[$k]);
                              }
                          } 
                          else{
                              $entityname_val=$adb->query_result($query,$i,$entityname);
                          }
                              
                           if(strpos($columns,'smownerid')!==false)
                           {   array_push($col,'smownerid');}
                           if(strpos($columns,'createdtime')!==false)
                           {   array_push($col,'createdtime');}
                           if(strpos($columns,'modifiedtime')!==false)
                           {   array_push($col,'modifiedtime');}
                           if(strpos($columns,'description')!==false)
                           {   array_push($col,'description');}
                               
                          for($j=0;$j<sizeof($col);$j++)
                          {
                          if($col[$j]=='')
                               continue;
                            
                          $a=$adb->query("SELECT *
                                  from vtiger_field
                                  WHERE columnname='$col[$j]'"
                                  . " and tabid = '$tabid' ");
                              $uitype=$adb->query_result($a,0,'uitype');
                              $fieldname=$adb->query_result($a,0,'fieldname');
                              $col_fields[$fieldname]=$adb->query_result($query,$i,$col[$j]);
                              
                              $block_info=getDetailViewOutputHtml($uitype,$fieldname,'',$col_fields,'','',$pointing_module);

                                  $ret_val=$block_info[1];
                                  if(strpos($ret_val,'href')!==false) //if contains link remmove it because ng can't interpret it
                              {
                                  $pos1=strpos($ret_val,'>');
                                  $first_sub=substr($ret_val,$pos1+1);
                                  $pos2=strpos($first_sub,'<');
                                  $log->debug('ret_val'.$first_sub.' '.$pos2);
                                  $sec_sub=substr($first_sub,0,$pos2);
                                  $ret_val=$sec_sub;
                              }
                              
                              if(in_array($uitype,array(10,51,50,73,68,57,59,58,76,75,81,78,80)))
                              {
                                  //$content[$i][$col[$j]]=$col_fields[$fieldname]; 
                                  $arr[$col[$j]][]=array("x"=>$entityname_val,"y"=>$ret_val);
                              }
                              else
                              $arr[$col[$j]][]=array("x"=>$entityname_val,"y"=>$ret_val);
                          }
                      } 
                      for($j=0;$j<sizeof($col);$j++)
                          {
                          if($col[$j]=='')
                               continue;
                          $content[$j]=array("key"=>$col[$j],"values"=>$arr[$col[$j]]);
                          }
                echo json_encode($content);
            }
                elseif($kaction=='retrieve_json'){

                    $ngBlockInst=new NgBlock();
                    $fromwhere=$_REQUEST['from'];
                    $size=$_REQUEST['size'];
                    $where=$_REQUEST['where'];
                    $recid=$_REQUEST['id'];
                    $models=$where;
                    $where=json_decode($models,true);$records=$ngBlockInst->createElastic($elastic_id,$elastic_type,$fromwhere,$size,$where,$pointing_field_name,$recid);
                    $trans=explode(',',$columns);
                    include_once("modules/$pointing_module/language/$current_language.lang.php");
                    global $mod_strings;
                    for ($i=0;$i<count($trans);$i++){
                    if($mod_strings["$trans[$i]"]!='')
                    $tr[]=$mod_strings["$trans[$i]"];
                    else $tr[]=$trans[$i];
                    }
                    echo json_encode(array('columns'=>explode(',',$columns),'translations'=>$tr,'lines'=>$records, 'uitypes' => $uitype_col));

            }
                elseif($kaction=='create'){
                    require_once('modules/'.$pointing_module.'/'.$pointing_module.'.php');
                    $models=$_REQUEST['models'];
                    $mv=json_decode($models);
                     
                    $focus = CRMEntity::getInstance("$pointing_module");
                    $focus->id='';
                    for($j=0;$j<sizeof($col);$j++)
                      {
                      if($col[$j]!='')
                         {
                          $a=$adb->query("SELECT fieldname
                              from vtiger_field
                              WHERE columnname='$col[$j]'
                              and tabid = '$tabid' ");
                          $fieldname=$adb->query_result($a,0,'fieldname');
                          $focus->column_fields[$fieldname]=$mv->$col[$j];  // all chosen columns
                         }
                      } 
                    $a=$adb->query("SELECT fieldname from vtiger_field
                             WHERE columnname='$pointing_field_name'");
                    $fieldname=$adb->query_result($a,0,"fieldname");
                    $focus->column_fields["$fieldname"]=$id;   //  the pointing field ui10
                    $log->debug('albana2 '.$entityname);
                    $entity_field_arr=getEntityFieldNames($pointing_module);
                    $entity_field=$entity_field_arr["fieldname"];
                    if (is_array($entity_field)) {
			$entityname=$entity_field[0];
		    }
                    else {$entityname=$entity_field;}
                    $focus->column_fields["$entityname"]=$mv->$col[0].' - '.$mv->$col[1];
                    if(empty($focus->column_fields['assigned_user_id']))
                    $focus->column_fields['assigned_user_id']=$current_user->id;
                    
                    $focus->save("$pointing_module"); 
                    if($pointing_module=='Messages' && $ng_module=='Project'){
                    $res_same_idorig_project=$adb->pquery("SELECT projectid 
                          from vtiger_project
                          where idorig=(Select idorig from vtiger_project where 
                                               projectid=?) and projectid <> ?",array($id,$id));
                     $count_p=$adb->num_rows($res_same_idorig_project);
                     if($count_p>0){
                      for($c_proj=0;$c_proj<$count_p;$c_proj++){
                             
                        $curr_p=$adb->query_result($res_same_idorig_project,$c_proj,'projectid');
                        $focus = CRMEntity::getInstance("$pointing_module");
                        $focus->id='';
                        for($j=0;$j<sizeof($col);$j++)
                          {echo $col[$j];
                          if($col[$j]!='' && $mv->$col[$j])
                          $focus->column_fields["$col[$j]"]=$mv->$col[$j];  // all chosen columns
                          } 
                          $a=$adb->query("SELECT fieldname from vtiger_field
                                 WHERE columnname='$pointing_field_name'");
                          $fieldname=$adb->query_result($a,0,"fieldname");
                          $focus->column_fields["$fieldname"]=$curr_p;   //  the pointing field ui10
                          $log->debug('albana2 '.$entityname);
                          $entity_field_arr=getEntityFieldNames($pointing_module);
                          $entity_field=$entity_field_arr["fieldname"];
                          if (is_array($entity_field)) {
                            $entityname=$entity_field[0];
                          }
                         else {$entityname=$entity_field;}
                         $log->debug('albana2 '.$entityname);
                          //$focus->column_fields["$entityname"]=$mv->$col[0];
                          //'Generated from '.getTranslatedString($ng_module,$ng_module);   //  the entityname field 
                          $log->debug('klm3 '.$pointing_field_name.' '.$id);
                          $focus->column_fields['assigned_user_id']=$current_user->id;
                          $focus->column_fields["name"]=$mv->subject;
                          $focus->column_fields["messagio"]=$mv->messagio;
                        $focus->column_fields["subject"]=$mv->subject;
                        $focus->save("$pointing_module"); 
                         }
                     }
                    }
    
                } 
                elseif($kaction=='edit'){
                    require_once('modules/'.$pointing_module.'/'.$pointing_module.'.php');
                    $models=$_REQUEST['models'];
                    $mv=json_decode($models);
                     
                    if(strpos($col,'description')!==false)
                    {   array_push($col,'description');} 
                    $focus = CRMEntity::getInstance("$pointing_module");
                    $focus->retrieve_entity_info($mv->id,$pointing_module);
                    $focus->id=$mv->id;
                    
                     $focus->mode='edit';
                     for($j=0;$j<sizeof($col);$j++)
                     {
                     if($col[$j]!='')
                         {
                             $a=$adb->query("SELECT fieldname
                                  from vtiger_field
                                  WHERE columnname='$col[$j]'
                                  and tabid = '$tabid' ");
                              $fieldname=$adb->query_result($a,0,'fieldname');
                             $focus->column_fields[$fieldname]=$mv->$col[$j];  // all chosen columns
                         }
                     } 
                     
                     $entity_field_arr=getEntityFieldNames($pointing_module);
                     $entity_field=$entity_field_arr["fieldname"];
                      if (is_array($entity_field)) {
			$entityname=$entity_field[0];
		      }
                      else {$entityname=$entity_field;}
                      $log->debug('albana2 '.$entityname);
                      $focus->column_fields["$entityname"]=$focus->column_fields["$entityname"];   //  the entityname field 
                      $log->debug('klm3 '.$pointing_field_name.' '.$id);
                     $focus->column_fields["assigned_user_id"]=$focus->column_fields["assigned_user_id"];
                     $focus->save("$pointing_module"); 
                }              
                elseif($kaction=='delete'){
                     require_once('modules/'.$pointing_module.'/'.$pointing_module.'.php');
                     $models=$_REQUEST['models'];
                     $mv=json_decode($models);
                     
                     $focus = CRMEntity::getInstance("$pointing_module");
                     $focus->retrieve_entity_info($mv->id,$pointing_module);
                     $focus->id=$mv->id;
                     $focus->mode='edit';echo $mv->id;
                     $log->debug('klm5 '.$focus->id);
                     $a=$adb->query("SELECT fieldname from vtiger_field
                            WHERE columnname='$pointing_field_name'");
                     $fieldname=$adb->query_result($a,0,"fieldname");
                     $focus->column_fields["$fieldname"]='';   //  the pointing field ui10
                     
                     echo $fieldname;
                     $log->debug('klm6 '.$fieldname);
                     $focus->save("$pointing_module"); 
    
                    }
                elseif($kaction=='select_entity')
                {
                    $entity_field_arr=getEntityFieldNames($pointing_module);
                    $entity_field=$entity_field_arr["fieldname"];//var_dump($entity_field);
                    if (is_array($entity_field)) {
                       $entityname=$entity_field[0];
                    } 
                    else {$entityname=$entity_field;}
                        
//                    $query=$adb->pquery(" 
//                          SELECT $entityname,$pointing_module_table.$pointing_module_id
//                          FROM $pointing_module_table join vtiger_crmentity "
//                            . " on crmid = $pointing_module_table.$pointing_module_id
//                          where deleted = 0 and $pointing_field_name<> $id "
//                            . " $query_cond",array());
                    $count=0;//$adb->num_rows($query);

                    for($i=0;$i<$count;$i++){
                          $content[$i]['id']=$adb->query_result($query,$i,$pointing_module_id);
                          $content[$i]['name']=$adb->query_result($query,$i,$entityname);
                          
                    }
                    echo json_encode($content);
                }
                elseif($kaction=='setRelation')
                {
                    $relid=$_REQUEST['relid'];
                    $arr_ids=explode(',',$relid);
                        
                    $query=$adb->pquery(" 
                          Update $pointing_module_table "
                            . " set $pointing_field_name=$id"
                            . " where $pointing_module_id in (".  generateQuestionMarks($arr_ids).")"
                            ,array($arr_ids));
                    
                }
?> 
