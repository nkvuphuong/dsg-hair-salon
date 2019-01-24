<?php
use core\ezy;
class skin_news {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function news_header()
{
  global $CMS, $DB, $member;
	$search_url =str_replace($CMS->class->search->url_return,"",$CMS->class->search->url_return);

	$output = "";

$output .= <<<EOF
<section class="add_table main_form">
  <figure class="heading">
      <h3>{$CMS->lang['news_header']}</h3>
      <figure class="pull-right right">
        <div class="search">
          <form method="post" id="frm_quickserch_product"  action="{$CMS->vars['root_domain']}/?site=news&act=search" style="display:inline-block">

            <input type="submit" class="fa-input" value="&#xf002;">
            <input type="text" name="p_quick_search" autocomplete="off" minlength="2" maxlength="64" id="p_quick_search" style="position: :relative;" placeholder="{$CMS->lang['title_quick_search']}" value="{$p_name_convert}">
            <div id="suggesstion-box" class="box_result_find" style="display:none"></div>
          </form>
          <a id="expand_formsearch" title="">{$CMS->lang['title_advance_search']}<i class="fa fa-angle-double-right"></i></a>
        </div>
        <a href="{$CMS->vars['root_domain']}/?site=news&act=add" title="" class="add_bill">{$CMS->lang['news_add_form']}</a>
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
    </figure>

<section class="tabs-section">
    <div class="tabs-section-nav tabs-section-nav-inline">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" role="tab" data-toggle="tab">
                    {$CMS->lang['list_news_header']}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=config_parent_news">
                    {$CMS->lang['list_category_new']}
                </a>
            </li>
        </ul>
    </div><!--.tabs-section-nav-->
</section><!--.tabs-section-->
{$CMS->global->languageTab('langTab')}

<form name="quick_search" id="formsearch_adv" style="display: none;" method="post" class="quick_search" action="{$CMS->vars['root_domain']}/?site=news&act=search_do{$search_url}" onSubmit="return check_form(this.id);" >

    <div class="row">
			<div class="col-xl-2 col-md-4 col-sm-4">
			  <p class="selected">
				<select class="select2" name="user_id" id="user_id" defaultvalue="{$CMS->input['user_id']}" onchange="this.form.submit()">
				  <option value=''>{$CMS->lang['all']}</option>
					{$CMS->user->load_list_user()}
				</select>
			  </p>
			</div>
    
			<div class="col-xl-3 col-md-4 col-sm-4">
			  <p class="selected">
				<select class="select2" name="parent_cat_id" id="parent_cat_id" defaultvalue="{$CMS->input['parent_cat_id']}" onchange="this.form.submit()">
				  <option value=''>{$CMS->lang['all_cate']}</option>
					{$CMS->config_parent_news->load_list_cate()}
				</select>
			  </p>
			</div>

			<div class="col-xl-2 col-md-4 col-sm-4">
			  <p class="selected">
				<select class="select2" name="news_active" id="news_active" defaultvalue="{$CMS->input['news_active']}" onchange="this.form.submit()">
				  <option value=''>{$CMS->lang['all_active']}</option>
				  <option value='0'>{$CMS->lang['news_active_0']}</option>
				  <option value='1'>{$CMS->lang['news_active_1']}</option>
				  <option value='2'>{$CMS->lang['news_active_2']}</option>
				</select>
			  </p>
			</div>
         </div>

</form>
<!---script language="javascript">rebuild_form("quick_search",1,1);</script-->

<form method="post" name="news" id="news" action="{$CMS->vars['root_domain']}/?site=news" onSubmit="return check_form(this.id);">

        <section class="add_table">
          <div class="table-responsive">
            <table class="table table_cus table-hover">
              <thead>
                <tr>
                  <th width="2%" class="table-check">
                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('news');" id="checkall">
                   <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                   <label for="id_{$result['record_cnt']}"></label>
                    </div>
                  </th>
                  <th width="2%" style="text-align:center" id="order_news_id">{$CMS->lang['news_id']}</th>
                  <th width="20%" id="order_news_name">{$CMS->lang['news_name']}</th>   
                  <th width="15%">{$CMS->lang['parent_cat_id']}</th>
                  <th width="5%" style="text-align:center">{$CMS->lang['news_is_hot']}</th>
                  <th width="5%" style="text-align:center" id="order_news_display">{$CMS->lang['news_display']}</th>
                  <th width="10%" style="text-align:center">{$CMS->lang['news_active']}</th>          
                  <th width="10%" id="order_news_time">{$CMS->lang['news_time']}</th>
                  <th width="5%" style="text-align:center"></th>
                </tr>
              </thead>
              <tbody>

EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function news_middle($result)
{
	global $CMS, $DB, $member;
	
	$output = "";
  $btn_control = "";
  if($CMS->permit['news_read'])
  {
    $btn_control .=<<<EOF
           <a href="{$CMS->vars['root_domain']}/?site=news&act=edit&id={$result['news_id']}" title""="" class="edit"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['news_edit'])
  {
    $btn_control .=<<<EOF
        <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=news&act=delete&id={$result['news_id']}');"   class="edit"><i class="fa fa-trash-o"></i></a>
EOF;

  }
$output .= <<<EOF
  <tr>
    <td class="table-check">
          <div class="checkbox checkbox-only">
            <input type="checkbox"  name="id_{$result['record_cnt']}" id="id_{$result['record_cnt']}" value="{$result['news_id']}"/>
            <label for="id_{$result['record_cnt']}"></label>
          </div>
      </td>
    <td>#{$result['news_id']}</td>
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <td style="white-space: initial" class="langTab" lang="{$langCode}">{$result['news_name_bk'][$langCode]}
                   <p class="label label-success">{$result['user_name']}</p>
                </td>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <td style="white-space: initial">{$result['news_name_bk']} 
                   <p class="label label-success">{$result['user_name']}</p>
                </td>
EOF;
        }
$output .= <<<EOF

    
    <td>{$result['parent_cat_id_bk']}</td>
    <td style="text-align:center">{$result['news_is_hot_bk']}</td>
    <td style="text-align:center">{$result['news_display_bk']}</td>
    <td style="text-align:center">{$result['news_active_bk2']}</td>  
    <td>{$result['news_time']}</td>
    <td align="center">{$btn_control}</td>
  </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function news_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="11">{$CMS->lang['news_no_data']}</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function news_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
              </tbody>
            </table>
          </div>
      </section><!--.box-typical-->
    {$CMS->news->action_control}
<div class="block_bottom pagination pagination-sm">{$CMS->news->show_page}</div>
<input type="hidden" name="data_cnt" value="{$CMS->news->record_cnt}">
 </form>   
 </section>  
<script language="javascript">rebuild_form("news");</script>
<script language="javascript">arrange_setup("{$CMS->news->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function news_control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<div class="block_action">
	<div class="block_action_left">
        <select class="select2" name="act" style="width: 217px;" defaultvalue="delete_all" emsg="{$CMS->lang['comment_incomplete_action']}" ehide="1">{$CMS->vars['action_controller']}</select>
        <input class="btn btn-rounded" type="submit" name="submit" value="OK" style="font-size:12px">
    </div>
</div>
EOF;

    return $output;
}

//===========================================================================
//  HTML EDIT
//===========================================================================

public function form( $data ="")
{
	global $CMS;

	$output = "";

	// Check image URL
    if($data['news_image_url'])
    {
        $display = "display: block;";
    }else
    {
        $data['news_image_url'] = "";
        $display = "";
    }

    // Edit
    if ( $CMS->input['act'] == "edit" || $CMS->input['act'] == "edit_do") {
        $button_submit = $CMS->lang['news_edit_submit'];
        $url = "{$CMS->vars['root_domain']}/?site=news&id={$data['news_id']}&act=edit_do&page={$CMS->input['page']}";
    }
    // Add
    else {
        $button_submit = $CMS->lang['news_add_submit'];
        $url = "{$CMS->vars['root_domain']}/?site=news&act=add_do";
    }

    // Checked
    $data['news_is_hot_checked'] = $data['news_is_hot'] == 1 ? "checked" : "";
    $data['news_display_checked'] = $data['news_display'] == 1 ? "checked" : "";

$output .= <<<EOF
<script type='text/javascript' src='{$CMS->vars['js_url']}/acp_news.js'></script>

<form method="post" id="news" name="news" action="{$url}" enctype="multipart/form-data">
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['news_header']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=news{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>


<div class="row">
<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"  style="float:none;margin:0px auto;">
{$CMS->global->languageTab('langTab')}
<!--start tab-->
<section class="tabs-section">
				<div class="tabs-section-nav tabs-section-nav-inline">
					<ul class="nav" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" href="#tabs-4-tab-1" role="tab" data-toggle="tab">
								General
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#tabs-4-tab-2" role="tab" data-toggle="tab">
								SEO
							</a>
						</li>
					</ul>
				</div><!--.tabs-section-nav-->

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="tabs-4-tab-1">

<!--Start row -->
<div class="row">
      <div class="col-lg-12">
          <div class="form-group row">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
EOF;
        $news_name = isset($CMS->input['news_name']) ? $CMS->input['news_name'] : $data['news_name'];
        $news_description = isset($CMS->input['news_description']) ? $CMS->input['news_description'] : $data['news_description'];
        $news_content = isset($CMS->input['news_content']) ? $CMS->input['news_content'] :  $data['news_content'];
        
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $validate = $langCode == $CMS->vars['default_language'] ? "data-validation=\"[NOTEMPTY]\" data-validation-message=\"{$CMS->lang['news_incomplete_name']}\"" : "";

                $output .= <<<EOF
        
            <fieldset class="form-group langTab" lang="{$langCode}">
              <label class="form-label">{$CMS->lang['news_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
              <input class="form-control" type="text" name="news_name[{$langCode}]" id="news_name[{$langCode}]" value="{$news_name[$langCode]}" {$validate} />
            </fieldset>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <label class="form-label">{$CMS->lang['news_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                <input class="form-control" type="text" name="news_name" value="{$data['news_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['news_incomplete_name']}">
EOF;
        }


    $output .= <<<EOF

            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <div class="form-group row">
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="checkbox-toggle">
                             <input type="checkbox" id="check-1" value="1" name="news_is_hot" {$data['news_is_hot_checked']}>
                            <label for="check-1">{$CMS->lang['news_is_hot']}</label>
                        </div>
                    </div>

                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="checkbox-toggle">
                            <input type="checkbox" id="check-2" value="1" name="news_display" {$data['news_display_checked']}>
                            <label for="check-2">{$CMS->lang['news_display']}</label>
                        </div>
                  </div>
                </div>
            </div>
          </div>
          
          <div class="form-group row">
            <label class="form-control-label">{$CMS->lang['cat_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <select class="select2" name="cat_id[]" multiple="multiple">
                    {$CMS->news->load_cate_news($data['cat_id'])}
      			    </select>
              </div>
          
          </div>
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
        
          <fieldset class="form-group row langTab" lang="{$langCode}">
              <label class="form-control-label">{$CMS->lang['news_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="news_description[{$langCode}]"  cols="60" rows="5">{$news_description[$langCode]}</textarea>
              </div>
          </fieldset>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['news_description']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control" name="news_description"  cols="60" rows="5">{$data['news_description']}</textarea>
                  </div>
                </div>
EOF;
        }    

$output .= <<<EOF

          <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <div class="form-group row">
                  <label class="form-control-label" >{$CMS->lang['news_image']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="actionButtons pull-right">
                      <ul>
                          <li onclick="return performClick('ufile');">
                              <i tabindex="0" class="fa fa-pencil" ></i>
                          </li>
                          <li>
                              <span class="text-left">|</span>
                          </li>
                          <li onclick="return delete_fileToAttach();">
                              <i class="fa fa-trash-o"></i>
                          </li>
                      </ul>
                      <input  type="hidden" id="ufile_output_b64" name="image-data" value="" />
                    </div>
                   
                    <div class="drop-zone fileinput-button" style="height: 100px;">
                          <img id="upload_img_show" src="{$data['news_image_url']}"  style="height: 100%;margin: 0 auto;max-width: 100%; {$display}" />
                          <i class="font-icon font-icon-cloud-upload-2"></i>
                          <div class="drop-zone-caption">Drag file to upload</div>

                          <input type="file"  name="news_image" id="ufile" accept="image/*">
                    </div><!--.drop-zone-->
                    <img class="img-responsive" src="" style="max-width: 100%;" alt="">
                    <p class='note_post'>{$CMS->lang["note_ext_image"]}</p> 
                  </div> <!--End upload-->

                </div>
              </div>
              
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label class="form-control-label">{$CMS->lang['news_image_alt']}</label>
                <input class="form-control" type="text" name="news_image_alt" value="{$data['news_image_alt']}">
              </div>
EOF;

          if( in_array(ezy::$theme_key, array("wco", "fco", "nms")) )
          {
$output .=<<<EOF
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label class="form-control-label">{$CMS->lang['title_news_type']}</label>
                <select class="form-control auto_select" name="news_type" defaultvalue="{$data['news_type']}">
                  <option value="1">{$CMS->lang['news_type_1']}</option>
                  <option value="2">{$CMS->lang['news_type_2']}</option>
                </select>
              </div>
EOF;
          }
$output .=<<<EOF
        </div>

EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
        
          <fieldset class="form-group row langTab" lang="{$langCode}">
              <label class="form-control-label">{$CMS->lang['news_content']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <textarea class="editor_texarea" name="news_content[{$langCode}]">{$news_content[$langCode]}</textarea>
              </div>
          </fieldset>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['news_content']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="editor_texarea" name="news_content">{$data['news_content']}</textarea>
                  </div>
                </div>
EOF;
        }    

$output .= <<<EOF

        
        
        
        <div class="form-group row">
          <label class="form-control-label">Tags</label>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
              <select class="form-control select2" name="news_tags[]" multiple="multiple" id="tagSelector"></select>
            </div>
        </div>

        <div class="form-group row">
          <label class="form-control-label">{$CMS->lang['title_arrange']}</label>
          <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
            <input type="number" class="form-control" name="news_order" value="{$data['news_order']}"/>
          </div>
        </div>

      </div><!----end col-lg-8 ----->

      </div> <!--end row -->
		
                    </div><!--.tab-pane-->
					<figture role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2">
<!--Start row -->
<div class="row">
<div class="col-lg-12">

            <div class="form-group">
              <div class="fl-flex-label">
                    <textarea class="form-control content_textarea" name="meta_title" placeholder="{$CMS->lang['meta_title']}">{$data['meta_title']}</textarea>
              </div>
            </div>


            <div class="form-group">
              <div class="fl-flex-label">
                    <textarea class="form-control content_textarea" name="meta_description" placeholder="{$CMS->lang['meta_description']}">{$data['meta_description']}</textarea>
              </div>
            </div>

            <div class="form-group">
              <div class="fl-flex-label">
                    <textarea class="form-control content_textarea" name="meta_keywords" placeholder="{$CMS->lang['meta_keywords']}">{$data['meta_keywords']}</textarea>
              </div>
            </div>
      </div><!----end col-lg-2 ----->
</div><!--end row -->

</div><!--.tab-pane-->
				</div><!--.tab-content-->
			</section><!--.tabs-section-->
            
            <div class="form-group row">
              <label class="form-control-label"></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                 <button class="btn btn-rounded" type="submit" class="btn">{$button_submit}</button>
              </div>
            </div>

 </section><!--end tab-->
 
 </div>
 </div>

</form>
<script type="text/javascript">
  $(document).ready(function(){
      
      if ( "{$data['cat_id']}" )
      {
        var list_cat = "{$data['cat_id']}";
          list_cat = list_cat.split("|");
          
          for(x in list_cat)
          {
              $(".sub_cate[value='"+list_cat[x]+"']").prop("checked", true);
          }
        }
        
      validate_form_custom("#news");     
      select2Tags('{$data['news_tags']}');
  });
</script>

EOF;

	return $output;	
}

//===========================================================================
//  HTML SHOW
//===========================================================================

public function show( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
 <header class="section-header">
        <div class="tbl">
          <div class="tbl-row">
            <div class="tbl-cell">
              <h3>{$CMS->lang['title_header_show']}</h3>
            </div>
          </div>
        </div>
      </header>
 <section class="card">
{$CMS->global->languageTab('langTab')}
      <section class="box-typical">
<header class="box-typical-header">
    <div class="tbl-row">
        <div class="tbl-cell tbl-cell-title">
          <h3>{$CMS->lang['news_add_info']}:
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <strong class="langTab" lang="{$langCode}">{$data['news_name_bk'][$langCode]}</strong>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <strong>{$data['news_name_bk']}</strong>
EOF;
        }
$output .= <<<EOF
           </h3>
        </div>
        <div class="tbl-cell tbl-cell-action-bordered">
            <a href="{$CMS->vars['root_domain']}/?site=news{$CMS->class->search->url_return}">
            <button type="button" class="action-btn"><i class="fa fa-mail-reply"></i></button>
            </a>
        </div>                                    
    </div>
</header>
           </section>



        <div class="card-block">


                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{$CMS->lang['meta_title']}</label>
                        <div class="col-sm-9">
                        <p class="form-control-static">
                          {$data['meta_title']}
                        </p>
                        </div>
                      </div>



                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{$CMS->lang['meta_description']}</label>
                        <div class="col-sm-9">
                        <p class="form-control-static">
                            {$data['meta_description']}
                        </p>
                        </div>
                      </div>



                      <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['meta_keywords']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                        {$data['meta_keywords']}
                      </p>
                      </div>
                    </div>       

        
EOF;
    if($CMS->permit["news_active"] == TRUE) 
        {

      if($data['news_active'] == 0)
            {   
                     $output .=<<<EOF

                    <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_active']}</label>
                      <div class="col-sm-9">
							<div class="block_desc_bottom_submit">    
								<a class="label label-primary" href="{$CMS->vars['root_domain']}/?site=news&act=active&id={$data['news_id']}&method=1">Duyệt bài</a>
                        		<a class="label label-danger" href="{$CMS->vars['root_domain']}/?site=news&act=active&id={$data['news_id']}&method=2">Đánh dấu vi phạm</a>
							</div>
                      </div>
                    </div>
EOF;

    } 
          

                
  }
    $output .=<<<EOF


                      <div class="form-group row">
                      	<label class="col-sm-3 form-control-label">{$CMS->lang['news_is_hot']}</label>
                          <div class="col-sm-9">
                            <p class="form-control-static">
                                {$data['news_is_hot_bk']}
                            </p>
                          </div>
                    	</div>



                    <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_display']}</label>
                      <div class="col-sm-9">
                      	 <p class="form-control-static bold red">
                         {$data['news_display_bk']}</p>
                      </div>
                    </div>




                    <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['enable_comment']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['enable_comment_bk']}
                      </p>
                      </div>
                    </div>


                    <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_schedule']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['news_schedule_bk']} <span style="color:#F00">{$data['news_time_display_bk']}</span>
                      </p>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_link_frontend']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['news_url']} 
                      </p>
                      </div>
                    </div>


  <h5 class="with-border">{$CMS->lang['news_required_info']}</h5>


             <div class="form-group row">
                <label class="col-sm-3 form-control-label">{$CMS->lang['news_name']}</label>
                <div class="col-sm-9">
                <p class="form-control-static">
EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <span class="langTab" lang="{$langCode}">{$data['news_name_bk'][$langCode]}</span>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <span>{$data['news_name_bk']}</span>
EOF;
        }
$output .= <<<EOF

                    &nbsp; <script type="text/javascript">permission_btn("edit", "news", "{$CMS->vars['root_domain']}/?site=news&act=edit&id={$data['news_id']}");</script>
                      &nbsp; <script type="text/javascript">permission_btn("delete", "news", "{$CMS->vars['root_domain']}/?site=news&act=delete&id={$data['news_id']}");</script>
                </p>
                </div>
              </div>

EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['news_shorturl']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                      {$data['news_shorturl'][$langCode]}
                  </p>
                  </div>
                </div>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['news_shorturl']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                      {$data['news_shorturl']}
                  </p>
                  </div>
                </div>
EOF;
        }
$output .= <<<EOF

              <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_image']}</label>
                      <div class="col-sm-9">
                      <p class="img">
                          {$data['news_image']}
                      </p>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_image_alt']}</label>
                      <div class="col-sm-9">
                      <p class="img">
                          {$data['news_image_alt']}
                      </p>
                      </div>
                    </div>


              <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['cat_id']}</label>
                      <div class="col-sm-9">
                      
                          {$data['cat_id_bk']}
                      
                      </div>
                    </div>

EOF;

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
              $data['news_content'][$langCode] = html_entity_decode($data['news_content'][$langCode]);
                $output .= <<<EOF
                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['news_description']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                      {$data['news_description'][$langCode]}
                  </p>
                  </div>
                </div>

                <div class="form-group row langTab" lang="{$langCode}">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['news_content']}</label>
                  <div class="col-sm-9">
                      <div class='detail'> <div class="content"> {$data['news_content'][$langCode]}</div></div>
                  </div>
                </div>
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['news_description']}</label>
                  <div class="col-sm-9">
                  <p class="form-control-static">
                      {$data['news_description']}
                  </p>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{$CMS->lang['news_content']}</label>
                  <div class="col-sm-9">
                      <div class='detail'> <div class="content"> {$CMS->class->editor->shortcode($data['news_content'])}</div></div>
                  </div>
                </div>
EOF;
        }
$output .= <<<EOF


              


              <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['tags_id']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['news_tags_id']}
                      </p>
                      </div>
                    </div>



              <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_user_name']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['user_name']}
                      </p>
                      </div>
                    </div>


              <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_time']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['news_time']}
                      </p>
                      </div>
                    </div>


              <div class="form-group row">
                      <label class="col-sm-3 form-control-label">{$CMS->lang['news_time_update']}</label>
                      <div class="col-sm-9">
                      <p class="form-control-static">
                          {$data['news_time_update']}
                      </p>
                      </div>
                    </div>

         </div><!-- end .card-block-->
   </section><!--card -->
EOF;
	
	return $output;	
}

//===========================================================================
//  HTML SEARCH
//===========================================================================

public function search($data)
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF

      
<form method="post" id="news" name="news" action="{$CMS->vars['root_domain']}/?site=news&act=search_do" onSubmit="return check_form(this.id);">
     
 <section class="card">

      <section class="box-typical">
        <header class="box-typical-header">
            <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title"><h3>{$CMS->lang['title_search_news']}</h3></div>
                <div class="tbl-cell tbl-cell-action-bordered">
                    <a href="{$CMS->vars['root_domain']}/?site=news{$CMS->class->search->url_return}">
                    <button type="button" class="action-btn"><i class="font-icon font-icon-answer"></i></button>
                    </a>
                </div>
            </div>
        </header>
	  </section>
        <div class="card-block">

          <h5 class="with-border">Thông tin cần thiết</h5>

          <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['news_name']}</label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
               <input class='form-control' type="text" name="news_name" value="{$data['news_name']}">
              </p>
              </div>
            </div>



            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['cat_id']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <select class='select2' name="parent_cat_id" id="parent_cat_id" defaultvalue="{$CMS->input['parent_cat_id']}" onchange="this.form.submit()">
                    <option value=''>{$CMS->lang['all_cate']}</option>
                      {$CMS->config_parent_news->load_list_cate()}
                  </select>
              </p>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['news_active']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <select class='select2' name="news_active" id="news_active" defaultvalue="{$CMS->input['news_active']}" onchange="this.form.submit()">
                    <option value=''>{$CMS->lang['all_active']}</option>
                    <option value='0'>{$CMS->lang['news_active_0']}</option>
                    <option value='1'>{$CMS->lang['news_active_1']}</option>
                    <option value='2'>{$CMS->lang['news_active_2']}</option>            
                  </select>
            </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['news_time']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="news_time" value="{$data['news_time']}" etype="date" placeholder="{$CMS->lang['search_from']}">
              	</p>
              </div>
			</div>
            
            <div class="form-group row">
              <label class="col-sm-3 form-control-label hidden-xs"></label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              	<p class="form-control-static-input">
                   <input class='form-control' type="text" name="news_time_to" value="{$data['news_time_to']}" etype="date" placeholder="{$CMS->lang['search_to']}">
              	</p>
              </div>
			</div>
                          


            <h5 class="with-border">Thông tin bổ sung</h5>



            <div class="form-group row">
              <label class="col-sm-3 form-control-label">{$CMS->lang['news_display']}</label>
              <div class="col-xl-3 col-md-5 col-sm-9 col-xs-12">
              <p class="form-control-static-input">
                  <select class='select2' name="news_display" defaultvalue="{$data['news_display']}">{$CMS->vars['display_status']}</select>
              </p>
              </div>
            </div>


            <div class="form-group row">
              <label class="col-sm-3 form-control-label"></label>
              <div class="col-xl-5 col-lg-8 col-sm-9 col-xs-12"> 
                <p class="form-control-static-input">
                 <input class='btn btn-rounded' type="submit" name="submit" value=" {$CMS->lang['search_submit']} ">
                </p>
              </div>
            </div>

            

            </div><!-- end .card-block-->
   </section><!--card --> 
            

</form>
<script language="javascript">rebuild_form("news",1,1);</script>
<script>
$(document).ready(function(){
    $("input[name='news_time'], input[name='news_time_to']").datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "dd/mm/yy",
      yearRange: "-90:+10"
    });

});
</script>
EOF;
	
	return $output;	
}





public function logs()
{
	global $CMS, $DB, $member;
	
	$output = "";

	$format[0] = $CMS->lang['type_0'];
	$format[1] = $CMS->lang['type_1'];
	
	// Escalate Permission query
	if ( $CMS->vars['is_root'] == 1 )
	{
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE L.log_request LIKE '%update_royalty%' AND L.log_key LIKE '%news%' AND  1=1";
	}
	else if ( $CMS->vars['is_admin'] == 1 )
	{
		//$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE UG.userg_is_root=0";
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE L.log_request LIKE '%update_royalty%' AND L.log_key LIKE '%news%' AND 1=1";
	}
	else
	{
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE UG.userg_is_root=0 AND UG.userg_is_admin=0";
	}
	
	// Check Type
	$type_array = array("user_name", "log_name", "log_time", "log_request", "log_ip_address");

	$content = trim($CMS->input['content']);
	$type = $CMS->input['type'] ? $CMS->input['type'] : "log_name";		
	$orderby = $CMS->input['orderby'] ? $CMS->input['orderby'] : "DESC";

	if ( $CMS->input['act'] == "search" )
	{
		if ( in_array( $type, $type_array ) == false )
		{
			$type = "log_name";
		}

		if ( isset($_SESSION["log_name"]) AND !isset($content) )
		{
			$content = $_SESSION["log_name"];
			$type = $_SESSION["log_type"];
			$orderby = $_SESSION["log_orderby"];
		}

		$_SESSION["log_name"] = $content;
		$_SESSION["log_type"] = $type;
		$_SESSION["log_orderby"] = $orderby;

		if ( $type == "log_time" )
		{
			$content2 = explode("/", $content);
				
			if ( ! $content2[1] )
			{
				$content2[1] = $CMS->class->date->date_get("m", time());
			}
				
			if ( ! $content2[2] )
			{
				$content2[2] = $CMS->class->date->date_get("Y", time());
			}
				
			$content2 = strtotime("{$content2[2]}-{$content2[1]}-{$content2[0]}");

			$sql = "SELECT L.*, U.user_name FROM ".root_table."logs  AS L {$escalate_sql} AND {$type} > {$content2} AND {$type} < ({$content2}+86400) ORDER BY log_time {$orderby}";
		}
		else
		{
			$sql = "SELECT L.*, U.user_name FROM ".root_table."logs AS L {$escalate_sql} AND {$type} LIKE ('%{$content}%') ORDER BY log_time {$orderby}";
		}
	}
	else
	{
		$sql = "SELECT L.*, U.user_name FROM ".root_table."logs AS L {$escalate_sql} ORDER BY log_time {$orderby}";
	}
	
	list($CMS->show_page, $sql) = $CMS->class->page->create($sql, 30);

$output .= <<<EOF
<script language="javascript" src="{$CMS->vars['public_url']}/js/js_boxover.js"></script>
<div class="block_wrapper">
<div class="block_top"><p class="align_right"></p>{$CMS->lang['header_views_log']}</div>

<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="6">
  <tr>
    <th width="1%">{$CMS->lang['id']}</th>
    <th width="15%">{$CMS->lang['user_name']}</th>
    <th width="29%">{$CMS->lang['content']}</th>
    <th width="20%">{$CMS->lang['request']}</th>
    <th width="15%">{$CMS->lang['time']}</th>
    <th width="10%">{$CMS->lang['ip_address']}</th>
    <th width="10%">{$CMS->lang['type']}</th>
  </tr>
EOF;

	$i = 0;

while ( $data = $DB->fetch_array() )
{
		$data['log_ftime'] = $CMS->class->date->date_format( $data['log_time'], 1 );
		$data['log_time'] = $CMS->class->date->date_format( $data['log_time'], 1 );
		$data['log_name'] = $data['log_name'];
		$data['log_tip'] = $data['log_content'];
        
		if ( $data['log_tip'] )
		{
			$data['log_tip'] = " onmouseover=\"document.getElementById('log_{$data[louserg_id]}').style.background='#EEEEEE;'; showtip('{$data[log_tip]}');\" onmouseout=\"document.getElementById('log_{$data[louserg_id]}').style.background='#FCFCFC;'; hidetip();\" ";
		}
		else
		{
			$data['log_tip'] = " onmouseover=\"document.getElementById('log_{$data[louserg_id]}').style.background='#EEEEEE;'\" onmouseout=\"document.getElementById('log_{$data[louserg_id]}').style.background='#FCFCFC;'\" ";
		}
    		
        $data['log_request'] = $CMS->class->logs->print_request( $data['log_request'] );
        
    	$data['log_request'] = substr( $data['log_request'], 0, 50 );
    	
        if ( $i % 2 != 0 ) { $bg_color = "#FFFFFF"; } else { $bg_color = "#F6F6F6"; }
        
$output .= <<<EOF
  <tr bgcolor="{$bg_color}">
    <td>#{$data['log_id']}</td>
    <td><a href="{$CMS->vars['root_domain']}/?site=user&act=show&id={$data['user_id']}">{$data['user_name']}</a></td>
    <td id="log_{$data['id']}" {$data['tip']} style="font-size: 10px;">{$data['log_name']}</td>
    <td>{$data['log_request']}</td>
    <td>{$data['log_time']}</td>
    <td><a href="{$CMS->vars['root_domain']}/?site=firewall&folder=logs&act=search&ip={$data['log_ip_address']}">{$data['log_ip_address']}</a></td>
    <td>{$format[$data['log_type']]}</td>
  </tr>
EOF;
	
    $i++;
}

$output .= <<<EOF
</table>
</div>
</div>
<div class="block_bottom pagination pagination-sm"></div>
</div>
<div class="block_bottom pagination pagination-sm">{$CMS->show_page}</div>
<br />
<script language="javascript">rebuild_form("logs");</script>
EOF;
	
	return $output;	
}





}

?>