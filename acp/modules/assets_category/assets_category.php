<?php
if (!defined('IN_ROOT')) exit();
$cat = new cat;
$cat->autorun();

class cat{
	public $html;
	public function autorun(){
		global $CMS, $DB, $member;
		$CMS->class->language->load("assets_category");
		$this->html = $CMS->class->template->load_template("skin_assets_category");
		
		
		switch ($CMS->input['act']) {
			default:
                if(\lib\input::get('subact') == 'clear_cache')
                {
                    $CMS->class->cache->mdelete($CMS->assets_category->cache_prefix);
                    $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                    $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                }
				$this->default_page();
				break;
			case 'show':
				$this->show();
				break;
			case 'edit':
				$this->edit();
				break;
			case 'edit_do':
				$this->edit_do();
				break;
			case 'search':
				$CMS->assets_category->search();
				break;
			case 'del':
				$this->del();
				break;
			case 'add':
				$this->add();
				break;
			case 'add_do':
				$this->add_do();
				break;
		}
	}
	public function default_page() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['cat_title']}";
		$CMS->output.=$this->html->head();
		$CMS->output.=$CMS->assets_category->listing();
		$CMS->output.=$this->html->foot();
	}
	public function show() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['cat_title']}";
		$CMS->output.=$this->html->show($CMS->assets_category->convertvalue($CMS->assets_category->get_info($CMS->input['id'])));
		$CMS->output.=$CMS->global->logs("assets_category_{$CMS->input['id']}");
	}
	public function del() {
		global $CMS, $DB, $member;
		$CMS->assets_category->delete();
		$CMS->global->redirect("{$CMS->vars['http_referer']}");
	}
	
	public function add() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['cat_add']}";
		$CMS->output.=$this->html->add($CMS->input);
	}
	public function add_do() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['cat_add']}";
		if ($CMS->assets_category->add()) {
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets_category");
		}
		$CMS->output.=$this->html->add($CMS->input);
	}
	
	public function edit() {
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['cat_edit']}";
		$CMS->output.=$this->html->edit($CMS->assets_category->get_info($CMS->input['id']));
	}
	public function edit_do() { 
		global $CMS, $DB, $member;
		$CMS->core->page_title = "{$CMS->lang['cat_edit']}";
		if ($CMS->assets_category->edit($CMS->input['id'])) {
			$CMS->global->redirect("{$CMS->vars['root_domain']}/?site=assets_category");
		}
		$CMS->output.=$this->html->edit($CMS->assets_category->get_info($CMS->input['id']));
	}
}