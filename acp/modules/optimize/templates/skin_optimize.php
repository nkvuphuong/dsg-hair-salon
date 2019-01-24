<?php

class skin_optimize {

public function skin_optimize()
{
	global $CMS;
	
	$output = "";
	
$output .= <<<EOF
<script language="javascript">
<!--
	
	function cleanup_start(module, text)
	{
		document.getElementById("cleanup_text").innerHTML += '<div id="'+module+'_text">'+text+'</div>';
		document.getElementById("cleanup_image").innerHTML += '<div id="'+module+'_image"><img src="{$CMS->vars['img_url']}/icon_loading_gray.gif" align="absmiddle" />&nbsp;</div>';
	}
	
	function cleanup_complete(module, text)
	{
		document.getElementById(module+"_text").innerHTML = text;
		document.getElementById(module+"_image").innerHTML = '<img src="{$CMS->vars['img_url']}/icon_tick.png" align="absmiddle" />&nbsp;';
	}
	
	function cleanup_failure(module, text)
	{
		document.getElementById(module+"_text").innerHTML = text;
		document.getElementById(module+"_image").innerHTML = '<img src="{$CMS->vars['img_url']}/icon_cancel.png" align="absmiddle" />&nbsp;';
	}
	
	// Cache
	function cleanup_cache()
	{
		var url = "{$CMS->vars['root_domain']}/?site=optimize&act=exe&module=cache";
		
		AjaxRequest.get(
		{
			'url':''+url+''
			,'onLoading':function(req){ cleanup_start("cache", "{$CMS->lang['cache_cleaning']}"); }
			,'onSuccess':function(req){ if ( req.responseText == 1 ) { setTimeout('cleanup_complete("cache", "{$CMS->lang['cache_cleaned']}");', 200); } else { setTimeout('cleanup_failure("cache", "{$CMS->lang['cache_failed']}");', 1500); } setTimeout('cleanup_cachesql()', 1500); }
		}
		);
	}
	
	// SQL Cache
	function cleanup_cachesql()
	{
		var url = "{$CMS->vars['root_domain']}/?site=optimize&act=exe&module=cachesql";
		
		AjaxRequest.get(
		{
			'url':''+url+''
			,'onLoading':function(req){ cleanup_start("cachesql", "{$CMS->lang['cachesql_cleaning']}"); }
			,'onSuccess':function(req){ if ( req.responseText == 1 ) { setTimeout('cleanup_complete("cachesql", "{$CMS->lang['cachesql_cleaned']}")', 200); } else { setTimeout('cleanup_failure("cachesql", "{$CMS->lang['cachesql_failed']}");', 1500); } setTimeout('email_checker()', 1500); }
		}
		);
	}
	
	
	// Email authentication checker
	function email_checker()
	{
		var url = "{$CMS->vars['root_domain']}/?site=optimize&act=exe&module=email";
		
		AjaxRequest.get(
		{
			'url':''+url+''
			,'onLoading':function(req){ cleanup_start("email", "{$CMS->lang['email_cleaning']}"); }
			,'onSuccess':function(req){ if ( req.responseText == 1 ) { setTimeout('cleanup_complete("email", "{$CMS->lang['email_cleaned']}")', 200); } else { setTimeout('cleanup_failure("email", "{$CMS->lang['email_failed']}");', 1500); } setTimeout('cleanup_completed()', 1500); }
		}
		);
	}
	
	// Status
	var message = "{$CMS->lang['cleaning']}";
	var line = 0;
	var cursor = "_";
	
	function cleanup_completed()
	{
		line = 0;
		cursor = "_";
		message = "{$CMS->lang['cleaned']}";
		document.getElementById("cleanup_completed").innerHTML = 1;
		cleanup_completed_status();
	}

	function cleanup_completed_status()
	{	
		if( line == message.length )
		{
			cursor = '';
		}
	
		document.getElementById("cleanup_status").innerHTML = message.substring(0,line)+cursor;
			
		if( line++ < message.length )
		{
			setTimeout("cleanup_completed_status()",50);
			return false;
		}
	}

	function cleanup_status()
	{
		if ( document.getElementById("cleanup_completed").innerHTML == 1 )
		{
			line = 0;
			cursor = "_";
			message = "{$CMS->lang['cleaned']}";
			setTimeout("cleanup_completed_status()", 0);
			return false;
		}
	
		if( line == message.length )
		{
			cursor = '';
		}
		
		document.getElementById("cleanup_status").innerHTML = message.substring(0,line)+cursor;
		
		if( line++ < message.length )
		{
			setTimeout("cleanup_status()",50);
			return false;
		}
		
		if( line >= message.length )
		{
			line = 0;
			cursor = "_";
			if ( document.getElementById("cleanup_completed").innerHTML == 1 )
			{
				//setTimeout("cleanup_status()",200);
			}
			else
			{
				//setTimeout("cleanup_status()",2000);
			}
		}
	}
	
	// Create Task
	function cleanup()
	{
		document.getElementById("cleanup_ready").style.display = "none";
		document.getElementById("cleanup_result").style.display = "block";
		document.getElementById("cleanup_image").innerHTML = "";
		document.getElementById("cleanup_text").innerHTML = "";
		document.getElementById("cleanup_completed").innerHTML = "";
		
		cleanup_status();
		
		setTimeout("cleanup_cache()", 2500);
	}
	
//-->
</script>
<header class="section-header">
    <div class="tbl">
      <div class="tbl-row">
        <div class="tbl-cell">
          <h3>{$CMS->lang['header']}</h3>
        </div>
      </div>
    </div>
</header>
      


<section class="block_middle">
	

			<div id="cleanup_result" style="display: none;">
            	<div id="cleanup_completed" style="display:none;">0</div>
				<div id="cleanup_status"></div> 
				<div style="float: left; width: 25px; line-height: 30px;" id="cleanup_image"></div>
				<div style="float: left; width: 850px; line-height: 30px;" id="cleanup_text"></div>
			</div>
			<div id="cleanup_ready">{$CMS->lang['welcome']}

            <center><script type="text/javascript">permission_text("optimize_exe", '<input class="input_submit" type="submit" name="submit" class="btn btn-rounded" id="cleanup_button" value="{$CMS->lang['cleanup']}" onclick="cleanup();">');</script></center>

		</div>

</section>


<div class="block_bottom pagination pagination-sm"></div>
EOF;

	return $output;
}

}

?>