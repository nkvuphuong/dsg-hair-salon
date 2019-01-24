<?php

namespace controller;

use \core\ezy;
use \lib\input;

ezy::load_model("interface");

new interface_editor;

class interface_editor {
	
	public function __construct()
	{
		global $CMS, $DB, $tpl;
 
		// Title
		ezy::$title = "-> {$CMS->lang['header_home']}";

        // Main switch
        switch ( $CMS->input['act'] )
        {
            case "show":
                self::load_template();
                break;
            case "edit":
                self::save_template();
                break;
            default:
                self::default_page();
                break;
        }
	}

    /**
     * Default page
     */

	static private function default_page()
    {
        global $CMS, $tpl;

        // Load data
        $tpl->dateTemplateTree = \models\interface_editor::loadTemplateTree();
        // List page
        $tpl->listPage = $CMS->pages->getListPage();
        $CMS->output .= ezy::html();
    }

    /**
     * Load Template
     */

    static private function load_template()
    {
        global $CMS;

        // Clean input
        list($folder, $file) = explode("-", $CMS->class->filter->clean_value($CMS->input['file']));

        // Load content
        if($CMS->vars['translations'])
        {
            //Check multi lang
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $editable[$langCode] =\models\interface_editor::loadTemplateContent($folder, $file, $langCode);
            }
        }
        else
        {
            $editable = \models\interface_editor::loadTemplateContent($folder, $file);
        }

        $original = \models\interface_editor::loadTemplateOriginal($folder, $file);

        echo json_encode(array("htmlEditable" => $editable, "htmlOriginal" => $original));

        exit;
    }

    /**
     * Save template
     */

    static private function save_template()
    {
        global $CMS;

        // Clean input
        list($folder, $file) = explode("-", $CMS->class->filter->clean_value($CMS->input['file']));

        // Revert
        $revert = isset($CMS->input['revert']) ? ($CMS->input['revert'] == "true" ? true : false) : false;

        // Save content
        $result = \models\interface_editor::saveTemplateContent($folder, $file, $CMS->class->editor->input('content'), $revert, $CMS->input['lang']);

        // Revert
        if ( $result == "revert" )
        {
            echo $CMS->lang['int_notify_reverted'];
        }
        // Save
        else if ( $result == "save" )
        {
            echo $CMS->lang['int_notify_success'];
        }
        // Permission
        else if ( $result == "permission" )
        {
            echo $CMS->lang['int_notify_permission'];
        }
        // Failed
        else{
            echo $CMS->lang['int_notify_failed'];
        }
        exit;
    }
}
	
?>