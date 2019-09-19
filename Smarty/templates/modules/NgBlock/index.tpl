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
<link rel="stylesheet" href="Smarty/angular/material/angular-material.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,700,400italic">
<script src="Smarty/angular/material/angular.min.js"></script>
<script src="Smarty/angular/material/angular-animate.min.js"></script>
<script src="Smarty/angular/material/angular-aria.min.js"></script>
<script src="Smarty/angular/material/angular-material.min.js"></script>

<script src="Smarty/angular/angular.min.js"></script>
<script  src="Smarty/angular/ng-table.js"></script>
<link rel="stylesheet" href="Smarty/angular/ng-table.css" />
<script src="Smarty/angular/ui-bootstrap-tpls-0.6.0.js"></script>
<link rel="stylesheet" type="text/css" href="Smarty/angular/bootstrap.min.css"/>
<script src="Smarty/angular/angular-multi-select.js"></script>  
<link rel="stylesheet" href="Smarty/angular/angular-multi-select.css">

<table width=96% align=center border="0" ng-app="demoApp" style="padding:10px;">
    <tr><td style="height:2px"><br/><br/></td></tr>
    <tr>
        <td style="padding-left:20px;padding-right:50px" class="moduleName" nowrap colspan="2">
            Generic AngularJS component providing widgets functionality</td>
     </tr>
     <tr>  
	<td class="showPanelBg" valign="top" style="padding:10px;" width=96%>
              <div  layout="column" class="demo" >
                <md-content class="md-padding">
                    <md-tabs md-dynamic-height md-border-bottom>
                      <md-tab label="{'NgBlocks'|@getTranslatedString:'NgBlocks'}">
                        <md-content ng-controller="ng_Block" class="md-padding">
                          <h5 >{'Manage NgBlocks'|@getTranslatedString:'Manage NgBlocks'}</h5><br/>
                          <button class="btn btn-primary" ng-click="open(new_user,'add')">{'Add new NgBlock'|@getTranslatedString:'Add new NgBlock'}</button>
                          <table ng-table="tableParams" show-filter="true" class="table  table-bordered table-responsive" width=90% >
                            <tr ng-repeat="user in $data" >
                              <td>                
                                  <table>
                                      <tr>
                                          <th>
                                              <img alt="Edit" title="Edit" ng-if="!user.$edit" width="20" height="20" ng-click="open(user,'edit')" src="themes/images/editfield.gif" /> 
                                          </th>
                                          <th>
                                              <img alt="Delete" title="Delete" ng-if="!user.$edit" width="20" height="20" ng-click="delete_record(user)" src="themes/images/delete.gif" />
                                          </th> 
                                          <th>
                                              <img alt="Duplicate" title="Duplicate" ng-if="!user.$edit" width="20" height="20" ng-click="open(user,'duplicate')" src="themes/images/settingsActBtnDuplicate.gif" />
                                          </th> 
                                      </tr>             
                                  </table>
                              </td>
                              <td title="'{'Name'|@getTranslatedString:'Name'}'" filter="{literal}{ name: 'text'}{/literal}"> 
                                  {literal}  {{user.name}}{/literal}                                                                   
                              </td> 
                              <td title="'{'Module Name'|@getTranslatedString:'Module Name'}'" filter="{literal}{ module_name_trans: 'text'}{/literal}"> 
                                  {literal}  {{user.module_name_trans}}{/literal}
                              </td> 
                              <td title="'{'Pointing Block'|@getTranslatedString:'Pointing Block'}'" > 
                                  {literal}  {{user.pointing_block_name_trans}}{/literal}
                              </td> 
                              <td title="'{'Pointing Module'|@getTranslatedString:'Pointing Module'}'" filter="{literal}{ pointing_module_name_trans: 'text'}{/literal}"> 
                                  {literal}  {{user.pointing_module_name_trans}}{/literal}
                              </td> 
                              <td title="'{'Columns'|@getTranslatedString:'Columns'}'" > 
                                  {literal}  {{user.columns_disp}}{/literal}
                              </td> 
                              <td  data-title="'{'BR'|@getTranslatedString:'BR'}'" > 
                                  <a ng-if="user.br_id!=''&& user.br_id!=null && user.br_id!=undefined" href="index.php?module=BusinessRules&action=DetailView&record={literal}{{user.br_id}}{/literal}">BR</a>
                              </td>
                              <td title="'{'Add Record'|@getTranslatedString:'Add Record'}'" > 
                                  <img ng-if="user.add_record==1" width="20" height="20" src="themes/images/yes.gif" />
                                  <img ng-if="user.add_record!=1" width="20" height="20" src="themes/images/no.gif" />  
                              </td> 
                              <td title="'{'Order'|@getTranslatedString:'Order'}'"> 
                                  {literal}  {{user.sort}}{/literal}
                              </td> 
                              <td  title="'{'Edit'|@getTranslatedString:'Edit'}'"> 
                                  <img ng-if="user.edit_record==1" width="20" height="20" src="themes/images/yes.gif" />
                                  <img ng-if="user.edit_record!=1" width="20" height="20" src="themes/images/no.gif" />
                              </td> 
                              <td  title="'{'Delete'|@getTranslatedString:'Delete'}'"> 
                                  <img ng-if="user.delete_record==1" width="20" height="20" src="themes/images/yes.gif" />
                                  <img ng-if="user.delete_record!=1" width="20" height="20" src="themes/images/no.gif" />
                              </td> 
                              <td  data-title="'{'Sequence'|@getTranslatedString:'Sequence'}'"> 
                                  {literal}  {{user.sequence_ngblock}}{/literal}
                              </td> 
                              <td  data-title="'{'Destination'|@getTranslatedString:'Destination'}'"> 
                                  {literal}  {{user.destination}}{/literal}
                              </td> 
                              <td  data-title="'{'Type'|@getTranslatedString:'Type'}'"> 
                                  {literal}  {{user.type}}{/literal}
                              </td> 
                              <td  data-title="'{'Action'|@getTranslatedString:'Action'}'" > 
                                  <a href="index.php?module=BusinessActions&action=DetailView&record={literal}{{user.respective_act}}{/literal}">{'Action'|@getTranslatedString:'Action'}</a>
                              </td> 
                              
                            </tr>
                        </table>
                        </md-content>
                      </md-tab>
                      <md-tab label="{'RL Tabs'|@getTranslatedString:'RL Tabs'}">
                        <md-content class="md-padding">
                          <h5 class="md-display-5">{'Manage Tabs of Related Lists'|@getTranslatedString:'Manage Tabs of Related Lists'}</h5>
                            <table ng-controller="ng_Block" ng-table="tableParamsTabs" class="table  table-bordered table-responsive" width=100% >
                                <tr >
                                    <td colspan="4">
                                         <button class="btn btn-primary" ng-click="open2(new_user,'add')">{'Add New Tab RL'|@getTranslatedString:'Add New Tab RL'}</button>
                                    </td>
                            </tr>
                            <tr style="background-color:#c9dff0">
                                <th style="align:center">
                                </th> <th style="text-align: center">
                                    {'Name'|@getTranslatedString:'Name'}
                                </th>
                                <th style="text-align: center">
                                    {'Module'|@getTranslatedString:'Module'}
                                </th>
                                <th style="text-align: center">
                                    {'Sequence'|@getTranslatedString:'Sequence'}
                                </th>
                            </tr> 
                            <tr ng-repeat="user in $data" >
                                  <td>                
                                      <table>
                                      <tr>
                                          <th>
                                              <img ng-if="!user.$edit" width="20" height="20" ng-click="open2(user,'edit')" src="themes/images/editfield.gif" /> 
                                              <a ng-click="open2(user,'edit')">{'Edit'|@getTranslatedString:'Edit'}</a> 
                                          </th>
                                          <th>
                                              <img ng-if="!user.$edit" width="20" height="20" ng-click="delete_record2(user)" src="themes/images/delete.gif" />
                                              <a ng-click="delete_record2(user)">{'Delete'|@getTranslatedString:'Delete'}</a>
                                          </th>   
                                      </tr>             
                                      </table>
                                  </td>
                                  <td > 
                                      {literal}  {{user.name}}{/literal}                                                                   
                                  </td> 
                                  <td  > 
                                      {literal}  {{user.modulename}}{/literal}
                                  </td>
                                  <td  > 
                                      {literal}  {{user.sequence}}{/literal}
                                  </td>
                            </tr>
                            </table>
                        </md-content>
                      </md-tab>
                      <md-tab label="{'Manage UiType Evo'|@getTranslatedString:'Manage UiType Evo'}">
                        <md-content class="md-padding">
                          <h5 class="md-display-5">{'Manage UiType Evo Fields'|@getTranslatedString:'Manage UiType Evo Fields'}</h5>
                          {include file="modules/NgBlock/uiEvo.tpl"}
                        </md-content>
                      </md-tab>
                                
                    </md-tabs>
                  </md-content>
                </div>
             </td>
            </tr>
</table>
<script>
{literal}
angular.module('demoApp',['ngTable','ui.bootstrap','multi-select','ngMaterial']) 
.controller('ng_Block', function($scope, $http,$filter, $modal, ngTableParams) {

    $scope.new_user={"id":"","id_hidden":"","name":"","module_name":"",
        "module_name_trans":"","pointing_block_name":"",
        "pointing_block_name_trans":"","pointing_module_name":"",
        "pointing_module_name_trans":"","pointing_field_name":"",
        "pointing_field_name_trans":"","columns":"","cond":"",
        "paginate":"","nr_page":"","add_record":"",
        "sort":" ","edit_record":"","delete_record":""
        ,"br_id":""};

    $scope.tableParams = new ngTableParams({
        page: 1,            // show first page
        count: 5,  // count per page
        filter: { name: "",module_name:"",pointing_module_name:""} 
    }, {
       counts: [5,15], 
        getData: function($defer, params) {
        $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction=retrieve').
            success(function(data, status) {
              var orderedData = data;
              orderedData = params.filter() ?
                    $filter('filter')(orderedData, params.filter()) :
                    orderedData;
              params.total(orderedData.length);
              $defer.resolve(orderedData.slice((params.page() - 1) * params.count(),params.page() * params.count()));
        })
            }
    });
    $scope.tableParamsTabs = new ngTableParams({
        page: 1,            // show first page
        count: 5  // count per page

    }, {
       counts: [5,15], 
        getData: function($defer, params) {
        $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction=get_tab').
            success(function(data, status) {
              var orderedData = data;
              params.total(data.length);
              $defer.resolve(orderedData.slice((params.page() - 1) * params.count(),params.page() * params.count()));
        })
            }
    });

    // delete selected record
    $scope.delete_record =  function(user) {
     if(confirm('Are you sure you want to delete?'))
     {
         var data_send =JSON.stringify(user);
         $http.post('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction=delete&models='+encodeURIComponent(data_send)
        )
        .success(function(data, status) {
              $scope.tableParams.reload();

         });
        }
    };
        // delete selected record
    $scope.delete_record2 =  function(user) {
     if(confirm('Are you sure you want to delete?'))
     {
         var data_send =JSON.stringify(user);
         $http.post('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction=deletetab&models='+encodeURIComponent(data_send)
        )
        .success(function(data, status) {
              $scope.tableParamsTabs.reload();

         });
        }
    }

      $scope.open = function (us,type) {
          
        var modalInstance = $modal.open({
          templateUrl: 'Smarty/templates/modules/NgBlock/edit_modal.html',
          controller: 'ModalInstanceCtrl',
          resolve: {
            user :function () {
              return us;
            },
            type :function () {
              return type;
            },
            tbl :function () {
              return $scope.tableParams;
            }
          }
        });

        modalInstance.result.then(function (selectedItem) {
          $scope.selected = selectedItem;
        }, function () {
          //$log.info('Modal dismissed at: ' + new Date());
        });
      };

    $scope.open2 = function (us,type) {
        var modalInstance = $modal.open({
          templateUrl: 'Smarty/templates/modules/NgBlock/add_ng_tab.html',
          controller: 'TabInstanceCtrl',
          resolve: {
              user :function () {
              return us;
            },
            type :function () {
              return type;
            },
            tbl :function () {
              return $scope.tableParamsTabs;
            }
          }
        });

        modalInstance.result.then(function (selectedItem) {
          $scope.selected = selectedItem;
        }, function () {
          //$log.info('Modal dismissed at: ' + new Date());
        });
      };

});
angular.module('demoApp')
.controller('ModalInstanceCtrl',function ($scope,$http,$modalInstance,user,type,tbl) {

      $scope.translations = {/literal}{$translations}{literal};console.log($scope.translations);
      $scope.user = (type === 'add' ? {} : user);
      $scope.selected = {
        item: 0
      };
      $scope.Action = (type === 'add' ? 'Create' : 'Edit');
      $scope.Action = (type === 'duplicate' ? 'Duplicate' : $scope.Action);
      type=(type === 'duplicate' ? 'add' : type);

      $scope.module_sel=[];
      $scope.blocks=[];
      $scope.pointing_field=[];
      $scope.tab_rl_opt=[];
      $scope.mod_sel={'tablabel':$scope.user.module_name,'tabid':$scope.user.module_id};
      $scope.pointing_module_name_sel={'tablabel':$scope.user.pointing_module_name};
      $scope.pointing_block_name_sel={'label':$scope.user.pointing_block_name};
      $scope.pointing_field_name_sel={'columnname':$scope.user.pointing_field_name};
      $scope.related_tab_sel={'id':$scope.user.related_tab};
      
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=relation&kaction=retrieve').
                    success(function(data, status) {
                      $scope.modules = data;
      });
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=block&kaction=retrieve').
                    success(function(data, status) {
                      $scope.blocks = data;
      });
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=pointing_field_name&kaction=retrieve').
                    success(function(data, status) {
                      $scope.pointing_field = data;
      });
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction=get_tab').
                    success(function(data, status) {
                      $scope.tab_rl_opt = data;
      });
      
      $scope.destination_opt=['DETAILVIEWWIDGET','RELATEDVIEWWIDGET','PORTALDV','PORTALSV','PORTALLV','DETAILVIEWWIDGET_PORTAL','CUSTOMERPORTAL'];
      $scope.type_opt=['Table','Graph','Elastic','Text','Custom'];  
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction=get_elastic_indexes').
                    success(function(data, status) {
                      $scope.get_elastic_indexes = data;
      });
      $scope.myFilter = function(value) {
       return ($scope.filterValues.indexOf(value.id) !== -1);
      };
      // edit selected record
      $scope.setEditId =  function(user) {
            user =JSON.stringify(user);
            $http.post('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction='+type+'&models='+encodeURIComponent(user)
                )
                .success(function(data, status) {
                      tbl.reload();  
                      $modalInstance.close($scope.selected.item);
                 });
            };
            
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve&selected='+$scope.user.columns+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.columns=data;
                  });
       
       $scope.functionClick = function( data ) {
           //alert($scope.user.columns);
           if($scope.user.columns!=undefined)
               var arr = $scope.user.columns.split(',');
           else
               var arr = new Array();
           var index =arr.indexOf(data.fieldname);
           if(index!==-1)
           {
               arr.splice(index,1);
           }
           else
           {
               arr.push(data.fieldname);
           }
           $scope.user.columns=arr.join(',');
       };
       
        $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve_br&selected='+$scope.user.br_id+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.br_id=data;
          });
        $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve_br_profile&selected='+$scope.user.createcol+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.createcol=data;
          });
       
       $scope.functionClick_br = function( data ) {          
           if ($scope.user.br_id===data.businessrulesid){
               $scope.user.br_id='';
           }
           else{
               $scope.user.br_id=data.businessrulesid;
           }
           
       };
       $scope.functionClick_createcol = function( data ) {
           //alert($scope.user.columns);
           if($scope.user.createcol!=undefined && $scope.user.createcol!='')
               var arr = $scope.user.createcol.split(',');
           else
               var arr = new Array();
           var index =arr.indexOf(data.businessrulesid);
           if(index!==-1)
           {
               arr.splice(index,1);
           }
           else
           {
               arr.push(data.businessrulesid);
           }
           $scope.user.createcol=arr.join(',');
       };
       $scope.refresh_columns = function(  ) {
           $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve&selected='+$scope.user.columns+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.columns=data;
                  });
       };
       
       $scope.refresh_br = function(  ) {
           $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve_br&selected='+$scope.user.br_id+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.br_id=data;
                  });
       };
      
      $scope.fill_condition = function (a,b) {
        $scope.user.cond+=' '+a+' '+b+' ';
      };
      $scope.fill_sort = function (a,b) {
        $scope.user.sort=' '+a+','+b;
      };
      $scope.getTab = function (a) {
        return $scope.mod_sel.tabid===a;
      };
      
      $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
      };

      $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
      };
});
angular.module('demoApp')
    .filter('filter_blocks', function() {
          return function(blocks,user) {
            var filterEvent = [];
            for (var i = 0;i < blocks.length; i++){
                if(blocks[i]['tablabel']==user.module_name){
                    filterEvent.push(blocks[i]);
                }
            }
            return filterEvent;
        }
    }
    );
angular.module('demoApp')
.filter('filter_pointing_fields', function() {
      return function(pointing_field,user) {
        var filterEvent = [];
        for (var i = 0;i < pointing_field.length; i++){
            if(pointing_field[i]['tablabel']==user.pointing_module_name){
                filterEvent.push(pointing_field[i]);
            }
        }
        return filterEvent;
    }
});
angular.module('demoApp')
    .filter('filter_tab_rl', function() {
          return function(tab_rl_opt,user) {
            var filterEvent = [];
            for (var i = 0;i < tab_rl_opt.length; i++){
                if(tab_rl_opt[i]['moduleid']==user.module_name){
                    filterEvent.push(tab_rl_opt[i]);
                }
            }
            return filterEvent;
        }
    }
    );
angular.module('demoApp')
.filter('filter_source_fields', function() {
      return function(pointing_field,user) {
        var filterEvent = [];
        for (var i = 0;i < pointing_field.length; i++){
            if(pointing_field[i]['tablabel']==user.module_name){
                filterEvent.push(pointing_field[i]);
            }
        }
        return filterEvent;
    }
});
angular.module('demoApp')
.filter('filter_dest_fields', function() {
      return function(columns_search,user) {
        var filterEvent = [];
        for (var i = 0;i < columns_search.length; i++){
            if(columns_search[i]['tablabel']==user.pointing_module_name){
                filterEvent.push(columns_search[i]);
            }
        }
        return filterEvent;
    }
});
angular.module('demoApp')
.controller('TabInstanceCtrl',function ($scope,$http,$modalInstance,user,type,tbl) {

      $scope.translations = {/literal}{$translations}{literal};console.log($scope.translations);
      $scope.user = (type === 'add' ? {} : user);
      $scope.selected = {
        item: 0
      };
      $scope.Action = (type === 'add' ? 'Create' : 'Edit');
      $scope.act = (type === 'add' ? 'addtab' : 'edittab');

      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=relation&kaction=retrieve').
                success(function(data, status) {
                  $scope.modules = data;
      });
      $scope.module_sel=[];
      $scope.mod_sel={'tablabel':$scope.user.moduleid,'tabid':$scope.user.moduleid};
      
      $scope.setEditId =  function(user) {
            user =JSON.stringify(user);
            $http.post('index.php?module=NgBlock&action=NgBlockAjax&file=index&kaction='+$scope.act+'&models='+user
                )
                .success(function(data, status) {
                      tbl.reload();  
                      $modalInstance.close($scope.selected.item);
                 });
            };
      $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
      };
});
angular.module('demoApp') 
.controller('ng_EvoFields', function($scope, $http, $modal, ngTableParams) {

    $scope.new_user={};
    $scope.translations = {/literal}{$translations}{literal};console.log($scope.translations);

    $scope.tableParams = new ngTableParams({
        page: 1,            // show first page
        count: 5  // count per page

    }, {
       counts: [5,15], 
        getData: function($defer, params) {
        $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=ng_fields&kaction=retrieve').
            success(function(data, status) {
              var orderedData = data;
              params.total(data.length);
              $defer.resolve(orderedData.slice((params.page() - 1) * params.count(),params.page() * params.count()));
        })
            }
    });
    
    // delete selected record
    $scope.delete_record =  function(user) {
     if(confirm('Are you sure you want to delete?'))
     {
         var data_send =JSON.stringify(user);
         $http.post('index.php?module=NgBlock&action=NgBlockAjax&file=ng_fields&kaction=delete&models='+data_send
        )
        .success(function(data, status) {
              $scope.tableParams.reload();

         });
        }
    };
     
      $scope.open2 = function (us,type) {
          
        var modalInstance = $modal.open({
          templateUrl: 'Smarty/templates/modules/NgBlock/edit_evo_fields.html',
          controller: 'ng_Evo_Edit',
          resolve: {
            user :function () {
              return us;
            },
            type :function () {
              return type;
            },
            tbl :function () {
              return $scope.tableParams;
            }
          }
        });

        modalInstance.result.then(function (selectedItem) {
          $scope.selected = selectedItem;
        }, function () {
          //$log.info('Modal dismissed at: ' + new Date());
        });
      };
});
angular.module('demoApp')
.controller('ng_Evo_Edit',function ($scope,$http,$modalInstance,user,type,tbl) {

      $scope.user = (type === 'add' ? {"fieldid":"","fieldname":"","module_name":"","pointing_block_name":"",
        "columns_search":"","columns_shown":"",
        "br_id":"","type":""
        ,"existing":false,source:[]} : user);
    $scope.translations = {/literal}{$translations}{literal};console.log($scope.translations);
      $scope.selected = {
        item: 0
      };
      $scope.Action = (type === 'add' ? 'Create' : 'Edit');
      $scope.act = (type === 'add' ? 'add_ng_field' : 'edit_ng_field');
      $scope.blocks=[];

      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=relation&kaction=retrieve').
                success(function(data, status) {
                  $scope.modules = data;
      });
      $scope.module_sel=[];
      $scope.mod_sel={'tablabel':$scope.user.module_name,'tabid':$scope.user.module_id};
      $scope.pointing_module_name_sel={'tablabel':$scope.user.pointing_module_name};
      $scope.pointing_block_name_sel={'label':$scope.user.pointing_block_name};
      $scope.pointing_field_name_sel={'columnname':$scope.user.fieldname};
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve').
                    success(function(data, status) {
                      $scope.pointing_field = data;
      });
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=block&kaction=retrieve').
                    success(function(data, status) {
                      $scope.blocks = data;
      });
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve&selected='+$scope.user.columns_search+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.columns_search=data;
                  });
      $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve&selected='+$scope.user.columns_shown+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.columns_shown=data;
                  });            
      $scope.functionClick_shown = function( data ) {
           //alert($scope.user.columns_shown);
           if($scope.user.columns_shown!='')
               var arr = $scope.user.columns_shown.split(',');
           else
               var arr = new Array();
           var index =arr.indexOf(data.columnname);
           if(index!==-1)
           {
               arr.splice(index,1);
           }
           else
           {
               arr.push(data.columnname);
           }
           $scope.user.columns_shown=arr.join(',');
       };
       $scope.functionClick_search = function( data ) {
           //alert($scope.user.columns_search);
           if($scope.user.columns_search!='')
               var arr = $scope.user.columns_search.split(',');
           else
               var arr = new Array();
           var index =arr.indexOf(data.columnname);
           if(index!==-1)
           {
               arr.splice(index,1);
           }
           else
           {
               arr.push(data.columnname);
           }
           $scope.user.columns_search=arr.join(',');
       };
       
        $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve_br&selected='+$scope.user.br_id+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.br_id=data;
                  });
       
       $scope.functionClick_br = function( data ) {          
           if ($scope.user.br_id===data.businessrulesid){
               $scope.user.br_id='';
           }
           else{
               $scope.user.br_id=data.businessrulesid;
           }
           
       };
      $scope.refresh_columns_shown = function(  ) {
           $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve&selected='+$scope.user.columns_shown+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.columns_shown=data;
                  });
       };
       $scope.refresh_columns_search = function(  ) {
           $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve&selected='+$scope.user.columns_search+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.columns_search=data;
                  });
       };
       
      $scope.refresh_br = function(  ) {
           $http.get('index.php?module=NgBlock&action=NgBlockAjax&file=field&kaction=retrieve_br&selected='+$scope.user.br_id+'&pointing_module='+$scope.user.pointing_module_name).
                success(function(data, status) {
                 $scope.br_id=data;
                  });
      };
      $scope.type_opt=[
          {name:'Reference10 autocomplete(1021)',id:'1021'},
          {name:'Picklist autocomplete(1022)',id:'1022'},
          {name:'MultiPicklist autocomplete(1023)',id:'1023'},
          {name:'MultiPicklist Role(1024)',id:'1024'},
          {name:'Reference10 MultiSelect(1025)',id:'1025'},
          {name:'Reference long description(1026)',id:'1026'}];  
      $scope.type={'id':$scope.user.type};
      $scope.setEditId =  function(user) {
          var br=user.br_id;
          var src='',dst='';
          for(var i = 0; i <$scope.source.length; i++){
            src+=(i==0 ? $scope.source[i]['source_field_name']['columnname'] : ','+$scope.source[i]['source_field_name']['columnname']);
            dst+=(i==0 ? $scope.source[i]['dest_field_name']['columnname'] : ','+$scope.source[i]['dest_field_name']['columnname']);
          }
          user['fld_source']=src;
          user['fld_destination']=dst;
          user =JSON.stringify(user);
            //if(br!==''){
                $http.post('index.php?module=NgBlock&action=NgBlockAjax&file=ng_fields&kaction='+$scope.act+'&models='+encodeURIComponent(user)
                    )
                    .success(function(data, status) {
                          tbl.reload();  
                          $modalInstance.close($scope.selected.item);
                     });
            //}
            //else{
            //    alert('Business Rule is mandatory');
            //}
        };
      $scope.source=$scope.user.source;
      $scope.addsourcedest=  function() {
         $scope.source.push({"source_field_name":'',"dest_field_name":''});
      };
      $scope.removesourcedest=  function(ind) {
          for(var i = 0; i <$scope.source.length; i++){
            if(i == ind){
                $scope.source.splice(i,1);
            }
          }
      };
      $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
      };
});
{/literal}
</script>
