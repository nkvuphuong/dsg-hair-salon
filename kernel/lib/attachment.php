<?php

$CMS->class->attachment = new class_attachment;

class class_attachment {
	
	//===========================================================================
	//  Check extension
	//===========================================================================
	
	public function check_ext( $file_ext = "" )
	{
		global $CMS;
	
		if ( strip_tags($CMS->vars['upload_allow_ext']) AND ! in_array($file_ext, explode(",", trim(strip_tags($CMS->vars['upload_allow_ext'])))) )
		{
			return false;
		}
		else
		{
			if ( preg_match( "/\.(cgi|pl|asp|php|jsp|jar|mp3|wma|mpg|mpeg|asf|asx|avi|wmv|rm|ram|htaccess)/", $file_ext ) )
			{
				return false;
			}
		}
		
		return true;
	}
	
	//===========================================================================
	//  CHECK IS IMAGE
	//===========================================================================
	
	public function is_image( $file_name )
	{
		$file_name = strtolower( $file_name );

		if ( preg_match( "/\.(jpg|jpeg|png|gif|swf|flv)/", $file_name ) )
		{
			return true;
		}
		
		return false;
	}
	
	//===========================================================================
	//  Clean name
	//===========================================================================
	
	public function clean_name( $input )
	{
		$output = str_replace(array(" ", "'"), array("_", ""), $input );
		
		return $output;
	}
	
	//===========================================================================
	//  GET FILE EXTENSION
	//===========================================================================
	
	public function get_ext( $file_type, $is_image = 0 )
	{
		$file_ext = strtolower( str_replace( ".", "", substr( $file_type, strrpos( $file_type, '.' ) ) ) );
		
		if ( $is_image == 1 )
		{
			if ( ! in_array(strtolower($file_ext), array("jpg", "jpeg", "gif", "png")) )
			{
				$file_ext = "jpg";
			}
		}
				
		return $file_ext;
	}
	
	//===========================================================================
	//  CHANGE FILE NAME
	//===========================================================================
	
	public function change_filename( $id, $name, $fid, $fname, $ext, $location, $url, $size, $dir )
	{
		global $CMS, $DB;
		
		$DB->query("SELECT * FROM ".root_table."{$name} WHERE {$fid}='{$id}'");
		$result = $DB->fetch_array();
	     
	     if ( ! $result["{$url}"] )
		{
			if ( $result["{$location}"] != $name."_".$result["{$fid}"]."_".$result["{$size}"].".".$result["{$ext}"] )
			{
				if ( $result['folder'] )
				{
					$dir .= "/{$result['folder']}";
				}
						
				$file = $CMS->vars['upload_dir']."/{$dir}/". $result["{$location}"];
				$new_file = $name."_".$result["{$fid}"]."_".$result["{$size}"].".".$result["{$ext}"];
				$file2 = $CMS->vars['upload_dir']."/{$dir}/". $new_file;
						
				@rename( $file, $file2) or die ("Kh&#244;ng th&#7875; rename file.");
				$DB->query("UPDATE ".root_table."{$name} SET {$location}='{$new_file}' WHERE {$fid}='{$id}'");
			}
		}
	}
	
	//===========================================================================
	//  GET FOLDER NAME
	//===========================================================================
	
	public function get_foldername( $data )
	{
		if ( $data < 10000 )
		{
			$folder = substr( $data, 0, 1 );
		}
		else
		{
			$folder = substr( $data, 0, 2 );
		}
		
		return $folder;
	}
	
	//===========================================================================
	//  IS FILE
	//===========================================================================
	
	public function is_file( $file )
	{
		if ( substr($file, strrpos($file, ".")) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//===========================================================================
	//  Download
	//===========================================================================
	
	public $allowed_referer = "";
	
	public $file_path = "";
	
	public $file_url = "";
	
	public $file_size = 0;
	
	public $file_name = "";
	
	public $file_mimetype = "";
	
	/*public function process( $id, $name, $fid, $fname, $ext, $location, $url, $size, $dir, $sql )
	{
		global $CMS, $DB;

		$DB->query("SELECT * FROM ".root_table."{$name} WHERE {$fid}='{$id}'");
		$result = $DB->fetch_array();
			     
		if ( $DB->num_rows() == 0 )
	     {
	           $CMS->output .= $CMS->global->page_error("&#272;&#432;&#7901;ng d&#7851;n m&#224; b&#7841;n v&#224;o &#273;&#227; b&#7883; x&#243;a b&#7887; ho&#7863;c kh&#244;ng t&#7891;n t&#7841;i !");
	           
	           return false;
	     }
	     
	     if ( $sql )
		{
			$DB->query("{$sql}");
		}
	     
	     if ( ! $result["{$url}"] )
		{
			if ( $result["{$location}"] != $name."_".$result["{$fid}"]."_".$result["{$size}"].".".$result["{$ext}"] )
			{
				if ( $result['folder'] )
				{
					$dir .= "/{$result['folder']}";
				}
				
		     	$file = $CMS->vars['upload_dir']."/{$dir}/". $result["{$location}"];
		     	$new_file = $name."_".$result["{$fid}"]."_".$result["{$size}"].".".$result["{$ext}"];
		     	$file2 = $CMS->vars['upload_dir']."/{$dir}/". $new_file;
				
				@rename( $file, $file2) or die ("Kh&#244;ng th&#7875; rename file.");
				$DB->query("UPDATE ".root_table."{$name} SET {$location}='{$new_file}' WHERE {$fid}='{$id}'");
				
				$CMS->global->redirect("{$CMS->vars['root_domain']}/{$name}/get/{$id}/");
				
				exit;
			}
		
			if ( $result['folder'] )
			{
				$dir .= "/{$result['folder']}";
			}

	     	$file = $CMS->vars['root_domain']."/".$CMS->vars['upload_dirname']."/{$dir}/". $result["{$location}"];

			$CMS->global->redirect("{$file}");

		    $DB->query("SELECT * FROM ".root_table."mimetypes WHERE extension='". $result["{$ext}"] ."'");
			$atype = $DB->fetch_array();
	
			header( "Content-Type: ". $atype['mimetype'] );
			header( "Content-Disposition: inline; filename=\"".$result["{$fname}"]."\"" );
			header( "Content-Length: ".$result["{$size}"] );

			//@readfile( $file );
	
			$fh = fopen( $file, 'rb' ); 
			fpassthru( $fh );
			@fclose( $fh );

			exit();
	     }
	     else
	     {
	     	$file = $result["{$url}"];

	     	$CMS->global->redirect("{$file}");

			exit;
	     }
	}
	*/

	public function check_is_image($file_url='')
	{
		$arr_value = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF, IMAGETYPE_PSD, IMAGETYPE_BMP, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM, IMAGETYPE_JPC, IMAGETYPE_JP2, IMAGETYPE_JPX, IMAGETYPE_JB2, IMAGETYPE_SWC, IMAGETYPE_IFF, IMAGETYPE_WBMP, IMAGETYPE_XBM, IMAGETYPE_ICO);
		$check_file = exif_imagetype($file_url);
		if(in_array($check_file, $arr_value)) 
		{
			return true;
		}else
		{
			return false;
		}
	}
}