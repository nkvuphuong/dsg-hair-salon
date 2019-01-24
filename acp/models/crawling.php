<?php
/**
 * Created by PhpStorm.
 * User: Phuong3F
 * Date: 5/23/2017
 * Time: 9:44 AM
 */

namespace models;

use lib\date;
use lib\input;
use lib\db;
use \lib\template;

class crawling
{
    /**
     * Crawling from specify url
     * @param $src
     * @return array
     */
    static public function getSEO($src, $type='url')
    {
        global $CMS, $member, $DB;

        $result = [];
        $status = '';
        $msg = '';
        $data = [];

        if($src)
        {
            if($type == 'url')
            {
                $html_dom =  \lib\html_dom::file_get_html($src);
            }
            else
            {
                $html_dom =  \lib\html_dom::str_get_html($src);
            }

            if($html_dom)
            {
                $nodes = $html_dom->find("title");
                $data['tag']['title'] = !empty($nodes[0]->innertext) ? $nodes[0]->innertext : '';

                $nodes = $html_dom->find("meta[name]");
                foreach($nodes as $node)
                {
                    if($node->name == 'google-site-verification')
                    {
                        $data['name'][$node->name] = $node->outertext;
                    }
                    else
                    {
                        $data['name'][$node->name] = !empty($node->content) ? $node->content : '';
                    }
                }

                $nodes = $html_dom->find("meta[property]");
                foreach($nodes as $node)
                {
                    $data['property'][$node->property] = !empty($node->content) ? $node->content : '';
                }
            }

            if(!$data)
            {
                $status = 'fail';
                $msg = 'No data';
            }
            else
            {
                $status = 'ok';
            }
        }
        else
        {
            $status = 'fail';
            $msg = 'Incompleted source';
        }

        $result['status'] = $status;
        $result['data'] = $data;
        $result['msg'] = $msg;

        return $result;
    }
}