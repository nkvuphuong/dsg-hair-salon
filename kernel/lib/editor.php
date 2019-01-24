<?php

$CMS->class->editor = new class_editor;

class class_editor
{
	public $html = "";

	public function replace($text2, $type = 0)
	{
		//Hight character
		
		/*
		if ( $type == 1 )
		{
			$text2 = str_replace( "&", "&amp;", $text2 );
		}*/
		
		//$text2 = str_replace( "(", "&#40;", $text2 );
		//$text2 = str_replace( ")", "&#41;", $text2 );
		$text2 = str_replace( "$", "&#036;", $text2 );
		$text2 = str_replace( "!", "&#33;", $text2 );
		$text2 = str_replace( "'", "&#39;", $text2 );
		$text2 = str_replace( "`", "&#96;", $text2 );
		$text2 = str_replace( "\"", "&quot;", $text2 );
		//$text2 = str_replace( "<", "&lt;", $text2 );
		//$text2 = str_replace( ">", "&gt;", $text2 );

		// Javascript
		$text2 = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $text2 );
		$text2 = preg_replace( "/alert/i"      , "&#097;lert"          , $text2 );
		$text2 = preg_replace( "/about:/i"     , "&#097;bout:"         , $text2 );
		$text2 = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $text2 );
		$text2 = preg_replace( "/onclick/i"    , "&#111;nclick"        , $text2 );
		$text2 = preg_replace( "/onload/i"     , "&#111;nload"         , $text2 );
		$text2 = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $text2 );
		
		$text2 = preg_replace( "#javascript\:#is"    , "java script:", $text2 );
		$text2 = preg_replace( "#vb(.+?)?script\:#is", "vb script:"  , $text2 );
		$text2 = str_replace(  "`"                   , "&#96;"       , $text2 );
		$text2 = preg_replace( "#moz\-binding:#is"   , "moz binding:", $text2 );
		$text2 = str_replace(  "<script"			   , "&lt;script"  , $text2 );
		
	   	$text = strip_tags("$text2", '<br><b><u><i><font><span><a><img><div>');
	   	
		$search[0] = "/\[url=([^<> \n]+?)\](.+?)\[\/url\]/i";
		$replace[0] = '<a href="\\1" target="_blank">\\2</a>';
		
		$search[1] = "/\[img\](.+?)\[\/img\]/i";
		$replace[1] = '<img src="\\1">';
		
		$search[2] = "/\[color=([^<> \n]+?)\]/i";
		$replace[2] = '<font color="\\1">';
		
		$search[3] = "/\[\/color\]/i";
		$replace[3] = '</font>';		
			
		$search[4] = "/\[size=([^<> \n]+?)\]/i";
		$replace[4] = '<span style="font-size:\\1pt;line-height:100%">';
		
		$search[5] = "/\[\/size\]/i";
		$replace[5] = '</span>';	
		
		$search[6] = "/\[font=([^<> \n]+?)\]/i";
		$replace[6] = '<font face="\\1">';
		
		$search[7] = "/\[\/font\]/i";
		$replace[7] = '</font>';	
		
		//Author
		$search[8] = "/\[author image=([^<> \n]+?)\](.+?)\[\/author\]/i";
		$replace[8] = '<div class="author-info"><img class="author-img"  /><div class="author-info-content">\\1 \\2</div></div>';
		
		$text = preg_replace($search, $replace, $text);
		
		
		// Auto convert to email address
		//$text = eregi_replace('([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)', '<a href="mailto:\\1">\\1</a>', $text);
		
		/*
		// Auto convert to web address
		$text = eregi_replace('([[:space:]]|^)(www)', '\\1http://\\2', $text); // no prefix (www.myurl.ext)
		$prefix = '(http|https|ftp|telnet|news|gopher|file|wais)://';
		$pureUrl = '([[:alnum:]/\n+-=%&:_.~?]+[#[:alnum:]+]*)';
		$text = eregi_replace($prefix . $pureUrl, '<a href="\\1://\\2" target="_blank">\\1://\\2</a>', $text);
		*/
		// 	Highlight
		$text = str_replace( array('[highlight]','[/highlight]') , array('<span class="highlight">', '</span>') ,$text);
		// 	Drop cap
		$text = str_replace( array('[dropcap]','[/dropcap]') , array('<span class="dropcap">', '</span>') ,$text);
		// 	List
		$text = str_replace( array('[li]','[/li]') , array('<li>', '</li>') ,$text);
		$text = str_replace( array('[checklist]','[/checklist]') , array("<div class='checklist'><ul>", '</ul></div>') ,$text);
		// 	Box
		$text = str_replace( array('[/box]') , array('</div></div>'),$text);
		$text = str_replace("[box type=&quot;info&quot;]","<div class=\"box shadow aligncenter\"><div>",$text);
		$text = str_replace("[box type=&quot;shadow&quot;]","<div class=\"box info aligncenter\"><div>",$text);
		$text = str_replace("[box type=&quot;success&quot;]","<div class=\"box success aligncenter\"><div>",$text);
		$text = str_replace("[box type=&quot;warning&quot;]","<div class=\"box warning aligncenter\"><div>",$text);
		$text = str_replace("[box type=&quot;note&quot;]","<div class=\"box note aligncenter\"><div>",$text);
		
		// 	Colum
		$text = str_replace( array('[one_half]','[/one_half]') , array('<div class="one_half">', '</div>') ,$text);
		$text = str_replace( array('[one_half_last]','[/one_half_last]') , array("<div class='one_half last'>", '</div>') ,$text);	
		
		//$text = str_replace('[box type="info"]', '<div class=\"box shadow aligncenter\"><div>', $text);

		// Alignment Codes
		$text = str_replace( array('[center]','[CENTER]','[/center]','[/CENTER]') , array('<div align=\"center\">', '<DIV ALIGN=\"CENTER\">','</div>','</DIV>') ,$text);
		$text = str_replace( array('[right]','[RIGHT]','[/right]','[/RIGHT]') , array('<div align=\"right\">', '<DIV ALIGN=\"RIGHT\">','</div>','</DIV>') ,$text);
		$text = str_replace( array('[left]','[LEFT]','[/left]','[/LEFT]') , array('<div align=\"left\">', '<DIV ALIGN=\"LEFT\">','</div>','</DIV>') ,$text);
		
		// Lines breaks, spacing
		$text = str_replace( array('[br]','[BR]') , array('<br>', '<br>') ,$text);
		$text = str_replace( array('[p]','[P]','[/p]','[/P]') , array('<p>', '<P>','</p>','</P>') ,$text);
		$text = str_replace( array('[tab]','[TAB]') , array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') ,$text);
		$text = str_replace( array('[space]','[SPACE]','[s]','[S]') , array('&nbsp;','&nbsp;','&nbsp;','&nbsp;') ,$text);
		$text = str_replace( array('[hr]','[HR]') , array('<hr>', '<HR>') ,$text);
		
		// Text formatting
		$text = str_replace( array('[b]','[B]','[/b]','[/B]') , array('<b>', '<B>','</b>','</B>') ,$text);
		$text = str_replace( array('[u]','[U]','[/u]','[/U]') , array('<u>', '<U>','</u>','</U>') ,$text);
		$text = str_replace( array('[i]','[I]','[/i]','[/I]') , array('<i>', '<I>','</i>','</I>') ,$text);
		
		// Quote, Code & List
		$text = str_replace( array('[quote]','[QUOTE]','[/quote]','[/QUOTE]') , array('<div id=\"QUOTE\">', '<DIV id=\"QUOTE\">','</div>','</div>') ,$text);
		$text = str_replace( array('[code]','[CODE]','[/code]','[/CODE]') , array('<div id=\"CODE\">', '<DIV id=\"CODE\">','</div>','</div>') ,$text);
	
		//List	
		$text = preg_replace( "#\n?\[list\](.+?)\[/list\]#ies", "\$this->regex_list('\\1')" , $text );
		$text = preg_replace( "#\n?\[list=(a|A|i|I|1)\](.+?)\[/list\]\n?#ies", "\$this->regex_list('\\2','\\1')" , $text );
		
		// (c) (r) and (tm)
		$text = preg_replace( "#\(c\)#i"     , "&copy;" , $text );
		$text = preg_replace( "#\(tm\)#i"    , "&#153;" , $text );
		$text = preg_replace( "#\(r\)#i"     , "&reg;"  , $text );
	
		// <br> \n 
		//$text = preg_replace( "/<br>|<br \/>/", "\n", $text );
		$text = preg_replace( "/\n/", "<br />", $text );
		
		//$text = $this->emoticon( $text );
		
		return $text;
	}
		
		
	public function shortcode($text, $type = 0)
	{
		//Hight character
		

		
		//Author(.+?)
			$search[8] = "/\[author image=(.*)\](.+?)\[\/author\]/i";
		$replace[8] = '<div class="author-info"><img class="author-img" src=\\1 /><div class="author-info-content">\\2</div></div>';
		
		$text = preg_replace($search, $replace, $text);

		// 	Highlight
		$text = str_replace( array('[divider]') , array('<div class="divider"></div>') ,$text);
		// 	Highlight
		$text = str_replace( array('[highlight]','[/highlight]') , array('<span class="highlight">', '</span>') ,$text);
		// 	Drop cap
		$text = str_replace( array('[dropcap]','[/dropcap]') , array('<span class="dropcap">', '</span>') ,$text);
		// 	List
		$text = str_replace( array('[li]','[/li]') , array('<li>', '</li>') ,$text);
		$text = str_replace( array('[checklist]','[/checklist]') , array("<div class='checklist'><ul>", '</ul></div>') ,$text);
		// 	Box
		$text = str_replace( array('[/box]') , array('</div></div>'),$text);
		$text = str_replace("[box type=&quot;info&quot;]",'<div class="box shadow aligncenter"><div>',$text);
		$text = str_replace("[box type=&quot;shadow&quot;]",'<div class="box info aligncenter"><div>',$text);
		$text = str_replace("[box type=&quot;success&quot;]",'<div class="box success aligncenter"><div>',$text);
		$text = str_replace("[box type=&quot;warning&quot;]",'<div class="box warning aligncenter"><div>',$text);
		$text = str_replace("[box type=&quot;note&quot;]",'<div class="box note aligncenter"><div>',$text);
		
		$text = str_replace('[box type="info"]',"<div class=\"box shadow aligncenter\"><div>",$text);
		$text = str_replace('[box type="shadow"]',"<div class=\"box info aligncenter\"><div>",$text);
		$text = str_replace('[box type="success"]',"<div class=\"box success aligncenter\"><div>",$text);
		$text = str_replace('[box type="warning"]',"<div class=\"box warning aligncenter\"><div>",$text);
		$text = str_replace('[box type="note"]',"<div class=\"box note aligncenter\"><div>",$text);
		// 	Colum
		$text = str_replace( array('[one_half]','[/one_half]') , array('<div class="one_half">', '</div>') ,$text);
		$text = str_replace( array('[one_half_last]','[/one_half_last]') , array("<div class='one_half last'>", '</div>') ,$text);	
		
		$text = str_replace( array('[one_third]','[/one_third]') , array('<div class="one_third">', '</div>') ,$text);
		
		$text = str_replace( array('[two_third]','[/two_third]') , array('<div class="two_third">', '</div>') ,$text);
		$text = str_replace( array('[one_third_last]','[/one_third_last]') , array("<div class='one_third last'>", '</div>') ,$text);	

		//$text = str_replace('[box type="info"]', '<div class=\"box shadow aligncenter\"><div>', $text);

		
		//$text = $this->emoticon( $text );
		
		return $text;
	}
	
	public function regex_list( $txt="", $type="" )
	{
		if ($txt == "")
		{
			return;
		}
		
		if ( $type == "" )
		{
			// Unordered list.
			
			return "<ul>".$this->regex_list_item($txt)."</ul>";
		}
		else
		{
			return "<ol type='$type'>".$this->regex_list_item($txt)."</ol>";
		}
	}
	
	public function regex_list_item($txt)
	{
		$txt = preg_replace( "#\[\*\]#", "</li><li>" , trim($txt) );
		
		$txt = preg_replace( "#^</?li>#"  , "", $txt );
		
		return str_replace( "\n</li>", "</li>", $txt."</li>" );
	}
			
	public function convert($text2){
		
	   	$text = strip_tags("$text2", '<br><b><u><i><font><span><a><img><div><ul><li>');
	   	
		$search[0] = "/\[url=([^<> \n]+?)\](.+?)\[\/url\]/i";
		$replace[0] = '<a href="\\1">\\2</a>';
		
		$search[1] = "#<font color=['\"](.+?)['\"]>(.+?)</font>#is";
		$replace[1] = '[color=\\1]\\2[/color]';
			
		$search[2] = "#<span style=['\"]font-size:(.+?)pt;line-height:100%['\"]>(.+?)</span>#is";
		$replace[2] = '[size=\\1]\\2[/size]';
	
		$search[3] = "#<img src=['\"](.+?)['\"]>#is";
		$replace[3] = '[img]\\1[/img]';
	
		$search[4] = "#<a href=['\"](.+?)['\"] target=['\"]_blank['\"]>(.+?)</a>#is";
		$replace[4] = '[url=\\1]\\2[/url]';
						
		$search[5] = "#<div id=['\"]QUOTE['\"]>(.+?)</div>#is";
		$replace[5] = '[quote]\\1[/quote]';
			
		$search[6] = "#<div id=['\"]CODE['\"]>(.+?)</div>#is";
		$replace[6] = '[code]\\1[/code]';
		
		$search[7] = '#<div align="(.+?)">(.+?)</div>#is';
		$replace[7] = '[\\1]\\2[/\\1]';
		
		$search[8] = "#<font face=['\"](.+?)['\"]>(.+?)</font>#is";
		$replace[8] = '[font=\\1]\\2[/font]';
		
		$text = preg_replace($search, $replace, $text);
		
		//Alignment Codes
		//$text = str_replace( array('<div align=\"center\">', '<DIV ALIGN=\"CENTER\">','</div>','</DIV>') , array('[center]','[CENTER]','[/center]','[/CENTER]') ,$text);
		//$text = str_replace( array('<div align=\"right\">', '<DIV ALIGN=\"RIGHT\">','</div>','</DIV>') , array('[right]','[RIGHT]','[/right]','[/RIGHT]') ,$text);
		//$text = str_replace( array('<div align=\"left\">', '<DIV ALIGN=\"LEFT\">','</div>','</DIV>') , array('[left]','[LEFT]','[/left]','[/LEFT]') ,$text);
		
		//Lines breaks, spacing
		$text = str_replace( array('<p>', '<P>','</p>','</P>') , array('[p]','[P]','[/p]','[/P]') ,$text);
		$text = str_replace( array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') , array('[tab]','[TAB]') ,$text);
		$text = str_replace( array('&nbsp;','&nbsp;','&nbsp;','&nbsp;') , array('[space]','[SPACE]','[s]','[S]') ,$text);
		$text = str_replace( array('<hr>', '<HR>') , array('[hr]','[HR]') ,$text);
		
		//Text formatting
		$text = str_replace( array('<b>', '<B>','</b>','</B>') , array('[b]','[B]','[/b]','[/B]') ,$text);
		$text = str_replace( array('<u>', '<U>','</u>','</U>') , array('[u]','[U]','[/u]','[/U]') ,$text);
		$text = str_replace( array('<i>', '<I>','</i>','</I>') , array('[i]','[I]','[/i]','[/I]') ,$text);
	
		//List
		$text = preg_replace( "#(\n){0,}<ul>#" , "\\1[list]"  , $text );
		$text = preg_replace( "#(\n){0,}<ol type='(a|A|i|I|1)'>#" , "\\1[list=\\2]\n"  , $text );
		$text = preg_replace( "#(\n){0,}<li>#" , "\n[*]"     , $text );
		$text = preg_replace( "#(\n){0,}</ul>(\n){0,}#", "\n[/list]\\2" , $text );
		$text = preg_replace( "#(\n){0,}</ol>(\n){0,}#", "\n[/list]\\2" , $text );
		$text = str_replace( "</li>", "", $text );
						
		// BR
		$text = str_replace( "<br />", "\n", $text );
		
		// (c) (r) and (tm)
		$text = str_replace( array('&copy;', '&#153;','&reg;') , array('(C)','(TM)','(R)') ,$text);

		return $text;
	}
	
	public function emoticon( $input )
	{
		global $CMS, $DB, $member;
		
		if ( $CMS->class->cache->check("emoticons") == 1 )
		{
			$icon = $CMS->class->cache->load("emoticons");
		}
		else
		{
			$DB->query("SELECT * FROM ".root_table."emoticons ORDER BY emo_typed DESC ");
			
			$icon = "";
			
			while ( $result = $DB->fetch_array() )
			{
				$icon .= "{$result['emo_typed']} {$result['emo_image']}\n";
			}
			
			$CMS->class->cache->save("emoticons", $icon);
		}
		
		$data = explode("\n", $icon);
		
		for ( $i = 0; $i < count($data); $i++ )
		{
			$data2 = explode(" ", $data[$i]); 
			
			if ( $data2[1] )
			{
				$input = " ".$input;
				$input = str_replace(" {$data2[0]}", " <img align='absmiddle' src='{$CMS->vars['public_url']}/emoticon/{$data2[1]}' />", $input );
				$input = str_replace(strtolower(" {$data2[0]}"), " <img align='absmiddle' src='{$CMS->vars['public_url']}/emoticon/{$data2[1]}' />", $input );
			}	
		}
		
		$input = trim($input);
	
		return $input;
	}
	
	public function rich_convert( $text2 )
	{
		$text2 = str_replace( "$", "&#036;", $text2 );
		$text2 = str_replace( "'", "&#39;", $text2 );
		$text2 = str_replace( "`", "&#96;", $text2 );
		//$text2 = addslashes($text2);
		//$text2 = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $text2 );
		
		//$text2 = eregi_replace('([[:space:]]|^)(www)', '\\1http://\\2', $text2); // no prefix (www.myurl.ext)
		//$prefix = '(http|https|ftp|telnet|news|gopher|file|wais)://';
		//$pureUrl = '([[:alnum:]/\n+-=%&:_.~?]+[#[:alnum:]+]*)';
		//$text2 = eregi_replace($prefix . $pureUrl, '<a href="\\1://\\2" target="_blank">\\1://\\2</a>', $text2);
		
		//$text2 = $this->xss_clean( $text2 );
		
		return $text2;
	}
	
	public function auto_link( $text2 )
	{
		$text2 = str_replace("<br />", " <br />", $text2);
		$text2 = eregi_replace('([[:space:]]|^)(www)', '\\1http://\\2', $text2); // no prefix (www.myurl.ext)
		$prefix = '(http|https|ftp|telnet|news|gopher|file|wais)://';
		$pureUrl = '([[:alnum:]/\n+-=%&:_.~?]+[#[:alnum:]+]*)';
		$text2 = eregi_replace($prefix . $pureUrl, '<a href="\\1://\\2" target="_blank">\\1://\\2</a>', $text2);
		
		return $text2;
	}
	
	public function substr( $string, $from = 0, $to = 40, $type = 0 )
	{
		global $CMS;
		
		if ( $type == 1 )
		{
			$string = str_replace("&nbsp;", "", $string);
		}
		
		// Check for string
		if ( strlen($string) < $to )
		{
			return $string;
		}
		
		// Continue		
		$output = substr($string, $from, $to);

		for ( $i = 0; $i < 20; $i++ )
		{
			$to += 1;
			
			if ( substr($output, -1) != " " )
			{
				$output = substr($string, $from, $to);
			}
			else
			{
				break;
			}
		}
		
		$output = substr($output, 0, strlen($output)-1);
		
		if ( substr($output, -1) == "." )
		{
			$output = substr($output, 0, strlen($output)-1);
		}
		
		//if ( strlen($output) > strlen($input) )
		if ( strlen($string) > $to )
		{
			$output .= "...";
		}
		
		return $output;
	}
	
	public function xss_clean( $input )
	{
		$output = $input;
	
		while( preg_match( "/[&|&amp\;]#x(\w+?);/i", $output ) )
		{
			$output = preg_replace( "/[&|&amp\;]#x(\w+?);/ies"		, "\$this->regex_bash_hex( '\\1' )" , $txt );
		}
		
		$output = preg_replace( "#&amp(?!\;)#", "", $output );
			
		return $output;
	}
	
	public function regex_bash_hex($hex_entity)
	{
		return html_entity_decode( "&#".hexdec( $hex_entity ).";" );
	}
	
	//===========================================================================
	//  WYSIWYG
	//===========================================================================
	
	public function show( $mode = 0 )
	{
		global $CMS;
		
		$this->html = $CMS->global->wysiwyg( $mode );
		
		return true;
	}
	
	public function simple()
	{
		global $CMS;
		
		$this->html = $CMS->global->wysiwyg(0);

		return $this->html;
	}
	
	public function advance()
	{
		global $CMS;
		
		$this->html = $CMS->global->wysiwyg(1);

		return $this->html;
	}
	
	public function quick()
	{
		global $CMS;
		
		$this->html = $CMS->global->wysiwyg(2);

		return $this->html;
	}

	public function input( $input_name, $type = "input" )
	{
		global $CMS, $DB;
 
		if ( $type == "input" )
		{
			$data = isset($_POST[$input_name]) ? $_POST[$input_name] : null;
		}
		else if ( $type == "text" )
		{
			$data = $input_name;
		}
		else
		{
			$data = $_POST[$input_name];
		}


		// Remove \n\r 30/06
		$data = preg_replace( "/\r|\n/", "", $data);
 
		$data = stripslashes($data);
		
		$data = str_replace("\\", "&#92;", $data);
		
		$data = stripcslashes($data); // Remove slashes

		$data = str_replace("'", "&#39;", $data); // This will help the SQL Query becomes safety

		//nkvp - fix lỗi khi nhập textarea vào tinymce
		$data = str_replace("<textarea", "&lt;textarea", $data);
		$data = str_replace("</textarea>", "&lt;/textarea&gt;", $data);

		// Check for custom type
		if ( $type == "username" ) //  OR preg_match("/(username)/", $input_name 
		{
			$data = trim(strip_tags($data));
		}
		else if ( $type == "textplain" )
		{
			$data = strip_tags($data);
		}

		return $data;
	}

	public function clean($string) {
	   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}


}

?>
