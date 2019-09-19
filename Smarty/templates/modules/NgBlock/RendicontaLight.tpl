<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">
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
                                    <img id="aid{$NG_BLOCK_NAME|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Hide" title="Hide"/>
                            </a></div><b>&nbsp;
                            {$NG_BLOCK_NAME}
                    </b></div>
    </td>{/strip}
</tr>
</table>

<div style="width:auto;display:inline;" id="tbl{$NG_BLOCK_NAME|replace:' ':''}" >
<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
    <tr>
        <td>
            <table ng-controller="block_{$NG_BLOCK_ID}"  ng-table="tableParams" class="table  table-bordered table-responsive">
            
            <tr ng-repeat="user in $data"  class="dvtCellInfo">
              <td style="text-align:center;">
                  <input type="button" ng-click="changeSubstatus(user.valuereal)" 
                         class="{literal}{{user.color}}{/literal}" align="center" value="{literal}{{user.value}}{/literal}"/>
              </td> 
            </tr>
        </table>
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

.green{
    font-size: 100%;
    color: white;
    border-radius: 4px;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
    background: rgb(28, 184, 65);
    width:70px;
} 
.orange{
    font-size: 100%;
    color: white;
    border-radius: 4px;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
    background: #FF9900;
}
{/literal}
</style>
<script type="text/ng-template" id="DetailViewBlockNgEdit{$NG_BLOCK_ID}.html">
<div class="modal-header">
    <h4 class="modal-title" ng-model="columnName">Cambio stato in corso</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <progressbar class="progress-striped active" value="dynamic" type="info"></progressbar>
    </div>
</div>
</script>
<script>
{literal}
var path='module=Cases&action=CasesAjax&file=get_substatuses&id={/literal}{$RECORD_ID}{literal}';
angular.module('demoApp')
.controller('block_{/literal}{$NG_BLOCK_ID}{literal}',function($scope, $http, $modal, ngTableParams,$timeout) {
    $scope.user={};
            
    $scope.tableParams = new ngTableParams({
        page: 1,            // show first page
        count: 5  // count per page

    }, {
       counts: [5,15], 
        getData: function($defer, params) {
        $http.get('index.php?'+path+'&kaction=retrieve').
            success(function(data, status) {
              var orderedData = data;
              params.total(data.length);
              $defer.resolve(orderedData.slice((params.page() - 1) * params.count(),params.page() * params.count()));
        })
            }
    });

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
            }
          }
        });

        modalInstance.result.then(function (selectedItem) {
          $scope.selected = selectedItem;
        }, function () {
          //$log.info('Modal dismissed at: ' + new Date());
        });
      };
      
    $scope.changeSubstatus = function (val) {
        $scope.modalInstance = $modal.open({
              templateUrl: 'DetailViewBlockNgEdit{/literal}{$NG_BLOCK_ID}{literal}.html',
              controller: 'ModalInstanceCtrl{/literal}{$NG_BLOCK_ID}{literal}',
              windowClass: 'app-modal-window',
              //backdrop: "static",
              resolve: {
                tbl :function () {
                  return $scope.tableParams;
                }
              }
        });
        $http.get('index.php?'+path+'&kaction=rendiconta&next_sub='+val).
            success(function(data, status) {
              $scope.tableParams.reload();  
              $scope.modalInstance.close();
              location.reload();
        })
      };

})
.controller('ModalInstanceCtrl{/literal}{$NG_BLOCK_ID}{literal}',function ($scope,$http,$modalInstance,tbl) {

    $scope.dynamic = 50;
      
});
{/literal}
</script>