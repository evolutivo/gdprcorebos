<link href="include/air-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">
<script src="include/air-datepicker/js/datepicker.min.js"></script>
<script src="include/air-datepicker/js/i18n/datepicker.en.js"></script>

<tr style="height:50px" class="createview_field_row">
    <td id="td_othercountry" width="20%" class="dvtCellLabel" align="right"><font color="red"></font>{'LBL_ContactRole_Dates'|@getTranslatedString:'ContactRole'}</td>
    <td id="mouseArea_contactrole_vacations"
        width="80%"
        align="left"
        class="dvtCellInfo">

        <input
            style="height:40px; margin-top: 3px;"
            type="text"
            id="txtbox_contactrole_vacations"
            name ="contactrole_vacations"
            data-multiple-dates="60"
            data-multiple-dates-separator=","
            data-position="top left"
            data-date-format="dd/mm/yyyy"
            data-language="en"
            class="detailedViewTextBox datepicker-here"
            onfocus="this.className='detailedViewTextBoxOn'"
            onblur="this.className='detailedViewTextBox'"
            value="{$FIELDS.contactrole_vacations}" />
            <br>
    </td>
</tr>


<script>
    $(document).ready(function(){

        var datepicker = $('#txtbox_contactrole_vacations').datepicker().data('datepicker'),
            selectedValues = "{$FIELDS.contactrole_vacations}",
            selected_dates = [];

        var res = selectedValues.split(",");

        for(var j=0; j<res.length; j++){
            var parts =res[j].split('/');
            var mydate = new Date(parts[2], parts[1] - 1, parts[0]);
            selected_dates.push(mydate);
        }

        if({$FIELDS.contactrole_vacations}){
            for(var i = 0; i<selected_dates.length; i++){
                datepicker.selectDate(selected_dates[i])
            }
        }else{
            document.getElementById('txtbox_contactrole_vacations').value  = "{$FIELDS.contactrole_vacations}"
        }
    });
</script>
