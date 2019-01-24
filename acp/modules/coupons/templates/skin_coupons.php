<?php

class skin_coupons {

//===========================================================================
//  HTML HEADER 010
//===========================================================================

public function coupon_header()
{
	global $CMS, $DB, $member;

	$output = "";
        
        $html_image = $CMS->coupons->getListImage();
  
$output .= <<<EOF
  <section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['coupon_header']}</h3>
      <figure class="pull-right right">
EOF;
          if($CMS->permit['coupons_add'])
          {
$output .=<<<EOF

        <a href="{$CMS->vars['root_domain']}/?site=coupons&act=add" title="" class="add_bill  ">{$CMS->lang['coupon_add']}</a>
EOF;
          }
$output .=<<<EOF
            <a class="btn btn-warning" href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache">{$CMS->lang['clear_cache']}</a>
      </figure>
    </figure>
   

    <section class="add_table">
      <section class="box-typical box-typical-full-height-with-header">
          <header class="box-typical-header box-typical-header-bordered">
            <div class="tbl-row">
              <div class="tbl-cell tbl-cell-title">
                <h3>{$CMS->lang['coupon_list']}</h3>
              </div>
            </div>
          </header>
          <div class="box-typical-body">
            <div class="coupon-grid">
              {$html_image}
            </div><!--.coupon-grid-->
          </div><!--.box-typical-body-->
        </section>


      <div id="box_coupon" class="popup_coupon mfp-hide">
            <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['coupon_add_submit']}</p>
            <form id="coupon_form" onsubmit="return submitcoupon(this, event);" action="{$CMS->vars['root_domain']}/?site=coupons&act=add_do" name="coupon_form" enctype="multipart/form-data">
                <ul class="list_field_coupon">
                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['coupon_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="coupon_name" value="{$data['coupon_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['coupon_incomplete_name']}">
                          </div>
                        </div>
                    </li>
                            
                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_coupon_code']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="coupon_coupon_code" value="{$data['coupon_coupon_code']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['coupon_incomplete_code']}">
                        </div>
                      </div>
                    </li>

                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_desc_1']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="coupon_desc_1" value="{$data['coupon_desc_1']}">
                        </div>
                      </div>
                    </li>
					
                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_desc_2']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="coupon_desc_2" value="{$data['coupon_desc_2']}">
                        </div>
                      </div>
                    </li>

                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_desc_3']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="coupon_desc_3" value="{$data['coupon_desc_3']}">
                        </div>
                      </div>
                    </li>
                          
                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="max-height: 300px; overflow-y: scroll;">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_image']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          

                          <div class="box-typical-upload box-typical-upload-in">
                                <div class="drop-zone fileinput-button" style="width: 100%">
                                    <img id="upload_img_show"  width="205" src="{$src_image_upload}" {$style_display}  />
                                    <i class="font-icon font-icon-cloud-upload-2"></i>
                                    <div class="drop-zone-caption">Drag file to upload</div>
                                    <input type="file" multiple name="list_image[]" id="list_image" class="multiple_upload" accept="image/*">
                                </div><!--.drop-zone-->
                            <p class="box_error" style="display: none;"></p>
                            <h6 class="uploading-list-title title_upload" style="display: none;">Uploading</h6>
                            <ul class="uploading-list list_upload">
                                
                            </ul>
                        </div>
                      </div>
                    </li>
                    
                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_image_alt']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="coupon_image_alt" value="{$data['coupon_image_alt']}">
                        </div>
                      </div>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <div class="form-group row">
                          <label class="form-control-label"></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="btn" type="button" name="add_coupons" value="{$CMS->lang['coupon_add_submit']}">
                          </div>
                        </div>
                    </li>
                
                </ul>
            
            </form>
        </div>


        <div id="box_edit_coupon" class="popup_coupon mfp-hide">
            <p style="font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase;">{$CMS->lang['coupon_edit_form']}</p>
            <form id="coupon_edit_form" onsubmit="return submitcoupon(this, event);" action="{$CMS->vars['root_domain']}/?site=coupons&act=edit_do" name="coupon_edit_form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{$data['coupon_id']}" />
                <ul class="list_field_coupon">
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['coupon_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="coupon_name" value="{$data['coupon_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['coupon_incomplete_name']}">
                          </div>
                        </div>
                    </li>
                        
                    <li class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="form-group row">
                          <label class="form-control-label">{$CMS->lang['coupon_coupon_code']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="text" name="coupon_coupon_code" value="{$data['coupon_coupon_code']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['coupon_incomplete_code']}">
                          </div>
                        </div>
                    </li>

                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_desc_1']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" name="coupon_desc_1" value="{$data['coupon_desc_1']}">
                        </div>
                      </div>
                    </li>
					
                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_desc_2']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" name="coupon_desc_2" value="{$data['coupon_desc_2']}">
                        </div>
                      </div>
                    </li>

                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_desc_3']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" name="coupon_desc_3" value="{$data['coupon_desc_3']}">
                        </div>
                      </div>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="max-height: 300px; overflow-y: scroll;">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_image']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
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
                    </li>
                    
                    <li class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                      <div class="form-group row">
                        <label class="form-control-label">{$CMS->lang['coupon_image_alt']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="coupon_image_alt" value="{$data['coupon_image_alt']}">
                        </div>
                      </div>
                    </li>

                    <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
                        <div class="form-group row">
                          <label class="form-control-label"></label>
                          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="btn" type="submit" value="{$CMS->lang['coupon_edit_submit']}">
                          </div>
                        </div>
                    </li>
                
                </ul>
            
            </form>
        </div>  

        <script>
          $(document).ready(function() {
            $(".add_coupon").click(function(){
                $.magnificPopup.open({
                  type: 'inline',
                  preloader: false,
                  focus: '#name',
                  items: {
                    src: '#box_coupon'
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
      $('.coupon-grid').magnificPopup({
        delegate: 'button.view_detail',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        coupon: {
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
         validate_form_custom("#coupon_form", "input[name='add_coupons']", "coupon");
          validate_form_custom("#coupon_edit_form", "button[type='submit']", "coupon");
    });
  
 
  
  </script>




EOF;

	return $output;
}

//===========================================================================
//  HTML MIDDLE
//===========================================================================

public function coupon_middle($result)
{
	global $CMS, $DB, $member;

	$output = "";
  $btn_control = "";
  if($CMS->permit['coupon_edit'])
  {
    $btn_control .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=coupons&act=edit&id={$result['coupon_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;

  }

  if($CMS->permit['coupon_delete'])
  {
    $btn_control .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=coupon&act=delete&id={$result['coupon_id']}');" class="edit" aria-describedby="ui-id-17"><i class="fa fa-trash"></i></a>
EOF;

  }
$output .= <<<EOF
    <tr bgcolor="{$result['bgcolor']}">
      <td>#{$result['coupon_id']}</td>
      <td>{$result['coupon_name_bk']}</td>
      <td>{$result['cat_name']}</td>
      <td>{$result['coupon_is_hot_bk']}</td>
      <td>{$result['coupon_display_bk']}</td>
      <td>{$result['coupon_time']}</td>
      <td align="center">{$btn_control}</td>
    </tr>
EOF;
	
    return $output;
}

//===========================================================================
//  NO DATA
//===========================================================================

public function coupon_none()
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

public function coupon_footer()
{
	global $CMS, $DB, $member;
	
	$output = "";

$output .= <<<EOF
</table>
</div>
<input type="hidden" name="data_cnt" value="{$CMS->coupons->record_cnt}">
</div>
<div class="block_bottom">{$CMS->show_page}</div>
</form>

<script language="javascript">rebuild_form("coupon");</script>
<script language="javascript">arrange_setup("{$CMS->coupons->arrange_data}");</script>
EOF;

	return $output;
}

//===========================================================================
//  USER CONTROL
//===========================================================================

public function coupon_control()
{
	global $CMS, $DB, $member;
    
	$output = "";

$output .= <<<EOF
<div class="block_action">
	<div class="block_action_check" onclick="javascript:form_checkall('coupon');" id="checkall">
    	<input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
	</div>
	<div class="block_action_left">
        <select style="margin-left: 6px;;" class="input_text" name="act" style="width: 300px;" defaultvalue="delete_all" emsg="{$CMS->lang['coupon_incomplete_action']}" ehide="1">{$CMS->vars['action_controller']}</select>
        &nbsp;
        <input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['coupon_action_submit']} ">
    </div>
    <div class="block_action_right">
        <script type="text/javascript">permission_text("coupon_search", '<input class="input_submit" type="button" name="submit" value="{$CMS->lang['search_form']}" onclick="javascript:window.location.href=\'{$CMS->vars['root_domain']}/?site=coupon&act=search{$CMS->class->search->url_return}\'" />');</script>
    </div>
</div>
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

  $html_image = $CMS->coupons->getListImage($data['coupon_id'],1);

$output .= <<<EOF
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang["coupon_header"]}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=coupon" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
    
  <div class="box-typical box-typical box-typical-padding border">
    <div class="row">
      <div class="col-xl-4 col-md-6 col-sm-6 col-xs-12">
        <fieldset class="row">
          <div class="form-control-label2" >
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['coupon_name']}</div>
                  <div class="form-control-span2">{$data['coupon_name']}</div>
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
                  <div class="title_label {$label_100} pull-left">{$CMS->lang['coupon_description']}</div>
                  <div class="form-control-span2">{$data['coupon_description']}</div>
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
                  <h3>The vectors coupon</h3>
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
              <div class="coupon-grid">
                {$html_image}

              </div><!--.coupon-grid-->
            </div><!--.box-typical-body-->
          </section>
      </div>



    </div>
  </div>
</section>

<section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=coupon" class="pull-left cancel"><i class="fa fa-mail-reply-all"></i><span>{$CMS->lang['title_back_list']}</span></a>
EOF;

            if($CMS->permit['coupon_delete'] == 1)
            {

              $output .=<<<EOF
              <a onclick="delete_confirm('{$CMS->vars['root_domain']}/?site=coupon&act=delete&id={$data['coupon_id']}');"  class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['delete']}</a>

EOF;
            
            }

            if($CMS->permit['coupon_edit'] == 1)
            {
              $output .=<<<EOF
              <a href="{$CMS->vars['root_domain']}/?site=coupon&act=edit&id={$data['coupon_id']}"   class="pull-right add_cart_2 hidden-md-down">{$CMS->lang['edit']}</a>
EOF;

            }

$output .=<<<EOF

    </section>

  <script>
    $(document).ready(function(){
      $('.coupon-grid').magnificPopup({
        delegate: 'button.view_detail',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        coupon: {
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




  public function editor() 
  {
    global $CMS, $DB, $member;
    
    list($count_g, $gc_output)= $CMS->giftcards->library_giftcard();            
    // format
    $maskFormat = preg_replace('/[^\/]/','0',$CMS->vars['date_format']) ;
    $maskPlaceHolder = preg_replace('/[^\/]/','_',$CMS->vars['date_format']);

    $out=<<<EOF
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['coupon_add_submit']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=coupons" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
    
           <!-- Font Awesome 3.0 -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/font-awesome.css" rel="stylesheet">
        <!-- Annimate -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/animate.css" rel="stylesheet">

        <!-- app Style -->
        <link  href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/app.css" rel="stylesheet">
        <!-- app Responsive Style -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/jquery-ui-1.8.17.custom.css" rel="stylesheet">

        <!-- Roboto Google font-->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
        <!-- google font Loader API -->

        <!-- color picker Style -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/pick-a-color-1.1.7.min.css" rel="stylesheet">

        <!-- Modernizr-->
        <script type="text/javascript" src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/modernizr.custom.28468.js"></script>

   <form id="coupon_form" onsubmit="return submitcoupon(this, event);" action="{$CMS->vars['root_domain']}/?site=coupons&act=add_do" name="coupon_form" enctype="multipart/form-data">
    <figure class="box-typical box-typical box-typical-padding border">
 
      

      <div class="row">     
          <div class="col-md-6">
              <div class="row">
                <div class="col-xl-6">  
                    <fieldset class="form-group">
                      <label class="form-label">{$CMS->lang['coupon_name']}<span style="color:red">(*)</span></label>
                       
                         <div class="form-control-wrapper">
                          <input class="form-control " name="coupon_name" value="{$data['coupon_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['coupon_incomplete_name']}">
                        </div>
                  </fieldset>
                 </div>
                 <div class="col-xl-6">    
                   <fieldset class="form-group">
                      <label class="form-label">{$CMS->lang['coupon_image_alt']}</label>
                       
                         <div class="form-control-wrapper">
                          <input class="form-control " name="coupon_image_alt" value="{$data['coupon_image_alt']}">
                        </div>
                  </fieldset>

                 </div>
             </div>     
             <fieldset class="form-group">
                                <label class="form-label">{$CMS->lang['option_upload_image']}</label>
                                <!-- option_upload_image -->
                                    <div class="radio checkbox w25">
                                        <input type="radio" name="option_upload_image" id="radio-method-0" value="0" checked="">
                                        <label for="radio-method-0">{$CMS->lang['option_upload_image_0']}</label>
                                    </div>
                                   <div class="radio checkbox w25">
                                        <input type="radio" name="option_upload_image" id="radio-method-1" value="1">
                                        <label for="radio-method-1">{$CMS->lang['option_upload_image_1']}</label>
                                    </div>
                                <!-- End option_upload_image -->
                </fieldset>

          </div><!-- col-md-6-->
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label class="form-label" for="coupon_end_time">{$CMS->lang['coupon_end_date']}</label>
                      <div class="form-control-wrapper form-control-icon-right">
                          <input type="text" class="form-control coupon_datepicker" id="coupon_end_time" name="coupon_end_date" value="{$data['coupon_end_date']}">
                          <i class="font-icon font-icon-calend"></i>
                      </div>
                  </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">{$CMS->lang['coupon_status']}</label>
                    <div class="radio w25">
                      <input type="radio" name="coupon_status" id="radio-show-1" value="1" checked="checked">
                      <label for="radio-show-1">{$CMS->lang['coupon_status_1']}</label>
                    </div>   
                    <div class="radio w25">
                      <input type="radio" name="coupon_status" id="radio-show-0" value="0">
                      <label for="radio-show-0">{$CMS->lang['coupon_status_0']}</label>
                    </div>
                </div>
              </div>

            </div>
          </div><!-- col-md-6-->

      </div>  

       <div class="row" id="option_upload_image_0">

            <div class="form-group">
                        <label class="form-control-label">{$CMS->lang['coupon_image']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          

                          <div class="box-typical-upload box-typical-upload-in">
                                <div class="drop-zone fileinput-button" style="width: 100%">
                                    <img id="upload_img_show"  width="205" src="{$src_image_upload}" {$style_display}  />
                                    <i class="font-icon font-icon-cloud-upload-2"></i>
                                    <div class="drop-zone-caption">Drag file to upload</div>
                                    <input type="file" multiple name="list_image[]" id="list_image" class="multiple_upload" accept="image/*">
                                </div><!--.drop-zone-->
                            <p class="box_error" style="display: none;"></p>
                            <h6 class="uploading-list-title title_upload" style="display: none;">Uploading</h6>
                            <ul class="uploading-list list_upload">
                                
                            </ul>
                        </div>
                      </div>
               </div>         


       </div><!-- end class row opption 1 -->  
       <div class="row" style="display:none" id="option_upload_image_1"> 

          <div class="col-md-4">      
                <div class="span3">
                    <!-- widget  -->
                    <div class="widget">

                        <div class="widget-header">

                            <i class="icon-star"></i>
                            <h3>Text Options</h3>

                        </div> <!-- /widget-header -->

                        <div class="widget-content">   
                        <!-- options -->
                            <label for="designtext"><i class=" icon-edit"></i> Enter text below</label>
                            <!-- Texts on T-shirt -->
                            <div id="texts" class='clearfix'>
                                <!-- Text where tro put text -->
                                <textarea type="text" id='designtext' name='designtext[]' placeholder='Text' class='form-control' style="margin-bottom:5px">add text</textarea>
                                <!-- button to generate new textarea -->
                                <div class='btn pull-right nexText'><i class="icon-plus"></i> New text</div>
                            </div><!-- /.text -->
                            
                       

                            <label><i class="icon-zoom-in"></i> Font Size</label>
                            <!-- Slider to change font size -->
                            <div class="slider">
                                <!-- default value -->
                                <div class='size'>12px</div>
                                <!-- slider generated by jQuery UI -->
                                <div id="slider"></div>
                            </div>

                          
                            <!-- Font color to change color - <i class="icon-magic"></i> icons using Fontawesome  -->
                            <label style="margin-top:13px"><i class="icon-magic"></i> Font Color</label>
                            <!-- input colors -->
                            <input id='color' type="text" style="border: 1px solid rgba(197,214,222,.7); box-shadow: none;font-size: 13px;" class="pick-a-color span8">

                           
                            <!-- section to change Fonts -->
                            <label style="margin-top:10px"><i class="icon-beaker"></i> Fonts</label>
                            <!-- btn groupe using bootstrap - dropup : to show menu up -->
                            <div class="btn-group dropup">
                                
                            
                            <div class="dropdown">
                              <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Select font
                              </button>
                              <div class="dropdown-menu" id="font" aria-labelledby="dd-header-add">
                                   <li><a class="dropdown-item" data-font = 'Cantora+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/1.jpg" alt=""></a></li>
                                    <li><a href="#" data-font ='Londrina+Outline'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/2.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Raleway'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/3.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Kavoon'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/4.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Kotta+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/5.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Parisienne'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/6.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Amarante'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/7.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Caesar+Dressing'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/8.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Spirax'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/9.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Indie+Flower'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/10.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Erica+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/11.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='UnifrakturMaguntia'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/12.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Shojumaru'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/13.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Finger+Paint'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/14.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Sigmar+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/15.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Petit+Formal+Script'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/16.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Monoton'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/17.jpg" alt=""></a></li>
                                 <li><a href="#" data-font ='Lobster'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/18.png" alt=""></a></li>
                                  
                              </div>
                          </div>
                              <!-- google fonts  - images - data-font contain name of font-->
 
                            </div>
                        </div><!-- widget content -->
                    </div><!-- widget -->

                </div><!-- Span3 -->
               </div><!--end col 6 -->
               
               <div class="col-md-8" style="margin-bottom:5px">           
                <div class="website-info-wrap">
                    <script>
                        function toggleWebInfo(){
                            $("#website-info-content").toggle();
                            if($("#website-info-content").is(":visible"))
                            {
                                $(".website-info-content-toggle").removeClass("fa-caret-down").addClass("fa-caret-up");
                            }
                            else
                            {
                                $(".website-info-content-toggle").removeClass("fa-caret-up").addClass("fa-caret-down");
                            }
                        }
                    </script>
                    <h6 style="cursor: pointer;" onclick="toggleWebInfo();">Website information <i class="fa fa-caret-down website-info-content-toggle"></i></h6>
                    <div id="website-info-content" style="display: none">
                        <div class="row">
                            <div class="col-xs-3"><strong>Name: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_name']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Address: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_address']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Email: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_email']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Phone: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_phone']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Open hours (Short): </strong></div>
                            <div class="col-xs-9">{$CMS->config_general->getOpenhoursHtml()[1]}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Open hours (Full): </strong></div>
                            <div class="col-xs-9">{$CMS->config_general->getOpenhoursHtml()[0]}</div>
                        </div>
                    </div>
                </div>
                 <div class="row">
                   <div class="span6">
                  
                    <div class='designContainer' id='printable' style="float:left;max-height:400px;"  >
                        
                        <!-- Text container  - class ='no-delete' we can't delete it (the originale text) -->

                        <div class='text t designtext1 no-delete ' style=" z-index:20;">
                            <!-- icon clickable to remove text -  -->
                            <i class="icon-remove action text-error" data-action = 'remove'></i>
                            <p style="border: 1px dashed #fff;">add text </p>
                            <!-- icon clickable to Edit text (font size, color, font) -->
                            <i class="icon-edit action" data-action = 'fsontSize'></i>
                             <i class="icon-rotation action fa fa-rotate-left" id="rotation" style="position:absolute;bottom: 5;right: 5;height: 10;width: 10;z-index:21;margin-left:4px" data-action = 'rotation'></i>
                        </div><!--  /.text-->


                        
                        <!-- default T-shirt -->
                        <img id='Tshirtsrc'   style="max-width:100%" src="" alt="">
                     

                    </div><!-- /. designContainer -->

            
                </div> <!-- span6 -->

                </div><!-- end class row -->
                <div class="row" style="margin-top:5px">  
                <div class="navbarupload">
                                <!-- navbar container -->
                                <div class="navbar-inner">
                                    <!-- actions -->
                                    <ul class='nav' style="margin-top:5px">
                                        
                                        <!-- Separator -->
                                        <li class="divider-vertical"></li>
                                        <!-- Print design -->
                                        <li>
                                            
                                        </li>
                                    
                                        <li class="divider-vertical"></li>
                                 
                                        <li>
                                              
                                               <div style="margin-top:20px"> 
                                              
                                               
                                                  <span class="btn btn-rounded btn-file method-coupon" mtgc="1">
                                                        <span>Choose file</span>
                                                        <input type="file" name="upload_image_method_1"  onchange="uploadPhotos()" >
                                                    </span>

EOF;
                                              if($count_g > 0)
                                              {
                                                $out .=<<<EOF
                                                 <span class="btn btn-rounded btn-file method-coupon" mtgc="2">
                                                        <span>Image gallery</span>
                                                        
                                                  </span>
EOF;

                                              }
                                                $out .=<<<EOF
                                                

                                               </div>
                                                {$gc_output}
EOF;
                                     
                                        $out .=<<<EOF

                                                <input type='hidden' id="src_image_library" val='' />

                                        </li>

                                        <li class="divider-vertical"></li>
 

                                    </ul>
                                </div><!-- /.navbar-inner -->
                            </div><!-- navbar -->
                 
                </div><!-- end row -->   
            </div><!-- end col 8 -->
              

       </div><!-- end row -->   

 
        <!-- Jquery UI -->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/jquery-ui-1.10.3.custom.min.js"></script>  
        <!-- Color picker Scripts --> 
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/pick-a-color-1.1.7.min.js"></script>   
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/tinycolor-0.9.15.min.js"></script>
        <!-- Print Script -->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/jquery.print-preview.js"></script> 
        <!-- Html2canvas script -->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/html2canvas.js"></script> 
        <!-- Convas To image script-->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/Canvas2Image.js"></script> 
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/base64.js"></script> 
     

        <!-- Default Script call -->
        <script type="text/javascript" src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/app.js"></script>

             <script type="text/javascript" src="{$CMS->vars['root_domain']}/jsacp/coupon.js"></script>    
 
      
  
  </figure>
  
  <section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=coupons" class="pull-left cancel">{$CMS->lang['coupon_header_back']}</a>
       
        <button type="button" name="add_coupons" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['coupon_add_submit']}</span><span class="ladda-spinner"></span></button>
    </section>
 </form>

 </section>

  <script>
    $(document).ready(function(){
      validate_form_custom("#coupon_form", "button[name='add_coupons']", "coupon");
      validate_form_custom("#coupon_edit_form", "button[id='add_coupons']", "coupon");
      // coupon date picker
      $(".coupon_datepicker").datetimepicker({ format:'{$CMS->vars['date_format']}'});
      $(".coupon_datepicker").mask("{$maskFormat}", {placeholder: "{$maskPlaceHolder}"});
    });
  </script>




<script>
      
window.uploadPhotos = function(url){
    // Read in file
    var file = event.target.files[0];

    // Ensure it's an image
    if(file.type.match(/image.*/)) {
    

        // Load the image
        var reader = new FileReader();
        reader.onload = function (readerEvent) {
            var image = new Image();
            image.onload = function (imageEvent) {

                // Resize the image
                var canvas = document.createElement('canvas'),
                    max_size = 544,// TODO : pull max size from a site config
                    width = image.width,
                    height = image.height;
                if (width > height) {
                    if (width > max_size) {
                        height *= max_size / width;
                        width = max_size;
                    }
                } else {
                    if (height > max_size) {
                        width *= max_size / height;
                        height = max_size;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
                var dataUrl = canvas.toDataURL('image/jpeg');
                var resizedImage = dataURLToBlob(dataUrl);
                $.event.trigger({
                    type: "imageResized",
                    blob: resizedImage,
                    url: dataUrl
                });
            }
           image.src = readerEvent.target.result;
            document.getElementById("Tshirtsrc").src  = image.src ;
            $(".designtext1").show();
        }
        reader.readAsDataURL(file);
    }
};

/* Utility function to convert a canvas to a BLOB */
var dataURLToBlob = function(dataURL) {
    var BASE64_MARKER = ';base64,';
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        var parts = dataURL.split(',');
        var contentType = parts[0].split(':')[1];
        var raw = parts[1];

        return new Blob([raw], {type: contentType});
    }

    var parts = dataURL.split(BASE64_MARKER);
    var contentType = parts[0].split(':')[1];
    var raw = window.atob(parts[1]);
    var rawLength = raw.length;

    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], {type: contentType});
}
 
</script>
EOF;
    return $out;
  }





  public function editor_edit($data = "") 
  {
    global $CMS, $DB, $member;
    list($count_g, $gc_output)= $CMS->giftcards->library_giftcard();            
    // format
    $maskFormat = preg_replace('/[^\/]/','0',$CMS->vars['date_format']) ;
    $maskPlaceHolder = preg_replace('/[^\/]/','_',$CMS->vars['date_format']);
    $data['coupon_end_date'] = $data['coupon_end_date'] ? $CMS->class->date->date_format( $data['coupon_end_date']) : "";

    $out .=<<<EOF
<section class="add_form main_form">
  <figure class="heading">
    <h3>{$CMS->lang['coupon_edit_submit']}</h3>
      <a href="{$CMS->vars['root_domain']}/?site=coupons" title=""><span class="font-icon font-icon-del"></span></a>
  </figure>
    
           <!-- Font Awesome 3.0 -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/font-awesome.css" rel="stylesheet">
       
        <!-- Annimate -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/animate.css" rel="stylesheet">

        <!-- app Style -->
        <link  href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/app.css" rel="stylesheet">
        <!-- app Responsive Style -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/jquery-ui-1.8.17.custom.css" rel="stylesheet">

        <!-- Roboto Google font-->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
        <!-- google font Loader API -->

        <!-- color picker Style -->
        <link href="{$CMS->vars['root_domain']}/assets/t_shirt_designer/css/pick-a-color-1.1.7.min.css" rel="stylesheet">

        <!-- Modernizr-->
        <script type="text/javascript" src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/modernizr.custom.28468.js"></script>

   <form id="coupon_form" onsubmit="return submitcoupon(this, event);" action="{$CMS->vars['root_domain']}/?site=coupons&act=edit_do&id={$data['coupon_id']}" name="coupon_form" enctype="multipart/form-data">
    <figure class="box-typical box-typical box-typical-padding border">
 
      

      <div class="row">     
          <div class="col-md-6">
               <div class="row">
                <div class="col-xl-6">  
                    <fieldset class="form-group">
                      <label class="form-label">{$CMS->lang['coupon_name']}<span style="color:red">(*)</span></label>
                       
                         <div class="form-control-wrapper">
                          <input class="form-control " name="coupon_name" value="{$data['coupon_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['coupon_incomplete_name']}">
                        </div>
                  </fieldset>
                </div>
                <div class="col-xl-6">  

                   <fieldset class="form-group">
                      <label class="form-label">{$CMS->lang['coupon_image_alt']}</label>
                       
                         <div class="form-control-wrapper">
                          <input class="form-control " name="coupon_image_alt" value="{$data['coupon_image_alt']}">
                        </div>
                  </fieldset>
               </div>
             </div>  
            
             <fieldset class="form-group">
                                <label class="form-label">{$CMS->lang['option_upload_image']}</label>
                                <!-- option_upload_image -->
                                    <div class="radio checkbox w25">
                                        <input type="radio" name="option_upload_image" id="radio-method-0" value="0" checked="">
                                        <label for="radio-method-0">{$CMS->lang['option_upload_image_0']}</label>
                                    </div>
                                   <div class="radio checkbox w25">
                                        <input type="radio" name="option_upload_image" id="radio-method-1" value="1">
                                        <label for="radio-method-1">{$CMS->lang['option_upload_image_1']}</label>
                                    </div>
                                <!-- End option_upload_image -->
                </fieldset>

          </div><!-- col-md-6-->
          <div class="col-md-6">
              <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label class="form-label" for="coupon_end_time">{$CMS->lang['coupon_end_date']}</label>
                      <div class="form-control-wrapper form-control-icon-right">
                          <input type="text" class="form-control coupon_datepicker" id="coupon_end_time" name="coupon_end_date" value="{$data['coupon_end_date']}">
                          <i class="font-icon font-icon-calend"></i>
                      </div>
                  </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">{$CMS->lang['coupon_status']}</label>
                    <div class="radio w25">
                      <input type="radio" name="coupon_status" id="radio-show-1" value="1" checked="checked">
                      <label for="radio-show-1">{$CMS->lang['coupon_status_1']}</label>
                    </div>   
                    <div class="radio w25">
                      <input type="radio" name="coupon_status" id="radio-show-0" value="0">
                      <label for="radio-show-0">{$CMS->lang['coupon_status_0']}</label>
                    </div>
                </div>
              </div>

            </div>
          </div><!-- col-md-6-->

      </div>  

       <div class="row" id="option_upload_image_0">

            <div class="form-group">
                        <label class="form-control-label">{$CMS->lang['coupon_image']}</label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                          

                          <div class="box-typical-upload box-typical-upload-in">
                                <div class="drop-zone fileinput-button" style="width: 100%">
                                   
                                    <i class="font-icon font-icon-cloud-upload-2"></i>
                                    <div class="drop-zone-caption">Drag file to upload</div>
                                    <input type="file" multiple name="list_image[]" id="list_image" class="multiple_upload" accept="image/*">
                                </div><!--.drop-zone-->
                            <p class="box_error" style="display: none;"></p>
                            <h6 class="uploading-list-title title_upload" style="display: none;">Uploading</h6>
                            <ul class="uploading-list list_upload">
                                
                            </ul>
                        </div>
                         <img id="upload_img_show"  width="205" src="{$CMS->vars['upload_url']}/coupon/{$data['coupon_image']}"    />
                      </div>
               </div>         


       </div><!-- end class row opption 1 -->  
       <div class="row" style="display:none" id="option_upload_image_1"> 

          <div class="col-md-4">      
                <div class="span3">
                    <!-- widget  -->
                    <div class="widget">

                        <div class="widget-header">

                            <i class="icon-star"></i>
                            <h3>Text Options</h3>

                        </div> <!-- /widget-header -->

                        <div class="widget-content">   
                        <!-- options -->
                            <label for="designtext"><i class=" icon-edit"></i> Enter text below</label>
                            <!-- Texts on T-shirt -->
                            <div id="texts" class='clearfix'>
                                <!-- Text where tro put text -->
EOF;

                   
                    $coupon_style = json_decode($data['coupon_style_content'],true);
                    $coupon_style = array_reverse($coupon_style);
                    $stt = count($coupon_style);
                    
                  if($stt > 0)
                  {


                    foreach($coupon_style as $key => $value ) {
                        

                        if($stt == 1){ $first_el = " id='designtext' ";  }
                        else { $first_el = " ";  }

                        if($value['text'] != "")
                        {
                         
                         $out .=<<<EOF
                           <textarea type="text"  {$first_el} name='designtext[]' placeholder='Text' data-id="{$stt}" class='form-control  designtext designtext{$stt}' style="margin-bottom:5px">{$value['text']}</textarea>
EOF;
                          $stt--;
                        }
                      
                    }                                 
                  }
                  else
                  { 

                     $out .=<<<EOF
                           <textarea type="text" id='designtext' name='designtext[]' placeholder='Text' data-id="1" class='form-control  designtext designtext1' style="margin-bottom:5px">Add text</textarea>
EOF;
                  }  
                       $out .=<<<EOF
                            
                                <div class='btn pull-right nexText'><i class="icon-plus"></i> New text</div>
                            </div><!-- /.text -->
                            
                        
                            <label><i class="icon-zoom-in"></i> Font Size</label>
                            <!-- Slider to change font size -->
                            <div class="slider">
                                <div class='size'>12px</div>
                                <div id="slider"></div>
                            </div>

                        
                            <!-- Font color to change color - <i class="icon-magic"></i> icons using Fontawesome  -->
                            <label style="margin-top:13px"><i class="icon-magic"></i> Font Color</label>
                            <!-- input colors -->
                            <input id='color' type="text" style="border: 1px solid rgba(197,214,222,.7); box-shadow: none;font-size: 13px;" class="pick-a-color span8">

                          
                            <!-- section to change Fonts -->
                            <label style="margin-top:10px"><i class="icon-beaker"></i> Fonts</label>
                          
                            <div class="btn-group dropup">
                                
                            
                            <div class="dropdown">
                              <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Select font
                              </button>
                              <div class="dropdown-menu" id="font" aria-labelledby="dd-header-add">
                                   <li><a class="dropdown-item" data-font = 'Cantora+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/1.jpg" alt=""></a></li>
                                    <li><a href="#" data-font ='Londrina+Outline'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/2.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Raleway'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/3.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Kavoon'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/4.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Kotta+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/5.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Parisienne'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/6.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Amarante'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/7.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Caesar+Dressing'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/8.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Spirax'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/9.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Indie+Flower'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/10.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Erica+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/11.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='UnifrakturMaguntia'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/12.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Shojumaru'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/13.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Finger+Paint'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/14.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Sigmar+One'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/15.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Petit+Formal+Script'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/16.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Monoton'><img src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/img/fonts/17.jpg" alt=""></a></li>
                                  
                              </div>
                          </div>
                              <!-- google fonts  - images - data-font contain name of font-->
 
                            </div>
                        </div><!-- widget content -->
                    </div><!-- widget -->

                </div><!-- Span3 -->
               </div><!--end col 6 -->
               
               <div class="col-md-8" style="margin-bottom:7px">           
                <div class="website-info-wrap">
                    <script>
                        function toggleWebInfo(){
                            $("#website-info-content").toggle();
                            if($("#website-info-content").is(":visible"))
                            {
                                $(".website-info-content-toggle").removeClass("fa-caret-down").addClass("fa-caret-up");
                            }
                            else
                            {
                                $(".website-info-content-toggle").removeClass("fa-caret-up").addClass("fa-caret-down");
                            }
                        }
                    </script>
                    <h6 style="cursor: pointer;" onclick="toggleWebInfo();">Website information <i class="fa fa-caret-down website-info-content-toggle"></i></h6>
                    <div id="website-info-content" style="display: none">
                        <div class="row">
                            <div class="col-xs-3"><strong>Name: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_name']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Address: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_address']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Email: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_email']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Phone: </strong></div>
                            <div class="col-xs-9">{$CMS->vars['company_phone']}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Open hours (Short): </strong></div>
                            <div class="col-xs-9">{$CMS->config_general->getOpenhoursHtml()[1]}</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><strong>Open hours (Full): </strong></div>
                            <div class="col-xs-9">{$CMS->config_general->getOpenhoursHtml()[0]}</div>
                        </div>
                    </div>
                </div>
                 <div class="row">
                  <div class="span6">
        
 
                    <div class='designContainer' id='printable' style="float:left;max-height:400px;"  >
                        <!-- images and shapes Container -->
                  
                        <!-- Text container  - class ='no-delete' we can't delete it (the originale text) -->
EOF;

                   
                    $coupon_style = json_decode($data['coupon_style_content'],true);
                    $coupon_style = array_reverse($coupon_style);
                    $stt = count($coupon_style);
                   
              
                    if(count($coupon_style) > 0)
                    {
                    foreach ($coupon_style as $key => $value) {
                       if($key == 0){ $no_del = "no-delete";}
                       else{  $no_del = " "; }
                       // Get font
                       //
                        if($stt == 1){ $first_text = " text ";  }
                        else {$first_text = " ";}
                       $style_p_push = explode("; ", $value['style_p_push']);
                       
                       foreach ($style_p_push as $key_s => $value_s) {

                         $s_2 = explode(":", $value_s);
                         if($s_2[0] == "font-family" )
                         {     
                            $s_2[1] = str_replace('&quot;', "", $s_2[1]);
                        
                            $s_2[1] = rtrim($s_2[1],";"); 
                            $s_2[1] = trim($s_2[1]);
                              // echo  $font;exit;
                            $font .=  $s_2[1].",";
                          
                         }
                       }
                      if($value['text'] != "")
                      {
                         
                       $out .=<<<EOF
                        <div class='{$first_text} text{$stt} t designtext{$stt} {$no_del} ' style="{$value['style_push']}" data-id="{$stt}">
                            <i class="icon-remove action text-error" data-action = 'remove'></i>
                            <p style="border: 1px dashed #fff;{$value['style_p_push']}">{$value['text']}</p>
                          
                            <i class="icon-edit action" data-action = 'fsontSize'></i>
                             <i class="icon-rotation action fa fa-rotate-left" id="rotation" style="position:absolute;bottom: 5;right: 5;height: 10;width: 10;z-index:21;margin-left:4px" data-action = 'rotation'></i>
                        </div> 
EOF;
                        $stt--;
                       } 
                    }
                    
                   
                     $out .=<<<EOF

                    <script>
                        $('#printable .t').draggable({ containment: "#printable" })
                         $('#printable .t').find('p').resizable();
                       
                         $('#printable .t').find('.icon-rotation').draggable({ 
                             opacity: 0.01, 
                              helper: 'clone',
                            drag: function(event, ui){
                                 console.log(ui.position.left);
                                var rotateCSS = 'rotate(' + ui.position.left + 'deg)';

                                $(this).parent().css({
                                    '-moz-transform': rotateCSS,
                                    '-webkit-transform': rotateCSS
                                });
                          }      
                       }); 
                        
                    </script>
                    
                  
EOF;
                    $font_nk = explode(",", $font);
                    foreach ($font_nk as $key_f => $value_f) {
                        if($value_f != "")
                        {
                          $new_font_f .= "'".$value_f."',";
                        }
                    }
               
                  $new_font_f = rtrim($new_font_f,",");
              
                  
                  if($new_font_f != "")
                  { 
                    $new_font_f = trim($new_font_f);  
                    
                    $out .=<<<EOF

                    <script src="http://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js"></script> 
                  <script> 
                        WebFont.load({
                                    google: { 
                                           families: [ {$new_font_f} ] 
                                     } 
                         }); 
                   </script>    

EOF;
                  }
      }//End if count
      else
      {
               $out .=<<<EOF
                        <div class='text text1 t designtext1 no-delete' style="" data-id="1">
                            <i class="icon-remove action text-error" data-action = 'remove'></i>
                            <p style="border: 1px dashed #fff;">Add text</p>
                          
                            <i class="icon-edit action" data-action = 'fsontSize'></i>
                             <i class="icon-rotation action fa fa-rotate-left" id="rotation" style="position:absolute;bottom: 5;right: 5;height: 10;width: 10;z-index:21;margin-left:4px" data-action = 'rotation'></i>
                        </div> 
EOF;
            }
                      if($data['coupon_image_original'] != "")
                      {
                        $coupon_image_original = "{$CMS->vars['upload_url']}/coupon/{$data['coupon_image_original']}";
                      }

                      $out .=<<<EOF
                        
            
                        <img id='Tshirtsrc'   style="max-width:100%" src="{$coupon_image_original}" alt="Add image">
                     

                    </div><!-- /. designContainer -->

                  
    
                    </div> <!-- span6 -->
                  </div><!-- class row -->
                   <div class="row">  
                <div class="navbarupload">
                                <!-- navbar container -->
                                <div class="navbar-inner">
                                    <!-- actions -->
                                    <ul class='nav'>
                                        
                                        <!-- Separator -->
                                        <li class="divider-vertical"></li>
                                        <!-- Print design -->
                                        <li>
                                             
                                          
                                        </li>
                                    
                                        <li class="divider-vertical"></li>
                                 
                                        <li>
                                              <div style="margin-top:20px"> 
                                                 
                                                  <span class="btn btn-rounded btn-file">
                                                        <span>Choose file</span>
                                                        <input type="file" name="upload_image_method_1"  onchange="uploadPhotos()" >
                                                    </span>
EOF;
                                              if($count_g > 0)
                                              {
                                                $out .=<<<EOF
                                                 <span class="btn btn-rounded btn-file method-coupon" mtgc="2">
                                                        <span>Image gallery</span>
                                                        
                                                  </span>
EOF;

                                              }
                                                $out .=<<<EOF
                                                

                                               </div>
                                                {$gc_output}
EOF;
                                     
                                        $out .=<<<EOF

                                                <input type='hidden' id="src_image_library" val='' />
                                        </li>

                                        <li class="divider-vertical"></li>
 

                                    </ul>
                                </div><!-- /.navbar-inner -->
                            </div><!-- navbar -->
                 
          </div><!-- end row -->   
                 </div><!-- end col 8 -->
              
                

              

       </div><!-- end row -->   

 
  

        <!-- javascript
        ================================================== -->
        <!-- Placed at the endif of the document so the pages load faster -->
       
 
        <!-- Jquery UI -->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/jquery-ui-1.10.3.custom.min.js"></script>  
        <!-- Color picker Scripts --> 
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/pick-a-color-1.1.7.min.js"></script>   
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/tinycolor-0.9.15.min.js"></script>
        <!-- Print Script -->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/jquery.print-preview.js"></script> 
        <!-- Html2canvas script -->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/html2canvas.js"></script> 
        <!-- Convas To image script-->
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/Canvas2Image.js"></script> 
        <script type="text/javascript"  src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/base64.js"></script> 
     

        <!-- Default Script call -->
        <script type="text/javascript" src="{$CMS->vars['root_domain']}/assets/t_shirt_designer/js/app.js"></script>

             <script type="text/javascript" src="{$CMS->vars['root_domain']}/jsacp/coupon.js"></script>    
 
      
  
  </figure>
  
  <section class="add_cart_footer">
      <a href="{$CMS->vars['root_domain']}/?site=coupons" class="pull-left cancel">{$CMS->lang['coupon_header_back']}</a>
       
        <button type="button" name="add_coupons" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label">{$CMS->lang['coupon_edit_submit']}</span><span class="ladda-spinner"></span></button>
    </section>
 </form>

 </section>

  <script>
    $(document).ready(function(){
      var coupon_upload_option = {$data['coupon_upload_option']};
      if(coupon_upload_option == 1)
      {
        $("#option_upload_image_"+coupon_upload_option).show();
        $("#option_upload_image_0").hide();
        $("#radio-method-1").prop("checked",true);
      }

      validate_form_custom("#coupon_form", "button[name='add_coupons']", "coupon");
      validate_form_custom("#coupon_edit_form", "button[id='add_coupons']", "coupon");
      
      $("input[name='coupon_status'][value='{$data[coupon_status]}']").prop("checked",true);
      // coupon date picker
      $(".coupon_datepicker").datetimepicker({ format:'{$CMS->vars['date_format']}'});
      $(".coupon_datepicker").mask("{$maskFormat}", {placeholder: "{$maskPlaceHolder}"});
    });
  </script>




<script>
      
window.uploadPhotos = function(url){
    // Read in file
    var file = event.target.files[0];

    // Ensure it's an image
    if(file.type.match(/image.*/)) {
    

        // Load the image
        var reader = new FileReader();
        reader.onload = function (readerEvent) {
            var image = new Image();
            image.onload = function (imageEvent) {

                // Resize the image
                var canvas = document.createElement('canvas'),
                    max_size = 544,// TODO : pull max size from a site config
                    width = image.width,
                    height = image.height;
                if (width > height) {
                    if (width > max_size) {
                        height *= max_size / width;
                        width = max_size;
                    }
                } else {
                    if (height > max_size) {
                        width *= max_size / height;
                        height = max_size;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
                var dataUrl = canvas.toDataURL('image/jpeg');
                var resizedImage = dataURLToBlob(dataUrl);
                $.event.trigger({
                    type: "imageResized",
                    blob: resizedImage,
                    url: dataUrl
                });
            }
           image.src = readerEvent.target.result;
            document.getElementById("Tshirtsrc").src  = image.src ;
            $(".designtext1").show();
        }
        reader.readAsDataURL(file);
    }
};

/* Utility function to convert a canvas to a BLOB */
var dataURLToBlob = function(dataURL) {
    var BASE64_MARKER = ';base64,';
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        var parts = dataURL.split(',');
        var contentType = parts[0].split(':')[1];
        var raw = parts[1];

        return new Blob([raw], {type: contentType});
    }

    var parts = dataURL.split(BASE64_MARKER);
    var contentType = parts[0].split(':')[1];
    var raw = window.atob(parts[1]);
    var rawLength = raw.length;

    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], {type: contentType});
}
 
</script>
EOF;
    return $out;
  }





}

?>