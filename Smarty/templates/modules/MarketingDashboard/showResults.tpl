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
{include file='Buttons_List1.tpl'}
<div id="grid_results"></div>
{literal}
<script>
$(document).ready(function() {
$("#grid_results").kendoGrid({                     
    dataSource: {
     data:{/literal}{$info}{literal},
    schema: {
            model: {
                fields:{/literal}{$fields}{literal}
            }
        },
    pageSize: 20,
    group: {/literal}{$groups}{literal}
    },
    height: 400,
    sortable: {
    mode: "multiple",
    allowUnsort: true
    },
    groupable:true,
    scrollable: true,
    selectable: "multiple",
    pageable: true,
    filterable: true,
    sortable:false,
    columns:{/literal}{$columns}{literal} 
    });
});
    
</script>
{/literal}
