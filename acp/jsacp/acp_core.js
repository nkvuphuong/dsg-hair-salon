	//-------------------------------------
	//	Admin
	//-------------------------------------

function scalate_permission( is_root, is_admin, user_id, group_id )
{
	if ( site_is_root != 1 )
	{
		if ( is_root && is_root != site_is_root )
		{
			return false;
		}
		
		if ( is_root == 0 && is_admin == 0 && site_is_admin == 1 )
		{
			return true;
		}
		else
		{
			if ( group_id && group_id == site_group_id && site_is_leader == 1 )
			{
				if ( user_id != site_user_id )
				{
					return true;	
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
	
	return true;
}

function permission_btn( action, module_name, url, is_root, is_admin, user_id, group_id )
{
	if ( is_root && is_admin )
	{		
		if ( scalate_permission(is_root, is_admin, user_id, group_id) == false )
		{
			return false;	
		}
	}
	
	if ( pms[module_name+'_'+action] == 1 )
	{
		if ( action == "edit" )
		{
			if ( url.match("javascript") )
			{
				document.writeln('<a href="'+url+'" class="edit"><i class="fa fa-edit"></i></a>');
			}
			else
			{
				document.writeln('<a href="'+url+'&page='+site_page+'" class="edit"><i class="fa fa-edit"></i></a>');
			}
		}
		else if ( action == "delete" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');" class="edit"><i class="fa fa-trash-o"></i></a>');
		}
		else if ( action == "suspend" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_suspend.png"></a>');
		}
		else if ( action == "unsuspend" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_unsuspend.png"></a>');
		}
		else if ( action == "postpone" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_stop.png"></a>');
		}
		else if ( action == "continue" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_start.png"></a>');
		}
		else if ( action == "renew" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_start.png"></a>');
		}
		else if ( action == "empty" )
		{
			document.writeln('<a onclick="return alert_empty(\''+url+'\');"><img width="20" height="20" align="absmiddle" src="'+site_root_domain+'/images/icon_empty.png"></a>');
		}
	}
}

// Backup permission_btn
function permission_btn_bk( action, module_name, url, is_root, is_admin, user_id, group_id )
{
	if ( is_root && is_admin )
	{		
		if ( scalate_permission(is_root, is_admin, user_id, group_id) == false )
		{
			return false;	
		}
	}
	
	if ( pms[module_name+'_'+action] == 1 )
	{
		if ( action == "edit" )
		{
			if ( url.match("javascript") )
			{
				document.writeln('<a href="'+url+'"><img align="absmiddle" src="'+site_root_domain+'/images/icon_edit.png"></a>');
			}
			else
			{
				document.writeln('<a href="'+url+'&page='+site_page+'"><img align="absmiddle" src="'+site_root_domain+'/images/icon_edit.png"></a>');
			}
		}
		else if ( action == "delete" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icon_delete.png"></a>');
		}
		else if ( action == "suspend" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_suspend.png"></a>');
		}
		else if ( action == "unsuspend" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_unsuspend.png"></a>');
		}
		else if ( action == "postpone" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_stop.png"></a>');
		}
		else if ( action == "continue" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_start.png"></a>');
		}
		else if ( action == "renew" )
		{
			document.writeln('<a onclick="return alert_delete(\''+url+'&page='+site_page+'\');"><img align="absmiddle" src="'+site_root_domain+'/images/icons/icon_start.png"></a>');
		}
		else if ( action == "empty" )
		{
			document.writeln('<a onclick="return alert_empty(\''+url+'\');"><img width="20" height="20" align="absmiddle" src="'+site_root_domain+'/images/icon_empty.png"></a>');
		}
	}
}

function permission( module_name, text, is_root, is_admin, user_id, group_id )
{
	if ( is_root && is_admin )
	{
		if ( scalate_permission(is_root, is_admin, user_id, group_id ) == false )
		{
			return false;	
		}
	}
	
	if ( parseInt(pms[module_name]) == 1 )
	{
		return text;
	}
	else
	{
		return "";	
	}
}

function permission_text( module_name, text, is_root, is_admin, user_id, group_id )
{
	if ( is_root && is_admin )
	{
		if ( scalate_permission(is_root, is_admin, user_id, group_id ) == false )
		{
			return false;	
		}
	}

	if ( pms[module_name] == 1 )
	{
		document.writeln(text);
	}
}

	function getScrollXY() {
	  var scrOfX = 0, scrOfY = 0;
	  if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		scrOfY = window.pageYOffset;
		scrOfX = window.pageXOffset;
	  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		//DOM compliant
		scrOfY = document.body.scrollTop;
		scrOfX = document.body.scrollLeft;
	  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		//IE6 standards compliant mode
		scrOfY = document.documentElement.scrollTop;
		scrOfX = document.documentElement.scrollLeft;
	  }
	  return [ scrOfX, scrOfY ];
	}
	
	function getInnerSize() {
	  var myWidth = 0, myHeight = 0;
	  if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	  }
	  return [ myWidth,  myHeight ];
	}
	
	function GetCenteredXY(w, h) {
		var ps = getScrollXY();
		var sz = getInnerSize();
		var Left = (sz[0] - w) / 2 + ps[0];
		var Top = (sz[1] - h) / 2 + ps[1];
		return [ Math.ceil(Left), Math.ceil(Top) ];
	}

	function csrf_token()
	{
        $.ajax({
            type: "post",
            url: "/acp/?subact=create_token",
            success: function(token)
            {
                $("form").each(function(){
                	if($(this).find("[name='token']:hidden").length)
					{
                        $(this).find("[name='token']:hidden").val(token);
					}
					else
					{
                        $(this).prepend("<input type='hidden' name='token' value='"+token+"' />");
					}
                });
            }
        });
    }

    function toggleCheckboxAll(checkAllObj, itemsObj, callback = null)
	{
		if($(checkAllObj).is(':checked'))
		{
			$(itemsObj).prop('checked', true);
		}
		else
		{
            $(itemsObj).prop('checked', false);
		}

		if(callback !== null) callback();
	}

	function toggleDisabledInput(obj, parentSelectorName)
	{
		let row = obj.parents(parentSelectorName + ':first');

		if(obj.is(':checked'))
		{
            row.find("input, select, button").prop("disabled", false);
		}
		else
		{
            row.find("input, select, button").prop("disabled", true);
		}

        obj.prop("disabled", false);
	}

	function customCommissionItem(obj)
	{
		let row = obj.parents('tr:first');

		let commission_type_obj = row.find('[name="product_commission_type[]"]:first');
        let commission_value_obj = row.find('[name="product_commission_value[]"]:first');
        let commission_show_obj = row.find('.commission_show:first');
		let commission_type = commission_type_obj.val()*1;
		let commission_value = commission_value_obj.val()*1;

        swal({
            title: cms_lang.gcommission,
            html:
            `
			<div class="row">
				<div class="col-xs-1 col-sm-2 col-lg-3">&nbsp;</div>
				<div class="col-xs-10 col-sm-8 col-lg-6">
					<div class="input-group">
					  <input id="custom_commission_value" type="number" class="form-control" aria-label="Text input with dropdown button">
					  <div id="custom_commission_dropdown" class="input-group-btn">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%</button>
						<div class="dropdown-menu dropdown-menu-right">
						  <a class="dropdown-item" value="0">%</a>
						  <a class="dropdown-item" value="1">$</a>
						</div>
					  </div>
					</div>
				  </div>
				  <div class="col-xs-1 col-sm-2 col-lg-3">&nbsp;</div>
            </div>
            `,
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: 'OK',
            cancelButtonText: cms_lang.cancel,
            onOpen: () => {
                dropdownInput($('#custom_commission_dropdown'), commission_type_obj);
                $('#custom_commission_dropdown').find(".dropdown-item[value="+commission_type+"]").trigger('click');
                $('#custom_commission_value').val(commission_value);
            }
        }).then((result) => {
            commission_value_obj.val($('#custom_commission_value').val());
            showCommissionItem(commission_type_obj.val(), commission_value_obj.val(), commission_show_obj);
            calculate_total_transaction();
        }).catch(swal.noop)
	}

	function showCommissionItem(commmission_type, commission_value,commission_show_obj)
    {
    	if(commmission_type == 1) //so tien
		{
            commission_show_obj.html(formatNumberInput(commission_value*1));
		}
        else
		{
            commission_show_obj.html(toFixedNumber(commission_value*1)+'%');
		}
    }