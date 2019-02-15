<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source in cooperation with a-g-c (Andreas Goebel)
 * The Initial Developer of the Original Code is vtiger and a-g-c (Andreas Goebel)
 * Portions created by vtiger are Copyright (C) vtiger, portions created by a-g-c are Copyright (C) a-g-c.
 * All Rights Reserved.
 * www.a-g-c.de
 ************************************************************************************/

function agc_checkSQLCode($sqlCode)
{
    $forbiddenArray = array('insert into ','update ','delete from ','create database ','create table ','create index ','drop database ','drop table ','drop index ','alter table ');
    $sqlCode = strtolower($sqlCode);
    foreach($forbiddenArray as $token)
    {
       $token = strtolower($token);
       if(strstr($sqlCode,$token))
            return 0;
    }

    return 1;
}

?>
