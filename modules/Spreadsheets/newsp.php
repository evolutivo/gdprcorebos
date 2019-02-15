<?php
chdir('../..');
include_once("include/database/PearDatabase.php");
include_once("include/utils/utils.php");
include_once("modules/GlobalVariable/GlobalVariable.php");
global $adb,$current_user;
$current_user->id=1;
$userName=GlobalVariable::getVariable('ecusername','');
$userAccessKey=GlobalVariable::getVariable('ecuseraccesskey', '');
$endpointUrl=GlobalVariable::getVariable('ecendpointurl', '');
$ethercalcendpoint=GlobalVariable::getVariable('ecendpoint', '');
$usethis=substr($ethercalcendpoint,0,-1);
$test1=$_REQUEST["id"];
$test1=$usethis.$test1;
//echo $test1;
$query1=$adb->query("select id from ethercalc where name='$test1'");
$norows=$adb->num_rows($query1);
$idis=$adb->query_result($query1,0,0);
?><!DOCTYPE html>
<html>
    <head>
        <title>EtherCalc</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="Spreadsheets.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     </head>
    <body>
    <button style="background-color: #008CBA;" onclick="updatedatanew('<?php echo $idis;?>');">Update Data</button>
<iframe onload="this.width=screen.width;this.height=screen.height;" src="<?php echo $test1;?>"></iframe>
</body>
</html>   