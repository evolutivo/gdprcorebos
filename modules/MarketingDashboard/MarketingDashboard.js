/*************************************************************************************************
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
*************************************************************************************************/
$.getScript("index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=getjslanguage", function(){});
function MarketingDashboardsetValueFromCapture(recordid,value,target_fieldname) {
//    alert(recordid);
  //  alert(value);
    //    alert(target_fieldname);
document.getElementById(target_fieldname).value=recordid;
document.getElementById(target_fieldname+'_display').value=value;
  /*      if(target_fieldname=='campaignleadid') {
                document.getElementById('campaignleadid').value = recordid;
                document.getElementById('campaignleadid_display').value = value;}
            if(target_fieldname=='parentid') {
                document.getElementById('parentid').value = recordid;
                document.getElementById('parentid_display').value = value;}
             if(target_fieldname=='campaignid') {
                document.getElementById('campaignid').value = recordid;
                document.getElementById('campaignid_display').value = value;}
            if(target_fieldname=='campaignaccid') {
                document.getElementById('campaignaccid').value = recordid;
                document.getElementById('campaignaccid_display').value = value;}
             if(target_fieldname=='campaignmessid') {
                document.getElementById('campaignmessid').value = recordid;
                document.getElementById('campaignmessid_display').value = value;}
            if(target_fieldname=='campaigntaskid') {
                document.getElementById('campaigntaskid').value = recordid;
                document.getElementById('campaigntaskid_display').value = value;}
         if(target_fieldname=='campaignpotid') {
                document.getElementById('campaignpotid').value = recordid;
                document.getElementById('campaignpotid_display').value = value;}*/

}

function updateGridSelectAllCheckbox() {
	var index = (mktdb_selectedtab == 1 ? '' : mktdb_selectedtab);
	var all_state=false;
	var groupElements = document.getElementsByName('selected_id'+index);
	for (var i=0;i<groupElements.length;i++) {
        if(groupElements[i].disabled)
        	var state=true;
        else
        	var state=groupElements[i].checked;
		if (state == false) {
			all_state=false;
			break;
		}
	}
	jQuery('#selectall'+index).attr('checked', all_state);
}

function check_object(sel_id,index,groupParentElementId) {
	$.ajax({
		  url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=Update&mktdbtab="+index+"&selid="+sel_id.checked+"&crmid="+$(sel_id).val(),
		  context: document.body
		}).done(function() {
			$.ajax({
				  url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=AreAllSelected&mktdbtab="+index,
				  context: document.body
				}).done(function(response) {
					var rsp = $.parseJSON(response);
			    	if (rsp.allselected) {
			    		document.getElementById("chooseAllBtn"+index).value = vtmkt_arr.UNSELECTALL;
			    		document.getElementById("selectallrecords"+index).value = 1;
			    	} else {
			    		document.getElementById("chooseAllBtn"+index).value = vtmkt_arr.SELECTALL;
			    		document.getElementById("selectallrecords"+index).value = 0;
			    	}
				});
	      });
	updateGridSelectAllCheckbox();
}

function toggleSelectAllGrid(state,relCheckName,index) {
    var obj = document.getElementsByName(relCheckName);
	if (obj) {
		var chkdvals = '';
        for (var i=0;i<obj.length;i++) {
            if(!obj[i].disabled){
            	obj[i].checked=state;
            	//check_object(obj[i],index);
            	chkdvals = chkdvals + obj[i].value + ',';
            }
        }
        jQuery.ajax({
  		  url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=Update&mktdbtab="+mktdb_selectedtab+"&selid="+(state ? '1' : '0')+"&crmid="+chkdvals.replace(/(^,)|(,$)/g, ''),
		  context: document.body
        });
    }
}
function toggleSelect_ListViewmd(state,relCheckName,groupParentElementId,index) {
	var obj = document.getElementsByName(relCheckName);
	if (obj) {
		for (var i=0;i<obj.length;i++) {
			obj[i].checked=state;
			if(typeof(check_object) == 'function') {
				// This function is defined in ListView.js (check for existence)
				check_object(obj[i],index,groupParentElementId);
			}
		}
	}
	if(document.getElementById('curmodule') != undefined && document.getElementById('curmodule').value == 'Documents' && Document_Folder_View) {
		if(state==true) {
			var count = document.getElementById('numOfRows_'+groupParentElementId).value;
			if(count == '') {
				getNoOfRows(groupParentElementId);
				count = document.getElementById('numOfRows_'+groupParentElementId).value;
			}
			if(parseInt(document.getElementById('maxrecords').value) < parseInt(count)) {
				document.getElementById('linkForSelectAll_'+groupParentElementId).style.display="table-cell";
			}
		} else {
			if(document.getElementById('selectedboxes_'+groupParentElementId).value == 'all') {
				document.getElementById('linkForSelectAll_'+groupParentElementId).style.display="table-cell";
			} else {
				document.getElementById('linkForSelectAll_'+groupParentElementId).style.display="none";
			}
		}
	} 
}

function toggleSelectAllEntries_ListView(index){
	var state = document.getElementById("selectallrecords"+index).value;
	var newstate = 0;
	if (state == 0) {
		//select
		document.getElementById("chooseAllBtn"+index).value = vtmkt_arr.UNSELECTALL;
		document.getElementById("selectallrecords"+index).value = 1;
                toggleSelect_ListViewmd(true, 'selected_id','',index);
		newstate = 1;
	} else {
		//unselect
		document.getElementById("chooseAllBtn"+index).value = vtmkt_arr.SELECTALL;
		document.getElementById("selectallrecords"+index).value = 0;
                toggleSelect_ListViewmd(false, 'selected_id','',index);
		newstate = 0;
	}
    jQuery.ajax({
  	  url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=UpdateAll&mktdbtab="+mktdb_selectedtab+"&selid="+newstate,
  	  context: document.body
      }).done(function() {
    	  switch (mktdb_selectedtab) {
    	    case "1":
    	    	dsMDCampaignResults.read();
    	      break;
    	    case "2":
    	    	dsMDContactResults.read();
      	      break;
    	    case "3":
    	    	dsMDAssignResults.read();
            case "4":
                dsMDCalculationResults.read();
      	      break;
    	  }
      });
}

function showHideStatus(sId,anchorImgId,flag) {
	oObj = eval(document.getElementById(sId));
	if(oObj.style.display == 'block')
	{
		oObj.style.display = 'none';
                $("#"+anchorImgId).removeClass("ui-icon-triangle-1-s");
		$("#"+anchorImgId).addClass("ui-icon-triangle-1-e");
                if(flag==1)
		document.getElementById("show"+sId).value="none";
	}
	else
	{
		oObj.style.display = 'block';
                $("#"+anchorImgId).removeClass("ui-icon-triangle-1-e");
		$("#"+anchorImgId).addClass("ui-icon-triangle-1-s");
                if(flag==1)
		document.getElementById("show"+sId).value="block";
	}
}

function oneSelected(formName) {
    jQuery.ajax({
      url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&mktdbtab="+mktdb_selectedtab+"&exec=OneSelected",
      context: document.body
    }).done(function(response) {
    	var rsp = $.parseJSON(response);
    	if (rsp.oneselected) {
    		var form = document.forms[formName];
    		if (!form) return false;
    		form.submit();
    	} else {
    		$.alert(vtmkt_arr.SELECTONE);
    	}
    });
	return false;
}
function prova(){

 jQuery.ajax({
      url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=selectprova",
      async: false,
      context: document.body
    }).done(function(response) {
       result=response;
    });
return result;
}

function mass_sms(index) {
	var select_options = prova();
        var select_options2 = prova();
alert("selecti"+select_options);
       var campaign_contact=  $("#campaignid").val();
    var campaign_account=  $("#campaignaccid").val();
    var campaign_lead=  $("#campaignleadid").val();
    var campaign=  $("#gencampaignid").val();
    if(campaign_contact!='')
     campaignid=campaign_contact;
        else if( campaign_account !='') 
          campaignid=campaign_account;
           else if( campaign_lead )
               campaignid=campaign_lead;
              else  if(campaign!='')
                  campaignid=campaign;
	var x = select_options.split(';');
        var x2 = select_options2.split(';');
        var y;
        var leads='Leads@@45@@';
        var cont='Contacts@@78@@';
        var acc='Accounts@@9@@';
        var k='';
        for(i=0;i<x.length;i++)
            {
                if(x[i]!='')
                {
                y=x[i].split('_');
                k=k+y[1]+';';
                module=y[0];
                if(module=='l')
                    {
                        leads=leads+k;
                    }
                else if(module=='c')
                    {
                        cont=cont+k;
                    }
                 else if(module=='a')
                    {
                        acc=acc+k;
                    }
                }                    
            }
	var count = x.length;
	
	if(count > 1) {
            var i=0;
            var found=false;
            while(i<x2.length && !found)
            {el=x2[i].split('_');
            if((el[0]=='c' && campaign_contact!='') || (el[0]=='a' && campaign_account !='')  || (el[0]=='l' && campaign_lead !='') || (campaign!=''))
                found=true;
             i++;
            }
            if (found){
            	idstring=leads+'**'+cont+'**'+acc;
                //alert('index.php?module=Emails&action=EmailsAjax&file=EditView&idlist='+idstring+'&mark_dash=mark_dash&campaignid='+campaignid);
                 openPopUp('xComposeEmail',this,'index.php?module=Emails&action=EmailsAjax&file=EditView&idlist='+idstring+'&mark_dash=mark_dash&campaignid='+campaignid,
                'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes');
            } 
            else {
		$.alert('Select at least one contact,account or lead and make sure you have choosen a campaign for each of them\n\
        otherwise select the last campaign');
                return false;
	   }
	} else {
		$.alert(alert_arr.SELECT);
		return false;
	}
	return false;
}
function updateContacts() {
    jQuery.ajax({
        url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=AtLeastOneEntity&mktdbtab="+mktdb_selectedtab,
        data: { entities: "'a','c','l','m'" },
        context: document.body
      }).done(function(response) {
      	var rsp = $.parseJSON(response);
      	if (rsp.oneselected) {
      	  var campaignid=document.getElementById('stcampaignid').value;
					var sequencerId = $( "#stsequencerid" ).val(),
					plannedactionId = $( "#stplannedactionid" ).val(),
					subslistId = $( "#stsubslistid" ).val();
					addtag = $( "#add_tag" ).val();
					removetag = $( "#remove_tag").val();
      	  if(campaignid>0 || sequencerId > 0 || plannedactionId > 0 || subslistId > 0 || addtag != '' || removetag != '') {
      	    $.post('index.php', {
      	    	module:'MarketingDashboard',
      	    	action:'MarketingDashboardAjax',
      	    	file:'updateContacts',
      	    	ajax:'true',
      	    	campid:campaignid,
							sequencer_id: sequencerId,
							plannedaction_id: plannedactionId,
							subslist_id: subslistId,
							add_tag: addtag,
							remove_tag: removetag
      	    },
      	    function(result){
							$("#showsms").css({backgroundColor: '#9FDAEE', border: '1px solid #2BB0D7',width:'80%', 'margin-top': '15px;'});
							$("#showsms").html('<br>'+vtmkt_arr.Linked2Campaign+"<br><br>");
      	    });
      	  } else {
      		$.alert(vtmkt_arr.SELECTCAMPAIGN);
      	  }
      	} else {
      		$.alert(vtmkt_arr.SELECTACCOUNTCONTACT);
      	}
      });
    return false;
}

function change_assign(){
    var user_val=$("#assignto_account").val();
    var related_mod=$("#relatedmodules").val();
    if(user_val==0 && related_mod==null){
       $.alert(vtmkt_arr.CHOOSEPARAM);
       $("#accordion3").accordion('activate', 0);
       return false;
    }
	jQuery.ajax({
        url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=AtLeastOneEntity&mktdbtab="+mktdb_selectedtab,
        data: { entities: "'a'" },
        context: document.body
      }).done(function(response) {
      	var rsp = $.parseJSON(response);
      	if (rsp.oneselected) {
    		var form = document.forms['InvoiceLines3'];
    		if (!form) return false;
    		form.submit();
      	} else {
      		$.alert(vtmkt_arr.SELECTONEACCOUNT);
      	}
      });
    return false;
}

function movefieldsbetweenentities() {
	var from_mod=$("#modulefrom").val();
	if (from_mod==4) 
	  myel="'c'";
	else if(from_mod==6) 
	  myel="'a'";
	jQuery.ajax({
        url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=AtLeastOneEntity&mktdbtab="+mktdb_selectedtab,
        data: { entities: "'a','c'" },
        context: document.body
      }).done(function(response) {
      	var rsp = $.parseJSON(response);
      	if (rsp.oneselected) {
      		jQuery.ajax({
      	        url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=AtLeastOneEntity&mktdbtab="+mktdb_selectedtab,
      	        data: { entities: myel },
      	        context: document.body
      	      }).done(function(response) {
      	      	var rsp = $.parseJSON(response);
      	      	if (rsp.oneselected) {
      	    		var form = document.forms['InvoiceLines3'];
      	    		if (!form) return false;
      	    		form.submit();
      	      	} else {
      	      		$.alert(vtmkt_arr.SELECTONEFROM);
      	      	}
      	      });
      	} else {
      		$.alert(vtmkt_arr.SELECTONE);
      	}
      });
    return false;
}

function findfields(fldname, moduleid, rel) {
	from_mod = $("#modulefrom").val();
	$('#load').show();
	$('#load').html('<img src="modules/MarketingDashboard/styles/images/loader.gif"><br><font color="red">' + vtmkt_arr.PLEASEWAIT + '</font>');
	var fieldname = "#" + fldname + "fields";
	$.post('index.php', {
		module : 'MarketingDashboard',
		action : 'MarketingDashboardAjax',
		file : 'findfields',
		ajax : 'true',
		id : moduleid,
		related : rel,
		origin : from_mod,
		async : false
	}, function(result) {
		var a = JSON.parse(result);
		var fieldarray = a['fields'];
		$(fieldname).empty();
		$.each(fieldarray, function(key, value) {
			$(fieldname).append("<option value='" + key + "'>" + value + "</option>");
			$(fieldname).multiselect('refresh');
		});
		if (rel == 1) {
			var modulearray = a['modules'];
			$("#moduleto").empty();
			$.each(modulearray, function(key, value) {
				$("#moduleto").append("<option value='" + key + "'>" + value + "</option>");
				$("#moduleto").multiselect('refresh');
			});
			var relfldarray = a['relatedfields'];
			$("#moduletofields").empty();
			$.each(relfldarray, function(key, value) {
				$("#moduletofields").append("<option value='" + key + "'>" + value + "</option>");
				$("#moduletofields").multiselect('refresh');
			});
		}
		$('#load').hide();
	});
}

function view_template() {
 var selectedtemplate=$("#emailtemplateid").val();
 if(selectedtemplate=="0" || selectedtemplate=="")
   $.alert(vtmkt_arr.NOACTIONSELECTED);
 else {
   window.open("index.php?module=EmailsTemplates&action=DetailView&record="+selectedtemplate);
 }
}

function message() {
    var selectedtemplate=$("#emailtemplateid").val();
//    var campaignid=  $("#campaignconvert").val();
//    if (campaignid=='' || campaignid==0){
//    	$.alert(vtmkt_arr.SELECTACPC);
//        return false;
//    } else if(selectedtemplate=='' || selectedtemplate==0){
    if(selectedtemplate=='' || selectedtemplate==0){
      $.alert(vtmkt_arr.SELECTTPL);
      $("#accordion").accordion('activate', 0);
      return false;
    } else
      return true;
}

function task() {
	jQuery.ajax({
        url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=AtLeastOneEntity&mktdbtab="+mktdb_selectedtab,
        data: { entities: "'a','c','l','m','p','t'" },
        context: document.body
      }).done(function(response) {
      	var rsp = $.parseJSON(response);
      	if (rsp.oneselected) {
    		var form = document.forms['EditView'];
    		if (!form) return false;
    		form.submit();
      	} else {
      		$.alert(vtmkt_arr.SELECTONENOTASK);
      	}
      });
    return false;
}
function updateInputCalculations(index,fieldvalue,entityid){
    $.ajax({
        url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&exec=UpdateQTY&mktdbtab=" + index + "&qty=" + fieldvalue + "&crmid=" + entityid,
        context: document.body
    }).done(function(res) {
       
    });
}
function oneSelectedMandatory(formName) {
    jQuery.ajax({
      url: "index.php?module=MarketingDashboard&action=MarketingDashboardAjax&file=crudSelected&mktdbtab="+mktdb_selectedtab+"&exec=oneSelectedMandatory",
      context: document.body
    }).done(function(response) {
    	var rsp = $.parseJSON(response);
    	if (rsp.oneselected) {
    		var form = document.forms[formName];
    		if (!form) return false;
    		form.submit();
    	} else {
    		$.alert(vtmkt_arr.SELECTONE);
    	}
    });
	return false;
}
