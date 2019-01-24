	
	var user_array = new Array();
	var user_record = new Array();
	var user_cnt = 0;
	var user_field = "";
	var user_remove_if_no_data = 0;
	
	
	
	function set_user( value, value2 )
	{
		user_field.value = value;
	}
	
	function print_user_row()
	{
		var i = user_cnt;
		
		if ( user_array.length-1 <= i )
		{
			document.getElementById("user_list_loading").innerHTML = "";
			return false;
		}
		
		user_record = user_array[i].split("|");
		
		var row = document.createElement("tr");
		var cell1 = document.createElement("td");
		cell1.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
		cell1.setAttribute("width", "15%");
		cell1.innerHTML = "<input type='radio' id='user_name' name='user_name' value='"+user_record[1]+"' onclick='set_user(this.value);'> #" + user_record[0];

		row.appendChild(cell1);
		
		var cell2 = document.createElement("td");
		cell2.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
		cell2.setAttribute("width", "25%");
		cell2.innerHTML = pms['user_read'] == 1 ? "<a href='"+site_root_domain+"/?site=user&act=show&id="+user_record[0]+"'><b>"+user_record[1]+"</b></a>" : "<b>" + user_record[1] + "</b>";
		row.appendChild(cell2);
		
		var cell3 = document.createElement("td");
		cell3.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
		cell3.setAttribute("width", "25%");
		cell3.innerHTML = user_record[3];
		row.appendChild(cell3);
		
		var cell4 = document.createElement("td");
		cell4.setAttribute("style", (i%2==0?"background: #F3F3F3;":"background: #FFFFFF;"));
		cell4.setAttribute("width", "25%");
		cell4.innerHTML = user_record[2];
		row.appendChild(cell4);

		if ( ! document.getElementById("user_list_tr") )
		{
			return false;	
		}
		
		document.getElementById("user_list_tr").appendChild(row);

		user_cnt++;
		
		setTimeout("print_user_row()", 100);
	}
	
	function print_user( data )
    {
		user_array = data.split("||");
		user_record = new Array();
		user_cnt = 0;
       
	    var user_html = "";
		user_html += "<table width='100%' id ='customer_list_table' cellspacing='0' cellpadding='2' style='margin-top: 10px;'><tbody id='user_list_tr'>";
		user_html += "</tbody></table><div id='user_list_loading'>"+lang_loading+"</div>";
    
    	document.getElementById("user_list").innerHTML = user_html;

		if ( user_array.length == 1 )
		{
			if ( user_remove_if_no_data == 1 )
			{
				user_field.value = "";	
			}
			
			document.getElementById("user_list_loading").innerHTML = "<b><font color='red'>" + lang_no_result + "</font></b>";
		}
		else
		{
			print_user_row();
		}
    }

	function user_search( userdata, remove_if_no_data )
    {
		user_field = userdata;
		user_remove_if_no_data = remove_if_no_data;

		user_field.value = trim(user_field.value);
		
    	if ( user_field.value )
        {
			var url = site_root_domain + "/?site=user&act=search&is_ajax=1&user_id="+user_field.value;

           AjaxRequest.get(
            {
                'url':''+url+''
                ,'onLoading':function(req){ document.getElementById("user_list").innerHTML = lang_loading; }
                ,'onSuccess':function(req){ print_user(req.responseText);  }
            }
            );
        }
        else
        {
        	document.getElementById("user_list").innerHTML = "";
        }
    }
	
	var data_backup = "";
	
	function group_change()
	{
        swal({
            title: cms_lang.confirm,
            type: 'info',
            html: `
            <div>${cms_lang.is_change_group}</div>
            <div class="row">
            	<div class="col-md-6 col-sm-12">
            		<label><input type="checkbox" id="approve_permission_cbx"> ${cms_lang.user_permission}</label>
				</div>
            	<div class="col-md-6 col-sm-12">
            		<label><input type="checkbox" id="approve_commission_cbx"> ${cms_lang.gcommission}</label>
				</div>
			</div>
            `,
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: 'OK',
            cancelButtonText: cms_lang.cancel
        }).then((result) => {
            if($('#approve_permission_cbx').is(':checked'))
			{
                group_change_add();
			}

			if($('#approve_commission_cbx').is(':checked'))
			{
				approveCommissionByGroup();
			}
        }).catch(swal.noop);
	}
	function group_change_add()
	{

			blockLoading($("#tab_permission").parents(".tab-content:first"));
			var gr_id = document.getElementById("userg_id").options[document.getElementById("userg_id").selectedIndex].value;
			
			 $.ajax({
								type:"POST",
								url: site_root_domain+"/?site=user&act=permission_ajax",
								data: "id="+gr_id,
								success: function(data) 
								{
									$("#update_permission").html(data);
                                    unblockLoading($("#tab_permission").parents(".tab-content:first"));
								},
				 				error: function(err) {
                                    unblockLoading($("#tab_permission").parents(".tab-content:first"));
                                    console.log("Error");
								},
							});
		
	}
	
	
	var on_mouse = 0;
			
	function checkbox( object, id )
	{
		if ( on_mouse == 0 )
		{
			if ( document.getElementById( id ).checked == true )
			{
				object.style.background = "#dddddd";
				document.getElementById( id ).checked = "";
			}
			else
			{
				object.style.background = "#ffee66";
				document.getElementById( id ).checked = "checked";
			}
		}
	}
	
	
	// Function search viewer for task
	
	var tracker_array = new Array();
	
	function set_tracker( value)
	{
		var trackerlist = new Array();
		trackerlist = user_field.value.split(',');
		for (i=0; i< trackerlist.length; i++)
		{
			trackerlist[i] = trim(trackerlist[i]);
		}
		trackerlist.pop();
		trackerlist.push(value);
		user_field.value = trackerlist.join(', ') + ", ";
	}
	
	function tracker_search( userdata, remove_if_no_data )
    {
		user_field = userdata;
		user_remove_if_no_data = remove_if_no_data;
		//user_field.value = trim(user_field.value);
		tracker_array = user_field.value.split(',');	
		var keyword = trim(tracker_array[tracker_array.length-1]);
		
    	if ( keyword )
        {
			var url = site_root_domain + "/?site=user&act=search&is_ajax=1&user_id="+keyword;

           AjaxRequest.get(
            {
                'url':''+url+''
                ,'onLoading':function(req){ document.getElementById("user_list").innerHTML = lang_loading; }
                ,'onSuccess':function(req){ print_tracker(req.responseText);  }
            }
            );
        }
        else
        {
        	document.getElementById("user_list").innerHTML = "";
        }
    }
	
	function print_tracker( data )
    {
		user_array = data.split("||");
		user_record = new Array();
		user_cnt = 0;
       
	    var user_html = "";
		user_html += "<table width='100%' cellspacing='0' cellpadding='2' style='margin-top: 10px;'><tbody id='user_list_tr'>";
		user_html += "</tbody></table><div id='user_list_loading'>"+lang_loading+"</div>";
    
    	document.getElementById("user_list").innerHTML = user_html;

		if ( user_array.length == 1 )
		{	
			if ( user_remove_if_no_data == 1 )
			{
				var trackerlist = new Array();
				trackerlist = user_field.value.split(',');
				for (i=0; i< trackerlist.length; i++)
				{
					trackerlist[i] = trim(trackerlist[i]);
				}
				trackerlist.pop();
				user_field.value = trackerlist.join(', ') + ", ";
			}
			document.getElementById("user_list_loading").innerHTML = "<b><font color='red'>" + lang_no_result + "</font></b>";
		}
		else
		{
			print_tracker_row();
		}
    }
	
	function print_tracker_row()
	{
		var i = user_cnt;
		
		if ( user_array.length-1 <= i )
		{
			document.getElementById("user_list_loading").innerHTML = "";
			return false;
		}
		
		user_record = user_array[i].split("|");
		
		var row = document.createElement("tr");
		
		if ( i % 2 == 0 )
		{
			row.setAttribute("style", "background: #F6F6F6';");
		}
		else
		{
			row.setAttribute("style", "background: #FFFFFF';");
		}
							
		var cell1 = document.createElement("td");
		cell1.setAttribute("width", "15%");

		cell1.innerHTML = " <input type='button' id='user_name' name='user_name' value = ' + ' title='"+user_record[1]+"' onclick='set_tracker(this.title);'>";

		row.appendChild(cell1);
		
		var cell2 = document.createElement("td");
		cell2.setAttribute("width", "25%");
		cell2.innerHTML = pms['user_read'] == 1 ? "<a href='"+site_root_domain+"/?site=user&act=show&id="+user_record[0]+"'><b>"+user_record[1]+"</b></a>" : "<b>" + user_record[1] + "</b>";
		row.appendChild(cell2);
		
		var cell3 = document.createElement("td");
		cell3.setAttribute("width", "25%");
		cell3.innerHTML = user_record[3];
		row.appendChild(cell3);
		
		var cell4 = document.createElement("td");
		cell4.setAttribute("width", "25%");
		cell4.innerHTML = user_record[2];
		row.appendChild(cell4);

		if ( ! document.getElementById("user_list_tr") )
		{
			return false;	
		}
		
		document.getElementById("user_list_tr").appendChild(row);

		user_cnt++;
		
		setTimeout("print_tracker_row()", 100);
	}

	function approveCommissionByGroup()
	{
		blockLoading($("#tab_permission").parents(".tab-content:first"));

		//Clear
        $("commission_id").val(0);
        $("[id^='product_commission_dropdown_']").find(".dropdown-item[value='0']").trigger('click');

        var gr_id = document.getElementById("userg_id").options[document.getElementById("userg_id").selectedIndex].value;
		$.ajax({
			url: site_root_domain + '/?site=user_group&subact=load_commission&id='+gr_id,
			dataType: 'json',
			success: function(res){
				$.each(res, function(k,v){
                    $("[commission_id='" + v.product_id + "'").val(v.product_commission_value);
                    $("#product_commission_dropdown_" + v.product_id).find(".dropdown-item[value='"+v.product_commission_type+"']").trigger('click');
                });
                unblockLoading($("#tab_permission").parents(".tab-content:first"));
			},
			error: function(err) {
                unblockLoading($("#tab_permission").parents(".tab-content:first"));
                console.log("Error!");
			}
		})
	}