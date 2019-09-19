<link rel="stylesheet" type="text/css" href="Smarty/angular/sortable/dist/ng-sortable.min.css">
<link rel="stylesheet" type="text/css" href="Smarty/angular/sortable/styles/board.css">
<!--scripts-->
<script type="text/javascript" src="Smarty/angular/sortable/model.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/app.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/scripts/services/BoardManipulator.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/scripts/services/BoardDataFactory.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/scripts/services/BoardService.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/scripts/controllers/KanbanController.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/scripts/controllers/SprintController.js"></script>
<script type="text/javascript" src="Smarty/angular/sortable/scripts/controllers/NewCardController.js"></script>

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
        <nav class="navbar navbar-default " role="navigation">
            <div class="container-fluid">
                <div class="board-header">
                    <ul class="nav nav-tabs nav-justified">
                        <li ><a href="#/kanban" >FLOW</a></li>
                        <!--<li ><a href="#/sprint" >RISPOSTE</a></li>-->
                    </ul>
                </div>
            <div ng-view></div>
        </nav>
</div>
    