<?php

class skin_assets_category {
	public function head() {
		global $CMS, $DB, $member;
		$output='';
		$CMS->input['title']=str_replace('%20',' ',$CMS->input['title']);
		$output.=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
			<div class="tbl-cell">
				<h3>{$CMS->lang['cat_head']} <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a></h3>
			</div>
		</div>
	</div>
</header>

<form name="quick_search" id="quick_search" method="post" class="quick_search assets_category" action="{$CMS->vars['root_domain']}/?site=assets_category&act=search" >
<div class="row">

	<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<input name="sid" id="sid" size="45" type="text" class="form-control" placeholder="{$CMS->lang['cat_text_id']}" value="{$CMS->input['sid']}">
			</span>
		</p>
	</div>
	<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<input name="sname" id="sname" size="45" type="text" class="form-control" placeholder="{$CMS->lang['cat_text_name']}" value="{$CMS->input['sname']}">
			</span>
		</p>
	</div>
	<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-xs-12"> 
		<p class="typeahead-field">
			<span class="typeahead-query">
				<button class="search btn btn-rounded" type="submit"><i class="fa fa-search" aria-hidden="true"></i>Search</button>
			</span>
		</p>
	</div>
</div>
</form>

<section class="box-typical">
	<header class="box-typical-header">
        <div class="tbl-row">
            <div class="tbl-cell tbl-cell-title">
				<h3>{$CMS->lang['cat_list']}</h3>
            </div>
            <div class="tbl-cell tbl-cell-action-bordered">
				<a href="{$CMS->vars['root_domain']}/?site=assets_category&act=add">
					<button type="button" class="action-btn"><i class="fa fa-plus-circle"></i></button>
                </a>
            </div>
        </div>
    </header>
	<div class="box-typical-body">
        <div class="table-responsive">
            <table class="table table-hover">
				<thead>
					<tr>
						<th width="5%" id="order_cat_id">{$CMS->lang['cat_id']}</th>
						<th width="15%" id="order_cat_name">{$CMS->lang['cat_name']}</th>
						<th width="15%" id="order_cat_status">{$CMS->lang['cat_status']}</th>
						<th width="15%" >{$CMS->lang['user_id']}</th>
						<th width="10%" id="order_cat_time">{$CMS->lang['cat_time']}</th>
						<th width="5%" style="text-align:center">{$CMS->lang['edit']}</th>
						<th width="5%" style="text-align:center">{$CMS->lang['delete']}</th>
					</tr>
				</thead>
				<tbody>
EOF;
		return $output;
	}
	
	public function foot() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				</tbody>
			</table>
		</div>
	</div>
</section>
{$CMS->assets_category->action_control}
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
<input type="hidden" name="data_cnt" value="{$CMS->promotion->record_cnt}">
<script language="javascript">arrange_setup("{$CMS->assets_category->arrange_data}");</script>
EOF;
		return $output;
	}
	
	public function mid($data=NULL) { 
		global $CMS, $DB, $member;

		
		$output=<<<EOF
			<tr>
				<td>{$data['cat_id']}</td>
				<td><a href="{$CMS->vars['root_domain']}/?site=assets_category&act=show&id={$data['cat_id']}">{$data['cat_name']}</a></td>
                <td>{$data['cat_status']}</td>
				<td><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}" target="_blank">{$data['user_name']}</a></td>
				<td>{$data['cat_time']}</td>
				<td align="center"><script type="text/javascript">permission_btn("edit", "user", "{$CMS->vars['root_domain']}/?site=assets_category&act=edit&id={$data['cat_id']}");</script></td>
				<td align="center"><script type="text/javascript">permission_btn("delete", "user", "{$CMS->vars['root_domain']}/?site=assets_category&act=del&id={$data['cat_id']}");</script></td>
				</tr>
EOF;
		return $output;
	}
	
	public function none() {
		global $CMS, $DB, $member;
		$output=<<<EOF
				<tr>
					<td colspan="9" align="center" class="no_data"><h6>Not assets_category</h6></td>
				</tr>
EOF;
		return $output;
	}

	public function show($data=NULL) {
		global $CMS, $DB, $member;
		$output=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['cat_info']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['cat_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
					<script type="text/javascript">permission_btn("edit", "user", "{$CMS->vars['root_domain']}/?site=assets_category&act=edit&id={$data['cat_id']}");</script>
                </div>
				<div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=assets_category{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
        		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['cat_text_id']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">#{$data['cat_id']}</span>
				</p>
			</div>
		</fieldset>
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['cat_text_name']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['cat_name']}</span>
				</p>
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['cat_type']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['cat_type']}</span>
				</p>
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['user_id']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['user_name']}</span>
				</p>
			</div>
		</fieldset>

		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['cat_time']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="form-control-static">
					<span class="typeahead-query">{$data['cat_time']}</span>
				</p>
			</div>
		</fieldset>


	</div>
</section>
<script>

</script>
EOF;
		return $output;
	}
    
	public function add($data=null) {
		global $CMS, $DB, $member;
        
        $data['cat_status'] = $data['cat_status']?$data['cat_status']:1;
                
		$output=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['cat_add']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['cat_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=assets_category{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=assets_category&act=add_do" method="POST" enctype="multipart/form-data">
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label" for="cat_title">{$CMS->lang['cat_name']}<font style="margin-left:" color="red">(*)</font></span></label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="typeahead-field">
					<span class="typeahead-query">
						<input name="cat_name" id="cat_name" size="45" type="text" value="{$data['cat_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['cat_err_name']}">
					</span>
				</p>
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label" for="cat_cam">{$CMS->lang['cat_status']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="typeahead-field">
					<span class="typeahead-query">
                    <select name="cat_status" class="form-control" defaultvalue="{$data['cat_status']}" />
                    	<option value="0">{$CMS->lang['cat_status_0']}</option>
                        <option value="1">{$CMS->lang['cat_status_1']}</option>
                    </select>
					</span>
				</p>                
			</div>
		</fieldset>
        
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label hidden-xs-down"></label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
				<p class="form-control-static-input">
					<button class="btn btn-rounded" type="submit" >{$CMS->lang['cat_add']}</button>
				</p>
			</div>
		</fieldset>
		</form>
	</div>
</section>
<script>
rebuild_form('form-signin_v1');	                                        
</script>
EOF;
		return $output;
	}
    
	public function edit($data=null) {
		global $CMS, $DB, $member;
                
		$output=<<<EOF
<header class="section-header">
	<div class="tbl">
		<div class="tbl-row">
            <div class="tbl-cell">
				<h3>{$CMS->lang['cat_edit']}</h3>
            </div>
		</div>
	</div>
</header>
<section class="card">
    <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                    <h3>{$CMS->lang['cat_info']}</h3>
                </div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=assets_category{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	</section>
	<div class="card-block">
		<form id="form-signin_v1" name="form-signin_v1" action="{$CMS->vars['root_domain']}/?site=assets_category&act=edit_do&id={$data['cat_id']}" method="POST" enctype="multipart/form-data">
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label" for="cat_name">{$CMS->lang['cat_name']}</label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
				<p class="typeahead-field">
					<span class="typeahead-query">
						<input name="cat_name" id="cat_name" size="45" type="text" value="{$data['cat_name']}" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['cat_err_title']}">
					</span>
				</p>
			</div>
		</fieldset>
        
        <fieldset class="form-group row">
            <label class="col-sm-3 form-control-label" for="cat_cam">{$CMS->lang['cat_status']}</label>
            <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
                <p class="typeahead-field">
                    <span class="typeahead-query">
                    <select name="cat_status" class="form-control" defaultvalue="{$data['cat_status']}" />
                        <option value="0">{$CMS->lang['cat_status_0']}</option>
                        <option value="1">{$CMS->lang['cat_status_1']}</option>
                    </select>
                    </span>
                </p>                
            </div>
        </fieldset>
        
		<fieldset class="form-group row">
			<label class="col-sm-3 form-control-label hidden-xs-down"></label>
			<div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
				<p class="form-control-static-input">
					<button class="btn btn-rounded" type="submit" >{$CMS->lang['cat_edit']}</button>
				</p>
			</div>
		</fieldset>
		</form>
	</div>
</section>
<script>
rebuild_form('form-signin_v1');	                                        
</script>
EOF;
		return $output;
	}
}