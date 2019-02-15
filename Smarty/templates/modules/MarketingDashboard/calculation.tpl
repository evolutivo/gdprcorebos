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
<form name="SearchView4" method="POST" action="index.php">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="index">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="mytab" value="4">
{*<!-- Contents -->*}
 <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                   <tr>
                        <td id ="autocom"></td>
                   </tr>
                   <tr>
                    <td style="padding:10px 5px 0px 5px">
                <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
                  <tr><td colspan=4>&nbsp;</td></tr>
                  <tr>{strip}
                     <td colspan=4 class="dvInnerHeader">
                        <div style="float:left;font-weight:bold;"><a href="javascript:showHideStatus('tbl41','aid41',1);">
                                {if $showdispproduct eq 'block'}
                                    <span id="aid41" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s" style="display: inline-block;"></span>
                                {else}
                                    <span id="aid41" class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>
                                {/if}<span style="float:right;color:#0073ea"> &nbsp; {$MOD.prodparams}</span></a>
                        </div>
                     </td>{/strip}
                  </tr>
                 </table>
                <div style="width:auto;display:{$showdispproduct};" id="tbl41" >
                 <input type="hidden" id="showtbl41" name="showproduct" value="{$showdispproduct}">
                 <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
                  <tr>

                      <td width="20%" class="dvtCellLabel" align=right>
                       {$MOD.filterproduct}
                      </td>
                      <td width="30%" align=left class="dvtCellInfo">
                                <select name="selfilterproduct" class="singlecombo" tabindex="30">
                                 {foreach key=row item=productfilter from=$productfilters}
                                    {if $row eq $indexprodfilter}
                                    <option value="{$row}" selected>{$productfilter}</option>
                                    {else}
                                    <option value="{$row}">{$productfilter}</option>
                                    {/if}
                                  {/foreach}
                                   
                                </select>
                    </td>
                       <td width="20%" class="dvtCellLabel" align=right>
                            
                    </td>
                    <td width="30%" align=left class="dvtCellInfo">
                    </td>
                  </tr>
                 </table>
                 </div>

                <br>
<div align="center">
<input title="{$APP.LBL_SEARCH_BUTTON_TITLE}" accessKey="{$APP.LBL_SEARCH_BUTTON_KEY}" class="ui-button ui-widget ui-state-default ui-corner-all searchbutton" type="submit" name="button" value="  {$APP.LBL_SEARCH_BUTTON_LABEL}  " id="searchbutton">
</div>
</form>
</td>
</tr>
</table>
<form name="EditView4" method="POST" action="index.php">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action" value="calculate">
<input type="hidden" name="parenttab" value="{$CATEGORY}">
<input type="hidden" name="selectallrecords4" id="selectallrecords4" value="0">
<input type="hidden" name="mytab" value="4"> 
<br><br><br>
<input class="ui-button ui-widget ui-state-default ui-corner-all ui-state-hover" type="button" id="chooseAllBtn" value="{'Select all records'|@getTranslatedString:'MarketingDashboard'}" onclick="toggleSelectAllEntries_ListView('');" {if $hideSelectAll eq 'true'} style="display:none"{/if}/>
<div class="k-content" style="clear:both;">
<div id="grid_calculation"></div>
{literal}
<script>
var crudServiceBaseUrl = "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected";
var dsMDCalculationResults = new kendo.data.DataSource({
    transport: {
            read:  {
                url: crudServiceBaseUrl + "&exec=List&mktdbtab=4",
                dataType: "json"
            },
            update: {
                url: crudServiceBaseUrl + "&exec=Update&mktdbtab=4",
                dataType: "json"
            },
            destroy: {
                url: crudServiceBaseUrl + "&exec=Destroy&mktdbtab=4",
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
            fields: {/literal}{$fields4}{literal}
        },
        data: "results",
        total: "total"
    },
    group: {/literal}{$groups4}{literal}
});

$(document).ready(function() {
  $("#grid_calculation").kendoGrid({
    dataSource: dsMDCalculationResults,
    height: 400,
    sortable: {
	    mode: "multiple",
	    allowUnsort: true
    },
    groupable:true,
    scrollable: true,
    selectable: "row",
    change: function (e) {
    	  //var selectedCells = this.select();
    	  //selectedCells.find('input[type="checkbox"]').click();
    	},
    dataBound: updateGridSelectAllCheckbox,
    pageable: true,
    filterable: false,
    sortable:false,
    columns:{/literal}{$columns4}{literal} 
    });
});
</script>
{/literal}
</div>
<br><br>
<div id="accordion">
<h3><a href="#">&nbsp;{'generalparams'|@getTranslatedString:'MarketingDashboard'}</a></h3>
    <div>
         <table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
             <tr>
                 <td width="10%" class="dvInnerHeader" align=right>
                     {$MOD.machine_factor}
                 </td>
                 <td width="20%" align=left class="dvInnerHeader">
                     <input type="text" name="machine_factor" value="{$machine_factor}">
                 </td>
                 <td width="10%" class="dvInnerHeader" align=right>
                     {$MOD.hr_factor}
                 </td>
                 <td width="20%" align=left class="dvInnerHeader">
                     <input type="text" name="hr_factor" value="{$hr_factor}">
                 </td>
                 <td width="10%" class="dvInnerHeader" align=right>
                     {$MOD.expired_factor}
                 </td>
                 <td width="20%" align=left class="dvInnerHeader">
                     <input type="text" name="expired_factor" value="{$expired_factor}">
                 </td>
             </tr>
             <tr>
                 <td width="10%" class="dvInnerHeader" align=right>
                     {$MOD.warehouse_factor}
                 </td>
                 <td width="20%" align=left class="dvInnerHeader">
                     <input type="text" name="warehouse_factor" value="{$warehouse_factor}">
                 </td>
                 <td width="10%" class="dvInnerHeader" align=right>
                     {$MOD.transport_factor}
                 </td>
                 <td width="20%" align=left class="dvInnerHeader">
                     <input type="text" name="transport_factor" value="{$transport_factor}">
                 </td>
                 <td width="10%" class="dvInnerHeader" align=right>
                     {$MOD.marketing_factor}
                 </td>
                 <td width="20%" align=left class="dvInnerHeader">
                     <input type="text" name="marketing_factor" value="{$marketing_factor}">
                 </td>
             </tr>
                        </table>
    </div>
                        </div>

 <div align="center">
<br>

<input title="{$MOD.CALCULATE}" accessKey="V" class="ui-button ui-widget ui-state-default ui-corner-all convertbutton" type="submit" name="button" id="calculate" value="  {$MOD.CALCULATE}  " onclick="return oneSelectedMandatory('EditView4');">
</div>
</form>
