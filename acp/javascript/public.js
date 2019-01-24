// JavaScript Document

	function preview_post_v()
	{
		$("#preview-post").show();
		$('html, body').animate({
			 scrollTop: $("#preview-post").offset().top
		}, 700);
	}
	
	function preview_disable()
	{
		$("#preview-post").hide();
	}
	//Preview Post
	function preview_post()
	{
		var news_name = $("#news_name").val();
		var news_description = $("#news_description").val();
		
		//var news_content = tinyMCE.activeEditor.getContent();
		//alert(tinyMCE.get('news_content').getContent());   
		//news_content= replace_with(news_content);
		var news_content = tinyMCE.get('news_content').getContent();

								
		//news_content= replace_with(news_content);
				var url = site_root_domain + "/?site=news&view=convert_content";
 				$.ajax({
								type:"POST",
								url: url,
								datatype:"json",
                                data:{news_content:news_content},
								success: function(text) 
								{
									
									var out = "";
										out += "<div class='detail'> ";
										out += "<h3 class='detail_title'>"+news_name+"</h3> ";
										out += "<p class='normal_post'>"+news_description+"</p> ";
										out += "<div class='content'>"+text+"</div> ";
										out +="</div>";
										
										$("#preview-post").html(out);
										$('html, body').animate({
											 scrollTop: $("#preview-post").offset().top
										}, 700);
										
								} 	
							});
						
			
		
		
			
			
	}	
	
	function replace_with(value)
	{


		/*if(value.match(/\[author image=([^<> \n]+?)\](.+?)\[\/author\]/ig))
		{
			
			// sdfsdfsdf[author][img ='url'"][text]fsfsdfsdf[text][/author]dsfsdf
			var fi = value
			alert("A");
			
					$search[8] = "/\[author image=([^<> \n]+?)\](.+?)\[\/author\]/i";
		$replace[8] = '<div class="author-info"><img class="author-img" src="\\1" /><div class="author-info-content">\\2</div></div>';
		

		}*/
		value  = value.replace('[text]', '<p>');
		value  = value.replace('[/text]', '</p>');
		
		//value  = value.replace('[img]', '<img>');
		value  = value.replace('[/text]', '</p>');
		
		value  = value.replace('[divider]', '<div class="divider"></div>');
		
		value  = value.replace('[highlight]', '<span class="highlight">');
		value  = value.replace('[/highlight]', '</span>');
		
		value  = value.replace('[dropcap]', '<span class="dropcap">');
		value  = value.replace('[/dropcap]', '</span>');
		
		value  = value.replace('[li]', '<li>');
		value  = value.replace('[/li]', '</li>');
		
		value  = value.replace('[checklist]', '<div class="checklist"><ul>');
		value  = value.replace('[/checklist]', '</ul></div>');
		
		value  = value.replace('[box type="info"]', '<div class="box shadow aligncenter"><div>');
		value  = value.replace('[box type="shadow"]', '<div class="box info aligncenter"><div>');
		value  = value.replace('[box type="success"]', '<div class="box success aligncenter"><div>');
		value  = value.replace('[box type="warning"]', '<div class="box warning aligncenter"><div>');
		value  = value.replace('[box type="note"]', '<div class="box note aligncenter"><div>');
		value  = value.replace('[/box]', '</div></div>');
		
		value  = value.replace('[one_half]', '<div class="one_half">');
		value  = value.replace('[/one_half]', '</div>');
		
		
		value  = value.replace('[one_half]', '<div class="one_half">');
		value  = value.replace('[/one_half_last]', '</div>');
		
		return value;
	}
	
	function refresh_post()
	{
		$("#preview-post").html("");
		preview_post();
	}
// Update tien nhuan but

$("#btn_active").click(function(e) {

    $("#update_royalty").slideToggle("slow");
});
		
	$(".btn_active_bk").click(function(e) {

    $("#update_royalty").slideToggle("slow");
});
		

function load_newstpl(record)
{
	var newstpl_id = record.options[record.selectedIndex].value;
	
	var url = site_root_domain + "/?site=newstpl&act=search&is_ajax=1&type=1&newstpl_id="+newstpl_id;

	AjaxRequest.get(
	{
		'url':''+url+''
		,'onLoading':function(req){ }
		,'onSuccess':function(req){ 
		
				var text = req.responseText;
				var template_split = text.split("||");
				$("#content_newstpl").html(template_split[1]);
				
				var  btn = "<a id='add_tag' class='btn_active' onclick=\"return load_newstpl_insert("+newstpl_id+"); \" >Sử dụng mẫu tin</a>";
				$("#btn-newstpl").html(btn);
		} // tinyMCE.updateContent('email_content');
	});
}	

function load_newstpl_insert(newstpl_id)
{

	var url = site_root_domain + "/?site=newstpl&act=search&is_ajax=1&newstpl_id="+newstpl_id;

	AjaxRequest.get(
	{
		'url':''+url+''
		,'onLoading':function(req){ }
		,'onSuccess':function(req){ print_template(req.responseText); $("#btn-newstpl").html("");  $("#content_newstpl").html("");} // tinyMCE.updateContent('email_content');
	}
	);
}	

function print_template(text)
{
	var template_split = text.split("||");
	if ( template_split[1] )
	{
		tinyMCE.activeEditor.setContent(template_split[1]);
		//parent.tinyMCE.execCommand("mceInsertContent",false,template_split[1]);

	}
	$("#update-box-login").hide();	$('#mask').remove();  
}



function load_popup(text, newstpl_id)
{
	var template_split = text.split("||");

	if ( template_split[1] )
	{
				//Getting the variable's value from a link 
											var loginBox = $(this).attr('href');
							
											//Fade in the Popup
											$(loginBox).fadeIn(300);
											
											//Set the center alignment padding + border see css style
											var popMargTop = ($(loginBox).height() + 24) / 2; 
											var popMargLeft = ($(loginBox).width() + 24) / 2; 
											
											$(loginBox).css({ 
												'margin-top' : -popMargTop,
												'margin-left' : -popMargLeft
											});
											
											$("#content_tpl").html(template_split[1]);
											$("#content_tpl").append("<input type='button' class='btn_login' onclick='return load_newstpl_insert("+newstpl_id+");' value='Sử dụng mẫu tin'/>");
											
											// Add the mask to body
											$('body').append('<div id="mask"></div>');
											$('#mask').fadeIn(300);
											$("#update-box-login").show();
											return false;
	}
}





										
										// When clicking on the button close or the mask layer the popup closed
										// $('a.close, #mask').live('click', function() { 
										//   $('#mask , .login-popup').fadeOut(300 , function() {
										// 	$('#mask').remove();  
										// }); 
										// return false;});
	
	






$('#add_tag').click(function(e) {
				
		var input = $('#tags_input').val();		
		var cnt = $("li.tag").size();
		$("#list_tag").append('<li class="tag" onClick="return del_tags('+cnt+');"  id="tags_'+cnt+'">'+input+' <input type="hidden" name="news_tags_id[]" value="'+input+'"/><a class="close" href="javascript: void();">close</a></li>');
		$("#wrap_tags").hide();		$("#tags_input").val("");	
			
});
			
$(document).ready(function() {
			
			$('#tags_input').keyup(function(e) {
				clearTimeout($.data(this, 'timer'));
				if (e.keyCode == 13)
				  search(true);
				else
				  $(this).data('timer', setTimeout(search, 100));
			});
			function search(force) {
				var tags_id = $('#tags_input').val();
	
				var tags_id = $.trim(tags_id);
				var cat_id;
				var cnt = 0;
				if (!force && tags_id.length < 2)
				{	
					$("#wrap_tags").hide();
					 return; //wasn't enter, not > 2 char
				}
				
						 $.ajax({
								type:"POST",
								url: site_root_domain+"/?site=tags&act=search",
								data: "type=0&tags_id="+tags_id,
								success: function(data) 
								{
							
									$("#wrap_tags").html(data);
									$("#wrap_tags").show();
									
								} 
							});
			}
			return false;
	
    });
    
	
	function  add_tags_temp(id,name)
	{
			var cnt = $("li.tag").size();
			$("#list_tag").append('<li class="tag" onClick="return del_tags('+cnt+');"  id="tags_'+cnt+'">'+name+' <input type="hidden" name="news_tags_id[]" value="'+name+'"/><a class="close" href="javascript: void();">close</a></li>');
				$("#wrap_tags").hide();		$("#tags_input").val("");				
	}	
	
	function del_tags(id)
	{
		$("#tags_"+id).remove();
	}
	
		$("body").click(function() {
			$("#wrap_tags").hide();$("#tags_input").val("");	
		});

			$("#wrap_tags").click(function(e) {
				e.stopPropagation();
			});
			
	