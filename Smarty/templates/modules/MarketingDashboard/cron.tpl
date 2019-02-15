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
<div class="k-content">
<div id="fileslist"></div>
{literal}
<script>
$(document).ready(function() {

$("#fileslist").kendoGrid({
    dataSource: {
     data:{/literal}{$allfiles}{literal},
    schema: {
            model: {
                fields:{/literal}{$fieldsfile}{literal}
            }
        },
    pageSize: {/literal}{$PAGESIZE}{literal},
    group: {/literal}{$groupfile}{literal},
    aggregate: {/literal}{$aggregatefile}{literal}
    },
    height: 300,
    sortable: {
    mode: "multiple",
    allowUnsort: true
    },
    groupable:false,
    scrollable: true,
    selectable: "multiple",
    pageable: true,
    filterable: true,
    sortable:false,
    columns:[
    {field:"filename",title:"{/literal}{'Filename'|@getTranslatedString:'MarketingDashboard'}{literal}"},
    { command: [{ text: "{/literal}{'Run'|@getTranslatedString:'MarketingDashboard'}{literal}", click: execscript,name:"run-script" },{ text: "{/literal}{'Delete'|@getTranslatedString:'MarketingDashboard'}{literal}", click: deletescript,name:"delete-script" }], title: "{/literal}{'Actions'|@getTranslatedString:'MarketingDashboard'}{literal}" , width: "170px"}]
    });
 function execscript(e) {
        kendo.ui.progress($("#fileslist"), true);
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
        scriptname=dataItem.filename;
        $.ajax({
        type: "POST",
        url:  "index.php",
        data: "module=MarketingDashboard&action=MarketingDashboardAjax&ajax=true&file=run&scriptname="+scriptname,
        success:
        function(result){
            kendo.ui.progress($("#fileslist"), hide);
            $("#tabs").tabs({select:3});
        }
        });
   }
       function deletescript(e) {
        kendo.ui.progress($("#fileslist"), true);
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
        scriptname=dataItem.filename;
        $.ajax({
        type: "POST",
        url:  "index.php",
        data: "module=MarketingDashboard&action=MarketingDashboardAjax&ajax=true&file=remove&scriptname="+scriptname,
        success:
        function(result){
        kendo.ui.progress($("#fileslist"), hide);
        $("#tabs").tabs({select:3});
        }
        });
   }
});
</script>
{/literal}
</div>
<br><br>
{'Last results'|@getTranslatedString:'MarketingDashboard'}:
<div class="k-content">
<div id="lastresults"></div>
{literal}
<script>
$(document).ready(function() {
$("#lastresults").kendoGrid({
    dataSource: {
     data:{/literal}{$lastinfo}{literal},
    schema: {
            model: {
                fields:{/literal}{$lastfields}{literal}
            }
        },
    pageSize: {/literal}{$PAGESIZE}{literal},
    group: {/literal}{$lastgroups}{literal},
    aggregate: {/literal}{$lastaggregates}{literal}
    },
    height: 300,
    sortable: {
    mode: "multiple",
    allowUnsort: true
    },
    groupable:false,
    scrollable: true,
    selectable: "multiple",
    pageable: true,
    filterable: true,
    sortable:false,
    columns:{/literal}{$lastcolumns}{literal} 
    });
});
</script>
{/literal}
</div>
