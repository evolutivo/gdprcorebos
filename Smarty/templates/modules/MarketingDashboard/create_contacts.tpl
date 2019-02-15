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
<form name="EditView2" method="POST" action="index.php">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="index">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="mytab" value="2">
{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                   <tr>
                    <td style="padding:10px 5px 0px 5px">
                    {assign var=accheadtext value=$MOD.accparams|replace:' ':''}
                    <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
                    <tr>{strip}
                         <td colspan=4 class="dvInnerHeader">
                            <div style="float:left;font-weight:bold;"><a href="javascript:showHideStatus('tbl{$accheadtext}','aid{$accheadtext}',1);">
                            {if $showdispacc eq 'block'}
                                 <span id="aid{$accheadtext}" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s" style="display: inline-block;"></span>
                                   {else}
                                  <span id="aid{$accheadtext}" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>
                                 {/if}<span style="float:right;color:#0073ea"> &nbsp; {$MOD.accparams}</span></a>
                            </div>
                         </td>{/strip}
                      </tr>
                   </table>
		<div style="width:auto;display:{$showdispacc};" id="tbl{$accheadtext}" >
                    <input type="hidden" id="showtbl{$accheadtext}" name="showacc" value="{$showdispacc}">
		  <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
                  <tr>
                    <td width="20%" class="dvtCellLabel" align=right>
                            {$MOD.filteracc}
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfilteracc1" class="singlecombo" tabindex="30">
                                 {foreach key=row item=accfilter from=$accountfilters}
                                    {if $row eq $indexaccfilter1}
                                    <option value="{$row}" selected>{$accfilter}</option>
                                    {else}
                                    <option value="{$row}">{$accfilter}</option>
                                    {/if}
                                  {/foreach}
                                   
                                </select>
                    </td>
                     <td width="20%" class="dvtCellLabel" align=right>
                            {$MOD.filtercont}
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfilteraccountcon" class="singlecombo" tabindex="30">
                                 {foreach key=row item=confilter from=$contactfilters}
                                    {if $row eq $indexaccountconfilter}
                                    <option value="{$row}" selected>{$confilter}</option>
                                    {else}
                                    <option value="{$row}">{$confilter}</option>
                                    {/if}
                                  {/foreach}
                                </select>
                    </td>
                    </tr>
                   <tr>
                        <td width="20%" class="dvtCellLabel" align=right>
				{$MOD.nocontacts}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			<input type=checkbox name="nocontacts" {$nocontacts}>
			</td>
                        <td width="20%" class="dvtCellLabel" align=right>
				{$MOD.withcontacts}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			<input type=checkbox name="withcontacts" {$withcontacts}>
			</td>
                    </tr>
	         </table>
		</div>
</td>
</tr>
                   <tr>
                    <td style="padding:10px 5px 0px 5px">
                    {assign var=con1headtext value=$MOD.conparams|replace:' ':''}
                    <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
                    <tr>{strip}
                         <td colspan=4 class="dvInnerHeader">
                            <div style="float:left;font-weight:bold;"><a href="javascript:showHideStatus('tbl1{$con1headtext}','aid1{$con1headtext}',1);">
                            {if $showdispcon1 eq 'block'}
                                 <span id="aid1{$con1headtext}" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s" style="display: inline-block;"></span>
                                   {else}
                                  <span id="aid1{$con1headtext}" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>
                                 {/if}<span style="float:right;color:#0073ea;"> &nbsp; {$MOD.conparams}</a></span>
                            </div>
                         </td>{/strip}
                      </tr>
                   </table>
		<div style="width:auto;display:{$showdispcon1};" id="tbl1{$con1headtext}" >
                    <input type="hidden" id="showtbl1{$con1headtext}" name="showcon1" value="{$showdispcon1}">
		  <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
                  <tr>
                    <td width="20%" class="dvtCellLabel" align=right>
                            {$MOD.filtercont}
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfiltercon1" class="singlecombo" tabindex="30">
                                 {foreach key=row item=confilter from=$contactfilters}
                                    {if $row eq $indexconfilter1}
                                    <option value="{$row}" selected>{$confilter}</option>
                                    {else}
                                    <option value="{$row}">{$confilter}</option>
                                    {/if}
                                  {/foreach}
                                </select>
                    </td>
                         <td width="20%" class="dvtCellLabel" align=right>
                            {$MOD.filteracc}
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfilteracccon1" class="singlecombo" tabindex="30">
                                 {foreach key=row item=accfilter from=$accountfilters}
                                    {if $row eq $indexfilterconacc1}
                                    <option value="{$row}" selected>{$accfilter}</option>
                                    {else}
                                    <option value="{$row}">{$accfilter}</option>
                                    {/if}
                                  {/foreach}
                                </select>
                    </td>
                    </tr>
	         </table>
		</div>
</td>
</tr>
</table>
<br>
<div align="center">
<input title="{$APP.LBL_SEARCH_BUTTON_TITLE}" accessKey="{$APP.LBL_SEARCH_BUTTON_KEY}" class="ui-button ui-widget ui-state-default ui-corner-all searchbutton" type="submit" name="button" value="  {$APP.LBL_SEARCH_BUTTON_LABEL}  " id="searchbutton1">
</div>
</form>
<form name="InvoiceLines2" method="POST" action="index.php">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="convert">
<input type="hidden" name="convertto" value="contacts">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="selectallrecords2" id="selectallrecords2" value="0">
<input type="hidden" name="mytab" value="2">
{assign var=iv1headtext value=$MOD.params|replace:' ':''}
<br>
<input class="ui-button ui-widget ui-state-default ui-corner-all ui-state-hover" type="button" id="chooseAllBtn2" value="{'Select all records'|@getTranslatedString:'MarketingDashboard'}" onclick="toggleSelectAllEntries_ListView('2');" {if $hideSelectAll1 eq 'true'} style="display:none"{/if}/>
<div class="k-content" style="clear:both;">
<div id="grid_contacts"></div>
<script>
{literal}
var dsMDContactResults = new kendo.data.DataSource({
    transport: {
            read:  {
                url: crudServiceBaseUrl + "&exec=List&mktdbtab=2",
                dataType: "json"
            },
            update: {
                url: crudServiceBaseUrl + "&exec=Update&mktdbtab=2",
                dataType: "json"
            },
            destroy: {
                url: crudServiceBaseUrl + "&exec=Destroy&mktdbtab=2",
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
            fields: {/literal}{$fields1}{literal}
        },
        data: "results",
        total: "total"
    },
    group: {/literal}{$groups1}{literal}
});
$(document).ready(function() {
  $("#grid_contacts").kendoGrid({
    dataSource: dsMDContactResults,
    height: 400,
    sortable: {
    mode: "multiple",
    allowUnsort: true
    },
    groupable:true,
    scrollable:true,
    selectable: "row",
    pageable: true,
    dataBound: updateGridSelectAllCheckbox,
    filterable: false,
    sortable: false,
    columns:{/literal}{$columns1}{literal} 
    });
});
{/literal}
</script>
</div>
<table border=0 cellspacing=0 cellpadding=0 width=100% class="small" style="padding:10px 5px 0px 5px">
<tr><td colspan=4>&nbsp;</td></tr>
<tr>{strip}
<td colspan=4 class="dvInnerHeader" >
<div style="float:left;font-weight:bold;"><div style="float:left;"><a href="javascript:showHideStatus('tbl1{$iv1headtext}','aid1{$iv1headtext}',0);">
<span id="aid1{$iv1headtext}" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>
<span style="float:right;color:#0073ea;"> &nbsp; {$MOD.params}</a></span>
</div>
</td>{/strip}
</tr>
</table>
<div style="width:auto;display:none;padding:0px 5px 0px 5px" id="tbl1{$iv1headtext}" >
  <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
     <tr>
     <td width="20%" class="dvtCellLabel" align=right>{$MOD.AssignInvoiceTo}</td>
    <td width="30%" align=left class="dvtCellInfo">
        <select name="assignto_contact" class="singlecombo" tabindex="10">
        {foreach key=key_one item=arr from=$ASSIGNEDTO_ARRAY1}
            {foreach key=sel_value item=value from=$arr}
                <option value="{$key_one}" {$value}>{$sel_value}</option>
            {/foreach}
        {/foreach}
        </select>
    </td>
    <td width="20%" class="dvtCellLabel" align=right>
    </td>
    <td width="30%" align=left class="dvtCellInfo"></td>
    </tr>
  </table>
</div>
<div align="center">
<br><br>
<input title="{$MOD.CREATE_CONTACTS}" accessKey="V" class="ui-button ui-widget ui-state-default ui-corner-all convertbutton" type="submit" name="button" id="createcontacts" value="  {$MOD.CREATE_CONTACTS}  " onclick="return oneSelected('InvoiceLines2');">
</div>
</form>
