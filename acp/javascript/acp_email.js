function email_confirm_resend(confirm_text)
{
	if ( confirm(confirm_text) )
	{
		return true;
	}
	else
	{
		return false;	
	}
}

function print_template(text)
{
	var template_split = text.split("||");
	
	document.getElementById("email_title").value = template_split[0] ? template_split[0] : document.getElementById("email_title").value;
	
	if ( template_split[1] )
	{
		tinyMCE.activeEditor.setContent(template_split[1]);
	}
}

function load_email_template(record)
{
	var emailtpl_id = record.options[record.selectedIndex].value;
	
	var url = site_root_domain + "/?site=emailtpl&act=search&is_ajax=1&emailtpl_id="+emailtpl_id;

	$.ajax(
	{
		'url':''+url+''
		,'success':function(req){print_template(req); } // tinyMCE.updateContent('email_content');
	}
	);
}

function email_preview()
{
	// Form
	var this_form = document.forms["email"];
	this_form.action = site_root_domain + "/?site=email&act=preview";
	
	// Update content
	document.getElementById("email_content").innerHTML = tinyMCE.get('email_content').getContent();

	// Submit
	AjaxRequest.submit(
		this_form,{
			//site_alert_open( lang_email_preview, req.responseText, "" );
			'onSuccess':function(req){ alert("This feature has been removed"); }
		}
	);

	this_form.action = site_root_domain + "/?site=email&act=add";
}