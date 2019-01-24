	function inputLoading(e) 
	{
		var str  = '<div class="cssload-container" id="input_loading">';
			str += '<div class="cssload-progress cssload-float cssload-shadow">';
			str += '<div class="cssload-progress-item"></div>';
			str += '</div>';
			str += '</div>';
		e.parent().append(str);
	}
	
	function update_location(ob)
	{
		var name = ob.getAttribute("name");
		var name_id = "";
		
		if(name.indexOf("country")!=-1)
		{
			name_id="country_id";
			name = name.replace("country","city");
		}
		else if(name.indexOf("city")!=-1)
		{
			name_id="city_id";
			name = name.replace("city","district");
		}
		else
		{
			name_id="district_id";
			name = name.replace("district","town");
		}

		$.ajax({
			url: site_root_domain+"/?site=supplier&subact=update_location&"+name_id+"="+ob.value, 
			beforeSend: function( xhr ) {
				$('#'+name).html("");
				inputLoading($('#'+name));
			},
			success: function(result){
            	if ( result == 0 )
				{
					// Trường hợp quận/huyện không có phường/xã trực thuộc
					document.getElementById(name).setAttribute("emsg","");
					document.getElementById(name).value = "";
					
					if(name_id == "country_id")
					{
						var name_1 = name.replace("city","district");
						document.getElementById(name_1).setAttribute("emsg","");
						document.getElementById(name_1).value = "";
						
						var name_2 = name.replace("city","town");
						document.getElementById(name_2).setAttribute("emsg","");
						document.getElementById(name_2).value = "";
					}
					
					if(name_id == "city_id")
					{
						var name_1 = name.replace("district","town");
						document.getElementById(name_1).setAttribute("emsg","");
						document.getElementById(name_1).value = "";
					}
				}
				else
				{
					if(document.getElementById(name).disabled == true)
					{
						document.getElementById(name).style.opacity = 1;
						document.getElementById(name).style.filter = "alpha(opacity=100)";
						document.getElementById(name).disabled = false;
						document.getElementById(name).setAttribute("emsg","Bạn phải nhập thông tin mục này");
						//document.getElementById("sympol_"+name).style.display = "inline";
					}
					
					if(name_id == "country_id")
					{
						var name_1 = name.replace("city","district");
						document.getElementById(name_1).style.opacity = 1;
						document.getElementById(name_1).style.filter = "alpha(opacity=100)";
						document.getElementById(name_1).disabled = false;
						document.getElementById(name_1).setAttribute("emsg","Bạn phải nhập thông tin mục này");
						//document.getElementById("sympol_"+name_1).style.display = "inline";
						
						var name_2 = name.replace("city","town");
						document.getElementById(name_2).style.opacity = 1;
						document.getElementById(name_2).style.filter = "alpha(opacity=100)";
						document.getElementById(name_2).disabled = false;
						document.getElementById(name_2).setAttribute("emsg","Bạn phải nhập thông tin mục này");
						//document.getElementById("sympol_"+name_2).style.display = "inline";
					}
					
					if(name_id == "city_id")
					{
						var name_2 = name.replace("city","town");
						document.getElementById(name_2).style.opacity = 1;
						document.getElementById(name_2).style.filter = "alpha(opacity=100)";
						document.getElementById(name_2).disabled = false;
						document.getElementById(name_2).setAttribute("emsg","Bạn phải nhập thông tin mục này");
						//document.getElementById("sympol_"+name_2).style.display = "inline";
					}
					
					$('#input_loading').remove();
					// document.getElementById(name).innerHTML = result;
					
					var ob_select = $('select[name="'+name+'"]');
						ob_select.html(result);
						ob_select.val('').trigger('change');
				}
	        }
	    });
		
		// AjaxRequest.get({
		// 	'url':site_root_domain+"/?site=supplier&subact=update_location&"+name_id+"="+ob.value,
		// 	'onLoading': function(req){ 
		// 		$('#'+name).html("");
		// 		inputLoading($('#'+name));
		// 		},
		// 	'onSuccess': function(req){
		// 		if(req.responseText == 0)
		// 		{
		// 			// Trường hợp quận/huyện không có phường/xã trực thuộc
		// 			document.getElementById(name).setAttribute("emsg","");
		// 			document.getElementById(name).value = "";
					
		// 			if(name_id == "country_id")
		// 			{
		// 				var name_1 = name.replace("city","district");
		// 				document.getElementById(name_1).setAttribute("emsg","");
		// 				document.getElementById(name_1).value = "";
						
		// 				var name_2 = name.replace("city","town");
		// 				document.getElementById(name_2).setAttribute("emsg","");
		// 				document.getElementById(name_2).value = "";
		// 			}
					
		// 			if(name_id == "city_id")
		// 			{
		// 				var name_1 = name.replace("district","town");
		// 				document.getElementById(name_1).setAttribute("emsg","");
		// 				document.getElementById(name_1).value = "";
		// 			}
		// 		}
		// 		else
		// 		{
		// 			if(document.getElementById(name).disabled == true)
		// 			{
		// 				document.getElementById(name).style.opacity = 1;
		// 				document.getElementById(name).style.filter = "alpha(opacity=100)";
		// 				document.getElementById(name).disabled = false;
		// 				document.getElementById(name).setAttribute("emsg","Bạn phải nhập thông tin mục này");
		// 				//document.getElementById("sympol_"+name).style.display = "inline";
		// 			}
					
		// 			if(name_id == "country_id")
		// 			{
		// 				var name_1 = name.replace("city","district");
		// 				document.getElementById(name_1).style.opacity = 1;
		// 				document.getElementById(name_1).style.filter = "alpha(opacity=100)";
		// 				document.getElementById(name_1).disabled = false;
		// 				document.getElementById(name_1).setAttribute("emsg","Bạn phải nhập thông tin mục này");
		// 				//document.getElementById("sympol_"+name_1).style.display = "inline";
						
		// 				var name_2 = name.replace("city","town");
		// 				document.getElementById(name_2).style.opacity = 1;
		// 				document.getElementById(name_2).style.filter = "alpha(opacity=100)";
		// 				document.getElementById(name_2).disabled = false;
		// 				document.getElementById(name_2).setAttribute("emsg","Bạn phải nhập thông tin mục này");
		// 				//document.getElementById("sympol_"+name_2).style.display = "inline";
		// 			}
					
		// 			if(name_id == "city_id")
		// 			{
		// 				var name_2 = name.replace("city","town");
		// 				document.getElementById(name_2).style.opacity = 1;
		// 				document.getElementById(name_2).style.filter = "alpha(opacity=100)";
		// 				document.getElementById(name_2).disabled = false;
		// 				document.getElementById(name_2).setAttribute("emsg","Bạn phải nhập thông tin mục này");
		// 				//document.getElementById("sympol_"+name_2).style.display = "inline";
		// 			}
					
					
		// 			$('#input_loading').remove();
					
		// 			document.getElementById(name).innerHTML = req.responseText;
					
					
		// 		}
		// 	}	
		// })
	}
