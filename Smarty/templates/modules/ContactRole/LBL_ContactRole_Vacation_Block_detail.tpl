
<link href="include/air-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">
<script src="include/air-datepicker/js/datepicker.min.js"></script>
<script src="include/air-datepicker/js/i18n/datepicker.en.js"></script>


<tr style="height:50px" class="createview_field_row">

    <td id="td_contactrole_vacations_desc" width="20%" class="dvtCellLabel" align="right"><font color="red"></font>{'LBL_ContactRole_Dates'|@getTranslatedString:'ContactRole'}</td>
    <td id="mouseArea_contactrole_vacations"
        width="80%"
        align="left" class="dvtCellInfo"  onmouseout="fnhide('crmspanid');">
        <input
            style="height:40px; margin-top: 3px;"
            type="text"
            id="txtbox_contactrole_vacations"
            name ="contactrole_vacations"
            data-multiple-dates="30"
            data-multiple-dates-separator=","
            data-position="top left"
            data-date-format="dd/mm/yyyy"
            data-language="en"
            class="detailedViewTextBox datepicker-here"
            onfocus="this.className='detailedViewTextBoxOn'"
            onblur="this.className='detailedViewTextBox'"
            onmouseover="hndMouseOver(1,'contactrole_vacations');"
            onclick="handleEdit(event);"
            value="{$FIELDS.contactrole_vacations}" />
            <br>

            <input id="dtlview_contactrole_vacations"  type="hidden" value="{$FIELDS.contactrole_vacations}" />

            <div id="editarea_contactrole_vacations" style="display:none;">
                <a class="detailview_ajaxbutton ajax_save_detailview"
                   onclick="document.getElementById('dtlview_contactrole_vacations').value=document.getElementById('txtbox_contactrole_vacations').value;
                   dtlViewAjaxSave('contactrole_vacations','ContactRole', 1,'vtiger_contactrole','contactrole_vacations','{$ID}');
                   fnhide('crmspanid');event.stopPropagation();">Save</a>
                <a href="javascript:;"
                    onclick="document.getElementById('txtbox_contactrole_vacations').value = document.getElementById('dtlview_contactrole_vacations').value;
                    hndCancel('dtlview_contactrole_vacations','editarea_contactrole_vacations','contactrole_vacations');
                    event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">Cancel</a>
            </div>
    </td>
</tr>

<script>
    $(document).ready(function(){
        
        var datepicker = $('#txtbox_contactrole_vacations').datepicker().data('datepicker'),
        selectedValues = "{$FIELDS.contactrole_vacations}",
        selected_dates = [];


        var res = selectedValues.split(",");
    
        for(var i=0; i<res.length; i++){
            var parts =res[i].split('/');
            var mydate = new Date(parts[2], parts[1] - 1, parts[0]);
            selected_dates.push(mydate);
        }
        datepicker.selectDate(selected_dates)
    });
</script>

