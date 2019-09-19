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
 *  Module	   : NgBlock
 *  Version	  : 5.5.0
 *  Author	   : OpenCubed.
 *************************************************************************************************/
-->*}
<link rel="stylesheet" type="text/css" href="Smarty/angular/bootstrap.min.css"/>
{strip}
	<div class="forceRelatedListSingleContainer">
		<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
			<div class="slds-card__header slds-grid">
				<header class="slds-media slds-media--center slds-has-flexi-truncate">
					<div class="slds-media__figure">
						<div class="extraSmall forceEntityIcon">
							<span class="uiImage">
								<a href="javascript:showHideStatus('tbl{$NG_BLOCK_NAME|replace:' ':''}','aid{$NG_BLOCK_NAME|replace:' ':''}','{$IMAGE_PATH}');">
									{if $OPENED eq 1}
										<img id="aid{$NG_BLOCK_NAME|replace:' ':''}" src="{'chevrondown_60.png'|@vtiger_imageurl:$THEME}" width="16" alt="Display" title="Display"/>
									{else}
										<img id="aid{$NG_BLOCK_NAME|replace:' ':''}" src="{'chevronright_60.png'|@vtiger_imageurl:$THEME}" width="16" alt="Hide" title="Hide"/>
									{/if}
								</a>
							</span>
						</div>
					</div>
					<div class="slds-media__body">
						<h2>
							<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$header}">
								<b>{$NG_BLOCK_NAME}</b>
							</span>
						</h2>
					</div>
				</header>
				<div class="slds-no-flex">
					<div class="actionsContainer mapButton">
						{if $NG_BLOCK_NAME eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts') }
							{if $MODULE eq 'Leads'}
								<input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="slds-button slds-button--small slds-button_success" type="button" onClick="searchMapLocation( 'Main' )" title="{$APP.LBL_LOCATE_MAP}">
							{/if}
						{/if}
					</div>
				</div>
			</div>
		</article>
	</div>
{/strip}

{if $OPENED eq 1}
	<div class="slds-truncate" style="display:block;" id="tbl{$NG_BLOCK_NAME|replace:' ':''}" >
{else}
	<div class="slds-truncate" style="display:none;" id="tbl{$NG_BLOCK_NAME|replace:' ':''}" >
{/if}

		<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
			<tr>
				<td>
					<table ng-controller="block_{$NG_BLOCK_ID}"  ng-table="tableParams" class="table table-bordered table-responsive">
						{if $ADD_RECORD eq 1 }
							<tr class="dvtCellLabel text-left">
								{math equation="x" x=$FIELD_LABEL|@count assign="nr_col"} 
								<td>
									<img width="20" height="20" ng-click="open(user,'create')" src="themes/softed/images/btnL3Add.gif" />
									{if $MODULE_NAME eq 'Project' && $POINTING_MODULE eq 'Messages'}
										<a ng-click="open(user,'create')">{'Crea Nota'|@getTranslatedString:'Crea Nota'}</a> 
									{else}
										<a ng-click="open(user,'create')">{'Add New'|@getTranslatedString:'Add New'} {$NG_BLOCK_NAME}</a> &nbsp;&nbsp;&nbsp;
									{/if}
								</td> 
								<td colspan="{$nr_col}">
									<a ng-click="open(user,'choose')" style="cursor: ">{'Choose'|@getTranslatedString:'Choose'} {$NG_BLOCK_NAME}</a> &nbsp;&nbsp;&nbsp;
								</td>
							</tr>
						{/if}
							<tr class="dvtCellLabel text-left">
								{foreach key=index item=fieldlabel from=$FIELD_LABEL} 
									{if $COLUMN_NAME_LIST.$index eq true} 
									<td> <b>{$fieldlabel}</b> </td> 
									{/if}
								{/foreach} 
								<td> </td> 
							</tr>
							<tr ng-repeat="user in $data"  class="dvtCellInfo">
								{foreach key=index item=fieldname from=$COLUMN_NAME} 
								 {if $COLUMN_NAME_LIST.$index eq true}  
									  {if $index eq 0}
										  <td >
											 <a href="{literal}{{user.href}}{/literal}">{literal}{{user.{/literal}{$fieldname}_display{literal}}}{/literal}</a>
										  </td> 
									  {else}
										  <td > 
											  {if in_array($FIELD_UITYPE.$index,array(10,51,50,73,68,57,59,58,76,75,81,78,80) )}
												  <div ng-bind-html="user.{$fieldname}_display | sanitize"></div> 
											  {elseif in_array($FIELD_UITYPE.$index,array(5,6,23,26,53,56) )}
												  <div ng-bind-html="user.{$fieldname}_display"></div> 
											  {elseif in_array($FIELD_UITYPE.$index,array(69,105,28) )}
												  <a ng-click="downloadfile(user.preview)"><b>{literal}{{{/literal}user.{$fieldname}{literal}}}{/literal}</b></a>
											  {else}
												  <div ng-bind-html="user.{$fieldname} | sanitize"></div> 
											  {/if}
										  </td>
									  {/if}
								 {/if} 
								{/foreach} 
								<td  width="80" >
								<table> 
									  <tr>
										  {if $EDIT_RECORD eq 1}
										  <td>
											  <img ng-if="!user.$edit" width="20" height="20" ng-click="open(user,'edit')" src="themes/images/editfield.gif" /> 
										  </td>
										  {/if}
										  {if $DELETE_RECORD eq 1}
										  <td>
											  <img ng-if="!user.$edit" width="20" height="20" ng-click="delete_record(user)" src="themes/images/delete.gif" />
										  </td> 
										  {/if}
										  {foreach key=count_i item=block_id from=$SUB_NG} 
												<td>
													<img width="20" height="20" ng-click="show_sub_ng_block(user,'{$block_id}')" src="themes/images/quickview.png" />
												</td> 
										  {/foreach} 
									  </tr>			 
								 </table>   
								</td>
							</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

<script type="text/ng-template" id="DetailViewBlockNgEdit{$NG_BLOCK_ID}.html">

<div class="modal-header">
	<h4 class="modal-title">{literal}{{Action}} {{PointigModule}} {{user.name}}{/literal}</h4>
</div>
<div class="modal-body">
	<table  >
	<tr ng-if="type=='subngblock'">
		<td style="height:300px;vertical-align:top">
			<table ng-table-dynamic="tableParamsSubNg with cols_SubNg" show-filter="false" >
				<tr style="cursor: pointer;" ng-repeat="row in $data ">
					<td ng-repeat="col in $columns" ng-bind-html="::row[col.field]"></td>
				</tr>
			</table>
		</td>
	</tr>
	 <tr ng-if="type=='choose'">
		<td style="height:300px;vertical-align:top">
				<input ng-model="choosen_entity" type="hidden"  >  
				<multi-select   
					input-model="choosen_entity1" 
					output-model="selected_values_choosen_entity"
					button-label="name"
					item-label="name"
					tick-property="ticked" 
					on-item-click="functionClick_en( data )">
				</multi-select>
		</td>
	</tr>
	{foreach key=index item=fieldname from=$COLUMN_NAME} 
		{if  $FIELD_UITYPE.$index neq '70' && $COLUMN_NAME_EDIT.$index eq true}
		  <tr ng-class-odd="'emphasis'" ng-class-even="'odd'" ng-if="type!='choose' && type!='subngblock'">
			  <td style="text-align:right;"> 
				  {$FIELD_LABEL.$index}
			  </td>
			  <td style="text-align:left;"> 
			  {if in_array($FIELD_UITYPE.$index,array(15,16))}
				  <select class="form-control" ng-model="user.{$fieldname}"  ng-options="op for op  in opt.{$fieldname}"></select>
			  {elseif in_array($FIELD_UITYPE.$index,array(26))}
				  <select class="form-control" ng-model="user.{$fieldname}"  ng-options="op.id as op.name for op  in opt.{$fieldname}"></select>
			  {elseif in_array($FIELD_UITYPE.$index,array(55)) && $fieldname eq 'salutationtype'}
				  <select class="form-control" ng-model="user.{$fieldname}"  ng-options="op for op  in opt.{$fieldname}"></select>
			  {elseif in_array($FIELD_UITYPE.$index,array(10,51,50,73,68,57,59,58,76,75,81,78,80) )  }
			  <input class="form-control" style="width:250px;" type="hidden" id="{$fieldname}" ng-model="user.{$fieldname}" value="user.{$fieldname}"/>
			  <input class="form-control" style="width:250px;" type="text" id="{$fieldname}_display" ng-model="user.{$fieldname}_display" value="user.{$fieldname}_display" onchange="alert(this.value);"/>
			  <img src="{'select.gif'|@vtiger_imageurl:$THEME}"
				alt="Select" title="Select" LANGUAGE=javascript  onclick='return window.open("index.php?module={$REL_MODULE.$fieldname}&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield={$fieldname}&srcmodule={$POINTING_MODULE}&responseTo=ngBlockPopup","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>
			  {elseif in_array($FIELD_UITYPE.$index,array(5,6,23) )}
				  <input type="hidden" ng-model="user.{$fieldname}"/>
				  <input type="date" class="form-control" ng-model="user.{$fieldname}_display2" placeholder="yyyy-MM-dd" ng-change="put_date('{$fieldname}');"/>
			  {elseif $FIELD_UITYPE.$index eq '33'}
				  <select multiple class="form-control" ng-model="user.{$fieldname}"  
						  ng-options="op for op  in opt[{$index}]"></select>
			  {elseif $FIELD_UITYPE.$index eq '19'}
					  <div text-angular="text-angular" name="user.{$fieldname}" ng-model="user.{$fieldname}"></div>
			  {elseif $FIELD_UITYPE.$index eq '56'}
					  <input ng-model="user.{$fieldname}" ng-checked="user.{$fieldname}" name="{$fldname}" tabindex="{$vt_tab}" type="checkbox"> 
			  {elseif $FIELD_UITYPE.$index eq '53'}
				  <input type="hidden" ng-model="user.{$fieldname}"/>{literal}{{{/literal}user.{$fieldname}_display2{literal}}}{/literal}
				  <select class="form-control" ng-model="user.{$fieldname}_display2" ng-change="put_ass('{$fieldname}');"
						  ng-options="op as op.crmname group by op.crmtype for op  in opt.{$fieldname} track by op.crmid"></select>
			  {elseif in_array($FIELD_UITYPE.$index,array(69,105,28) )}
				  <input type="file" file-upload />
						{literal}{{{/literal}user.{$fieldname}{literal}}}{/literal}
			  {else}
				  <input class="form-control" style="width:350px;" type="text" ng-model="user.{$fieldname}" placeholder="user.{$fieldname}_display2" />
			  {/if}
			   </td>
		  </tr>
	  {/if}
	{/foreach}
	</table>
</div>
<div class="modal-footer">
	{if $POINTING_MODULE neq 'Messages'}
		<button class="btn btn-primary" ng-click="setEditId(user)" ng-disabled="processing" ng-if="type!='choose'">{'Save'|@getTranslatedString:'Save'}</button>
	{else}
		<button class="btn btn-primary" ng-click="setEditId(user)" ng-if="type!='choose'">{'Send'|@getTranslatedString:'Send'}</button>
	{/if}
	<button class="btn btn-primary" ng-click="setRelation(choosen_entity)" ng-if="type=='choose'">{'Set Relation'|@getTranslatedString:'Set Relation'}</button>
	<button class="btn btn-warning" ng-click="cancel()">{'Cancel'|@getTranslatedString:'Cancel'}</button>
</div>
</script>
<style>
{literal}
.ta-editor {
	min-height: 50px;
	height: auto;
	overflow: auto;
	font-family: inherit;
	font-size: 100%;
	margin:20px 0;
}
{/literal}
</style>
<script>
{literal}
angular.module('demoApp')
.filter("sanitize", ['$sce', function($sce) {
  return function(htmlCode){
	return $sce.trustAsHtml(htmlCode);
  }
}])
.controller('block_{/literal}{$NG_BLOCK_ID}{literal}',function($scope,$window,$http, $modal, ngTableParams) {
	$scope.user={};
	$scope.showNgBlock=false;
	
		  $scope.tableParams = new ngTableParams({
				page: 1,			// show first page
				count: 5  // count per page

			}, {
			   counts: [5,15], 
				getData: function($defer, params) {
				$http.get('index.php?{/literal}{$blockURL}{literal}&kaction=retrieve').
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
		 user.href='';
		 user={id:user.id};
		 var data_send =JSON.stringify(user);
		 $http.post('index.php?{/literal}{$blockURL}{literal}&kaction=delete&models='+data_send
		)
		.success(function(data, status) {
			  $scope.tableParams.reload();

		 });
		}
	};
	
	$scope.downloadfile = function(path) {
		$window.open(path, '_blank');
	};

	$scope.show_sub_ng_block =  function(user,subngid) {
		var modalInstance = $modal.open({
		  templateUrl: 'DetailViewBlockNgEdit{/literal}{$NG_BLOCK_ID}{literal}.html',
		  controller: 'ModalInstanceCtrl{/literal}{$NG_BLOCK_ID}{literal}',
		  windowClass: 'app-modal-window',
		  //backdrop: "static",
		  resolve: {
			user :function () {
			  return user;
			},
			type :function () {
			  return 'subngblock';
			},
			tbl :function () {
			  return $scope.tableParams;
			},
			subngid :function () {
			  return subngid;
			}
		  }
		});

		modalInstance.result.then(function (selectedItem) {
		  $scope.selected = selectedItem;
		}, function () {
		  //$log.info('Modal dismissed at: ' + new Date());
		});
	};
	 
	$scope.open = function (user,type) {
		var modalInstance = $modal.open({
		  templateUrl: 'DetailViewBlockNgEdit{/literal}{$NG_BLOCK_ID}{literal}.html',
		  controller: 'ModalInstanceCtrl{/literal}{$NG_BLOCK_ID}{literal}',
		  windowClass: 'app-modal-window',
		  //backdrop: "static",
		  resolve: {
			user :function () {
			  return user;
			},
			type :function () {
			  return type;
			},
			tbl :function () {
			  return $scope.tableParams;
			},
			subngid :function () {
			  return '';
			}
		  }
		});

		modalInstance.result.then(function (selectedItem) {
		  $scope.selected = selectedItem;
		}, function () {
		  //$log.info('Modal dismissed at: ' + new Date());
		});
	  };

})
.controller('ModalInstanceCtrl{/literal}{$NG_BLOCK_ID}{literal}',function ($scope,$http,$filter,$modalInstance,ngTableParams,user,type,tbl,subngid) {
	  $scope.user = (type === 'create' ? {} : user);
	  $scope.user1 = {};
	  $scope.choosen_entity='';
	  $scope.selected = {
		item: 0
	  };
	  $scope.type=type;
	  $scope.Action = (type === 'create' ? 'Create' : 
			  type == 'subngblock' ? 'List' :
			  type == 'choose' ? 'Choose to relate' : 'Edit');
	  $scope.PointigModule = "{/literal}{$POINTING_MODULE}{literal}";
	  $scope.opt={/literal}{$OPTIONS}{literal}; 
	  $scope.col_json={/literal}{$COLUMN_NAME_JSON}{literal};
	  $scope.ui_json={/literal}{$FIELD_UITYPE_JSON}{literal};
	  $scope.default_json={/literal}{$DEFAULT_VALUE_JSON}{literal};
	  //$scope.map_field_dep={/literal}{$FLDDEP}{literal};
	  //$scope.MAP_PCKLIST_TARGET={/literal}{$MAP_PCKLIST_TARGET}{literal};
	  var array_date = ["5","6","23"];
	  for(var i=0;i<$scope.col_json.length;i++){
		  if(array_date.indexOf($scope.ui_json[i])!==-1){
			  $scope.user[$scope.col_json[i]+'_display2']=($scope.user[$scope.col_json[i]] != undefined ? new Date($scope.user[$scope.col_json[i]]) : new Date());
		  }
		  else if($scope.ui_json[i]=='53'){
			  $scope.user[$scope.col_json[i]+'_display2']={'crmid':$scope.user[$scope.col_json[i]]};
		  }
		  else if($scope.ui_json[i]=='56'){
			  $scope.user[$scope.col_json[i]]=($scope.user[$scope.col_json[i]]==1 ? true : false);
		  }
		  if($scope.default_json[i]!=='' && type === 'create')
		  {
			  if(array_date.indexOf($scope.ui_json[i])!==-1){
				  if(angular.isUndefined($scope.default_json[i]) || $scope.default_json[i] === null){
					var date = new Date();
					var startDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
					$scope.user[$scope.col_json[i]+'_display2'] = new Date(startDate);
					$scope.user[$scope.col_json[i]] =  $filter('date')(new Date(), 'yyyy-MM-dd');

				  }
				  else{
					$scope.user[$scope.col_json[i]+'_display2']=new Date($scope.default_json[i]);
					$scope.user[$scope.col_json[i]]= $filter('date')(new Date(), 'yyyy-MM-dd');

				  }
			  }
			  else if($scope.ui_json[i]=='53'){
				  $scope.user[$scope.col_json[i]+'_display2']={'crmid':$scope.default_json[i]};
			  }
			  else if($scope.ui_json[i]=='56'){
				  $scope.user[$scope.col_json[i]]=($scope.default_json[i]==1 ? true : false);
			  }
			  else if($scope.col_json[i]==='time_start'){
				var date = new Date();
				var startTime = date.getHours() + ':' + date.getMinutes();
				$scope.user[$scope.col_json[i]] = startTime;
			  }
			  else if($scope.col_json[i]==='time_end'){
				var date = new Date();
				var endTime = (date.getHours()  + 1) + ':' + date.getMinutes();
				$scope.user[$scope.col_json[i]] = endTime;
			  }
			  else
				  $scope.user[$scope.col_json[i]]=$scope.default_json[i];
		  }
	  }
	  $scope.processing=false;
	  // edit selected record
	  $scope.put_ass =  function(fld) {
			  var name=fld+'_display2';
			  $scope.user[fld]=$scope.user[name]['crmid'];
	  };
	  $scope.put_date =  function(fld) {
			  var name=fld+'_display2';
			  $scope.user[fld]=$filter('date')($scope.user[name], 'yyyy-MM-dd');
	  };

		if(type=='choose'){
			$http.get('index.php?{/literal}{$blockURL}{literal}&kaction=select_entity').
				success(function(data, status) {
					$scope.choosen_entity1=data;
				});
		}
		else if(type=='subngblock'){
			
			$scope.generateColumns = function(sampleData) {
					var colNames = sampleData;
					var cols = colNames.map(function(name, idx) {
						var filter = {};
						var label_trans = name;
						var returned_arr = {
							title: label_trans,
							sortable: name,
							show: true,
							field: name
						};
						filter[name] = 'text';
						returned_arr['filter'] = filter;
						return returned_arr;
					});
					return cols;
				};
			$scope.followRecord = function(row) {
				window.location = row.href;
			};
			$http.get('index.php?{/literal}{$blockURL}{literal}&kaction=subNgBlock&subngid='+subngid+'&sub_recordid='+user.id).
			success(function(data, status) {
			  $scope.sub_data=data.records;
			  $scope.sub_config=data.config;
			  $scope.PointigModule = $scope.sub_config.pointingModule;
			  $scope.cols_SubNg = $scope.generateColumns($scope.sub_config.columns);
			  var blockUrl='module=NgBlock&action=NgBlockAjax'+
					  '&file=ng_block_actions&id='+user.id+
					  '&ng_block_id='+subngid;
			  $scope.tableParamsSubNg = new ngTableParams({
				page: 1,			// show first page
				count: 5  // count per page
				}, {
				   counts: [5,15], 
					getData: function($defer, params) {
					$http.get('index.php?'+blockUrl+'&kaction=retrieve').
						success(function(data, status) {
						  var orderedData = data;
						  params.total(data.length);
						  $defer.resolve(orderedData.slice((params.page() - 1) * params.count(),params.page() * params.count()));
					})
						}
			}); 
		})
	}
	   
		$scope.functionClick_en = function( data ) {		  
		   if($scope.choosen_entity!=undefined)
			   var arr = $scope.choosen_entity.split(',');
		   else
			   var arr = new Array();
		   var index =arr.indexOf(data.id);
		   if(index!==-1)
		   {
			   arr.splice(index,1);
		   }
		   else
		   {
			   arr.push(data.id);
		   }
		   $scope.choosen_entity=arr.join(',');
	   };
	   
	  $scope.setRelation =  function(choosen) {
			user.href='';
			user =JSON.stringify(user);
			$http.post('index.php?{/literal}{$blockURL}{literal}&kaction=setRelation&relid='+choosen
				)
				.success(function(data, status) {
					  tbl.reload();  
					  $modalInstance.close($scope.selected.item);
				 });
	  };
	//an array of files selected
	$scope.files = [];
	//listen for the file selected event
	$scope.$on("fileSelected", function (event, args) {
		$scope.$apply(function () {			
			//add the file object to the scope's files collection
			$scope.files.push(args.file);
		});
	});
	
	  $scope.setEditId =  function(user) {
		  $scope.processing=true;
			{/literal}
			{foreach key=index item=fieldname from=$COLUMN_NAME} 
			  {if in_array($FIELD_UITYPE.$index,array(10,51,50,73,8,57,59,58,76,75,81,78,80))}
				  if(document.getElementById("{$fieldname}").value!='user.{$fieldname}')
					  user.{$fieldname}=document.getElementById("{$fieldname}").value;
			  {elseif in_array($FIELD_UITYPE.$index,array(69,105,28))}
				  if($scope.files.length>0){ldelim}
					  user.{$fieldname}=$scope.files[0].name;
					{rdelim}
			  {/if}
			{/foreach}
			{literal}
			user.href='';
			user =JSON.stringify(user);
			$http({
				method: 'POST',
				url: 'index.php?{/literal}{$blockURL}{literal}&kaction='+type+'&models='+encodeURIComponent(user),
				headers: { 'Content-Type': undefined },
				transformRequest: function (data) {
					var formData = new FormData();
					for (var i = 0; i < data.files.length; i++) {
						formData.append("filename" , data.files[i]);
					}
					return formData;
				},
				data: { files: $scope.files}
			})
				.success(function(data, status, headers, config) {
					  tbl.reload(); 
					  $scope.processing=false;
					  $modalInstance.close($scope.selected.item);
				 });
	  };

	  $scope.cancel = function () {
		$modalInstance.dismiss('cancel');
	  };
})
.directive('fileUpload', function () {
	return {
		scope: true,		//create a new scope
		link: function (scope, el, attrs) {
			el.bind('change', function (event) {
				var files = event.target.files;
				//iterate files since 'multiple' may be specified on the element
				for (var i = 0;i<files.length;i++) {
					//emit event upward
					scope.$emit("fileSelected", { file: files[i] });
				}									   
			});
		}
	};
})
.filter('getPickListDep', function() {
	return function(input,map_field_dep,columns,MAP_PCKLIST_TARGET,moduleData) {
	  var i = 0,
	  len = input.length;
	  var records = new Array();
	  for (; i < len; i++) {
		  if(MAP_PCKLIST_TARGET.indexOf(columns)!==-1){
			  angular.forEach(map_field_dep, function(value, key) {
				  if(value.target_picklist.indexOf(columns)!==-1){
					  var conditionResp = '';
					  angular.forEach(value.respfield, function(resp_val, resp_val_key) {
							var resp_value = value.respvalue_portal[resp_val_key];
							var comparison = value.comparison[resp_val_key];
							if (resp_val_key !== 0 ){
								if (comparison === 'empty' || comparison === 'notempty')
									conditionResp += ' || ';
								else
									conditionResp += ' && ';
							}
							if (comparison === 'empty')
							  conditionResp += (moduleData[resp_val] === '' || moduleData[resp_val] === undefined);
							else if (comparison === 'notempty')
							  conditionResp += (moduleData[resp_val] !== '' && moduleData[resp_val] !== undefined);
							else{
							  conditionResp += (resp_value.indexOf(moduleData[resp_val])!= -1 && moduleData[resp_val]!=undefined);
							}
					  });
					  if ( eval(conditionResp) ) 
					  {
						  angular.forEach(value.target_picklist_values[columns], function(targ_val, targ_key) {
							 if(input[i]==targ_val){
								  records.push(input[i]);
							  }	   
						  });
					  }
				  }
			});
		  }
		  else{
			  records.push(input[i]);
		  }
	  }
	  return records;
	};
  });
{/literal}
</script>
