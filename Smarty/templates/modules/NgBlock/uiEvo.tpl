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
<table ng-controller="ng_EvoFields" ng-table="tableParams" class="table  table-bordered table-responsive" width=100% >
    <tr>
        <td colspan="5">
             <button class="btn btn-primary" ng-click="open2(new_user,'add')">{'Add New Evo Field'|@getTranslatedString:'Add New Evo Field'}</button>
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
            {'Shown Fields'|@getTranslatedString:'Shown Fields'}
        </th>
        <th style="text-align: center">
            {'Searchable Fields'|@getTranslatedString:'Searchable Fields'}
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
                  <!--<th>
                      <img ng-if="!user.$edit" width="20" height="20" ng-click="delete_record2(user)" src="themes/images/delete.gif" />
                      <a ng-click="delete_record2(user)">Delete</a>
                  </th>   -->
              </tr>             
              </table>
          </td>
          <td> 
              {literal}{{user.fieldname}} {/literal}                                                         
          </td> 
          <td> 
              {literal}{{user.module_name_trans}}{/literal}
          </td>
          <td> 
              {literal}{{user.columns_shown}} {/literal}                                                         
          </td> 
          <td> 
              {literal}{{user.columns_search}}{/literal}
          </td>
    </tr>
</table>
          