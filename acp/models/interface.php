<?php

namespace models;

use core\ezy;
use lib\input;
use \lib\template;

class interface_editor
{
    /**
     * Load Template Tree
     * Path: /web/views/xxx/tpl.yyy.html.php
     * @return array
     */

    static public function loadTemplateTree()
    {
        global $CMS;

        $data = input::scandir(root_path.ezy::$web_views, ".html.php", "tpl.");
 
        $output = [];

        // Check data again
        foreach ($data as $folder => $files)
        {
            $output2 = [];

            foreach ($files as $file) {
                $edited = file_exists(ezy::getTemplatePath($folder, explode(".",$file)[1], true)) ? " (Edited)" : "";

                // Create option  for optgroup
                $output2[$folder."-".explode(".",$file)[1]] = str_replace("_", " ",ucfirst(explode(".",$file)[1]).$edited); // Ex: about-content = Content
//                $output2[$folder."-".explode(".",$file)[1]] = $CMS->lang['menu-'.$folder."-".explode(".",$file)[1]];
            }

            // Replace some familiar text
            $fa_from = array("Board");
            $fa_to = array($CMS->lang['int_homepage']);

            // Create optgroup
            $output[str_replace($fa_from, $fa_to, ucfirst($folder))] = $output2; // Ex: About
//            $output[$CMS->lang['menu-group-'.$folder]] = $output2;
        }

        return $output;
    }

    /**
     * Load Template Content
     * @return mixed
     */

    static public function loadTemplateContent($folder, $file, $lang='')
    {
        global $CMS;

        $urlcustomize = ezy::getTemplatePath($folder, $file, true, $lang);

        if ( file_exists($urlcustomize) == true )
        {
            $url = $urlcustomize;
        }
        // If not, try to load original file
        else{
            $url = ezy::getTemplatePath($folder, $file);
        }

        // If not exist
        if ( !file_exists($url) )
        {
            exit("Error loading file");
        }

        // Load content
        $output = file_get_contents($url);
        // Convert some php vars
        $output = template::convertPhpTpl($output);
        // $output = str_replace("&#39;", "'", $output);

        // Convert src image
        $srcAlt = "{$CMS->vars['parent_domain']}/themes/{$CMS->vars['theme']}/assets/assets/";
        $output = str_replace(['src="assets/', 'src=\'assets/', 'src="/acp/assets/' , 'src=\'/acp/assets/'],["src=\"{$srcAlt}", "src='{$srcAlt}","src=\"{$srcAlt}", "src='{$srcAlt}"], $output);

        $srcAlt = "{$CMS->vars['parent_domain']}/themes/{$CMS->vars['theme']}/assets/images/";
        $output = str_replace(['src="images/', 'src=\'images/', 'src="/acp/images/' , 'src=\'/acp/images/'],["src=\"{$srcAlt}", "src='{$srcAlt}","src=\"{$srcAlt}", "src='{$srcAlt}"], $output);

        return $output;
    }

    /**
     * Load Template Content
     * @return mixed
     */

    static public function loadTemplateOriginal($folder, $file)
    {
        global $CMS;

        $url = ezy::getTemplatePath($folder, $file);

        // If not exist
        if ( !file_exists($url) )
        {
            exit("Error loading file");
        }

        // Load content
        $output = file_get_contents($url);

        // Convert some php vars
        $output = template::convertPhpTpl($output);

        // Convert src image
        $srcAlt = "{$CMS->vars['parent_domain']}/themes/{$CMS->vars['theme']}/assets/assets/";
        $output = str_replace(['src="assets/', 'src=\'assets/', 'src="/acp/assets/' , 'src=\'/acp/assets/'],["src=\"{$srcAlt}", "src='{$srcAlt}","src=\"{$srcAlt}", "src='{$srcAlt}"], $output);

        $srcAlt = "{$CMS->vars['parent_domain']}/themes/{$CMS->vars['theme']}/assets/images/";
        $output = str_replace(['src="images/', 'src=\'images/', 'src="/acp/images/' , 'src=\'/acp/images/'],["src=\"{$srcAlt}", "src='{$srcAlt}","src=\"{$srcAlt}", "src='{$srcAlt}"], $output);

        return $output;
    }

    /**
     * Save template content
     * @param $folder
     * @param $file
     * @param $content
     */

    static public function saveTemplateContent($folder, $file, $content, $revert = false, $lang='')
    {
        global $CMS;

        $edit_theme_path = "{$CMS->vars['upload_dir']}/views/".ezy::$web_theme;

        // Check folder exist
        if ( file_exists($edit_theme_path) == false )
        {
            if ( !@mkdir($edit_theme_path,0777, true) )
            {
                return "permssion";
            }
        }

        $original = ezy::getTemplatePath($folder, $file);

        // Prevent they use invalid file
        if ( !file_exists( $original ) )
        {
            return false;
        }

        // Continue
        $url = ezy::getTemplatePath($folder, $file, true, $lang, 'set');

        // Revert some php vars
        $content = template::convertTplPhp($content);
        // $content = str_replace("'", "&#39;", $content);
        $time = date("Y/m/d h:i", time());
        // Compare text, if it's the same, remove the custom files
        if ( $revert == true )
        {
            // Lưu log revert
            $CMS->class->logs->insert("You have reverted the \"{$folder}/tpl.{$file}.html.php\" ({$lang}) file at {$time}");
            unlink($url);
            // check revert file theo ngôn ngữ tanlv 14/11/2017
            if($CMS->vars['translations'])
            {
                $lang = $lang ? $lang : $CMS->vars['default_language'];
                $name_file = str_replace("-{$lang}.html.php", ".html.php", $url);
            
                if(file_exists($name_file))
                {
                    @unlink($name_file);
                }
            }
            else
            {
                foreach ($CMS->vars['default_language_data'] as $langCode => $langName)
                {
                    if(!is_numeric($langCode))
                    {
                        $name_file = str_replace(".html.php", "-{$langCode}.html.php", $url);
                    
                        if(file_exists($name_file))
                        {
                            @unlink($name_file);
                        }
                    }
                }
            }

            return "revert";
        }

        // Lưu log edit
        $CMS->class->logs->insert("You have edited the \"{$folder}/tpl.{$file}.html.php\" ({$lang}) file at {$time}", $content);
        // Decode html
        if(!in_array($file, array("script", "js", "javascript")))
        {
            $content = html_entity_decode($content);
        }
        // Save content
        file_put_contents($url, $content);

        return "save";
    }
}