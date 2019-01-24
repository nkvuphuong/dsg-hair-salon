<?php
use \core\ezy;
class skin_gallery {

//===========================================================================
//  HTML HEADER
//===========================================================================

public function gallery_header()
{
	global $CMS, $DB, $member;

	$output = "";

  $html_image = $CMS->gallery->getListImage();

  $sort_selected[$CMS->input['sort']*1] = "selected";

$output .= <<<EOF
  <section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['gallery_header']}</h3>
        <figure class="pull-right right">
        <a href="{$CMS->vars['root_domain']}/?site=gallery&act=add" title="" class="add_bill">{$CMS->lang['title_add_photo']}</a>
        <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure> 
    </figure>
        
<section class="tabs-section" style="margin-bottom:15px">
    <div class="tabs-section-nav tabs-section-nav-inline">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="{$CMS->vars['root_domain']}/?site=gallery">
                    {$CMS->lang['gallery_header']}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{$CMS->vars['root_domain']}/?site=config_gallery">
                    {$CMS->lang['title_list_category_gallery']}
                </a>
            </li>
        </ul>
    </div><!--.tabs-section-nav-->
</section><!--.tabs-section-->   

    <section class="add_table">
      <section class="box-typical box-typical-full-height-with-header">
          <header class="box-typical-header box-typical-header-bordered">
            <div class="tbl-row">
              <div class="tbl-cell tbl-cell-title">
                <h3 class="gallery-header">The Gallery</h3>
              </div>
              <div class="tbl-cell tbl-cell-actions">
EOF;

        if($CMS->class->security->checkPermission("gallery", "edit"))
        {
            $output .= <<<EOF
                <span class="label label-success change_cat_all_checked" onclick="galleryChangeCategoryShow();">
                  <i class="fa fa-exchange"></i>
                  Change category
                </span>
EOF;
        }


        $output .= <<<EOF
                <span class="label label-success dell_all_checked">
                  <i class="fa fa-trash"></i>
                  {$CMS->lang['act_delete']}
                </span>
                <div class="checkbox pull-right">
                  <label title="Check all gallery">
                    <span class='number_check'></span>
                    <input type="checkbox" class="btn_checkall" name="check_all_item">
                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                  </label>
                </div>
              </div>
          </div>
          </header>
          <div>
            <div class="gallery-grid">
                <form id="gallery_actions" action="" method="get">
                    <input type="hidden" name="site" value="gallery">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Sort by:</label>
                                <select class="form-control" name="sort" id="">
                                    <option {$sort_selected[0]} value="0">Created date</option>
                                    <option {$sort_selected[1]} value="1">Order</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="">Category:</label>
                                <select class="form-control" name="cat_id" id="">
                                    <option value="">{$CMS->lang['all']}</option>
                                    {$CMS->gallery->load_cate_gallery($CMS->input['cat_id'])}
                                </select>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                    </div>
                </form>
                <script>
                    $("#gallery_actions select").change(function(){
                        $("#gallery_actions").submit();
                    })
                </script>
            </div>
            <div class="gallery-grid">
              {$html_image}
            </div><!--.gallery-grid-->
            {$CMS->show_page}
          </div><!--.box-typical-body-->
        </section>


      <div id="box_gallery" class="popup_gallery mfp-hide">
            <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['gallery_add_submit']}</p>
            {$CMS->global->languageTab('langTab')}
            <form id="gallery_form" onsubmit="return submitGallery(this, event);" action="{$CMS->vars['root_domain']}/?site=gallery&act=add_do" name="gallery_form" enctype="multipart/form-data">
                <ul class="list_field_gallery">
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['cat_id']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control select2" name="cat_id" defaultvalue="{$data['cat_id']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_category']}">
                                <option value="">{$CMS->lang['select_category']}</option> 
                                {$CMS->gallery->load_cate_gallery()}
                               </select> 
                          </div>
                        </div>
                    </li>
EOF;
        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF

                <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12 langTab" lang="{$langCode}">
                    <div class="form-group row">
                      <label class="form-control-label">{$CMS->lang['gallery_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="gallery_name[{$langCode}]" id="gallery_name[{$langCode}]" value="{$data['gallery_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_name']}">
                      </div>
                    </div>
                </li>
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 langTab" lang="{$langCode}">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['gallery_note']}</label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control" name="gallery_description[{$langCode}]" id="gallery_description[{$langCode}]" cols="60" rows="5" >{$data['gallery_description']}</textarea>
                    </div>
                  </div>
                </li>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label">{$CMS->lang['gallery_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="gallery_name" value="{$data['gallery_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_name']}">
                      </div>
                    </div>
                </li>

                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['gallery_note']}</label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control" name="gallery_description" cols="60" rows="5" >{$data['gallery_description']}</textarea>
                    </div>
                  </div>
                </li>
EOF;
        }


    $output .= <<<EOF

                    

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="max-height: 300px; overflow-y: scroll;">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['gallery_image']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          

                          <div class="box-typical-upload box-typical-upload-in">
                                <div class="drop-zone fileinput-button" style="width: 100%">
                                    <img id="upload_img_show"  width="205" src="{$src_image_upload}" {$style_display}  />
                                    <i class="font-icon font-icon-cloud-upload-2"></i>
                                    <div class="drop-zone-caption">Drag file to upload</div>
                                    <input type="file" multiple name="list_image[]" id="list_image" class="multiple_upload" accept="image/*">
                                </div><!--.drop-zone-->
                            <p class="box_error" style="display: none;"></p>
                            <h6 class="uploading-list-title title_upload" style="display: none;">{$CMS->lang['title_note_uploading']}</h6>
                            <ul class="uploading-list list_upload">
                                
                            </ul>
                        </div>
                      </div>
                    </li>
                    
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['gallery_image_alt']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="gallery_image_alt" value="{$data['gallery_image_alt']}">
                          </div>
                        </div>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <div class="form-group row">
                          <label class="form-control-label"></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <button class="btn" type="submit">{$CMS->lang['gallery_add_submit']}</button>
                          </div>
                        </div>
                    </li>
                
                </ul>
            
            </form>
        </div>


        <div id="box_edit_gallery" class="popup_gallery mfp-hide">
            <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['gallery_edit_form']}</p>
            {$CMS->global->languageTab('langTab')}
            <form id="gallery_edit_form" onsubmit="return submitGallery(this, event);" action="{$CMS->vars['root_domain']}/?site=gallery&act=edit_do" name="gallery_edit_form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="" />
                <ul class="list_field_gallery">
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['cat_id']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control select2" name="cat_id" defaultvalue="{$data['cat_id']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_category']}">
                                <option value="">{$CMS->lang['select_category']}</option> 
                                {$CMS->gallery->load_cate_gallery()}
                               </select> 
                          </div>
                        </div>
                    </li>
EOF;
        
 
      

        if($CMS->vars['translations'])
        {
            foreach ($CMS->vars['translations'] as $langCode => $langName)
            {
                $output .= <<<EOF

                <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12 langTab" lang="{$langCode}">
                    <div class="form-group row">
                      <label class="form-control-label">{$CMS->lang['gallery_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="gallery_name[{$langCode}]" id="gallery_name[{$langCode}]" value="{$data['gallery_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_name']}">
                      </div>
                    </div>
                </li>
                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 langTab" lang="{$langCode}">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['gallery_note']}</label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control" name="gallery_description[{$langCode}]" id="gallery_description[{$langCode}]" cols="60" rows="5" >{$data['gallery_description']}</textarea>
                    </div>
                  </div>
                </li>
        
EOF;
            }
        }
        else
        {
            $output .= <<<EOF
            
                <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                    <div class="form-group row">
                      <label class="form-control-label">{$CMS->lang['gallery_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                      <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="gallery_name" value="{$data['gallery_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_name']}">
                      </div>
                    </div>
                </li>

                <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['gallery_note']}</label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <textarea class="form-control" name="gallery_description" cols="60" rows="5" >{$data['gallery_description']}</textarea>
                    </div>
                  </div>
                </li>
EOF;
        }

    if(ezy::$theme_key == "dsg")
          {

             $output .=<<<EOF
              <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                 {$this->list_hair_color()}
              </li>
EOF;
       
          }
    $output .= <<<EOF


                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="max-height: 300px; overflow-y: scroll;">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['gallery_image']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <label style="display: inline;" for="upload_type_file"><input checked type="radio" name="upload_type" id="upload_type_file" value="file"> Upload file</label>
                            <label style="display: inline;" for="upload_type_url"><input type="radio" name="upload_type" id="upload_type_url" value="url"> URL</label>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 upload_type_wrap upload_type_file">
                              <div class="box-typical-upload box-typical-upload-in">
                                    <div class="drop-zone fileinput-button" style="width: 100%">
                                        
                                        <i class="font-icon font-icon-cloud-upload-2"></i>
                                        <div class="drop-zone-caption">Drag file to upload</div>
                                        <input type="file" name="list_image[]" id="list_image" class="multiple_upload" accept="image/*">
                                    </div><!--.drop-zone-->
                                    <img id="upload_img_show" width="205" src="" />
      
                                <h6 class="uploading-list-title title_upload" style="display: none;">Uploading</h6>
                                <ul class="uploading-list list_upload">
                                </ul>
                            </div>
                          </div>
                      <div style="display:none" class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 upload_type_wrap upload_type_url">
                            <input class="form-control" type="text" name="url_image" value="">
                       </div>
                    </li>
                    
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['gallery_image_alt']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="gallery_image_alt" value="{$data['gallery_image_alt']}">
                          </div>
                        </div>
                    </li>

                    <li class="col-xl-3 col-lg-3 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['gallery_display']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <select name="gallery_display" class="form-control auto_select" defaultvalue="{$data['gallery_display']}">
                                <option value="0">{$CMS->lang['no']}</option>
                                <option value="1">{$CMS->lang['yes']}</option>
                            </select>
                          </div>
                        </div>
                    </li>
                    
                    <li class="col-xl-3 col-lg-3 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['gallery_sort_order']}</label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                              <input class="form-control" type="number" min="0" name="gallery_sort_order" value="{$data['gallery_sort_order']}">
                          </div>
                        </div>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <div class="form-group row">
                          <label class="form-control-label"></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <button class="btn" type="submit">{$CMS->lang['gallery_edit_submit']}</button>
                          </div>
                        </div>
                    </li>
                
                </ul>
            
            </form>
        </div>  

        <script>
          $(document).ready(function() {
            $(".add_gallery").click(function(){
                $.magnificPopup.open({
                  type: 'inline',
                  preloader: false,
                  focus: '#name',
                  items: {
                    src: '#box_gallery'
                  },

                  // When elemened is focused, some mobile browsers in some cases zoom in
                  // It looks not nice, so we disable it:
                  callbacks: {
                    beforeOpen: function() {
                      if($(window).width() < 700) {
                        this.st.focus = false;
                      } else {
                        this.st.focus = '#name';
                      }
                    }
                  }
                });
            });
            
          });
        </script>

        <script>
    $(document).ready(function(){
      $('.gallery-grid').magnificPopup({
        delegate: 'button.view_detail',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
          enabled: true,
          navigateByImgClick: true,
          preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
          tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
          titleSrc: function(item) {
            return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
          }
        }
      });

      validate_form_custom("#gallery_form", "button[type='submit']", "gallery");
      validate_form_custom("#gallery_edit_form", "button[type='submit']", "gallery");
    });
  </script>
EOF;

        //Modal change category
        $output .= <<<EOF
<div class="modal fade" id="modal-change-category" role="dialog">
    <form class="form-inline" action="/action_page.php">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Change category</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label style="display: inline" for="email">Choose category:</label>
            <select class="form-control" name="category" id="g-category-options">
                {$CMS->gallery->load_cate_gallery()}
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="galleryChangeCategoryDo();">OK</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    </form>
  </div>
EOF;


	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function gallery_middle($result)
{
	global $CMS, $DB, $member;

	$output = "";
  $btn_control = "";
  if($CMS->permit['gallery_edit'])
  {
    $btn_control .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=gallery&act=edit&id={$result['gallery_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['gallery_delete'])
  {
    $btn_control .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=gallery&act=delete&id={$result['gallery_id']}');" class="edit" aria-describedby="ui-id-17"><i class="fa fa-trash"></i></a>
EOF;

  }

$output .= <<<EOF
    <tr bgcolor="{$result['bgcolor']}">
      <td>#{$result['gallery_id']}</td>
      <td>{$result['gallery_name_bk']}</td>
      <td>{$result['cat_name']}</td>
      <td>{$result['gallery_is_hot_bk']}</td>
      <td>{$result['gallery_display_bk']}</td>
      <td>{$result['gallery_time']}</td>
      <td align="center">{$btn_control}</td>
    </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function gallery_none()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
  <tr>
    <td colspan="9">No data</td>
  </tr>
EOF;

	return $output;
}

//===========================================================================
//  HTML FOOTER
//===========================================================================

public function gallery_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
</div>
<input type="hidden" name="data_cnt" value="{$CMS->gallery->record_cnt}">
</div>
<div class="block_bottom">{$CMS->gallery->show_page}</div>
</form>

<script language="javascript">rebuild_form("gallery");</script>
<script language="javascript">arrange_setup("{$CMS->gallery->arrange_data}");</script>
EOF;
  // unset($_SESSION['highlight']);
	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function gallery_control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<div class="block_action">
	<div class="block_action_check" onclick="javascript:form_checkall('gallery');" id="checkall">
    	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
	</div>
	<div class="block_action_left">
        <select class="select2" style="margin-left: 6px;;" class="input_text" name="act" style="width: 300px;" defaultvalue="delete_all" emsg="{$CMS->lang['gallery_incomplete_action']}" ehide="1">{$CMS->vars['action_controller']}</select>
        &nbsp;
        <input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['gallery_action_submit']} ">
    </div>
    <div class="block_action_right">
        <script type="text/javascript">permission_text("gallery_search", '<input class="input_submit" type="button" name="submit" value="{$CMS->lang['search_form']}" onclick="javascript:window.location.href=\'{$CMS->vars['root_domain']}/?site=gallery&act=search{$CMS->class->search->url_return}\'" />');</script>
    </div>
</div>
EOF;

    return $output;
}

//===========================================================================
//  HTML EDIT
//===========================================================================

public function edit( $data = "", $tplformhair_custom = "")
{
	global $CMS, $DB, $member;
	
	$output = "";
	
    $output = $CMS->class->editor->simple();

$output .= <<<EOF
<form method="post" id="gallery" name="gallery" action="{$CMS->vars['root_domain']}/?site=gallery&id={$data['gallery_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">
    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['gallery_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=gallery{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-6">
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['gallery_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="gallery_name" value="{$data['gallery_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_name']}">
                    </div>
                  </div>

                </div>

                <div class="col-lg-6">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['cat_id']}</label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <select class="form-control auto_select select2" name="cat_id" defaultvalue="{$data['cat_id']}">
                          <option value="">{$CMS->lang['select_category']}</option> 
                          {$CMS->gallery->load_cate_gallery()}
                         </select> 
                    </div>
                  </div>
                </div>

              </div>
            
            {$tplformhair_custom}
            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['gallery_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <textarea class="form-control" name="gallery_description" cols="60" rows="5" >{$data['gallery_description']}</textarea>
              </div>
            </div>


            {$CMS->global->uploadMulti("news_image[]",$data['li_html'])}
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['gallery_is_hot']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="gallery_is_hot" defaultvalue="{$data['gallery_is_hot']}">
                      <option value="0">{$CMS->lang['gallery_no']}</option>
                      <option value="1">{$CMS->lang['gallery_yes']}</option>
                  </select>
                  </div>
                </div>

              </div>
              <div class="col-lg-6">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['gallery_display']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="gallery_display" defaultvalue="{$data['gallery_display']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['status_incomplete']}">
                        {$CMS->vars['display_status']}
                    </select>
                  </div>
                </div>
              </div>
            </div>
            

            


            <div class="form-group row">
              <label class="form-control-label"></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <button class="btn" type="submit">{$CMS->lang['gallery_edit_submit']}</button>
              </div>
            </div>


          </div><!--End col-lg-6 -->

          <div class="col-lg-6">
              
            

              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['meta_title']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="meta_title" cols="60" rows="5" emsg="{$CMS->lang['meta_title_incomplete']}">{$data['meta_title']}</textarea>
                </div>
              </div>

              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['meta_description']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="meta_description" cols="60" rows="5" emsg="{$CMS->lang['meta_description_incomplete']}">{$data['meta_description']}</textarea>
                </div>
              </div>


              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['meta_keywords']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="meta_keywords" cols="60" rows="5" emsg="{$CMS->lang['meta_keywords_incomplete']}">{$data['meta_keywords']}</textarea>
                </div>
              </div>




          </div><!--End col-lg-6 -->

        </div>
      </figure>
    </section>
</form>

<script language="javascript">
  $(document).ready(function(){
    validate_form_custom("#gallery");


  });
</script>

EOF;
	
	return $output;	
}

//===========================================================================
//  HTML ADD
//===========================================================================

public function add( $data )
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
<form method="post" id="gallery" name="gallery" action="{$CMS->vars['root_domain']}/?site=gallery&act=add_do" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

    <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['gallery_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=gallery{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

      <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-6">
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['gallery_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="gallery_name" value="{$data['gallery_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['gallery_incomplete_name']}">
                    </div>
                  </div>

                </div>

                <div class="col-lg-6">
                  <div class="form-group row">
                    <label class="form-control-label">{$CMS->lang['cat_id']}</label>
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                      <select class="form-control select2" name="cat_id" defaultvalue="{$data['cat_id']}">
                          <option value="">{$CMS->lang['select_category']}</option> 
                          {$CMS->gallery->load_cate_gallery()}
                         </select> 
                    </div>
                  </div>
                </div>

              </div>
            

            <div class="form-group row">
              <label class="form-control-label">{$CMS->lang['gallery_description']}</label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <textarea class="form-control" name="gallery_description" cols="60" rows="5" >{$data['gallery_description']}</textarea>
              </div>
            </div>


            {$CMS->global->uploadMulti("news_image[]", $data_image)}
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['gallery_is_hot']}</label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="gallery_is_hot" defaultvalue="{$data['gallery_is_hot']}">
                      <option value="0">{$CMS->lang['gallery_no']}</option>
                      <option value="1">{$CMS->lang['gallery_yes']}</option>
                  </select>
                  </div>
                </div>

              </div>
              <div class="col-lg-6">
                <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['gallery_display']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select" name="gallery_display" defaultvalue="{$data['gallery_display']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['status_incomplete']}">
                        {$CMS->vars['display_status']}
                    </select>
                  </div>
                </div>
              </div>
            </div>
            

            


            <div class="form-group row">
              <label class="form-control-label"></label>
              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <button class="btn" type="submit">{$CMS->lang['gallery_add_submit']}</button>
              </div>
            </div>


          </div><!--End col-lg-6 -->

          <div class="col-lg-6">
              
            

              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['meta_title']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="meta_title" cols="60" rows="5" emsg="{$CMS->lang['meta_title_incomplete']}">{$data['meta_title']}</textarea>
                </div>
              </div>

              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['meta_description']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="meta_description" cols="60" rows="5" emsg="{$CMS->lang['meta_description_incomplete']}">{$data['meta_description']}</textarea>
                </div>
              </div>


              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['meta_keywords']}</label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="meta_keywords" cols="60" rows="5" emsg="{$CMS->lang['meta_keywords_incomplete']}">{$data['meta_keywords']}</textarea>
                </div>
              </div>




          </div><!--End col-lg-6 -->

        </div>
      </figure>
    </section>

</form>
<script language="javascript">
$(document).ready(function(){
    validate_form_custom("#gallery");


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
// print "<pre>";

  // print_r($_SERVER);exit;
	$output = "";
  if($_SESSION['is_mobile'] and !$_SESSION['is_tablet']) 
  {
    $label_125 = 'title-label-120';
    $label_100 = 'title-label-120';
  }else
  {
    $label_125 = 'title-label-125';
    $label_100 = 'title-label-100';
  }

  $html_image = $CMS->gallery->getListImage($data['gallery_id'],1);
$output .= <<<EOF
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang["gallery_header"]}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=gallery" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
    
  <div class="box-typical box-typical box-typical-padding border">
    <div class="row">
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['gallery_name']}</div>
                  <div class="form-control-span2">{$data['gallery_name']}</div>
              </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['cat_id']}</div>
                  <div class="form-control-span2">{$data['cat_name']}</div>
              </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['gallery_description']}</div>
                  <div class="form-control-span2">{$data['gallery_description']}</div>
              </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['meta_title']}</div>
                  <div class="form-control-span2">{$data['meta_title']}</div>
              </div>
        </fieldset>


        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['meta_description']}</div>
                  <div class="form-control-span2">{$data['meta_description']}</div>
              </div>
        </fieldset>

        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['metat_keywords']}</div>
                  <div class="form-control-span2">{$data['metat_keywords']}</div>
              </div>
        </fieldset>
      </div>

      <div class="col-xl-8 col-md-6 col-sm-6 col-xs-12">
          <section class="box-typical box-typical-full-height-with-header">
            <header class="box-typical-header box-typical-header-bordered">
              <div class="tbl-row">
                <div class="tbl-cell tbl-cell-title">
                  <h3>The vectors Gallery</h3>
                </div>
                <!--div class="tbl-cell tbl-cell-actions">
                  <button type="button" class="action-btn view active">
                    <i class="font-icon font-icon-view-grid"></i>
                  </button>
                  <button type="button" class="action-btn view">
                    <i class="font-icon font-icon-view-rows"></i>
                  </button>
                  <button type="button" class="action-btn view">
                    <i class="font-icon font-icon-view-cascade"></i>
                  </button>
                </div-->
              </div>
            </header>
            <div class="box-typical-body">
              <div class="gallery-grid">
                {$html_image}

              </div><!--.gallery-grid-->
            </div><!--.box-typical-body-->
          </section>
      </div>



    </div>
  </div>
</section>

<section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=gallery" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
EOF;

            if($CMS->permit['gallery_delete'] == 1)
            {

              $output .=<<<EOF
              <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=gallery&act=delete&id={$data['gallery_id']}');"  class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['delete']}</a>

EOF;
            
            }

            if($CMS->permit['gallery_edit'] == 1)
            {
              $output .=<<<EOF
              <a href="{$CMS->vars['root_domain']}/?site=gallery&act=edit&id={$data['gallery_id']}"   class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['edit']}</a>
EOF;

            }

$output .=<<<EOF

    </section>

  <script>
    $(document).ready(function(){
      $('.gallery-grid').magnificPopup({
        delegate: 'button.view_detail',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
          enabled: true,
          navigateByImgClick: true,
          preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
          tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
          titleSrc: function(item) {
            return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
          }
        }
      });
    });
  </script>
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
<form method="post" id="gallery" name="gallery" action="{$CMS->vars['root_domain']}/?site=gallery&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=gallery{$CMS->class->search->url_return}">&laquo; {$CMS->lang['gallery_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
  	<th colspan="2">{$CMS->lang['gallery_required_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['gallery_name']}</b></td>
    <td><input class="input_text" size="45" type="text" name="gallery_name" value="{$data['gallery_name']}"></td>
  </tr>
 <tr>
    <td class="left25"><b>{$CMS->lang['cat_id']}</b></td>
    <td>
            <select class="input_text select2" name="cat_id" id="cat_id" defaultvalue="{$CMS->input['cat_id']}" onchange="this.form.submit()">
                <option value=''>{$CMS->lang['all_cate']}</option>
                    {$CMS->config_gallery->load_list_cate()}
            </select>

	</td>
  </tr>
   <tr>
    <td class="left25"><b>{$CMS->lang['gallery_active']}</b></td>
    <td>
            <select class="input_text select2" name="gallery_active" id="gallery_active" defaultvalue="{$CMS->input['gallery_active']}" onchange="this.form.submit()">
                <option value=''>{$CMS->lang['all_active']}</option>
                <option value='0'>{$CMS->lang['gallery_active_0']}</option>
                <option value='1'>{$CMS->lang['gallery_active_1']}</option>
                <option value='2'>{$CMS->lang['gallery_active_2']}</option>
                
		</select>
	</td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['gallery_time']}</b></td>
    <td>{$CMS->lang['search_from']} <input class="input_text" size="25" type="text" name="gallery_time" value="{$data['gallery_time']}" etype="date"> &nbsp; {$CMS->lang['search_to']} <input class="input_text" size="25" type="text" name="gallery_time_to" value="{$data['gallery_time_to']}" etype="date"></td>
  </tr>
  <tr>
  	<th colspan="2">{$CMS->lang['gallery_add_info']}</th>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['gallery_display']}</b></td>
    <td><select class="input_text" name="gallery_display" defaultvalue="{$data['gallery_display']}">{$CMS->vars['display_status']}</select></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom"></div>
</form>
<script language="javascript">rebuild_form("gallery",1,1);</script>
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
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE L.log_request LIKE '%update_royalty%' AND L.log_key LIKE '%gallery%' AND  1=1";
	}
	else if ( $CMS->vars['is_admin'] == 1 )
	{
		//$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE UG.userg_is_root=0";
		$escalate_sql = "LEFT JOIN ".root_table."user AS U ON U.user_id=L.user_id LEFT JOIN ".root_table."user_group AS UG ON UG.userg_id=U.userg_id WHERE L.log_request LIKE '%update_royalty%' AND L.log_key LIKE '%gallery%' AND 1=1";
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
<div class="block_bottom"></div>
</div>
<div class="block_bottom">{$CMS->show_page}</div>
<br />
<script language="javascript">rebuild_form("logs");</script>
EOF;
	
	return $output;	
}


public function list_hair_color($color_default = "")
  {
    global $CMS;
    // Load hair color

    $output .=<<<EOF

  <div class="form-group row">
    <label class="form-control-label">{$CMS->lang['hair_color']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
    <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
      <select class="form-control auto_select" name="hair_color" defaultvalue="{$color_default}">
            {$CMS->vars['list_hair_color_html']}
      </select>
    </div>
  </div>
EOF;
    return $output;
  }


}

?>