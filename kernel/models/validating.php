<?php

if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

$CMS->validating = new class_validating;

class class_validating {

	public $CMS = "";
	
	/**
	 * @param $html
	 *		The templates
	 */
	 
	public $html;
	
	//===========================================================================
	//  GET
	//===========================================================================
	
	public function get( $vid, $mod_name )
	{
		global $CMS, $DB;
		
		$DB->query("SELECT * FROM ".root_table."validating WHERE validate_id='{$vid}' AND module_name='{$mod_name}'");
		$validate = $DB->fetch_array();
		
		if ( $DB->num_rows() == 0 )
		{
			$validate['result'] = false;
		}
		else
		{
			$validate['result'] = true;
		}
		return $validate;
	}
	
	//===========================================================================
	//  INSRET
	//===========================================================================
	
	public function insert( $module_id, $module_name = "" )
	{
		global $CMS, $DB;
		
		$validate_id = $CMS->class->random->md5();
		
		// Delete
		$this->delete( $module_id, $module_name );
		
		$DB->query("INSERT INTO ".root_table."validating (validate_id, module_id, module_name, validate_time) VALUES ('{$validate_id}', '{$module_id}', '{$module_name}', '".time()."')");
		
		return $validate_id;
	}
	
	//===========================================================================
	//  DELETE
	//===========================================================================
	
	public function delete( $module_id, $module_name = "" )
	{
		global $CMS, $DB;

		$DB->query("DELETE FROM ".root_table."validating WHERE module_id='{$module_id}' AND module_name='{$module_name}'");
		
		return true;
	}
	
}

?>