<?php

namespace lib;

use core\ezy;

$CMS->class->page = new page;

class page
{
	public $maxpage = 0;
	public $type = 0;
	public $sql_query = "";
	public $max_rows = 0;
	public $get = 0;
    public $total_row = 0;

    public $page_module_search;

    public $page_type = "";

    // New var
    static public $page_distance = 2;

	public function create(  $sql_query, $max_page = 20, $prefix = "", $suffix = "", $is_ajax = 0, $scroll = "", $ajax_id = "module_baiviet", $type = 0, $num_page_count=4)
    {
        global $CMS, $DB, $member;

        $this->sql_query = $sql_query;

        if ( ! $prefix )
        {
            $prefix = $CMS->class->input->geturl("page");
            $prefix = str_replace("{$CMS->class->search->url_return}", "", $prefix);
            $prefix .= $CMS->class->search->url_return;
            $prefix .= "&page=";
        }

        $prefix = preg_match("/(&module_pagename={$this->page_module_search})/", $prefix) ? str_replace("&module_pagename={$this->page_module_search}","",$prefix) : $prefix;
        //
        $suffix = $this->page_module_search ? "&module_pagename=".$this->page_module_search.$suffix : $suffix;

        if ( $suffix )
        {
            $suffix = str_replace("{$CMS->class->search->url_return}", "", $suffix);
        }

        $show_page = "";

        $page = isset($CMS->input["page"]) ? intval($CMS->input["page"]) : 1;
        $page = $this->get != 0 ? $this->get : $page;

        // Fix this one for mod rewrite - lhl
        if ( $page == 0 )
        {
            $page = 1;
        }
 
        if($this->max_rows > 0)
        {
            $db_num_rows = $DB->num_rows($DB->query(str_replace("SELECT * FROM", "SELECT 0 FROM","{$sql_query} LIMIT {$this->max_rows}")));
        }
        else
        { 
            $db_num_rows = $DB->num_rows($DB->query(str_replace("SELECT * FROM", "SELECT 0 FROM", $sql_query)));
        }
        $this->total_row = $db_num_rows;
        //$this->max_rows = $db_num_rows = $this->max_rows ? $this->max_rows : $db_num_rows;

        $startrow = ($page - 1) * $max_page;

        $show_sql = $DB->query("{$sql_query} LIMIT {$startrow}, {$max_page}");

        // HTML
        $CMS->page_start = ($page-1)*$max_page;
        $CMS->page_end = $page*$max_page;

        $page_count = 0;
        $num_page = ceil( $db_num_rows / $max_page );

        if ($num_page > 1)
        {
            if ($page > 6)
            {
                if ( ($page - 6 ) > $page_count )
                {
                    if ( $scroll == 1 )
                    {
                        if($this->page_type == "customer")
                        {
                            $show_page .= "<li><a style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}1{$suffix}', 'GET'); return false;\" title='Trang: 1'>&laquo; {$CMS->lang['first_page']}</a></li>";
                        }
                        else
                        {
                            $show_page .= "<li><a page='1' class='page-link' style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}1{$suffix}', 'GET'); return false;\" title='Trang: 1'>&laquo; {$CMS->lang['first_page']}</a></li>";
                        }
                    }
                    else if ( $is_ajax == 1 )
                    {
                        if ( $type == 1 OR $this->page_type == "customer")
                        {
                            $show_page .= "<li><a href=\"{$prefix}1{$suffix}\" title='Trang: 1'>&laquo; {$CMS->lang['first_page']}</a></li>";
                        }
                        else
                        {
                            $show_page .= "<li><a href=\"{$prefix}1{$suffix}\" title='Trang: 1'>&laquo; {$CMS->lang['first_page']}</a></li>";

                        }
                    }else if ( $this->type == "campaigns" )
					{
						$show_page .= "<li class='page-item'><a class='page-link' href=\"{$CMS->vars['root_domain']}/{$prefix}1{$suffix}\" title='Trang: 1'>&laquo; </a></li>";
					}
                    elseif( $type == 2 )
                    {
                        $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}1{$suffix}\" title='Trang: 1'>&#139;&#139;</a></li>";
                    }
                    else
                    {
                        $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}1{$suffix}\" title='Trang: 1'>&laquo; {$CMS->lang['first_page']}</a></li>";
                    }
                }
            }

            for ( $i = 1; $i < $num_page + 1; $i++ )
            {
                if ($i == $page) 
                {
                    if ( $type == 1 )
                    {
                        $show_page .= "<li class='current'>{$i}</li>";
                    }
                    elseif( $type == 2 )
                    {
                        $show_page .= "<li class='active'><a href=\"{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a></li>";
                    }
                    elseif ( $this->type == "campaigns" )
					{
						$show_page .= "<li class='page-item active'><a class='page-link' page='{$i}'>{$i}</a></li>";
					}
                    elseif( $this->page_type == "web")
                    {
                        $show_page .= "<font class='block_page'>{$i}</font>";
                    }
                    else
                    { 
                        $show_page .= "<li class=\"active\"><a>{$i}<span class=\"sr-only\">(current)</span></a></li>";
                    }
                }
                else if ( ( $i < ( $page - $num_page_count ) ) OR ( $i > ( $page + $num_page_count ) ) )
                {
                    $show_page .= "";
                }
                else
                {
                    if ( $scroll == 1 )
                    {

                        if ( $this->page_type == "customer"  )
                        {
                            $show_page .= "<li><a style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}', 'GET'); return false;\" title='Trang: {$i}'>{$i}</a></li>";
                        }
                        if ( $this->page_type == "web"  )
                        {
                            $show_page .= "<a style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}', 'GET'); return false;\" title='Trang: {$i}'>{$i}</a>";
                        }
                        else
                        {
                            $show_page .= "<li><a page='{$i}' class='page-link' style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}', 'GET'); return false;\" title='Trang: {$i}'>{$i}</a></li>";

                        }
                    }
                    else if ( $is_ajax == 1 )
                    {
                        if ( $type == 1  )
                        {
                            $show_page .= "<li><a href=\"{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a></li>";
                        }
                        else
                        {
                            $show_page .= "<li><a href=\"{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a><li>";
                        }
                    }
                    elseif ( $type == 1 OR ($this->page_type == "customer" AND  $scroll != 1))
                    {
                        $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a></li>";
                    }
                    elseif($type == 2)
                    {
                        $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a></li>";
                    }
                    elseif ( $this->page_type == "web" )
                    {
                        $show_page .= "<a href=\"{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a>";
                    }else if ( $this->type == "campaigns" )
					{
						$show_page .= "<li class='page-item'><a page='{$i}' class='page-link' href=\"{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a></li>";
					}
                    else
                    {
                        $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}{$i}{$suffix}\" title='Trang: {$i}'>{$i}</a></li>";
                    }
                }

                $page_count++;
            }

            $raquo = $page + 1;

            if ( ($page + $num_page_count ) < $page_count )
            {
                if ( $scroll == 1 )
                {
                    if($this->page_type == "customer")
                    {
                        $show_page .= "<li><a style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}{$page_count}{$suffix}', 'GET'); return false;\" title='Trang: {$page_count}'>{$CMS->lang['last_page']} &raquo;</a></li>";
                    }
                    else
                    {
                        $show_page .= "<li><a page='{$page_count}' class='page-link' style='cursor:pointer' onclick=\"data_request('{$ajax_id}', '{$CMS->vars['root_domain']}/{$prefix}{$page_count}{$suffix}', 'GET'); return false;\" title='Trang: {$page_count}'>{$CMS->lang['last_page']} &raquo;</a></li>";
                    }
                }
                else if ( $is_ajax == 1 )
                {
                    if ( $type == 1  )
                    {
                        $show_page .= "<li><a href=\"{$prefix}{$page_count}{$suffix}\" title='Trang: {$page_count}'>{$CMS->lang['last_page']} &raquo;</a></li>";
                    }
                    else
                    {
                        $show_page .= "<li><a href=\"{$prefix}{$page_count}{$suffix}\" title='Trang: {$page_count}'>{$CMS->lang['last_page']} &raquo;</a></li>";
                    }
                }
                else if ( $type == 1 || $type == 2 )
                {
                    $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}{$page_count}{$suffix}\" title='Trang: {$page_count}'>&#155;&#155;</a></li>";
                }else if ( $this->type == "campaigns" )
				{
					$show_page .= "<li class='page-item'><a page='{$page_count}' class='page-link' href=\"{$CMS->vars['root_domain']}/{$prefix}{$page_count}{$suffix}\" title='Trang: {$page_count}'> &raquo;</a></li>";
				}
                else 
                {
                    $show_page .= "<li><a href=\"{$CMS->vars['root_domain']}/{$prefix}{$page_count}{$suffix}\" title='Trang: {$page_count}'>{$CMS->lang['last_page']} &raquo;</a></li>";
                }
            }
        }

        $this->maxpage = $page_count;

        if ( $show_page )
        {
            if ( $type == 1 )
            {
                $show_page = "<div class='knowledge-pager'><ul class='pagination pull-right'><li class='page-text'>Trang:</li>{$show_page}<div class='clr'></div></ul></div>";
            }
            elseif($type == 2)
            {
                $show_page = "<div class='pagination pagination-right'><ul class='pagination pull-right'>{$show_page}<div class='clr'></div></ul></div>";
            }
            elseif (  $this->page_type == "customer")
            {
                $show_page = "<nav class='pagging'><ul class='pagination pull-right'>{$show_page}</ul></nav>";
            }
            elseif ( $this->type === "campaigns" )
			{
				$show_page = "<ul class=''>{$show_page}</ul>";
			}
            else
            {
                $show_page = "<div id='block_page'><ul class='pagination pull-right'>{$show_page}</ul></div>";
            }
        }

        // Output
        $data = array($show_page, $show_sql);

        return $data;
    }

    /**
     * Init paging
     * @param $sql
     * @param $limit
     * @param $cache_prefix: without md5($sql)
     * @param bool $paging
     */

    static public function init($sql, $limit = 20, $paging = false, $cache_prefix = '')
    {
        global $DB;

        $use_cache = $cache_prefix ? 1 : 0;

        // Not limit or paging if $limit unvailable
        if ( !$limit OR $limit <= 0 )
        {
            return $DB->fetch_data($sql, $cache_prefix, $use_cache);
//            return $DB->query("{$sql}");
        }

        return $paging == true ? self::generate($sql, $limit, $cache_prefix) : $DB->fetch_data("{$sql} LIMIT {$limit}", $cache_prefix, $use_cache);
//        return $paging == true ? self::generate($sql, $limit, $cache_prefix) : $DB->query("{$sql} LIMIT {$limit}");
    }

    /**
     * Generate paging's data
     * @param $sql_query
     * @param int $max_page
     * @param $cache_prefix: without md5($sql)
     * @return array
     */

    static public function generate( $sql_query, $limit_per_page , $cache_prefix = '')
    {
        global $DB, $CMS;

        //define cache
        $use_cache = $cache_prefix ? 1 : 0;
        // Check page input
        $current_page = isset(ezy::$input['page']) ? intval(ezy::$input['page']) : 1;
        $current_page = $current_page > 0 ? $current_page : 1;

        $limit_per_page =  intval($limit_per_page) ? intval($limit_per_page) : 20;

        $cache_key_hash = md5($sql_query.'_'.$limit_per_page.'_'.$current_page);
        $cache_key = $cache_prefix.'.listing.'.$cache_key_hash;
        $cache_key_total_row = $cache_prefix.'.listing.total_row.'.$cache_key_hash;


        // SQL Raw
        ezy::$page['sqlraw'] = $sql_query;

        // Declare outpuit
        $output = [];

        // Get total rows
        if($use_cache && $CMS->class->cache->check($cache_key_total_row))
        {
            $db_num_rows = $CMS->class->cache->load($cache_key_total_row);
        }
        else
        {
            $DB->query(str_replace("SELECT * FROM", "SELECT 0 FROM", $sql_query)); // This will fix slow query
            $db_num_rows = $DB->num_rows();

            if($use_cache)
            {
                //save cache
                $CMS->class->cache->save($cache_key_total_row, $db_num_rows);
            }
        }


        // Set total rows, ex: Click here to see xxx results
        ezy::$page['rows'] = $db_num_rows;

        // Load pages
        $startrow = ($current_page - 1) * $limit_per_page;
        $limit_per_page = $limit_per_page <= 0 ? $db_num_rows : $limit_per_page;

        // Query
        $sql =  "{$sql_query} LIMIT {$startrow}, {$limit_per_page}";

        ezy::$page['sql'] = [];

        if($use_cache && $CMS->class->cache->check($cache_key))
        {
            ezy::$page['sql'] = $CMS->class->cache->load($cache_key);
        }
        else
        {
            $query = $DB->query($sql);
            while($rs = $DB->fetch_assoc($query))
            {
                ezy::$page['sql'][] = $rs;
            }

            if($use_cache)
            {
                //save cache
                $CMS->class->cache->save($cache_key, ezy::$page['sql']);
            }
        }

        // Total page
        $total_page = ceil( $db_num_rows / $limit_per_page );

        // Generate page when total_page over than 1
        if ($total_page > 1)
        {
            // Go back to first page (page 1)
            if ( $current_page - self::$page_distance > 0 )
            {
                $output[1] = array("page" => 1, "status" => "first");
                $page_start = $current_page - self::$page_distance;
            }
            else{
                $page_start = 1;
            }

            // Go back to last page (page end)
            if ( $current_page + self::$page_distance < $total_page )
            {
                $lastpage = true;
                $page_end = $current_page + self::$page_distance;
            }
            else{
                $page_end = $total_page;
            }

            // Middle pages
            for ( $i = $page_start; $i <= $page_end; $i++ )
            {
                // Current page
                if ($i == $current_page)
                {
                    $output[$i] = array("page" => $i, "status" => "active");
                }
                // Normal page
                else
                {
                    $output[$i] = array("page" => $i, "status" => "normal");
                }
            }

            // Check for lastpage
            if ( isset($lastpage) )
            {
                $output[$total_page] = array("page" => $total_page, "status" => "last");
            }
        }

        // Output data
        ezy::$page['data'] = $output;

        return ezy::$page['sql'];
    }
}

?>