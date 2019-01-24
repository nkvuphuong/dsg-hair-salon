<?php

$tags = new tags;
$tags->auto_run();

class tags {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================
	
	public function auto_run()
	{
		global $CMS, $DB, $member;

		// Title
		$CMS->core->page_title = "-> {$CMS->lang['tags_header']}";

		// Load the models
		$CMS->tags->auto_run();

		// Switch
		switch( $CMS->input["act"] )
		{
			default:
				$this->page_default();
			break;
		}
	}


	
	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================
	
	public function page_default()
	{
		global $CMS, $DB, $member;

        switch( \lib\input::get('subact') )
        {
            case 'load_autocomplete':
                $this->loadAutocomplete();
                break;
            default:
        }

		// Get List
		$CMS->tags->listing();
		
		// Write data
		$CMS->output .= $CMS->tags->html();
	}

	function loadAutocomplete()
    {
        global $CMS;

        $CMS->input['q'] = urldecode($CMS->input['q']);
        $module = $CMS->input['module'];
        $data = $CMS->tags->getTagsByKeyword($CMS->input['q'], $module);

        $return = [];

        if($data)
        {
            foreach ($data as $result)
            {
                $return[] = [
                    'id' => $result['tag_name'],
                    'text' => $result['tag_name'],
                ];
            }
        }
        else
        {
            $return[] = ['id' => $CMS->input['q'], 'text' => $CMS->input['q']];
        }

        header('Content-Type: application/json');
        echo @json_encode($return); exit;
    }
}

?>