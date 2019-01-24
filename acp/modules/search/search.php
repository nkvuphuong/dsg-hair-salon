<?php

$search = new search;
$search->auto_run();

class search {
	
	public function auto_run()
	{
		global $CMS, $DB, $member;
		
		// Title
		$CMS->core->page_title = "-> {$CMS->lang['search_header']}";

		// Default HTML
		$this->html = $CMS->class->template->load_template("skin_search");
		$data = array();
		
		// User Input
		$keyword = urldecode($CMS->class->filter->clean_value(trim($CMS->input["keyword"])));
		$_SESSION['search_keyword'] = $keyword;
		$module = $CMS->class->filter->clean_value(trim($CMS->input["module"]));
		
		// Wildcat
		$keyword = str_replace("%", "", $keyword);
		$keyword = str_replace(" ", "%", $keyword);
		$is_domainext = explode(".", $keyword);
		$is_domainext = count($is_domainext) > 1 AND $CMS->class->input->is_nan($is_domainext[count($is_domainext)-1]) ? true : false;

		// Location
		$CMS->input['location'] = "all";
		
		// Check for exactly keyword
		$in = array(
			"news" => false,
			"album" => false,
			"video" => false,
		);
		
		
			$in = array(
				"news" => true,
				"album" => true,
				"video" => true,
			);	
	
		// Movie
		if ( $CMS->permit['news_read'] == true AND $in['news'] == true)
		{
			$CMS->class->language->load("news");
			$CMS->news->loadhtml();
			$CMS->news->sql_add .= " news_name LIKE ('%{$keyword}%') AND ";
			$CMS->news->per_page = 15;
			$CMS->news->prefix_html ="?site=news";
			$CMS->news->show_page = "";

            $data = $CMS->news->listing();

			$data['news'] = $CMS->news->html($data);
			$data['news'] = $CMS->news->record_cnt > 0 ? $data['news'] : "";
		}
		
		// Movie
		if ( $CMS->permit['album_read'] == true AND $in['album'] == true)
		{
			$CMS->class->language->load("album");
			$CMS->album->loadhtml();
			$CMS->album->sql_add .= " album_name LIKE ('%{$keyword}%') AND ";
			$CMS->album->per_page = 10;
			$CMS->album->listing();
			$CMS->album->show_page = "";
			$data['album'] = $CMS->album->html();
			$data['album'] = $CMS->album->record_cnt > 0 ? $data['album'] : "";
		}
		
		// Movie
		if ( $CMS->permit['video_read'] == true AND $in['video'] == true)
		{
			$CMS->class->language->load("video");
			$CMS->video->loadhtml();
			$CMS->video->sql_add .= " video_name LIKE ('%{$keyword}%') AND ";
			$CMS->video->per_page = 10;
			$CMS->video->listing();
			$CMS->video->show_page = "";
			$data['video'] = $CMS->video->html();
			$data['video'] = $CMS->video->record_cnt > 0 ? $data['video'] : "";
		}
	
		// Convert HTML
		$output = "";
		$data_new = array(); // Temp
		$data_array = array_keys($data);
		
		// Smart result
		if ( $module )
		{
			$output .= $data[$module];
			$data_new[$module] = $module; // Temp
		}
		
		// Other result		
		for ( $i = 0; $i < count($data); $i++ )
		{
			if ( !in_array($data_array[$i], $data_new) AND $data[$data_array[$i]] )
			{
				$output .= $data[$data_array[$i]];
				$data_new[$data_array[$i]] = 1; // Temp
			}
		}
	
		// Error
		if ( ! $output )
		{
			$CMS->errormsg .= $CMS->lang['no_result'];
		}
	
		// Print data
		$CMS->output .= $this->html->index( $output );
	}
}
	
?>