
	var comment_moduleid = 0;
	var comment_modulename = "";

	var comment_array = new Array();
	var comment_record = new Array();
	var comment_cnt = 0;
	var comment_nowpage = 0; //
	
	function comment_create_page( maxpage )
	{
		page_html = "";
		
		page_html += "<ul>";
		
		page_html += comment_nowpage - 3 > 0 ? "<li><a onclick='comment_load( \""+comment_modulename+"\", "+comment_moduleid+", 0 )'>&laquo;</a></li>" : "";
		
		for ( var i = 1; i <= maxpage; i++ )
		{
			if ( comment_nowpage == i )
			{
				page_html += "<li><font>"+i+"</font></li>";
			}
			else if ( comment_nowpage - 2 > i || comment_nowpage + 3 < i )
			{
				page_html += "";
			}
			else
			{
				page_html += "<li><a onclick='comment_load( \""+comment_modulename+"\", "+comment_moduleid+", "+i+" )'>"+i+"</a></li>";
			}
		}
		
		page_html += comment_nowpage + 4 < i ? "<li><a onclick='comment_load( \""+comment_modulename+"\", "+comment_moduleid+", "+maxpage+" )'>&raquo;</a></li>" : "";
		
		page_html += "</ul>";
		
		return page_html;
	}

	function comment_show_time( row_id )
	{
		document.getElementById("comment_time_"+row_id).style.visibility = "visible";
		document.getElementById("comment_action_"+row_id).style.visibility = "visible";
	}
	
	function comment_hide_time( row_id )
	{
		document.getElementById("comment_time_"+row_id).style.visibility = "visible";
		document.getElementById("comment_action_"+row_id).style.visibility = "hidden";
	}

	function print_comment_row()
	{
		var i = comment_cnt;

		comment_record = comment_array[i].split("|");

		if ( comment_array.length-1 <= i )
		{
			document.getElementById("module_comment_page").innerHTML = comment_create_page(comment_record[0]);
			
			document.getElementById("module_comment_loading").innerHTML = "";
			return false;
		}

		var row = document.createElement("ul");
		
		if ( i % 2 == 0 )
		{
			row.setAttribute("style", "background: #F6F6F6';");
		}
		else
		{
			row.setAttribute("style", "background: #FFFFFF';");
		}
		
		row.setAttribute("onmouseover", "comment_show_time("+comment_record[0]+");");
		row.setAttribute("onmouseout", "comment_hide_time("+comment_record[0]+");");
							
		//var cell1 = document.createElement("li");
		//cell1.setAttribute("class", "comment_id");
		//cell1.innerHTML = "#" + comment_record[0];
		//row.appendChild(cell1);
		
		var cell2 = document.createElement("li");
		cell2.setAttribute("class", "comment_name");
		cell2.innerHTML = comment_record[1];
		cell2.innerHTML = "<div class='comment_action' id='comment_action_"+comment_record[0]+"'>" + permission("comment_edit", "<a style='cursor: pointer;' onclick='comment_edit("+comment_record[0]+");'><img src='"+site_img_url+"/icon_edit.png'></a>") + permission("comment_delete", " &nbsp; <a style='cursor: pointer;' onclick='comment_delete("+comment_record[0]+");'><img src='"+site_img_url+"/icon_delete.gif'></a>") + "</div>" + cell2.innerHTML;
		row.appendChild(cell2);
		
		var cell4 = document.createElement("li");
		cell4.setAttribute("class", "comment_content");
		cell4.innerHTML = comment_record[3];
		row.appendChild(cell4);

		var cell3 = document.createElement("li");
		cell3.setAttribute("class", "comment_time");
		cell3.setAttribute("id", "comment_time_"+comment_record[0]);
		cell3.innerHTML = comment_record[2];
		row.appendChild(cell3);

		document.getElementById("module_comment_ul").appendChild(row);

		comment_cnt++;
		
		setTimeout("print_comment_row()", 200);
	}
	
	function print_comment( data )
    {
		comment_array = data.split("||");
		comment_record = new Array();
		comment_cnt = 0;
       
	    var comment_html = "";
		comment_html += "<div id='module_comment_ul'>";
		comment_html += "</div><div id='module_comment_loading'>"+lang_loading+"</div>";
    
    	document.getElementById("module_comment").innerHTML = comment_html;

		if ( comment_array.length == 1 )
		{
			//document.getElementById("module_comment_loading").innerHTML = "<b><font color='red'>" + lang_no_result + "</font></b>";
			document.getElementById("module_comment_loading").innerHTML = "";
		}
		else
		{
			print_comment_row();
		}
    }

	function comment_load( module_name, module_id, page )
    {
		comment_moduleid = module_id;
		comment_modulename = module_name;
		page = page ? page : 1;
		comment_nowpage = page;
		
    	if ( module_name && module_id )
        {
            AjaxRequest.get(
            {
                'url': site_root_domain + "/?site=comment&act=show&is_ajax=1&module_name="+module_name+"&module_id="+module_id+"&page="+page
                ,'onLoading':function(req){ document.getElementById("module_comment").innerHTML = lang_loading; }
                ,'onSuccess':function(req){ print_comment(req.responseText);  }
            }
            );
        }
        else
        {
        	document.getElementById("module_comment").innerHTML = "";
        }
    }

	function comment_insert(theform)
	{
		var status = AjaxRequest.submit(
		theform
			,{
				'onLoading' : function(req){ document.getElementById("module_comment").innerHTML = lang_adding; },
			  'onSuccess':function(req){ comment_load( comment_modulename, comment_moduleid ); }
			}
	  	);
		
		theform.comment_content.value = "";
	}
	
	function comment_delete( comment_id )
	{
		if ( confirm( lang_alert_delete ) == true )
		{
			AjaxRequest.get(
            {
                'url': site_root_domain + "/?site=comment&act=delete&id=" + comment_id + "&page=" + comment_nowpage
                ,'onLoading':function(req){ }
                ,'onSuccess':function(req){ comment_load(comment_modulename, comment_moduleid, comment_nowpage); }
            }
            );
		}
		else
		{
			return false;
		}
	}
	
	function comment_edit( comment_id )
	{
		window.location.href = site_root_domain + "/?site=comment&act=edit&id="+ comment_id;
		return false;
	}
	