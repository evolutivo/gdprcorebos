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

<div id="example2" class="k-content" style="clear:both;">
 <div id="clientsDb2" >
        <table border=0 cellspacing=0 cellpadding=0 width=95% class="small" id="grid2" >
            <thead>
            <tr>
                <th class="detailedViewHeader" data-field="entity" ><b>{$MOD.Entity|strip:''}</b></th>
                <th class="detailedViewHeader" data-field="record" ><b>{$MOD.Title|strip:''}</b></th>
                <th class="detailedViewHeader" data-field="account"><b>{$APP.Account|strip:''}</b></th>
                <th class="detailedViewHeader" data-field="email"><b>{$APP.Email|strip:''}</b></th>
            </tr>
            </thead>
            <tbody>
            {foreach item=values from=$documents}
            <tr>
                <td class="dvtCellInfo">{$values.entity|@getTranslatedString:$values.entity}</a></td>
                <td class="dvtCellInfo"><a href="index.php?module={$values.entity}&action=DetailView&record={$values.recid}">{$values.name}</a></td>
                <td class="dvtCellInfo"><a href="index.php?module=Accounts&action=DetailView&record={$values.accountid}">{$values.account}</a></td>
                <td class="dvtCellInfo">{$values.email}</a></td>
            </tr>
            {/foreach}
            </tbody>
        </table>
</div>
{literal}
<style>
  #clientsDb2 {
    width:100%;
    height: 605px;
  }
</style>
<script>
    $(document).ready(function() { 
        $("#grid2").kendoGrid({
         dataSource: {
          schema: {
                    model: {
                        fields: {
                            entity: { type: "string" },
                            record: { type: "string" },
                            account:{type:"string"},
                            email:{type:"string"}
                        }
                    }
                },
         pageSize: {/literal}{$PAGESIZE}{literal}
         },
            height: 400,
            sortable: {
            mode: "multiple",
            allowUnsort: true
            },
            scrollable: true,
            pageable: true,
            columns:[
             {field:"entity",sortable:false,filterable:true},
             {field:"record",sortable:false,filterable:true},
             {field:"account",sortable:false,filterable:true},
             {field:"email",sortable:false,filterable:true}
            ]
         });
    });
</script>
{/literal}
</div>
