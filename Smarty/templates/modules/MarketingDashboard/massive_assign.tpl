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
<form name="EditView3" method="POST" action="index.php">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="index">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="mytab" value="3">
{* Contents *}
<table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                   <tr>
                    <td style="padding:10px 5px 0px 5px">
                    <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
                    <tr>{strip}
                         <td colspan=4 class="dvInnerHeader">
                            <div style="float:left;font-weight:bold;"><a href="javascript:showHideStatus('tbl31','aid31',1);">
                            {if $show31 eq 'block'}
                                 <span id="aid31" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s" style="display: inline-block;"></span>
                                   {else}
                                  <span id="aid31" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>
                                 {/if}<span style="float:right;color:#0073ea"> &nbsp; {$MOD.accparams}</span></a>
                            </div>
                         </td>{/strip}
                      </tr>
                   </table>
		<div style="width:auto;display:{$show31};" id="tbl31" >
          <input type="hidden" id="showtbl31" name="show31" value="{$show31}">
		  <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
                  <tr>
                    <td width="20%" class="dvtCellLabel" align=right>
                            {$MOD.filteracc}
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfilteracc2" class="singlecombo" tabindex="30">
                                 {foreach key=row item=accfilter from=$accountfilters}
                                    {if $row eq $indexaccfilter2}
                                    <option value="{$row}" selected>{$accfilter}</option>
                                    {else}
                                    <option value="{$row}">{$accfilter}</option>
                                    {/if}
                                  {/foreach}
                                </select>
                    </td>
                        <td width="20%" class="dvtCellLabel" align=right>
			</td>
			<td width="30%" class="dvtCellInfo" align=left>
                        </td>
                    </tr>
	         </table>
		</div>
</td>
</tr>
</table>
  <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                   <tr>
                    <td style="padding:10px 5px 0px 5px">
                    <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
                    <tr>{strip}
                         <td colspan=4 class="dvInnerHeader">
                            <div style="float:left;font-weight:bold;"><a href="javascript:showHideStatus('tbl32','aid32',1);">
                            {if $show32 eq 'block'}
                                 <span id="aid32" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s" style="display: inline-block;"></span>
                                   {else}
                                  <span id="aid32" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>
                                 {/if}<span style="float:right;color:#0073ea"> &nbsp; {$MOD.conparams}</span></a>
                            </div>
                         </td>{/strip}
                      </tr>
                   </table>
		<div style="width:auto;display:{$show32};" id="tbl32" >
          <input type="hidden" id="showtbl32" name="show32" value="{$show32}">
		  <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
                  <tr>
                    <td width="20%" class="dvtCellLabel" align=right>
                            {$MOD.filtercont}
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfiltercon2" class="singlecombo" tabindex="30">
                                 {foreach key=row item=confilter from=$contactfilters}
                                    {if $row eq $indexconfilter2}
                                    <option value="{$row}" selected>{$confilter}</option>
                                    {else}
                                    <option value="{$row}">{$confilter}</option>
                                    {/if}
                                  {/foreach}
                                </select>
                    </td>
                        <td width="20%" class="dvtCellLabel" align=right>
			</td>
			<td width="30%" class="dvtCellInfo" align=left>
                        </td>
                    </tr>
	         </table>
		</div>
</td>
</tr>
</table>
<br>
<div align="center">
<input title="{$APP.LBL_SEARCH_BUTTON_TITLE}" accessKey="{$APP.LBL_SEARCH_BUTTON_KEY}" class="ui-button ui-widget ui-state-default ui-corner-all searchbutton" type="submit" name="button" value="  {$APP.LBL_SEARCH_BUTTON_LABEL}  " id="searchbutton3">
</div>
</form>

<form name="InvoiceLines3" method="POST" action="index.php">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="convert">
<input type="hidden" name="convertto" value="contacts">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="selectallrecords3" id="selectallrecords3"  value="0">
<input type="hidden" name="mytab" value="3">
<br><br>
<input class="ui-button ui-widget ui-state-default ui-corner-all ui-state-hover" type="button" id="chooseAllBtn3" value="{'Select all records'|@getTranslatedString:'MarketingDashboard'}" onclick="toggleSelectAllEntries_ListView('3');" {if $hideSelectAll3 eq 'true'} style="display:none"{/if}/>
<div class="k-content" style="clear:both;">
<div id="massive_assign"></div>
{literal}
<script>
var dsMDAssignResults = new kendo.data.DataSource({
    transport: {
            read:  {
                url: crudServiceBaseUrl + "&exec=List&mktdbtab=3",
                dataType: "json"
            },
            update: {
                url: crudServiceBaseUrl + "&exec=Update&mktdbtab=3",
                dataType: "json"
            },
            destroy: {
                url: crudServiceBaseUrl + "&exec=Destroy&mktdbtab=3",
                dataType: "json"
            },
        },
    pageSize: {/literal}{$PAGESIZE}{literal},
    pageable: true,
    serverPaging: true,
    serverSorting: true,
    serverFiltering: true,
    schema: {
        model: {
            fields: {/literal}{$fields3}{literal}
        },
        data: "results",
        total: "total"
    },
    group: {/literal}{$groups3}{literal}
});

$(document).ready(function() {
  $("#massive_assign").kendoGrid({
    dataSource: dsMDAssignResults,
    height: 400,
    sortable: {
    mode: "multiple",
    allowUnsort: true
    },
    groupable:true,
    scrollable: true,
    selectable: "row",
    pageable: true,
    dataBound: updateGridSelectAllCheckbox,
    filterable: false,
    sortable: false,
    columns:{/literal}{$columns3}{literal} 
    });
});
</script>
{/literal}
</div>
<br><br>
<div id="accordion3">
 <h3><a href="#">{'REASSIGNPARAMS'|@getTranslatedString:'MarketingDashboard'}</a></h3>
 <div>
     <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
      <tr>
        <td width="20%" class="dvtCellLabel" align=right>
            {$MOD.AssignInvoiceTo}
	</td>
        <td width="30%" align=left class="dvtCellInfo">
                <select name="assignto_account" id="assignto_account" class="singlecombo" tabindex="10">
                {foreach key=key_one item=arr from=$ASSIGNEDTO_ACCOUNTS}
                        {foreach key=sel_value item=value from=$arr}
                                <option value="{$key_one}" {$value}>{$sel_value}</option>
                        {/foreach}
                {/foreach}
                </select>
        </td>
        <td width="20%" class="dvtCellLabel" align=right>
        {$MOD.relatedmodules}
        </td>
        <td width="30%" align=left class="dvtCellInfo">
                <select name="relatedmodules[]" id="relatedmodules" multiple>
                    {foreach key=key_one item=arr from=$RELATED_MODULES}
                        {foreach key=sel_value item=value from=$arr}
                                <option value="{$key_one}" {$value}>{$sel_value|@getTranslatedString:$sel_value}</option>
                        {/foreach}
                {/foreach}
                </select>
        </td>
   </tr>
 </table>
<div align="center">
<br>
<input title="{$MOD.REASSIGN}" accessKey="V" class="ui-button ui-widget ui-state-default ui-corner-all convertbutton" type="submit" name="button" id="reassign" value="  {$MOD.REASSIGN}  " onclick="this.form.convertto.value='reassign';return change_assign();">
</div>
</div>
 <h3><a href="#">{'MOVEONPARAM'|@getTranslatedString:'MarketingDashboard'}</a></h3>
<div>
    <center><div id="load"></div></center>
  <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
  <tr>
  <td width="20%" class="dvtCellLabel" align=right>{$MOD.module_from}</td>
    <td width="30%" align=left class="dvtCellInfo">
            <select name="modulefrom" id="modulefrom" class="singlecombo" tabindex="10" onchange="findfields(this.id,this.value,1);">
                {foreach key=modid item=modname from=$modulearray}
                <option value={$modid}>{$modname}</option>
                {/foreach}
            </select>
    </td>
    <td width="20%" class="dvtCellLabel" align=right>{$MOD.module_to}</td>
    <td width="30%" align=left class="dvtCellInfo">
            <select name="moduleto" id="moduleto" class="singlecombo" tabindex="10" onchange="findfields(this.id,this.value,0);">
                 {foreach key=key_one item=arr from=$RELATED_MODULES}
                        {foreach key=sel_value item=value from=$arr}
{if $sel_value!='ModComments'}
                           <option value="{$key_one}">{$sel_value|@getTranslatedString:$sel_value}</option>
{/if}
                        {/foreach}
                {/foreach}
            </select>
    </td>
    </tr>
    <tr>
    <td width="20%" class="dvtCellLabel" align=right>{$MOD.field_from}</td>
    <td width="30%" align=left class="dvtCellInfo">
        <select name="modulefromfields" id="modulefromfields" class="singlecombo" tabindex="10">
           {foreach key=fldname item=fldlabel from=$allfields}
             <option value={$fldname}>{$fldlabel}</option>
           {/foreach}  
        </select>
    </td>
    <td width="20%" class="dvtCellLabel" align=right>{$MOD.field_to}</td>
    <td width="30%" align=left class="dvtCellInfo">
        <select name="moduletofields" id="moduletofields" class="singlecombo" tabindex="10">
          {foreach key=fldname item=fldlabel from=$relatedfields}
             <option value={$fldname}>{$fldlabel}</option>
          {/foreach}
        </select>
    </td>
   </tr>
</tr>
</table>
<div align="center">
<br>
<input title="{$MOD.MOVEON}" accessKey="V" class="ui-button ui-widget ui-state-default ui-corner-all convertbutton" type="submit" name="button" id="moveon" value="  {$MOD.MOVEON}  " onclick="this.form.convertto.value='moveon'; return movefieldsbetweenentities();">
</div>
</div>
</div>
</form>
