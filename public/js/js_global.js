var site_var_cookieid = "";
var site_var_cookie_domain = "";
var site_var_cookie_path   = "/";
var data_request_sent = 0;
var data_request_received = 0;

var on_mouse = 1;

function form_checkall(name)
{
	if ( on_mouse == 0 )
	{
		for ( var i = 0; i < document.forms[name].elements.length; i++)
		{
			if ( document.forms[name].all.checked == true )
			{
				document.forms[name].elements[i].checked = true;
			}
			else
			{
				document.forms[name].elements[i].checked = false;
			}
		}
	}
	else
	{
		if ( document.forms[name].all.checked == false )
		{
			document.forms[name].all.checked = "checked";
		}
		else
		{
			document.forms[name].all.checked = false;
		}

		for ( var i = 0; i < document.forms[name].elements.length; i++)
		{
			if ( document.forms[name].all.checked == true )
			{
				document.forms[name].elements[i].checked = true;
			}
			else
			{
				document.forms[name].elements[i].checked = false;
			}
		}
	}
}

function page_loading_html( text )
{
	text = "<DIV style='width: 100%; padding-top: 7px; padding-bottom: 7px;' align='center'> &nbsp; " + text + " </div>";
	
	return text;
}

function page_loading(type)
{
	if ( type == 1 )
	{
		document.getElementById( this.clicked ).innerHTML = page_loading_html("<img align='absmiddle' src='" + site_root_domain + "/templates/images/warning.png'> <font size='2' face='verdana' color='red'>K&#7871;t n&#7889;i b&#7883; l&#7895;i....</font>");
	}
	else if ( type == 2 )
	{
		document.getElementById( this.clicked ).innerHTML = "";
	}
	else
	{
		document.getElementById( this.clicked ).innerHTML = page_loading_html("<font size='2' face='verdana'><img align='absmiddle' src='" + site_root_domain + "/templates/images/loading_layer.gif'>");
	}
}

function data_request(request, url, type, form_id, no_status,type_scroll)
{
	this.clicked = request;

		// $('html, body').animate({
		// 		 scrollTop: $("#"+request).offset().top
		// 	 }, 700);
	
	data_request_sent++;
	update_status(0);
	
	this.no_status = no_status;
	
	if ( no_status == 1 )
	{
		no_status = 1;
	}
	else
	{
		no_status = 0;
	}
	
	if ( ! type )
	{
		type = "GET";
	}
	else
	{
		type = "POST";
	}
	
	var fields_data = "";
	var lhl_data_got = "";

	if ( form_id )
	{
		if ( type == "GET" )
		{
			var fields_data = "?";
		}
		else if ( type == "POST" )
		{
			var fields_data = "";
		}

		var frm = document.forms[form_id];
		var numberElements = frm.elements.length;
				
		for( var i = 0; i < numberElements; i++ )
		{
			if( i < numberElements-1 )
			{
				if ( frm.elements[i].type == "select-one" )
				{
					fields_data += frm.elements[i].name+"="+frm.elements[i].value+"&";
				}
				else if ( frm.elements[i].type == "select-multiple" )
				{
					art_name = frm.elements[i].name;
					
					selmul = frm.elements[i];
	
					var seldt = new Array();
					var selcnt = 0;
					
					for ( var i = 0; i < selmul.length; i++ )
					{
						if ( selmul.options[i].selected == true )
						{
							seldt[selcnt] = selmul.options[i].value;
							selcnt++;
						}
					}
										
					fields_data += art_name+"="+seldt+"&";
				}
				else if ( ( frm.elements[i].type == "radio" ) || ( frm.elements[i].type == "checkbox" ))
				{
					if (frm.elements[i].checked)
					{
						fields_data += frm.elements[i].name+"="+frm.elements[i].value+"&";
					}
				}
				else
				{
					fields_data += frm.elements[i].name+"="+frm.elements[i].value+"&";
				}
			}
			else
			{
				if (frm.elements[i].type == "radio")
				{					
					if (frm.elements[i].checked)
					{
						fields_data += frm.elements[i].name+"="+frm.elements[i].value;
					}
				}
				else
				{
					fields_data += frm.elements[i].name+"="+frm.elements[i].value;
				}
			}
		}
		
	}

	http_request = false;

	//this.lhl_id_packet_send_to = request;
	
	if ( request )
	{
		if ( no_status != 1 )
		{
			page_loading();
		}
	}
	
	if (window.XMLHttpRequest) // Mozilla, Safari,...
	{
		http_request = new XMLHttpRequest();
		
		if (http_request.overrideMimeType)
		{
			http_request.overrideMimeType('text/xml');
		}
	
	}
	else if (window.ActiveXObject) // IE
	{
		try
		{
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
			
			}
		}
	}

	if ( ! http_request )
	{
		if ( request )
		{
			page_loading(1);
		}

		return false;
	}
		
	http_request.onreadystatechange = function(){if (http_request.readyState == 4 )
	{
		if (http_request.status == 200 )
		{
			data_request_received++;
			update_status(1);
				
			if ( request )
			{
				if ( no_status != 1 )
				{
					page_loading(2);
				}
			
				data_request_time = 1;

				document.getElementById( request ).innerHTML = http_request.responseText;
			}
		}
		else
		{
			if ( request )
			{
				if ( no_status != 1 )
				{
					page_loading(1);
				}
			}
		}
	}};

	if ( type == "POST" )
	{
		http_request.open('POST', url, true);
		http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
		http_request.setRequestHeader("Content-length", fields_data.length);
		http_request.setRequestHeader("Connection", "close");
		http_request.send(fields_data);
	}
	else if ( type == "GET" )
	{
		http_request.open('GET', url + fields_data, true);
		http_request.send(null);
	}
	
}

function update_status(type)
{
	
	if ( document.getElementById( 'SITE_request_sent' ) )
	{
		if ( type == 1 )
		{
			status_received = data_request_received;
	
			document.getElementById( 'SITE_request_received' ).innerHTML = status_received;
		}
		else
		{
			status_sent = data_request_sent;
	
			document.getElementById( 'SITE_request_sent' ).innerHTML = status_sent;
		}
	}
}

var IE = document.all?true:false
	
if ( ! IE )
{
	//document.captureEvents( Event.KEYPRESS );
}

document.onmousemove = getMouseXY;

function ietruebody()
{
	return (document.compatMode && document.compatMode != "BackCompat") ? document.documentElement : document.body;
}

var tempX = 0;
var tempY = 0;

function getMouseXY(e)
{
	if ( IE )
	{
		tempX = event.clientX + ietruebody().scrollLeft;
		tempY = event.clientY + ietruebody().scrollTop;
	}
	else
	{
	    tempX = e.pageX;
	    tempY = e.pageY;
	}
	
	if ( tempX < 0 )
	{
		tempX = 0
	}
	
	if ( tempY < 0 )
	{
		tempY = 0
	}
	
	return true;
}

var current_field = "";
var current_opacity = 0;

function close_warning()
{
	if ( ! current_field )
	{
		return false;	
	}

	var object = document.getElementById(current_field);

	if ( document.getElementById(object.id+"_head_alert") )
	{
		//var d = object.offsetParent;
		var d = object.parentNode;
		
		if ( d ) // Set this condition to fix the error appeared when the user set the object as invisible
		{
			var olddiv = document.getElementById(object.id+"_head_alert");
			d.removeChild(olddiv);
		}
	}
	else
	{
		//var d = object.offsetParent;
		var d = object.offsetParent;
		
		if ( d ) // Set this condition to fix the error appeared when the user set the object as invisible
		{
			var olddiv = document.getElementById(object.id+"_alert");
			d.removeChild(olddiv);
		}
	}
			
	current_field = "";
	current_opacity = 0;
}
 function check_element( object )
{
	if ( ! object )
	{
		return false;
	}

	if ( object.type == "select-one" )
	{
		var option_value = object.options[object.selectedIndex].value;
		option_value = option_value.toLowerCase();
		
		if ( option_value == "" || option_value == "none" )
		{
			return false;
		}
	}
	else if ( object.type == "textarea" )
	{
		if(object.value == "")
		{
			return false;
		}else
		{
			return true;
		}
	}
	else if ( ! object.value )
	{
		return false;	
	}

	return true;
}

function open_warning( element_id, display_type )
{
	if ( ! document.getElementById(element_id) )
	{
		return true;	
	}
	
	var object = document.getElementById(element_id);

	close_warning();
	
	if ( ! object )
	{
		return true;
	}

	if ( (check_element(object) == false && object.getAttribute('emsg')) || (object.value && object.getAttribute('etype')) )
	{
		// Setup message
		if( check_element(object) == false && object.getAttribute('emsg') )
		{
			var emsg = object.getAttribute('emsg');
		}
		else if ( object.getAttribute('etype') == "email" && check_email(object.value) == false )
		{
			var emsg = lang_invalid_email;
		}
		else if ( object.getAttribute('etype') == "phone" && check_phone(object.value) == false )
		{
			var emsg = lang_invalid_phone;
		}else if ( object.getAttribute('etype') == "fax" && check_phone(object.value) == false )
		{
			var emsg = lang_invalid_fax;
		}
		else if ( object.getAttribute('etype') == "number" && check_number(object.value) == false )
		{
			var emsg = lang_invalid_number;
		}
		else if ( object.getAttribute('etype') == "date" && check_date(object.value) == false )
		{
			var emsg = lang_invalid_date;
		}
		else if ( object.getAttribute('etype') == "ip" && check_ip_address(object.value) == false )
		{
			var emsg = lang_invalid_ip_address;
		}
		else if ( object.getAttribute('etype') == "domain" && check_domain(object.value) != true )
		{
			var emsg = check_domain(object.value);
		}
		else if ( object.getAttribute('etype') == "subdomain" && check_subdomain(object.value) != true )
		{
			var emsg = lang_invalid_subdomain;
		}
		else if ( object.getAttribute('etype') == "username" && check_username(object.value) != true )
		{
			var emsg = lang_invalid_username;
		}
		else if ( object.getAttribute('etype') == "http" && check_http(object.value) != true )
		{
			var emsg = lang_invalid_http;
		}
		else if ( object.getAttribute('maxvalue') && parseInt(object.value) > parseInt(object.getAttribute('maxvalue')) )
		{
			var emsg = lang_invalid_maxvalue.replace("%number%", number_format(parseInt(object.getAttribute('maxvalue'))));
		}
		else if ( object.getAttribute('minvalue') && parseInt(object.value) < parseInt(object.getAttribute('minvalue')) )
		{
			var emsg = lang_invalid_minvalue.replace("%number%", number_format(parseInt(object.getAttribute('minvalue'))));
		}
		else
		{
			return true;
		}

		// Get the co-ordinate of element
		var x = object.offsetLeft;
		var y = 0;
		
		// Check existing the element
		if ( ! document.getElementById(object.id+"_alert") )
		{
			/*
			// <ul><li>
			if ( object.offsetParent.innerHTML.length > 1000 )
			{
				x = object.offsetLeft + object.clientWidth;
				y = object.offsetTop - 4;
				
				object.parentNode.innerHTML = "<div id='"+object.id+"_alert"+"' style='position: absolute; height: 0px; padding-left: "+x+"px; padding-top: "+y+"px;'></div>" + object.parentNode.innerHTML;
			}
			// <table><tr><td>
			else
			{*/
				x = x == 0 ? object.clientWidth : x;
				
				object.parentNode.innerHTML = "<div id='"+object.id+"_head_alert"+"' style='position: absolute; padding-left: "+x+"px; width: 10px;'><div id='"+object.id+"_alert"+"' style='position: absoulute;'></div></div>" + object.parentNode.innerHTML;
			//}
		}

		// Fix focus() by reload the element, very important with lyhuuloi, haha :))
		object = document.getElementById(element_id);

		var alert_table = document.getElementById(object.id+"_alert");

		// Set queued name
		current_field = object.name;
				
		// Display the error board
		if ( display_type == "table" )
		{
			document.getElementById(object.id+"_alert").style.paddingLeft = (x+object.offsetWidth)+"px";
		}

		alert_table.innerHTML = "<div id='"+object.id+"_sub"+"' style='opacity:0; filter:alpha(opacity=0);'> <img style='position: absolute; z-index: 1; padding-top: 1px;' src='"+site_img_url+"/icons/arrow_alertbox.gif'> <div onclick='javascript:close_warning();' style='position: absolute; z-index: 1; cursor: pointer; border: solid #F2DDDD 1px; margin-left: 5px;'><div style='border: solid #992A2A 1px; background: #F2DDDD; width: 250px; padding: 4px;'>"+emsg+"</div></div></div>";

		setTimeout("open_opacity()", 1);
		
		object.focus();

		return false;
	}
}

function check_ip_address( str )
{
	//var filter = /^(([1-9][0-9]{0,2})|0)\.(([1-9][0-9]{0,2})|0)\.(([1-9][0-9]{0,2})|0)\.(([1-9][0-9]{0,2})|0)$/;
	
	//if ( ! filter.test( str ) )
	//{
	//	return false
	//}
	//else
	//{
	//	return true;	
	//}
	
	var ipaddr = str;
	
    ipaddr = ipaddr.replace( /\s/g, "") //remove spaces for checking
    var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/; //regex. check for digits and in all 4 quadrants of the IP

    if (re.test(ipaddr)) {
        //split into units with dots "."
        var parts = ipaddr.split(".");
        //if the first unit/quadrant of the IP is zero
        if (parseInt(parseFloat(parts[0])) == 0) {
            return false;
        }
        //if the fourth unit/quadrant of the IP is zero
        if (parseInt(parseFloat(parts[3])) == 0) {
            return false;
        }
        //if any part is greater than 255
        for (var i=0; i<parts.length; i++) {
            if (parseInt(parseFloat(parts[i])) > 255){
                return false;
            }
        }
        return true;
    } else {
        return false;
    }

}

function check_domain(nname)
{
	nname = trim(nname.toLowerCase());
	
	var arr = new Array(
	'.com','.net','.org','.biz','.coop','.info','.museum','.name',
	'.pro','.edu','.gov','.int','.mil','.ac','.ad','.ae','.af','.ag',
	'.ai','.al','.am','.an','.ao','.aq','.ar','.as','.at','.au','.aw',
	'.az','.ba','.bb','.bd','.be','.bf','.bg','.bh','.bi','.bj','.bm',
	'.bn','.bo','.br','.bs','.bt','.bv','.bw','.by','.bz','.ca','.cc',
	'.cd','.cf','.cg','.ch','.ci','.ck','.cl','.cm','.cn','.co','.cr',
	'.cu','.cv','.cx','.cy','.cz','.de','.dj','.dk','.dm','.do','.dz',
	'.ec','.ee','.eg','.eh','.er','.es','.et','.fi','.fj','.fk','.fm',
	'.fo','.fr','.ga','.gd','.ge','.gf','.gg','.gh','.gi','.gl','.gm',
	'.gn','.gp','.gq','.gr','.gs','.gt','.gu','.gv','.gy','.hk','.hm',
	'.hn','.hr','.ht','.hu','.id','.ie','.il','.im','.in','.io','.iq',
	'.ir','.is','.it','.je','.jm','.jo','.jp','.ke','.kg','.kh','.ki',
	'.km','.kn','.kp','.kr','.kw','.ky','.kz','.la','.lb','.lc','.li',
	'.lk','.lr','.ls','.lt','.lu','.lv','.ly','.ma','.mc','.md','.mg',
	'.mh','.mk','.ml','.mm','.mn','.mo','.mp','.mq','.mr','.ms','.mt',
	'.mu','.mv','.mw','.mx','.my','.mz','.na','.nc','.ne','.nf','.ng',
	'.ni','.nl','.no','.np','.nr','.nu','.nz','.om','.pa','.pe','.pf',
	'.pg','.ph','.pk','.pl','.pm','.pn','.pr','.ps','.pt','.pw','.py',
	'.qa','.re','.ro','.rw','.ru','.sa','.sb','.sc','.sd','.se','.sg',
	'.sh','.si','.sj','.sk','.sl','.sm','.sn','.so','.sr','.st','.sv',
	'.sy','.sz','.tc','.td','.tf','.tg','.th','.tj','.tk','.tm','.tn',
	'.to','.tp','.tr','.tt','.tv','.tw','.tz','.ua','.ug','.uk','.um',
	'.us','.uy','.uz','.va','.vc','.ve','.vg','.vi','.vn','.vu','.ws',
	'.wf','.ye','.yt','.yu','.za','.zm','.zw',
	'.asia','.me','.tel','.eu','.mobi','.xxx');
	
	var mai = nname;
	var val = true;
	
	var dot = mai.lastIndexOf(".");
	var dname = mai.substring(0,dot);
	var ext = mai.substring(dot,mai.length);
	
	if(dot>2 && dot<57)
	{
		for(var i=0; i<arr.length; i++)
		{
		  if(ext == arr[i])
		  {
			val = true;
			break;
		  }	
		  else
		  {
			val = false;
		  }
		}
		if(val == false)
		{
			 return lang_invalid_domain_ext;
		}
		else
		{
			for(var j=0; j<dname.length; j++)
			{
			  	var dh = dname.charAt(j);
			  	var hh = dh.charCodeAt(0);
			  	if((hh > 47 && hh<59) || (hh > 64 && hh<91) || (hh > 96 && hh<123) || hh==45 || hh==46)
			  	{
				 	if((j==0 || j==dname.length-1) && hh == 45)	
				 	{
						return lang_invalid_domain_start;
				 	}
			 	}
				else
				{
					return lang_invalid_domain_char;
			 	}
			}
		}
	}
	else
	{
		return lang_invalid_domain_length;
	}	
	
	return true;
}

function check_subdomain(str)
{
	//var subdomainPattern = /[a-zA-Z0-9-]$/;  
	//return subdomainPattern.test(str);
	if ( str.match(/[^\w\u00C0-\u1EF9-]/g) )
	{
		return false;	
	}
	else
	{
		return true;	
	}
}


function check_email( str )
{
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
	return emailPattern.test(str);
}

function check_number(str)
{
    if ( isNaN(str) == true )
	{
        return false;
    }
	
    return true;
}

function check_http(nname)
{
	if ( nname.substring(0,4).toLowerCase() != "http" )
	{
		return false;
	}
	
	return true;
}

function check_phone(str)
{
    var error = "";
    var stripped = str.replace(/[\(\)\.\-\ ]/g, '');   

	if ( str == "" )
	{
        return false;	
    }
	else if ( str.length < 8 )
	{
		return false;	
	}
	else if ( isNaN(stripped) == true )
	{
        return false;
    }
	else if ( stripped.length > 12 && stripped.substr(0,1) != "+" )
	{
        return false;
    }
	else if ( stripped.length > 13 && stripped.substr(0,1) == "+" )
	{
		return false;
	}
	
    return true;
}

function check_date(str)
{
	var d = new Date();

	str = str.split("\/");
	
	if ( str.length < 3 || str[0] > 31 || str[1] > 12 )
	{
		return false;
	}
}

function check_username(name)
{
	if ( name.match(/[^\w\u00C0-\u1EF9 -.]/g) )
	{
		return false;	
	}
	else
	{
		return true;	
	}
}

function check_form( form_id, type )
{
	var object;
	
	var frm = document.forms[form_id];
	var numberElements = frm.elements.length;

	for( var i = 0; i < numberElements; i++ )
	{
		object = frm.elements[i];

		if ( open_warning(object.name, type) == false )
		{
			return false;
		}
	}
}

var form_select_data = "";

function rebuild_form( form_id, type, marked )
{
	var frm = document.forms[form_id];
	
	if ( ! frm )
	{
		return false;	
	}
	
	var numberElements = frm.elements.length+5;
	var sympol = "(*)";
	var is_emsg = 0;

	for( var i = 0; i < numberElements; i++ )
	{
		if ( frm.elements[i] )
		{
			object = frm.elements[i];
	
			// Important, set attribute for the element, if not, no action will effect, lyhuuloi
			object.setAttribute("id", object.name);
			
			// Add suffix
			var addsuffix = "";
			
			if ( object.getAttribute("addsuffix") )
			{
				addsuffix = object.getAttribute("addsuffix");	
			}
			
			if ( type == 1 )
			{
				object.setAttribute("autocomplete", "off");
			}
	
			if ( object.getAttribute("ehide") != 1 )
			{
				var is_float = object.parentNode.style.cssFloat ? true : false;

				var x = object.offsetLeft + object.clientWidth - 5;
				var y = is_float == true ? object.offsetTop : 0;

				if ( object.offsetParent )
				{
					if ( object.getAttribute("addsuffix") && ! object.getAttribute("emsg") )
					{
						// Check float
						if ( is_float == true )
						{
							object.parentNode.innerHTML = object.parentNode.innerHTML + "<div style='float: left; padding-left: 4px;'>"+addsuffix+"</div>";
						}
						else
						{
							object.parentNode.innerHTML = object.parentNode.innerHTML + " " + addsuffix;	
						}
						
						is_emsg = 1;
					}
					else if ( object.getAttribute("emsg") && ! document.getElementById("sympol_"+object.name) )
					{
						// Check float
						if ( is_float == true )
						{
							object.parentNode.innerHTML = object.parentNode.innerHTML + "<div style='float: left; padding-left: 4px;' id='sympol_"+object.name+"'>"+addsuffix+" <font color='red'>"+sympol+"</font></div>";
						}
						else
						{
							object.parentNode.innerHTML = object.parentNode.innerHTML + " " + addsuffix +" <font color='red'>"+sympol+"</font>";
						}
					}
				}
			}
			
			// Fix Default value for select-one and select-multi, very important :) Lyhuuloi
			object = frm.elements[i];
			
			if ( object.getAttribute("defaultvalue") )
			{
				//document.getElementById("insert").innerHTML += object.type + " + ";
		
				if ( object.type == "select-one" )
				{  	 
					for ( var j = 0; j < object.length; j++ )
					{
						if ( object.options[j].value == object.getAttribute("defaultvalue") )
						{
							object.options[j].selected = true;
	
							break;
						}
					}
				}
				else if ( object.type == "radio" || object.type == "checkbox" )
				{
					if ( object.value == object.getAttribute("defaultvalue") )
					{
						object.checked = "checked";	
					}
				}
			}
	
			if ( marked == 1 )
			{
				if ( object.value && object.name != "submit" && object.type != "button" )
				{
					object.style.border = "solid #FFCC00 1px";
					object.style.background = "#FFFFCC";
					object.style.color = "#333333";
					object.style.fontWeight = "bold";
				}
			}
	
			//document.getElementById("insert").innerHTML += object.name + " + " + object.getAttribute("defaultvalue") + " + " + object.value + "<br />";
		}
	}
	
	if ( is_emsg == 1 )
	{
		//document.writeln("<font style='color: #FF0000'>"+sympol+"</font> " + lang_required_field);
	}
}

// lyhuuloi foundation date 23/1/2008, edited: 25/06/2009
function password_change( data )
{
	close_warning();
	
	var point = 0;
	var value = data.value;
	var simple = new Array("123456", "1234567", "12345678", "123456789", "321654", "654987", "321654987", "654321", "987654321");
	var special = new Array("!", "@", "#", "$", "%", "^", "&", "*", "(", ")", ",", ".", ";", "/", "|", "`", "~", "?", "<", ">", ":", "+", "-");
	
	if ( value.length > 0 )
	{
		document.getElementById("password_checker").style.display = "inline";

		// step 1
		point += Math.round(value.length / 2);
	
		// step 2
		var check1 = 0;
		
		for ( var i = 0; i < simple.length-1; i++ )
		{
			if ( simple[i] == value )
			{
				check1 = 1;
			}
		}

		if ( check1 == 0 && value.length >= 6 )
		{
			point += 2;	
		}
		else
		{
			point -= 4;	
		}

		// step 3
		if ( value.length >= 6 )
		{
			for ( var i = 1; i <= 2; i++ )
			{
				// Calculate the value to filter
				var max_value = Math.round(value.length/i)*i;
				var remainder = max_value - value.length;
				var real_value = value.length - remainder;
				var value = value.substr(0, real_value);
				
				// Start filter				
				var data2 = value;
				var result = data2;

				for ( var j = 0; j <= Math.round(data2.length/i); j++ )
				{
					var result = result.replace( data2.substr(0,i), "" );
				}

				if ( result != "" )
				{
					point += 2;
				}
				else
				{
					point -= 1;
				}
			}
		}
		
		// Step 4
		if ( value.length >= 6 )
		{
			for ( var i = 0; i < special.length-1; i++ )
			{
				var str_base = value;
				var str_replace = value.replace(special[i], "");
				
				if ( str_base.length > str_replace.length )
				{
					point++;	
				}
			}
		}
	}
	else
	{
		document.getElementById("password_checker").style.display = "none";
	}

	if ( point >= 5 )
	{
		document.getElementById("pwd1").innerHTML = '<span class="password_2">&nbsp;</span>';
	}
	else
	{
		document.getElementById("pwd1").innerHTML = '<span class="password_1">&nbsp;</span>';
	}
	
	if ( point >= 10 )
	{
		document.getElementById("pwd2").innerHTML = '<span class="password_2">&nbsp;</span>';
	}
	else
	{
		document.getElementById("pwd2").innerHTML = '<span class="password_1">&nbsp;</span>';
	}
	
	if ( point >= 15 )
	{
		document.getElementById("pwd3").innerHTML = '<span class="password_2">&nbsp;</span>';
	}
	else
	{
		document.getElementById("pwd3").innerHTML = '<span class="password_1">&nbsp;</span>';
	}
	
	if ( point >= 20 )
	{
		document.getElementById("pwd4").innerHTML = '<span class="password_2">&nbsp;</span>';
	}
	else
	{
		document.getElementById("pwd4").innerHTML = '<span class="password_1">&nbsp;</span>';
	}
	
	if ( point >= 25 )
	{
		document.getElementById("pwd5").innerHTML = '<span class="password_2">&nbsp;</span>';
	}
	else
	{
		document.getElementById("pwd5").innerHTML = '<span class="password_1">&nbsp;</span>';
	}

}

//=================================================
// ALERT
//=================================================

var alert_number = 1;
var alert_refresh = 15;
var alert_max = 20;
var alert_width = 994;
var alert_hade_1 = 1;
var alert_hade_2 = 100;
var alert_padding = 2;
var alert_height = 29;
var IE = document.all ? true : false;

function alert_new( text )
{
	document.getElementById("header_alert_text").innerHTML = text;
	check_alert();
	start_alert();
	alert_sound();
}

function check_alert()
{
	if ( document.getElementById("header_alert_text").innerHTML )
	{
		document.getElementById("header_alert_sound").innerHTML = '<embed src="'+site_img_url+'/alert.wav" name="alert_sound" id="alert_sound" loop="false" autoplay="false" width="0" height="0"></embed>';
		
		if ( IE == false )
		{
			window.captureEvents(Event.LOAD);
			window.onload = alert_sound;
			window.onload = alert;
		}
		else
		{
			window.onload = alert_sound;
			window.onload = alert;	
		}
		//setTimeout("alert_sound('alert_sound')", 100);
		//setTimeout("start_alert()", 200);	
	}
}

 function alert_delete(theURL)
{

	swal({
		   title: confirm_alert_title,
	       text: confirm_alert_delete,
	       type: "warning",
	       showCancelButton: true,
	       confirmButtonClass: "btn-danger",
	       confirmButtonText: cms_lang['gnotice_ok'],
	       cancelButtonText: cms_lang['gnotice_cancel'],
	       closeOnConfirm: true,
	       closeOnCancel: true
	   }).then(function () {
                    window.location.href=theURL;
                                                     
           });
	   

	// if (confirm("Bạn có chắc muốn sử dụng lệnh này"))
	// {
	// 	window.location.href=theURL;
	// }
	// else
	// {
	// 	return false;	
	// }
}

function alert_confirm_stockcustom(theURL, stock ="", msg  = "")
{
 
	var popup_check_send_mail = 0;
	var popup_auto_addbill_import = 0;
	swal({
		   title: cms_lang['gnotice'],
	  type: 'warning',
	  html:
	  	 '<span>'+msg+'</span>' +
	    '<div class="checkbox">' +
	    '<input type="checkbox" id="popup_check_send_mail" value="1"> ' +
	    '	<label for="popup_check_send_mail">'+cms_lang['gmsg_send_order_via_email']+'</label>'+
	    '</div>',
     		 showCancelButton: true,
	       confirmButtonClass: 'btn-primary',
 		   cancelButtonClass: 'btn-danger',
	       cancelButtonText: cms_lang['gnotice_cancel'],
	       confirmButtonText: cms_lang['gnotice_ok'],
	 }).then(function () {
	 		if(stock >= 1)
	 		{
				if ($("#popup_check_send_mail").prop( "checked" ) ) 
		 		{
		 			var popup_check_send_mail = 1;
				} 
						
				swal({
					   title: cms_lang['gnotice'],
				  type: 'warning',
				  html:
				  	 '<span>'+cms_lang['gnotice_not_enough_inventory']+'</span>'+
				  	 '<div class="checkbox">' +
					    '<input type="checkbox" id="popup_check_add_billimport" value="1"> ' +
					    '	<label for="popup_check_add_billimport">'+cms_lang['gmsg_auto_addbill_import']+'</label>'+
					 '</div>',
			     	   showCancelButton: true,
				       confirmButtonClass: 'btn-primary',
			 		   cancelButtonClass: 'btn-danger',
				       cancelButtonText: cms_lang['gnotice_cancel'],
				       confirmButtonText: cms_lang['gnotice_ok'],
				 }).then(function () {
				 		if ($("#popup_check_add_billimport").prop( "checked" ) ) 
				 		{
				 			var popup_auto_addbill_import = 1;
						} 	
				 		window.location.href=theURL+"&is_send_mail="+popup_check_send_mail+"&export_am=1"+"&addbill_import="+popup_auto_addbill_import;
					})	 	
			}
	 		else
	 		{
	 			if ($("#popup_check_send_mail").prop( "checked" ) ) 
		 		{
		 			var popup_check_send_mail = 1;
				} 
				if ($("#popup_check_add_billimport").prop( "checked" ) ) 
		 		{
		 			var popup_auto_addbill_import = 1;
				}
		 		window.location.href=theURL+"&is_send_mail="+popup_check_send_mail+"&addbill_import="+popup_auto_addbill_import; 			
	 		}		 
		})

}


function alert_confirm_custom(theURL, msg = "", confirm_msg = "")
{

    confirm_msg = confirm_msg == "" ? cms_lang['gmsg_send_order_via_email'] : confirm_msg;

	swal({
		   title: cms_lang['gnotice'],
	  type: 'warning',
	  html:
	  	 '<span>'+msg+'</span>' +
	    '<div class="checkbox">' +
	    '<input type="checkbox" id="popup_check_send_mail" value="1"> ' +
	    '	<label for="popup_check_send_mail">'+ confirm_msg +'</label>'+
	    '</div>' ,
     	   showCancelButton: true,
	       confirmButtonClass: 'btn-primary',
 		   cancelButtonClass: 'btn-danger',
	       cancelButtonText: cms_lang['gnotice_cancel'],
	       confirmButtonText: cms_lang['gnotice_ok'],
	 }).then(function () {
	 		if ($("#popup_check_send_mail").prop( "checked" ) ) 
	 		{
	 			window.location.href=theURL+"&is_send_mail=1";
	 		}
	 		else
	 		{
	 			window.location.href=theURL;
	 		}	 
		})

}

function delete_confirm_order(theURL)
{
	swal({
		   title: cms_lang['gnotice_cancel_invoice'],
	       text: cms_lang['gnotice_confirm_cancel_related_invoice'],
	       type: "warning",
	       showCancelButton: true,
	       confirmButtonClass: 'btn-primary',
 		   cancelButtonClass: 'btn-danger',
	       cancelButtonText: cms_lang['gnotice_cancel'],
	       confirmButtonText: cms_lang['gnotice_ok'],
	   }).then(function () {
			  window.location.href=theURL;
			 
		})
}


function delete_confirm(theURL)
{

	swal({
		   title: cms_lang['gnotice'],
	       text: cms_lang['gnotice_confirm_action'],
	       type: "warning",
	       showCancelButton: true,
	       confirmButtonClass: 'btn-primary',
 		   cancelButtonClass: 'btn-danger',
	       cancelButtonText: cms_lang['gnotice_cancel'],
	       confirmButtonText: cms_lang['gnotice_ok'], 
 
	   }).then(function () {
			  window.location.href=theURL;	 
		})

}

function cancelPackage(theURL)
{
    swal({
        title: cms_lang['gnotice'],
        text: cms_lang['cancel_package_confirm'],
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-primary',
        cancelButtonClass: 'btn-danger',
        cancelButtonText: cms_lang['gnotice_cancel'],
        confirmButtonText: cms_lang['gnotice_ok'],
    }).then(function () {
        window.location.href=theURL;

    })
}

function alert_empty(theURL)
{
	if (confirm( lang_alert_empty ))
	{
		window.location.href=theURL;
	}
	else
	{
		return false;
	}
}

function alert_sound(as)
{
	var as = "alert_sound";

	if ( IE == false )
	{
		obj = document.embeds[as];
		if(obj.Play) obj.Play();
	}
	else
	{
		document.getElementById(as).play();
	}
	return true;
}

var default_order = "";
var default_by = "";
		
	function arrange_check( field_name, default_field )
	{
		var url_current = Url.decode(window.location.href);
		url_current = url_current.toLowerCase();
		//sell/order.html
		url_current = url_current.replace("order.html","?site=order");
		var url_array = url_current.split("&");
		var new_url = "";
		var remove_cnt = 1;
		
		for ( var i = 0; i < url_array.length; i++ )
		{
			var url_item = url_array[i].split("=");
			
			if ( url_item[0] == "order" )
			{
				default_order = url_item[1];
			}
			else if ( url_item[0] == "by" )
			{
				default_by = url_item[1];
			}
			else if ( url_item[0] )
			{
				new_url += url_item[0]+"="+url_item[1]+"&";
			}
		}
		
		if ( field_name == default_order )
		{
			var by = default_by == "asc" ? "desc" : "asc";
		}
		else if ( field_name == default_field )
		{
			var by = "asc";
		}
		else
		{
			var by = "desc";
		}
		
		new_url += "order=" + field_name + "&by=" + by;
		
		//document.getElementById("insert").innerHTML += new_url + " ==> " + site_url_return + "<br />" ;
		
		new_url = new_url.replace(site_url_return, "");
		new_url = new_url + site_url_return;
		
		return new_url;
	}

	function arrange_setup( sort_data )
	{
		var sort_data = sort_data.split(",");
		var default_field = sort_data[0];

		for ( var i = 0; i < sort_data.length; i++ )
		{
			if ( sort_data[i] )
			{
				var sort_item = document.getElementById("order_"+sort_data[i]);

				if ( sort_item )
				{
					sort_item.innerHTML = "<a href='"+arrange_check(sort_data[i], default_field)+"'>"+sort_item.innerHTML+"</a>";
				}
			}
		}
		
		if ( ! document.getElementById("order_" + default_field) )
		{
			return false;	
		}
		else if ( ! default_order )
		{
			document.getElementById("order_" + default_field).innerHTML += " <img align='absmiddle' src='"+site_img_url+"/desc_order.png' title='Z-A'>";
		}
		else
		{
			if ( default_by == "asc" )
			{
				document.getElementById("order_" + default_order).innerHTML += " <img align='absmiddle' src='"+site_img_url+"/asc_order.png' title='Z-A'>";
			}
			else
			{
				document.getElementById("order_" + default_order).innerHTML += " <img align='absmiddle' src='"+site_img_url+"/desc_order.png' title='Z-A'>";
			}
		}
	}
	
	function create_random_password( element_id )
	{
		var password = document.getElementById( element_id );
		
		if ( password.value )
		{
			if ( confirm( lang_create_random_password ) == false )
			{
				return false;
			}
		}
		
		var pwd_array = "0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z ! @ # $ % ^ & *";
		pwd_array = pwd_array.split(" ");
		
		var new_password = "";
		
		for ( i = 0; i < 10; i ++ )
		{
			var result = Math.floor(Math.random()*pwd_array.length);
			new_password = new_password + pwd_array[result];
		}
		
		password.value = new_password;
	}
	
	function image_resize(width,height)
	{
		maxheight = width ? width : 300;
		maxwidth= height ? height : 300;
		imgs=document.getElementsByTagName("img");
		for (p=0; p<imgs.length; p++) {
		//if (imgs[p].getAttribute("alt")=="user posted image")
		//{
			w=parseInt(imgs[p].width);
			h=parseInt(imgs[p].height);
			if (parseInt(imgs[p].width)>maxwidth) {
			imgs[p].style.cursor="pointer";
			imgs[p].onclick=new Function("iw=window.open(this.src,'ImageViewer','resizable=1');iw.focus()");
			imgs[p].height=(maxwidth/imgs[p].width)*imgs[p].height;
			imgs[p].width=maxwidth;
			}
			if (parseInt(imgs[p].height)>maxheight) {
			imgs[p].style.cursor="pointer";
			imgs[p].onclick=new
			Function("iw=window.open(this.src,'ImageViewer','resizable=1');iw.focus()");

			imgs[p].width=(maxheight/imgs[p].height)*imgs[p].width;
			imgs[p].height=maxheight;
			}
			}
		//}
	}
	
	function redirect( url )
	{
		window.location.href = url;
	}
	
	
	
	function MM_preloadImages() { //v3.0
	  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
		var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
		if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
	}
	
	function MM_swapImgRestore() { //v3.0
	  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
	}
	
	function MM_findObj(n, d) { //v4.01
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	  if(!x && d.getElementById) x=d.getElementById(n); return x;
	}
	
	function MM_swapImage() { //v3.0
	  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
	   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
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
	
	var logolhl = new Array();
	var logo_name = "";
	
	function logo_start(logo_name)
	{
		if ( ! logolhl[logo_name]['logo_data'][logolhl[logo_name]['logo_cnt']] )
		{
			logolhl[logo_name]['logo_cnt'] = 0;	
		}
		
		if ( document.getElementById("station_"+logo_name+"") )
		{
			document.getElementById("station_"+logo_name+"").innerHTML = logolhl[logo_name]['logo_data'][logolhl[logo_name]['logo_cnt']];
			logolhl[logo_name]['logo_cnt']++;
			
			if ( logolhl[logo_name]['logo_total'] == 1 )
			{
				return false;	
			}
			
			setTimeout("logo_start('"+logo_name+"')",15000);
		}
		else
		{
			setTimeout("logo_start('"+logo_name+"')",0);
		}
	}

	function handleError()
	{
		//return true;
	}

	function logo_station( element_id )
	{
		//window.onerror = handleError;
		
		if ( ! element_id.innerHTML )
		{
			return false;	
		}
		
		logo_name = element_id.id;
		logolhl[logo_name] = new Array();
		logolhl[logo_name]['logo_data'] = eval("station_"+logo_name);
		logolhl[logo_name]['logo_total'] = logolhl[logo_name]['logo_data'].length;
		logolhl[logo_name]['logo_cnt'] = Math.floor((Math.random()*logolhl[logo_name]['logo_data'].length));

		if ( logolhl[logo_name]['logo_data'][0] )
		{
			document.write("<div id='station_"+logo_name+"'></div>");
		
			logo_start(logo_name);
		}
	}
	
	function f_clientWidth() {
		return f_filterResults (
		window.innerWidth ? window.innerWidth : 0,
		document.documentElement ? document.documentElement.clientWidth : 0,
		document.body ? document.body.clientWidth : 0
	);
	}
	function f_clientHeight() {
		return f_filterResults (
			window.innerHeight ? window.innerHeight : 0,
			document.documentElement ? document.documentElement.clientHeight : 0,
			document.body ? document.body.clientHeight : 0
		);
	}
	function f_scrollLeft() {
		return f_filterResults (
			window.pageXOffset ? window.pageXOffset : 0,
			document.documentElement ? document.documentElement.scrollLeft : 0,
			document.body ? document.body.scrollLeft : 0
		);
	}
	function f_scrollTop() {
		return f_filterResults (
			window.pageYOffset ? window.pageYOffset : 0,
			document.documentElement ? document.documentElement.scrollTop : 0,
			document.body ? document.body.scrollTop : 0
		);
	}
	function f_filterResults(n_win, n_docel, n_body) {
		var n_result = n_win ? n_win : 0;
		if (n_docel && (!n_result || (n_result > n_docel)))
			n_result = n_docel;
		return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
	}

	function input_update_html(object,target)
	{
		document.getElementById(target).innerHTML = object.value;
	}
	
	// LHL-05-08-2011	
	function timer_countdown(ob, obtime, obdiff)
	{
		var minute = 0;
		var second = 0;
		var obt = document.getElementById(ob);

		if ( ! obt )
		{
			document.write("<font id='"+ob+"'></font>");
			
			obt = document.getElementById(ob);
		}
		
		minute = obtime > 0 ? Math.floor(obtime/60) : 0;
		second = obtime - (minute*60);

		if ( obtime <= 0 )
		{
			obt.innerHTML = (obdiff ? obdiff : "");
			return false;	
		}

		obt.innerHTML = minute + ":" + (second <= 9 ? "0"+second : second) + "";

		// Update timer
		setTimeout("timer_countdown('"+obt.getAttribute("id")+"', "+(parseInt(obtime)-1)+")", 1000);
	}
	
	function get_domain_name(domainname)
	{
		var domain_name = domainname.split('.');

		return domain_name[0];
	}
	
	function get_domain_ext(domainname)
	{
		var domain_name = domainname.split('.');
		var domain_ext = domainname.substring(domain_name[0].length, domainname.length);
		
		return domain_ext;
	}
	
	// Show detail logs - hvu 02.04.2013
	function show_detail_logs(log_id)
	{
		$.ajax(
		{
			'type': "post",
			'url':site_root_domain+'/?site=logs&act=detail&type=ajax&id='+log_id,
			'beforeSend': function(req){
					$("#big_logs").css("display","block");
					$("#detail_logs").html('');
				},
			'success': function(req){
				$("#detail_logs").html(req);
			}		
		})
	}


	function isNumberKey(evt)
    {
        //validate(value);
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

Date.prototype.addDays = function(days)
{
	var dat = new Date(this.valueOf());
	this.setDate(dat.getDate() + days);
	return this;
}

Date.isLeapYear = function (year) {
	return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
};

Date.getDaysInMonth = function (year, month) {
	return [31, (Date.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
};

Date.prototype.isLeapYear = function () {
	return Date.isLeapYear(this.getFullYear());
};

Date.prototype.getDaysInMonth = function () {
	return Date.getDaysInMonth(this.getFullYear(), this.getMonth());
};

Date.prototype.addMonths = function (value) {
	var n = this.getDate();
	this.setDate(1);
	this.setMonth(this.getMonth() + value);
	this.setDate(Math.min(n, this.getDaysInMonth()));
	return this;
};

var getFisrtLastInCurrentMonth = function()
{
	var date = new Date();
	var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
	var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	return [firstDay, lastDay];
}

var getFisrtLastInCurrentWeek = function()
{
	var curr = new Date; // get current date
	var first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
	var last = first + 6; // last day is the first day + 6

	var firstDay = new Date(curr.setDate(first));
	var lastDay = new Date(curr.setDate(last));
	return [firstDay, lastDay];
}


function submit_action_control(onthis, form)
{
	var act = $(onthis).val();
	if(act == ""){return false;}
	$("form#"+form+" input[name='act']").val(act);
	$("form#"+form+" input[name='subact']").val(act);
	$("form#"+form).submit();

}

(function($) {
    $.fn.extend( {
        limiter: function(limit, elem) {

            $(this).unbind("keyup focus");

            $(this).on("keyup focus", function() {
                setCount(this, elem);
            });
            function setCount(src, elem) {
                if(typeof src == 'undefined') return false;
                var chars = src.value.length;
                if (chars > limit) {
                    src.value = src.value.substr(0, limit);
                    chars = limit;
                }
                elem.html( limit - chars );
            }
            setCount($(this)[0], elem);
        }
    });
})(jQuery);

function setCookie(cname, cvalue, second) {
    var d = new Date();
    d.setTime(d.getTime() + (second*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}



    function change_district_global(obj , appendid )
    {
      var city_id = $(obj).val();
      $("#product_district").html("");
      $.ajax({
          type: "post",
          url: "/acp/?site=product&subact=getdistrict",
          data: {city_id: city_id},
          success: function(response)
          {
              $(appendid).html(response);
          }
      });
    }

    