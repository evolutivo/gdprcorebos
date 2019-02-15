{*/*************************************************************************************************
* Copyright 2012-2013 OpenCubed  --  This file is a part of vtMktDashboard.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : MarketingDashboard
*  Version      : 1.9
*  Author       : OpenCubed
*************************************************************************************************/*}
<script type="text/javascript">
{literal}
$(function(){
  $('#tabs').tabs({
	  activate: function( event, ui ) {
		mktdb_selectedtab = ui.newPanel[0].id.substring(5);
	    if (mktdb_selectedtab==1) 
	    	dsMDCampaignResults.read();
	    else if (mktdb_selectedtab==2)
	    	dsMDContactResults.read();
        else
            dsMDAssignResults.read();
	   },
  });
  $(".searchbutton").button();
  $(".convertbutton").button();
  $("#relatedmodules").multiselect().multiselectfilter();
  $("#accordion,#accordion3").accordion({ clearStyle: true },{ active: false });
  $(".singlecombo").multiselect({
   multiple: false,
   header: false,
   noneSelectedText: "{/literal}{'Select an Option'|@getTranslatedString:'MarketingDashboard'}{literal}",
   selectedList: 1
});
	$(".kendomultiselect").kendoMultiSelect();
$(".datepicker").datepicker({ changeMonth: true ,changeYear: true,dateFormat:{/literal}"{$dateFormat}"{literal}});
var x = "{/literal}{'QContinue'|@getTranslatedString:'MarketingDashboard'}{literal}";
$('#convertmessage').click(function() {
    $('<div>' + x + '</div>').dialog({
        resizable: false,
        buttons: {
        	"{/literal}{'LBL_YES'|@getTranslatedString}{literal}": function() {
               if(message()){
              document.EditView.convertto.value='message';
               document.EditView.submit();
               }
                $(this).dialog("close");
            },
            "{/literal}{'LBL_NO'|@getTranslatedString}{literal}": function() {
                $(this).dialog("close");
            }
        }
    });
}); 
$('#sendsms').click(function() {
    $('<div>' + x + '</div>').dialog({
        resizable: false,
        buttons: {
                "{/literal}{'LBL_YES'|@getTranslatedString}{literal}": function() {
               if(message()){
               document.EditView.convertto.value='sendsms';
               document.EditView.submit();
               }
                $(this).dialog("close");
            },
            "{/literal}{'LBL_NO'|@getTranslatedString}{literal}": function() {
                $(this).dialog("close");
            }
        }
    });
 }); 
});

function showUrlInDialog(url){
  var tag = $("<div></div>");
  $.ajax({
    url: url,
    success: function(data) {
      tag.html(data).dialog({width:$(window).width()*0.8,height:$(window).height()*0.8,modal: true}).dialog('open');
    }
  });
}
$.extend({ alert: function (message, title) {
  $("<div></div>").dialog( {
    buttons: { "Ok": function () { $(this).dialog("close"); } },
    close: function (event, ui) { $(this).remove(); },
    resizable: false,
    title: "{/literal}{'Checking parameters'|@getTranslatedString:'MarketingDashboard'}{literal}",
    modal: true
  }).text(message);
}
});
{/literal}
</script>
{literal}
<!-- overriding the pre-defined #company to avoid clash with vtiger_field in the view -->
<style type='text/css'>
#company {
	height: auto;
	width: 90%;
}
</style>
{/literal}

{include file='Buttons_List1.tpl'}
<br><br>
<script type="text/javascript">
var mktdb_selectedtab = {$mytab|default:1};
{literal}
$(function(){
     $('#tabs') .tabs( "select" , mktdb_selectedtab-1)
});
{/literal}
</script>
<div id="tabs" style="padding:20px; width:95%">
	<ul>
        <li><a href="#tabs-1">{$MOD.convert_entities}</a></li>
	<li><a href="#tabs-2">{$MOD.create_contacts}</a></li>
        <li><a href="#tabs-3">{$MOD.manage_assign}</a></li>
        <!--<li><a href="#tabs-4">{*{$MOD.calculation}*}</a></li>-->
        <li><a href="index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=batch_status">{$MOD.CRONJOB}</a></li>
	</ul>
    <div id="tabs-1">
        {include file='modules/MarketingDashboard/campaign_management.tpl'}
    </div>
    <div id="tabs-2">
        {include file='modules/MarketingDashboard/create_contacts.tpl'}
    </div>
    <div id="tabs-3">
        {include file='modules/MarketingDashboard/massive_assign.tpl'}
    </div>
     <!--<div id="tabs-4">
        {*{include file='modules/MarketingDashboard/calculation.tpl'}*}
    </div>-->
    </div>
