<?php

$transaction_terms = new transaction_terms;
$transaction_terms->auto_run();

class transaction_terms {

	//===========================================================================
	//  AUTO RUN
	//===========================================================================

	public function auto_run()
	{
		global $CMS, $DB, $member;

		// Title
		$CMS->core->page_title = "-> {$CMS->lang['header']}";

		// Load the models
		$CMS->transaction_terms->auto_run();

		// Switch
		switch( $CMS->input["act"] )
		{
			case "show":
				$this->show();
				break;
			case "add":
				$this->add();
				break;
			case "add_do":
				$this->add_do();
				break;
			case "edit":
				$this->edit();
				break;
			case "edit_do":
				$this->edit_do();
				break;
			case "search":
				$this->search();
				break;
			case "search_do":
				$this->search_do();
				break;
			case "delete":
				$this->delete();
				break;
			case "delete_all":
				$this->delete_all();
				break;
			default:

                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete('transaction_terms');
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }

			    if(\lib\input::get('subact') == 'ajax_get_data_transaction_terms')
                {
                    $this->ajax_get_data_transaction_terms();
                }

                if(\lib\input::get('subact') == 'dataAjax')
                {
                    $this->dataAjax();
                }

				$this->page_default();
				break;
		}
	}

	//===========================================================================
	//  SHOW INFO
	//===========================================================================

	public function show()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->transaction_terms->html->show( $CMS->transaction_terms->convertvalue($CMS->transaction_terms->get_info()) );
	}


	//===========================================================================
	//  ADD
	//===========================================================================

	public function add()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->transaction_terms->html->add( $CMS->transaction_terms->defaultvalue($CMS->input) );
	}

	public function add_do()
	{
		global $CMS, $DB, $member;


		if ( $transaction_term = $CMS->transaction_terms->add() )
		{
		    if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
            {
                $return = [
                    'status' => 'success',
                    'msg' => strip_tags($_SESSION['msg']),
                    'days' => $transaction_term['term_days'],
                    'name' => $transaction_term['term_name'],
                ];

                unset($_SESSION['msg']);

                echo @json_encode($return); exit;
            }
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transaction_terms&act=show&id={$transaction_term['term_id']}");
		}

        if(isset($CMS->input['ajax']) && $CMS->input['ajax'] == 1)
        {
            $return = [
                'status' => 'error',
                'msg' => strip_tags($_SESSION['msg'])
            ];

            unset($_SESSION['msg']);

            echo @json_encode($return); exit;
        }

		$CMS->output .= $CMS->transaction_terms->html->add( $CMS->transaction_terms->defaultvalue($CMS->input) );
	}


	//===========================================================================
	//  EDIT
	//===========================================================================

	public function edit()
	{
		global $CMS, $DB, $member;

		$id = intval($CMS->input['id']);

		$data = $CMS->transaction_terms->get_info($id);

		if(!$data)
		{
			if(!$data)
			{
				$data = $CMS->transaction_terms->get_info();
			}
		}

		$data = $CMS->transaction_terms->editvalue($data);

		$CMS->output .= $CMS->transaction_terms->html->edit($data);
	}

	public function edit_do()
	{
		global $CMS, $DB, $member;

		$id = intval($CMS->input['term_id']) ? intval($CMS->input['term_id']) : intval($CMS->input['id']) ;

		$data = $CMS->transaction_terms->get_info($id);

		if(!$data)
		{
			$_SESSION['msg'] = "{$CMS->lang['no_data']}";

			if($CMS->input['ajax'])
            {
                $return = ['status' => 'error', 'msg' => strip_tags($_SESSION['msg'])];
                unset($_SESSION['msg']);
                echo @json_encode($return); exit;
            }

			$CMS->global->redirectReferer();
		}

		$data = array_merge($data, $CMS->input);

		if ( $result = $CMS->transaction_terms->edit($data, -1) )
		{
            if($CMS->input['ajax'])
            {
                $return = ['status' => 'success' ,'msg' => strip_tags($_SESSION['msg']), 'days' => $result['term_days'], 'name' => $result['term_name']];
                unset($_SESSION['msg']);
                echo @json_encode($return); exit;
            }

			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transaction_terms&act=show&id={$result['transaction_terms_id']}");
		}

        if($CMS->input['ajax'])
        {
            $return = ['status' => 'error', 'msg' => strip_tags($_SESSION['msg'])];
            unset($_SESSION['msg']);
            echo @json_encode($return); exit;
        }

		$data = $CMS->transaction_terms->editvalue($data);

		$CMS->output .= $CMS->transaction_terms->html->edit( $data );
	}


	//===========================================================================
	//  DELETE
	//===========================================================================

	public function delete()
	{
		global $CMS, $DB, $member;

		// Delete
		$CMS->transaction_terms->delete();

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transaction_terms");
	}

	public function delete_all()
	{
		global $CMS, $DB, $member;

		// Delete all
		$CMS->transaction_terms->mdelete();

		// Redirect
		$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=transaction_terms");
	}


	//===========================================================================
	//  DEFAULT PAGE
	//===========================================================================

	public function page_default()
	{
		global $CMS, $DB, $member;

		// Get List
		$data = $CMS->transaction_terms->listing();

		// Write data
		$CMS->output .= $CMS->transaction_terms->html($data);
	}

	//===========================================================================
	//  SEARCH
	//===========================================================================

	public function search()
	{
		global $CMS, $DB, $member;

		$CMS->output .= $CMS->transaction_terms->html->search( $CMS->class->search->clean($CMS->input) );
	}

	public function search_do()
	{
		global $CMS, $DB, $member;

		// Get List
		$data = $CMS->transaction_terms->search();

		// Write Data
		$CMS->output .= $CMS->transaction_terms->html($data);
	}

	function ajax_get_data_transaction_terms()
    {
        global $CMS;

        $data = $CMS->transaction_terms->get_info(intval($CMS->input['id']));

        if($data)
        {
            echo @json_encode(['status' => 'success', 'data' => $data]);
        }
        else
        {
            echo @json_encode(['status' => 'error', 'msg' => $CMS->lang['no_data']]);
        }

        exit;
    }

    function dataAjax()
    {
        global $CMS;
        echo @json_encode($CMS->transaction_terms->dataAjax()); exit;
    }

}

?>