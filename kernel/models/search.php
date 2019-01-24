<?php
if (!defined('IN_ROOT')) exit();

$CMS->search=new Search;
class Search {
	public $show_page=0;
	public $CMS = "";
	public $sql_query = "";
	public $sql_query_bk = "";
	public $arrange_data = "";
	public $record_cnt = 0;
	public $control = 0;
	public $total = 0;
	public $sql_add = "";
	public $action_control = "";
	public $news_project = "";
	public $data_array = array();	 
	public $per_page = 2;
	public $prefix_html = "";
	public $suffix_html = "";
	public $html;
	
	public function html() {
		global $CMS,$DB,$member;
		if (!isset($this->html)) {	
			$this->html = $CMS->class->template->load_template("skin_find");
		}
	}
	public function listing($key=null,$sub=null) {
		global $CMS,$DB,$member;
		$this->html();
		$sub=is_null($sub)?'':$sub;
		$order='';
		switch ($sub) {
			default:
				$order='cam_time';
				break;
			case '1':
				$order='cam_view';
				break;
		}
		$out='';
		$CMS->class->page->type='campaigns';
		list($this->show_page, $this->sql_query) = $CMS->class->page->create("SELECT p.`pcam_id`,p.`pcam_price`,c.`cam_time_end`,c.`cam_name`,i.`img_path`,i.`img_name`,i.`is_front`,c.`cam_url` FROM `nh_product_campaigns` p JOIN `nh_campaigns` c ON c.`cam_id`=p.`cam_id` JOIN `nh_data_image` i ON p.`pcam_id`=i.`pcam_id` WHERE c.`pcam_id_default`=p.`pcam_id` AND c.`is_front_default`=i.`is_front` AND  c.`attr_id_default`=i.`attr_id` AND c.`cam_deleted`=0 AND c.`cam_name` LIKE '%{$key}%' ORDER BY c.`{$order}` DESC", $this->per_page,$sub.'search','_'.$key);
		$out.=$this->html->head();
		if ($DB->num_rows($this->sql_query)>0) {
			while ($data=$DB->fetch_array($this->sql_query)) {
				$out.=$this->html->mid($data);
			}
		} else {
			$out.=$this->html->none();
		}
		$out.=$this->html->foot();
		return $out;
	}
}
?>