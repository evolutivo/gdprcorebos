{*<!--
 *************************************************************************************************
 * Copyright 2015 OpenCubed -- This file is a part of OpenCubed coreBOS customizations.
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
 *  Module       : NgBlock
 *  Version      : 5.5.0
 *  Author       : OpenCubed.
 *************************************************************************************************/
-->*}
<link rel="stylesheet" type="text/css" href="Smarty/angular/bootstrap.min.css"/>
<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
<tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align=right>
                {if $NG_BLOCK_NAME eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts') }
                        {if $MODULE eq 'Leads'}
                                <input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="crmbutton small create" type="button" onClick="searchMapLocation( 'Main' )" title="{$APP.LBL_LOCATE_MAP}">
                        {/if}
                {/if}
        </td>
</tr>
<tr>{strip}
    <td colspan=4 class="dvInnerHeader">

            <div style="float:left;font-weight:bold;"><div style="float:left;"><a href="javascript:showHideStatus('tbl{$NG_BLOCK_NAME|replace:' ':''}','aid{$NG_BLOCK_NAME|replace:' ':''}','{$IMAGE_PATH}');">
                                    <img id="aid{$NG_BLOCK_NAME|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Display" title="Display"/>
                            </a></div><b>&nbsp;
                            {$NG_BLOCK_NAME}
                    </b></div>
    </td>{/strip}
</tr>
</table>

<div style="width:auto;display:none;" id="tbl{$NG_BLOCK_NAME|replace:' ':''}" >
     <table ng-controller="block_{$NG_BLOCK_ID}" ng-table-dynamic="tableParams with cols" show-filter="true" class="table table-condensed table-bordered table-striped">
            <tr ng-repeat="row in $data">
              <td ng-repeat="col in $columns "><div ng-if="(uitypes[col.field] =='19' || uitypes[col.field] =='21') && !isExpanded[col.field]"> {literal}{{ row[col.field] | limitTo: 160 }}{{row[col.field].length > 160 ? '...' : ''}}{/literal}</div>
                <div ng-if="(uitypes[col.field]!='19' && uitypes[col.field]!='21') || isExpanded[col.field]">{literal}{{row[col.field]}}{/literal}</div>
                <input ng-show="row[col.field].length > 160 && (uitypes[col.field]=='19' || uitypes[col.field]=='21') && !isExpanded[col.field]" type="button" ng-click="expandField(col.field)" value="Leggi tutto" />
                <input ng-show="row[col.field].length > 160 && (uitypes[col.field]=='19' || uitypes[col.field]=='21') && isExpanded[col.field]" type="button" ng-click="shrinkField(col.field)" value="Chiudi" />
               </td>
            </tr>
      </table>
</div>

<style>
{literal}
.app-modal-window .modal-dialog {
    width: 500px;
    margin-left:-190px;
    }
{/literal}
</style>

<script>
{literal}
angular.module('demoApp')
.controller('block_{/literal}{$NG_BLOCK_ID}{literal}',function($scope, $http, $modal,ngTableParams) {

    $scope.hasReadMore = {};
    $scope.isExpanded = {};
    $scope.generateColumns = function(sampleData,sampleTranslations) {
        var colNames =sampleData;// Object.getOwnPropertyNames(sampleData);
        var fields=[];
        var i=0;
        angular.forEach(colNames, function(value, key) {
            var filter = {};
            filter[value] = 'text';
            var fld= {
                title: sampleTranslations[i],
                //sortable: value,
                filter: filter,
                show: true,
                field: value
              };
            i=i+1;
            fields.push(fld); 
        });
        return fields;
    };
    
    $scope.expandField = function (fld) {
       $scope.isExpanded[fld] = true;
    };
    
    $scope.shrinkField = function (fld) {
       $scope.isExpanded[fld] = false;
    };
    
    var pageItems=5;
    $scope.tableParams = new ngTableParams({
        page: 1,            // show first page
        count: 5  // count per page
    }, {
       filterDelay: 0,counts: [], 
        getData: function($defer, params) {
            var jsonFilter={};
            //jsonFilter['contactid']=$scope.pickpay.split("x")[1];
            //if($scope.type_draft!=='')
            //    jsonFilter['draft']=$scope.type_draft;
            var filterByFields=jsonFilter;
            var currentPage=params.page()-1;
            var where = JSON.stringify(params.filter());//JSON.stringify(filterByFields);
            var fromwhere = currentPage*pageItems;
            $http.get('index.php?{/literal}{$blockURL}{literal}&kaction=retrieve_json&from='+fromwhere+'&size='+pageItems+'&where='+where).
                success(function(data, status) {
                    $scope.cols = $scope.generateColumns(data.columns,data.translations);
                    $scope.pageRec      = data.lines.records;
                    $scope.uitypes = data.uitypes;
                    var orderedData = $scope.pageRec;
                    params.total(data.lines.total);
                    if (currentPage == 0) {
                        params.settings().startItemNumber = 1;
                    }
                    else {
                        params.settings().startItemNumber = currentPage * params.settings().countSelected + 1;
                    }
                    params.settings().endItemNumber = params.settings().startItemNumber +$scope.myItemsTotalCount - 1;
                    $defer.resolve(orderedData);
                });
        }
    });
        
});
{/literal}
</script>
