<?php
use \core\ezy;
use lib\date;
use lib\input;

class skin_config_general {

//===========================================================================
//  HTML HEADER
//===========================================================================

    public function header()
    {
        global $CMS, $DB, $member;

        $discount_code_checked = $CMS->vars['discount_code'] ? 'checked' : '';

        $output = "";

        $CMS->vars = array_merge($CMS->vars, unserialize($CMS->class->cache->loadsql("config")));

        // Check domain
        $arr_domain = array(".vn.media", ".top.vn");
        $domain_free = "";
        $domain_sub = "";
        $ext_domain = "";
        $check_domain = 1;
        if($CMS->vars['website_domain'])
        {
            $check_domain = 2;
            foreach ($arr_domain as $key => $value)
            {
                $pos = strpos($CMS->vars['website_domain'], $value);
                if($pos !== false)
                {
                    $check_domain = 1;
                    $list_domain = explode($value, $CMS->vars['website_domain']);
                    $domain_free = $list_domain[0];
                    $ext_domain = $value;
                    $domain_sub = "";
                    break;
                }
            }

            if($check_domain == 2)
            {
                $domain_sub = $CMS->vars['website_domain'];
                $domain_free = "";
            }

        }

        // Check image
        $url_image = "";
        $display = $css_logo = "";
        if($CMS->vars['logo_website'])
        {
            $display = " display: block; ";
            $url_image = "{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_website']}";
            $css_logo = "style='display: none;'";
        }

        $url_image_mobile = "";
        $display_mobile = $css_logom = "";
        if($CMS->vars['logo_website_mobile'])
        {
            $display_mobile = " display: block; ";
            $url_image_mobile = "{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_website_mobile']}";
            $css_logom = "style='display: none;'";
        }

        $url_image_avatar = "";
        $display_avatar = $css_logoa = "";
        if($CMS->vars['avatar_website'])
        {
            $display_avatar = " display: block; ";
            $url_image_avatar = "{$CMS->vars['upload_url']}/attach/{$CMS->vars['avatar_website']}";
            $css_logoa = "style='display: none;'";
        }

        $CMS->vars['sitestatus'] = isset($CMS->vars['sitestatus']) ? intval($CMS->vars['sitestatus']) : 0;
        $CMS->vars['recaptcha_google'] = isset($CMS->vars['recaptcha_google']) ? intval($CMS->vars['recaptcha_google']) : 1;
        $CMS->vars['enable_security_ip'] = isset($CMS->vars['enable_security_ip']) ? intval($CMS->vars['enable_security_ip']) : 0;
        $CMS->vars['time_allow_ip'] = isset($CMS->vars['time_allow_ip']) ? intval($CMS->vars['time_allow_ip']) : 5;
        $CMS->vars['booking_enable'] = isset($CMS->vars['booking_enable']) ? intval($CMS->vars['booking_enable']) : 1;
        $CMS->vars['booking_hours_enable'] = isset($CMS->vars['booking_hours_enable']) ? intval($CMS->vars['booking_hours_enable']) : 1;
        $CMS->vars['booking_open_hours'] = isset($CMS->vars['booking_open_hours']) ? intval($CMS->vars['booking_open_hours']) : 0;
        $CMS->vars['booking_email_form_enable'] = isset($CMS->vars['booking_email_form_enable']) ? intval($CMS->vars['booking_email_form_enable']) : 0;


        $CMS->vars['booking_before_day'] = isset($CMS->vars['booking_before_day']) ? intval($CMS->vars['booking_before_day']) : 0;
        $CMS->vars['booking_before_hours'] = isset($CMS->vars['booking_before_hours']) ? intval($CMS->vars['booking_before_hours']) : 4;
        $CMS->vars['date_format'] = $CMS->vars['date_format'] ? $CMS->vars['date_format'] : "YYYY/MM/DD";
        $CMS->vars['phone_format'] = $CMS->vars['phone_format'] ? $CMS->vars['phone_format'] : "(000) 000-0000";

        $CMS->vars['translations'] = @json_decode($CMS->vars['translations'], true);
        $CMS->vars['pagination_number'] = isset($CMS->vars['pagination_number']) ? intval($CMS->vars['pagination_number']) : 1000;
        $CMS->vars['step_time_booking'] = $CMS->vars['step_time_booking'] ? $CMS->vars['step_time_booking'] : 30;

        $CMS->vars['number_call_now'] = isset($CMS->vars['number_call_now']) ? intval($CMS->vars['number_call_now']) : 0;

        $optionCountry = $CMS->country->get_country_option(0,1);
        $CMS->vars['default_country'] = isset($CMS->vars['default_country']) ? $CMS->vars['default_country'] : (defined('is_web_vn') == true ? 238 : 231);

// print "<pre>";
// print_r($CMS->vars);exit;

        $ext_domain = $ext_domain ? $ext_domain : ".vn.media";
        $active_tab = $CMS->input['tab'] ? ltrim($CMS->input['tab'],"#") : "tabs-4-tab-1";
        $free_hidden = (isset($CMS->vars['web_free']) == 1 and $CMS->vars['is_root'] != 1) ? "tab_hidden" : "";

        $tab_shipping_fee = '';
        if( isset($CMS->vars['type_web']) AND $CMS->vars['type_web'] == "ecommerce" )
        {
            $tab_shipping_fee =<<<EOF
    <li class="nav-item">
      <a class="nav-link ontab config-menu" href="{$CMS->vars['root_domain']}/?site=config_general&act=shipping_fee">
        {$CMS->lang['title_tab_shipping_fee']}
      </a>
    </li>
EOF;
        }

        $output .= <<<EOF
 <section class="add_table main_form">
    <figure class="heading">
      <h3>{$CMS->lang['title_header_listing']}</h3>
      &nbsp;
      <span style="float: right;">{$CMS->global->importExportData($CMS->input['site'])}</span>
    </figure>
  <form name="config_general" id="config_general" onsubmit="check_payment_active();return false;" action="{$CMS->vars['root_domain']}/?site=config_general&act=edit_do" method="post" enctype="multipart/form-data">
    <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-inline">
          <ul class="nav" role="tablist">
            <li class="nav-item">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-1" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_general']}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-2" role="tab" data-toggle="tab">
                SEO
              </a>
            </li>
            <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-11" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_advertisement']}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-3" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_company']}
              </a>
            </li>
            <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-4" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_invoice']}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-5" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_date_time']}
              </a>
            </li>
            <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-6" role="tab" data-toggle="tab">
               API
              </a>
            </li>
            <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-9" role="tab" data-toggle="tab">
               Security
              </a>
            </li>    
            <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-7" role="tab" data-toggle="tab">
               {$CMS->lang['title_tab_booking']}
              </a>
            </li>
            <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" key="payment" href="#tabs-4-tab-8" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_payment']}
              </a>
            </li>
           <li class="nav-item {$free_hidden}">
              <a class="nav-link ontab config-menu" href="#tabs-4-tab-10" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_email_server']}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ontab config-menu" href="#tabs-config-cache" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_cache']}
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ontab config-menu" href="#tabs-config-sale" role="tab" data-toggle="tab">
                {$CMS->lang['title_tab_sale']}
              </a>
            </li>

            {$tab_shipping_fee}
          </ul>
        </div><!--.tabs-section-nav-->
        <input id="is_payment_tab" value="0" type="hidden" />
        <div class="tab-content" style="margin-bottom: 10px;">
          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-1">
              <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_title']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][website_title]" value="{$CMS->vars['website_title']}" crawling="tag.title">
                      </div>
                    </div>

                    <div class="form-group row" style="display:none">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_url']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <div class="box_checkbox">
                            <div class="radio pull-left">
                              <input type="radio" name="check_domain" class="check_domain" id="check_domain_1" value="1" >
                              <label for="check_domain_1">Free domain</label>
                            </div>
                            <div class="radio pull-left" style="margin-left: 10px;">
                              <input type="radio" name="check_domain" class="check_domain" id="check_domain_2" value="2">
                              <label for="check_domain_2">Sub domain</label>
                            </div>
                          </div>
                          <div class="box_url" style="clear:both;">
                              <div class="form-group box_free_domain">
                                <div class="input-group">
                                  <input type="text" class="form-control free_domain" name="free_domain" value="{$domain_free}" />
                                  <div class="input-group-btn">
                                    <button type="button" class="btn dropdown-toggle change_domain fix_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      {$ext_domain}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                      <a class='dropdown-item choose_domain' domain=".vn.media">.vn.media</a>
                                      <a class='dropdown-item choose_domain' domain=".tóp.vn">.tóp.vn</a>
                                    </div>
                                    <input type="hidden" name="domain_free" value="{$ext_domain}" />
                                  </div>
                                </div>
                                <p class="show_domain"></p>
                              </div>
                              <div class="subdomain" style="display: none">
                                <input name="subdomain" class="form-control" type="text" value="{$domain_sub}" />
                              </div>
                            <script>
                              $(document).ready(function(){
                                $(".check_domain[value='{$check_domain}']").prop("checked", true);
                                setTimeout(function(){
                                  $(".check_domain[value='{$check_domain}']").trigger("click");
                                }, 700);

                                $(".choose_domain").click(function(){
                                    var domain = $(this).attr("domain");
                                    $(".change_domain").html(domain);
                                    $("input[name='domain_free']").val(domain);

                                    var sub_domain = $(".free_domain").val();
                                    var main_domain = $("input[name='domain_free']").val();
                                    $(".show_domain").html(sub_domain+main_domain);

                                });

                                $(".free_domain").keyup(function(){
                                    var sub_domain = $(this).val();
                                    var domain = $("input[name='domain_free']").val();
                                    $(".show_domain").html(sub_domain+domain);
                                });

                                $(".check_domain").click(function(){
                                    var type = $(this).val();
                                    if(type == 1)
                                    {
                                      $(".subdomain").hide();
                                      $(".box_free_domain").show();
                                    }else
                                    {
                                      $(".subdomain").show();
                                      $(".box_free_domain").hide();
                                    }

                                });

                              });
                            </script>
                          </div>

                      </div>
                    </div>
EOF;

        if(ezy::$theme_key == "dsg")
        {
            $output .=<<<EOF
                   <div class="form-group row">
                      <label class="col-xl-4 form-control-label">Quản lý màu tóc</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <a href="{$CMS->vars['root_domain']}/?site=config&code=04&group=49&set=485">Cấu hình tại đây </a>
                      </div>
                    </div>

EOF;

        }
        $output .=<<<EOF
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_multi_language']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select name="config[input][translations][]" class="select2 js-example-basic-multiple js-states form-control" multiple="multiple">
EOF;

        foreach ($CMS->vars['default_language_data'] as $langCode => $langName)
        {
            if(!is_numeric($langCode))
            {
                $selectedLang = isset($CMS->vars['translations'][$langCode]) ? 'selected' : '';

                $output .= "<option value='{$langCode}' {$selectedLang}>{$langName}</option>";
            }
        }

        $output .= <<<EOF
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_default_language']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[select][default_language]" defaultvalue="{$CMS->vars['default_language']}">
                          {$CMS->vars['default_language_html']}
                        </select>
                      </div>
                    </div>
                     <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_currency_type']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[select][currency_type]" defaultvalue="{$CMS->vars['currency_type']}">
                            <option value="$">USD</option>
                            <option value="đ">VND</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_row_per_page']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[select][pagination_number]" defaultvalue="{$CMS->vars['pagination_number']}">
                            <option value="1000">{$CMS->lang['no_pagination']}</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="12">12</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="24">24</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_gallery_sort_type']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[select][gallery_sort_type]" defaultvalue="{$CMS->vars['gallery_sort_type']}">
                            <option value="0">{$CMS->lang['sort_type_0']}</option>
                            <option value="1">{$CMS->lang['sort_type_1']}</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_phone_format']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[select][phone_format]" defaultvalue="{$CMS->vars['phone_format']}">
                            <option value="(000) 000-0000">US - (999) 999-9999</option>
                            <option value="0000 000 0000">VN - 9999 999 9999</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_default_country']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[select][default_country]" defaultvalue="{$CMS->vars['default_country']}">
                            {$optionCountry}
                        </select>
                      </div>
                    </div>
EOF;
        if($CMS->vars['addon_website_enable'] == 1)
        {
            $output .=<<<EOF

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_sitestatus']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" type="text" name="config[yes_no][sitestatus]" defaultvalue="{$CMS->vars['sitestatus']}">
                          <option value="0">{$CMS->lang['title_off']}</option>
                          <option value="1">{$CMS->lang['title_on']}</option>
                        </select>
                      </div>
                    </div>
EOF;
        }
        $output .=<<<EOF
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_logo_website']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <fieldset class="form-group" style="margin-bottom: 0;">
                                <div class="actionButtons pull-right">
                                    <ul>
                                        <li onclick="return performClick('ufile');">
                                            <i tabindex="0" class="fa fa-pencil"></i>
                                        </li>
                                    </ul>
                                    <input type="hidden" id="ufile_output_b64" name="base64_image" value="">
                                </div>
                                 
                                <div class="drop-zone fileinput-button" style="height: 110px !important; padding-top: 0;">
                                        <img id="upload_img_show" style="margin: 0 auto; max-width: 100%; max-height: 110px; {$display}" src="{$url_image}">
                                        <i class="font-icon font-icon-cloud-upload-2" {$css_logo}></i>
                                        <div class="drop-zone-caption" {$css_logo}>Drag file to upload</div>
                                        <input type="file" name="logo_website" id="ufile" accept="image/*">
                                </div><!--.drop-zone-->
                            </fieldset>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_logo_website_mobile']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <fieldset class="form-group" style="margin-bottom: 0;">
                                <div class="actionButtons pull-right">
                                    <ul>
                                        <li onclick="return performClick('ufile_mobile');">
                                            <i tabindex="0" class="fa fa-pencil"></i>
                                        </li>
                                        
                                    </ul>
                                    <input type="hidden" id="ufile_mobile_output_b64" name="base64_image_mobile" value="">
                                </div>
                                 
                                <div class="drop-zone fileinput-button" style="height: 110px !important; padding-top: 0;">
                                        <img id="upload_img_mobile_show" style="margin: 0 auto; max-width: 100%; max-height: 110px; {$display_mobile}" src="{$url_image_mobile}">
                                        <i class="font-icon font-icon-cloud-upload-2" {$css_logom}></i>
                                        <div class="drop-zone-caption" {$css_logom}>Drag file to upload</div>
                                        <input type="file" name="logo_website_mobile" id="ufile_mobile" accept="image/*">
                                </div><!--.drop-zone-->
                            </fieldset>
                      </div>
                    </div>


                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['title_avatar_website']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <fieldset class="form-group" style="margin-bottom: 0;">
                                <div class="actionButtons pull-right">
                                    <ul>
                                        <li onclick="return performClick('ufile_mobile');">
                                            <i tabindex="0" class="fa fa-pencil"></i>
                                        </li>
                                        
                                    </ul>
                                    <input type="hidden" id="ufile_avatar_output_b64" name="base64_image_avatar" value="">
                                </div>
                                 
                                <div class="drop-zone fileinput-button" style="padding-top: 0;">
                                        <img id="upload_img_avatar" style="margin: 0 auto; max-width: 100%; max-height: 200px; {$display_avatar}" src="{$url_image_avatar}">
                                        <i class="font-icon font-icon-cloud-upload-2" {$css_logoa}></i>
                                        <div class="drop-zone-caption" {$css_logo}a>Drag file to upload</div>
                                        <input type="file" name="avatar_website" id="ufile_avatar" accept="image/*">
                                </div><!--.drop-zone-->
                            </fieldset>
                      </div>
                    </div>


                    
                </div>
              </div>

          </div><!--.tab-pane-->


          {$this->seoTab()}

          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-3">
              <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_name']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_name]" value="{$CMS->vars['company_name']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_address']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <div class="input-append input-group">
                            <input id="pac-input" class="form-control" type="text" name="config[input][company_address]" value="{$CMS->vars['company_address']}" onfocus="mapEnabled($('#map-toogle'), $('#map'))" onkeypress="return event.keyCode != 13;">
                            <span tabindex="100" title="Click here to show/hide maps" class="add-on input-group-addon" style="cursor: pointer;"><i id="map-toogle" class="glyphicon glyphicon-map-marker" onclick="mapToggle($(this), $('#map'))"></i></span>
                        </div>
                      </div>
                    </div>


                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">Lat, Lng</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <div class="row">
                          <div class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" placeholder="lat coordinate" type="text" name="config[input][google_lat]" value="{$CMS->vars['google_lat']}">
                          </div>
                          <div class="col-xl-6 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" placeholder="long coordinate" type="text" name="config[input][google_lng]" value="{$CMS->vars['google_lng']}">
                          </div>
                        </div>
                        <span class="note_info" style="font-size: 12px;color: red;font-style: italic;">Get lat, lng information at <a href="https://www.latlong.net/" target="_blank">https://www.latlong.net/</a></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_address']}2</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_address2]" value="{$CMS->vars['company_address2']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_email']}
                        <span class="question"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;">This email is used to get the contact information from the contact form and it is displayed on the website</p></span>
                      </label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_email]" value="{$CMS->vars['company_email']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_email']}2
                        <span class="question"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;">This email is only visible on the site</p></span>
                      </label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_email2]" value="{$CMS->vars['company_email2']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_email_cc']}
                        <span class="question"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;">Email CC use for contact form. This email is not used for display on website</p></span>
                      </label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_email_cc]" value="{$CMS->vars['company_email_cc']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_mobile']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_mobile]" value="{$CMS->vars['company_mobile']}">
                        <span class="note_info" style="font-size: 12px;color: red;font-style: italic;">* {$CMS->lang['note_company_mobile']}</span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_phone']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_phone]" value="{$CMS->vars['company_phone']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_phone']}2</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_phone2]" value="{$CMS->vars['company_phone2']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_fax']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][company_fax]" value="{$CMS->vars['company_fax']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_company_intro']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" name="config[textarea][company_intro]" rows="3">{$CMS->vars['company_intro']}</textarea>
                      </div>
                    </div>
                    
                    {$this->openHours()}
                    
                    <h5 class="m-t-lg with-border">Other information</h5>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_whmcs_client_id']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][whmcs_client_id]" value="{$CMS->vars['whmcs_client_id']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_number_call_now']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" disabled="disabled" readonly="readonly" name="config[input][number_call_now]" value="{$CMS->vars['number_call_now']}">
                      </div>
                    </div>
                    
                </div>
                <div class="col-lg-6">
                    {$this->GoogleMapsIframe()}
                </div>
              </div>

          </div><!--.tab-pane-->





          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-4">
              <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_print_company_name']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][print_company_name]" value="{$CMS->vars['print_company_name']}">
                      </div>
                    </div>


                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_print_website']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][print_website]" value="{$CMS->vars['print_website']}">
                      </div>
                    </div>


                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_print_company_address']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][print_company_address]" value="{$CMS->vars['print_company_address']}">
                      </div>
                    </div>


                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_print_company_phone']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][print_company_phone]" value="{$CMS->vars['print_company_phone']}">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_print_company_email']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][print_company_email]" value="{$CMS->vars['print_company_email']}">
                      </div>
                    </div>


                </div>
              </div>
          </div><!--.tab-pane-->

          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-5">
              <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_format_date']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select name="config[input][date_format]" defaultvalue="{$CMS->vars['date_format']}" class="form-control auto_select select2">
                            {$CMS->vars['option_date_format']}
                         </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_hours_time_format']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select name="config[input][hours_time_format]" defaultvalue="{$CMS->vars['hours_time_format']}" class="form-control auto_select select2">
                             <option value="12">12</option>
                             <option value="24">24</option>
                         </select>
                      </div>
                    </div>

                    <!--
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_timezone']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" name="config[select][timezone]" defaultvalue="{$CMS->vars['timezone']}">
                          {$CMS->vars['timezone_html']}
                        </select>
                      </div>
                    </div>
                    -->
                   
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_timezone']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <select class="form-control auto_select select2" name="config[select][timezone_id]" defaultvalue="{$CMS->vars['timezone_id']}">
EOF;

        foreach(DateTimeZone::listIdentifiers() as $timezone){
            if($timezone == $CMS->vars['timezone_id'])
            {
                $timezone_selected = "selected";
            }
            else
            {
                $timezone_selected = "";
            }

            $timezone_info = date::getTimezone($timezone);

            $output .= "<option {$timezone_selected} value='{$timezone}'>{$timezone} ({$timezone_info['timezone']} hours)</option>";
        }

        $output .= <<<EOF
                        </select>
EOF;

        if(!$CMS->vars['timezone_id']){
            $output .= "<a href='{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=update_timezone&tab=tabs-4-tab-5' class='btn btn_success'>Update timezone</a>";
        }

        $output .= <<<EOF
                      </div>
                    </div>



                </div>
              </div>

          </div><!--.tab-pane-->



            <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-6">
              <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_fb_app_id']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][fb_app_id]" value="{$CMS->vars['fb_app_id']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_fb_app_secret']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][fb_app_secret]" value="{$CMS->vars['fb_app_secret']}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_gg_app_id']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][gg_app_id]" value="{$CMS->vars['gg_app_id']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_gg_app_secret']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][gg_app_secret]" value="{$CMS->vars['gg_app_secret']}">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_gg_map_key']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][google_map_key]" value="{$CMS->vars['google_map_key']}">
                      </div>
                    </div>
                    
                    {$this->instagramConfig()}
                    {$this->smsConfig()}
					{$this->marConfig()}
                    
                </div>
              </div>

          </div><!--.tab-pane-->
                        
          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-9">
              <div class="row">
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['label_recaptcha_google']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control auto_select" type="text" name="config[yes_no][recaptcha_google]" defaultvalue="{$CMS->vars['recaptcha_google']}">
                            <option value="0">{$CMS->lang['title_off']}</option>
                            <option value="1">{$CMS->lang['title_on']}</option>
                            </select>
                        </div>
                    </div>
    
                        
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_gg_recaptcha_sitekey']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][gg_sitekey]" value="{$CMS->vars['gg_sitekey']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_gg_recaptcha_secrectkey']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][gg_secrectkey]" value="{$CMS->vars['gg_secrectkey']}">
                      </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['label_enable_security_ip']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                            <select class="form-control auto_select" type="text" name="config[yes_no][enable_security_ip]" defaultvalue="{$CMS->vars['enable_security_ip']}">
                            <option value="0">{$CMS->lang['title_off']}</option>
                            <option value="1">{$CMS->lang['title_on']}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_time_allow']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="config[input][time_allow_ip]" value="{$CMS->vars['time_allow_ip']}">
                      </div>
                    </div>

                </div>
              </div>

          </div><!--.tab-pane-->              

          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-7">
              <div class="row">
                <div class="col-lg-6">
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_hours_morning']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select class="select2" multiple="multiple" name="config[input][booking_hours_morning][]">
                              {$CMS->config_general->option_hours_morning($CMS->vars['booking_hours_morning'])}
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_hours_afternoon']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select class="select2" multiple="multiple" name="config[input][booking_hours_afternoon][]">
                              {$CMS->config_general->option_hours_afternoon($CMS->vars['booking_hours_afternoon'])}
                        </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_before_day']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <input type="text" name="config[input][booking_before_day]" value="{$CMS->vars['booking_before_day']}" class="form-control" />
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_before_hours']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <input type="text" name="config[input][booking_before_hours]" value="{$CMS->vars['booking_before_hours']}" class="form-control" />
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_step_time']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select class="form-control auto_select" type="text" name="config[yes_no][step_time_booking]" defaultvalue="{$CMS->vars['step_time_booking']}">
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="60">60</option>
                            </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_enable']} <span class="question"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;">For Booking works well, please go to Tab: API -> SMS -> Enable = Turn on</p></span></label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select class="form-control auto_select" type="text" name="config[yes_no][booking_enable]" defaultvalue="{$CMS->vars['booking_enable']}">
                            <option value="0">{$CMS->lang['title_off']}</option>
                            <option value="1">{$CMS->lang['title_on']}</option>
                            </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_hours_enable']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select class="form-control auto_select" type="text" name="config[yes_no][booking_hours_enable]" defaultvalue="{$CMS->vars['booking_hours_enable']}">
                            <option value="0">{$CMS->lang['title_off']}</option>
                            <option value="1">{$CMS->lang['title_on']}</option>
                            </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_booking_email_enable']}<span class="question"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        <p class="box_answer" style="display: none;">Disable: The booking page will use Form send by sms. <br/> Enable: The booking page will use Form send by email</p></span></label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                         <select class="form-control auto_select" type="text" name="config[yes_no][booking_email_form_enable]" defaultvalue="{$CMS->vars['booking_email_form_enable']}">
                            <option value="0">{$CMS->lang['title_off']}</option>
                            <option value="1">{$CMS->lang['title_on']}</option>
                            </select>
                      </div>
                    </div>

                </div>
              </div>

          </div><!--.tab-pane-->

          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-8">
            <div class="row">
                <div class="col-lg-12">
                  <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_pp']}</h5>         
                  <div class="form-group row">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_paypal_active']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="payment_active" name="config[yes_no][payment_active]" value="{$CMS->vars['payment_active']}">
                        <label for="payment_active"></label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row for_paypal">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_paypal_live']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="is_live" name="config[yes_no][is_live]" value="{$CMS->vars['is_live']}">
                        <label for="is_live"></label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row for_paypal">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_paypal_client_id']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control paypal_id" type="text" name="config[input][paypal_client_id]" value="{$CMS->vars['paypal_client_id']}">
                    </div>
                  </div>

                  <div class="form-group row for_paypal">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_paypal_client_secret']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control paypal_secret" type="text" name="config[input][paypal_client_secret]" value="{$CMS->vars['paypal_client_secret']}">
                    </div>
                  </div>
                <!-- Config Authorize -->
                <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_authorize']}</h5>         
                  <div class="form-group row">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_authorize_active']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="authorize_active" name="config[yes_no][authorize_active]" value="{$CMS->vars['authorize_active']}">
                        <label for="authorize_active"></label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row for_authorize">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_authorize_live']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="authorize_is_live" name="config[yes_no][authorize_is_live]" value="{$CMS->vars['authorize_is_live']}">
                        <label for="authorize_is_live"></label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row for_authorize">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_authorize_client_id']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control authorize_id" type="text" name="config[input][authorize_login_id]" value="{$CMS->vars['authorize_login_id']}">
                    </div>
                  </div>

                  <div class="form-group row for_authorize">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_authorize_client_secret']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control authorize_secret" type="text" name="config[input][authorize_transaction_key]" value="{$CMS->vars['authorize_transaction_key']}">
                    </div>
                  </div>
                  
                  
                    <!-- Config Stripe -->
                <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_stripe']}</h5>         
                  <div class="form-group row">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_stripe_active']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="stripe_active" name="config[yes_no][stripe_active]" value="{$CMS->vars['stripe_active']}">
                        <label for="stripe_active"></label>
                      </div>
                    </div>
                  </div>

                

                  <div class="form-group row for_stripe">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_stripe_publishable_key']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control stripe_id" type="text" name="config[input][stripe_publishable_key]" value="{$CMS->vars['stripe_publishable_key']}">
                    </div>
                  </div>

                  <div class="form-group row for_stripe">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_stripe_secret_key']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control stripe_secret" type="text" name="config[input][stripe_secret_key]" value="{$CMS->vars['stripe_secret_key']}">
                    </div>
                  </div>
                  
                  
                  
                <!-- Config Ngan Luong -->
                <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_nl']}</h5>      
                <div class="form-group row">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_nl_active']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="nl_active" name="config[yes_no][nl_active]" value="{$CMS->vars['nl_active']}">
                        <label for="nl_active"></label>
                      </div>
                    </div>
                </div>
                <div class="form-group row for_nl">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_nl_url_api']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][nl_url_api]" value="{$CMS->vars['nl_url_api']}">
                    </div>
                </div>
      
                <div class="form-group row for_nl">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_nl_merchant_id']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][nl_merchant_id]" value="{$CMS->vars['nl_merchant_id']}">
                    </div>
                </div>

                <div class="form-group row for_nl">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_nl_merchant_password']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][nl_merchant_password]" value="{$CMS->vars['nl_merchant_password']}">
                    </div>
                </div>

                <div class="form-group row for_nl">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_nl_receiver_email']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][nl_receiver_email]" value="{$CMS->vars['nl_receiver_email']}">
                    </div>
                </div>
                
                <!--Config Bao Kim-->
                <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_bk']}</h5>       
                <div class="form-group row">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_bk_active']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <div class="checkbox-toggle" style="margin-top: 10px;">
                        <input type="checkbox" class="checkactive autoCheckBox" id="bk_active" name="config[yes_no][bk_active]" value="{$CMS->vars['bk_active']}">
                        <label for="bk_active"></label>
                      </div>
                    </div>
                </div>
                <div class="form-group row for_bk">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_bk_url_api']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][bk_url_api]" value="{$CMS->vars['bk_url_api']}">
                    </div>
                </div>

                <div class="form-group row for_bk">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_bk_url_bpn']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][bk_url_bpn]" value="{$CMS->vars['bk_url_bpn']}">
                    </div>
                </div>

                <div class="form-group row for_bk">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_bk_merchant_id']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][bk_merchant_id]" value="{$CMS->vars['bk_merchant_id']}">
                    </div>
                </div>

                <div class="form-group row for_bk">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_bk_secure_pass']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][bk_secure_pass]" value="{$CMS->vars['bk_secure_pass']}">
                    </div>
                </div>
                      
                <div class="form-group row for_bk">
                    <label class="col-xl-3 form-control-label">{$CMS->lang['label_bk_email_business']}</label>
                    <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                      <input class="form-control" type="text" name="config[input][bk_email_business]" value="{$CMS->vars['bk_email_business']}">
                    </div>
                </div>

                    <!--Config COD-->
                    <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_cod']}</h5>
                    <div class="form-group row">
                        <label class="col-xl-3 form-control-label">{$CMS->lang['label_cod_note']}</label>
                        <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                          <textarea class="form-control editor_texarea" name="config[textarea][payment_cod_note]">{$CMS->vars['payment_cod_note']}</textarea>
                        </div>
                    </div>

                    <!--Config Bank transfer-->
                    <h5 class="m-t-lg with-border">{$CMS->lang['title_payment_bank']}</h5>
                    <div class="form-group row">
                        <label class="col-xl-3 form-control-label">{$CMS->lang['label_bank_transfer_activate']}</label>
                        <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                          <div class="checkbox-toggle" style="margin-top: 10px;">
                            <input type="checkbox" class="checkactive autoCheckBox" id="ck_active" name="config[yes_no][ck_active]" value="{$CMS->vars['ck_active']}">
                            <label for="ck_active"></label>
                          </div>
                        </div>
                    </div>
                    <div class="form-group row for_ck">
                        <label class="col-xl-3 form-control-label">{$CMS->lang['label_bank_info']}</label>
                        <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                          <textarea class="form-control editor_texarea" style="height:200px;" name="config[textarea][payment_bank_info]">{$CMS->vars['payment_bank_info']}</textarea>
                        </div>
                    </div>
                    
                    <!--Config Discount code-->
                    <h5 class="m-t-lg with-border">{$CMS->lang['label_discount_code']}</h5>
                    
                    <div class="form-group row">
                        <label class="col-xl-3 form-control-label">{$CMS->lang['label_discount_code_desc']}</label>
                        <div class="col-xl-9 col-lg-12 col-sm-12 col-xs-12">
                          <div class="checkbox-toggle" style="margin-top: 10px;">
                            <input type="checkbox" class="enabled_check" id="discount_code" name="config[yes_no][discount_code]" value="1" {$discount_code_checked}>
                            <label for="discount_code"></label>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
          </div><!--.tab-pane-->


            

          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-10">
              <div class="row">
                <div class="col-lg-6">

                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['is_smtp']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][is_smtp]" value="{$CMS->vars['is_smtp']}">
                          <span>0: {$CMS->lang['is_smtp_0']}, 1: {$CMS->lang['is_smtp_1']}, 2: {$CMS->lang['is_smtp_2']}, 3: {$CMS->lang['is_smtp_3']}</span>
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['smtp_email_display']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][smtp_email_display]" value="{$CMS->vars['smtp_email_display']}">
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['smtp_user_display']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][smtp_user_display]" value="{$CMS->vars['smtp_user_display']}">
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['email_mode']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][email_mode]" value="{$CMS->vars['email_mode']}">
                          0: {$CMS->lang['email_mode_0']}, 1: {$CMS->lang['email_mode_1']}
                          
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['smtp_server']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][smtp_server]" value="{$CMS->vars['smtp_server']}">
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['smtp_port']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][smtp_port]" value="{$CMS->vars['smtp_port']}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['smtp_user']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][smtp_user]" value="{$CMS->vars['smtp_user']}">
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['smtp_password']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <input class="form-control" type="text" name="config[input][smtp_password]" value="{$CMS->vars['smtp_password']}">
                        </div>
                    </div>
                 </div> <!-- col-lg-6 -->
              </div> <!-- row -->   
          </div><!--.tab-pane-->

          <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-11">
              <div class="form-group row">
                <label class="col-xl-3 form-control-label">{$CMS->lang['label_gg_adwords_remarketing']}</label>
                <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="config[textarea][gg_adwords]" rows="7">{$CMS->vars['gg_adwords']}</textarea>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 form-control-label">{$CMS->lang['label_fb_retageting']}</label>
                <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
                  <textarea class="form-control" name="config[textarea][fb_retageting]" rows="7">{$CMS->vars['fb_retageting']}</textarea>
                </div>
              </div>
          </div>
          <div role="tabpanel" class="tab-pane fade" id="tabs-config-cache">
            {$this->cacheConfig()}
          </div>

          <div role="tabpanel" class="tab-pane fade" id="tabs-config-sale">
            {$this->saleConfig()}
          </div>

          <!--div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-6">Tab 6</div--><!--.tab-pane-->
        </div><!--.tab-content-->
        <div class="form-group row">
          <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <input type="hidden" name="active_tab" value="{$active_tab}" />
            <button class="btn" name="submit" type="submit">{$CMS->lang['save_config']}</button>
          </div>
        </div>
      </section><!--.tabs-section-->
    </form>
</section>

<script>

    tinyMCESettings = [];
    tinyMCESettings['height'] = 200;

    var msg_error = "{$CMS->lang['payment_error_info']}";
    var pp_title = "{$CMS->lang['title_payment_pp']}";
    var nl_title = "{$CMS->lang['title_payment_nl']}";
    var bk_title = "{$CMS->lang['title_payment_bk']}";
    
    // Hidden tab
    $(".tab_hidden").hide();


    // Check is payment tab
    $('.config-menu').click(function(){
        $('#is_payment_tab').val($(this).attr('key'));
    });

    function check_payment_active()
    {
        // Check is not payment tab
        if($('#is_payment_tab').val() == '')
        {
            $('.for_paypal input').each(function(){
                if($(this).val() == '')
                {
                    $('#payment_active').prop('checked','');
                    $('#payment_active').val('');

                    $('#is_live').prop('checked','');
                    $('#is_live').val('');
                    return false;
                }
            });
    
            $('.for_nl input').each(function(){
                if($(this).val() == '')
                {
                    $('#nl_active').prop('checked','');
                    $('#nl_active').val('');
                    return false;
                }
            });
    
            $('.for_bk input').each(function(){
                if($(this).val() == '')
                {
                    $('#bk_active').prop('checked','');
                    $('#bk_active').val('');
                    return false;        
                }
            });

            $('.for_ck input').each(function(){
                if($(this).val() == '')
                {
                    $('#ck_active').prop('checked','');
                    $('#ck_active').val('');
                    return false;        
                }
            });
        }
    
        var is_error = 0;
        if($('#payment_active').prop("checked"))
        {
            $('.for_paypal input').each(function(){
                if($(this).val() == '')
                {
                    call_notify(pp_title,msg_error, 'danger','');
                    is_error = 1;
                    return false;
                }
            });
        }
        else
        {
            $('.for_paypal input').each(function(){
                $(this).val('');
            });        
        }
    
        if($('#nl_active').prop("checked"))
        {
            $('.for_nl input').each(function(){
                if($(this).val() == '')
                {
                    call_notify(nl_title,msg_error, 'danger','');
                    is_error = 1;
                    return false;    
                }
            });
        }
        else
        {
            $('.for_nl input').each(function(){
                $(this).val('');
            });        
        }
    
        if($('#bk_active').prop("checked"))
        {
            $('.for_bk input').each(function(){
                if($(this).val() == '')
                {
                    call_notify(bk_title,msg_error, 'danger','');
                    is_error = 1;
                    return false;
                }
            });
        }
        else
        {
            $('.for_bk input').each(function(){
                $(this).val('');
            });        
        }
    
        if(is_error != 1)
        {
            $( "#config_general" ).submit();
        }
        
        
    }

  $(document).ready(function(){
    // Checked checkbox custom status
    var obj = '{$CMS->vars['custom_status']}';
    if(obj)
    {
      var data_status = JSON.parse(obj);
      // console.log(data_status);
      for(var x in data_status)
      {
        var status = data_status[x];
        for(var j in status)
        {
          $(".box_list").find("input[type='checkbox'][value='"+status[j]+"']").prop("checked", true);
        }
      }
    }



      $(".ontab").click(function(){
          var activetab = $(this).attr("href");
          var list = activetab.split("#");
          // console.log(list);
          $("input[name='active_tab']").val(list[1]);
      });

      $(".ontab[href='#{$active_tab}']").trigger("click");
      
      // Check payment actice
      $('#payment_active').val()==1 ? $('.for_paypal').show() : $('.for_paypal').hide();
      $('#nl_active').val()==1 ? $('.for_nl').show() : $('.for_nl').hide();
      $('#bk_active').val()==1 ? $('.for_bk').show() : $('.for_bk').hide();
      $('#ck_active').val()==1 ? $('.for_ck').show() : $('.for_ck').hide();
      
    $('#payment_active').click(function(){
      if($('#payment_active').prop("checked"))
      {
        $('#payment_active').val(1);
        $('.for_paypal').show();
      }
      else
      {
        $('#payment_active').val(0);
        $('.for_paypal').hide();
        $('.for_paypal').val('');
      }
    });

    $('#authorize_active').click(function(){
      if($('#authorize_active').prop("checked"))
      {
        $('#authorize_active').val(1);
      }
      else
      {
        $('#authorize_active').val(0);
      }
    });
    
$('#stripe_active').click(function(){
      if($('#stripe_active').prop("checked"))
      {
        $('#stripe_active').val(1);
      }
      else
      {
        $('#stripe_active').val(0);
      }
    });
   
    $('#nl_active').click(function(){
      if($('#nl_active').prop("checked"))
      {
        $('#nl_active').val(1);
        $('.for_nl').show();
      }
      else
      {
        $('#nl_active').val(0);
        $('.for_nl').hide();
        $('.for_nl').val('');
      }
    });
      
    $('#bk_active').click(function(){
      if($('#bk_active').prop("checked"))
      {
        $('#bk_active').val(1);
        $('.for_bk').show();
      }
      else
      {
        $('#bk_active').val(0);
        $('.for_bk').hide();
        $('.for_bk').val('');
      }
    });

    $('#ck_active').click(function(){
      if($('#ck_active').prop("checked"))
      {
        $('#ck_active').val(1);
        $('.for_ck').show();
      }
      else
      {
        $('#ck_active').val(0);
        $('.for_ck').hide();
        $('.for_ck').val('');
      }
    });

    $('#is_live').click(function(){
      if($('#is_live').prop("checked"))
      {
        $('#is_live').val(1);
      }
      else
      {
        $('#is_live').val(0);
      }
    });

    $('#authorize_is_live').click(function(){
      if($('#authorize_is_live').prop("checked"))
      {
        $('#authorize_is_live').val(1);
      }
      else
      {
        $('#authorize_is_live').val(0);
      }
    });
    
  });
  
  
  //set selected for select
  $.each($("select[defaultvalue]"), function(key, obj){
    var val = $(obj).attr("defaultvalue");
    $(obj).find("option[value='" + val + "']").prop('selected',true);
  });
</script>        
EOF;

        return $output;
    }

//===========================================================================
//  HTML MIDDLE
//===========================================================================

    public function middle($result)
    {
        global $CMS, $DB, $member;

        $output = "";
        $btn_control = "";
        if($CMS->permit['config_general_edit'])
        {
            $btn_control .= <<<EOF
      <a href="{$CMS->vars['root_domain']}/?site=config_general&act=edit&id={$result['pos_id']}" class="edit"><i class="fa fa-edit"></i></a>
EOF;

        }

        if($CMS->permit['config_general_delete'])
        {
            $btn_control .= <<<EOF
      <a onclick="return alert_delete('{$CMS->vars['root_domain']}/?site=config_general&act=delete&id={$result['pos_id']}');" class="edit" aria-describedby="ui-id-17"><i class="fa fa-trash"></i></a>
EOF;

        }
        $output .= <<<EOF
  <tr bgcolor="{$result['bgcolor']}">
    <td>#{$result['pos_id']}</td>
    <td>{$result['pos_name']}</td>
    <td>{$result['pos_time']}</td>
    <td align="center">{$btn_control}</td>
  </tr>
EOF;

        return $output;
    }

//===========================================================================
//  NO DATA
//===========================================================================

    public function none()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
  <tr>
    <td colspan="7">{$CMS->lang['config_general_no_data']}</td>
  </tr>
EOF;

        return $output;
    }

//===========================================================================
//  HTML FOOTER
//===========================================================================

    public function footer()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
EOF;

        return $output;
    }

//===========================================================================
//  USER CONTROL
//===========================================================================

    public function control()
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF
<div class="block_action">
	
</div>
EOF;

        return $output;
    }

//===========================================================================
//  HTML EDIT
//===========================================================================

    public function edit( $data )
    {
        global $CMS, $DB, $member;

        $output = "";

        $output .= <<<EOF

<section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['config_general_edit_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=config_general{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

<form method="post" id="config_general" name="config_general" action="{$CMS->vars['root_domain']}/?site=config_general&id={$data['pos_id']}&act=edit_do&page={$CMS->input['page']}" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

 <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['config_general_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <input class="form-control" type="text" name="pos_name" value="{$data['pos_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['config_general_incomplete_name']}">
                </div>
              </div>
              <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['config_general_display']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select select2" name="pos_status" defaultvalue="{$data['pos_status']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['status_incomplete']}">
                        {$CMS->vars['display_status']}
                    </select>
                  </div>
              </div>

              <div class="form-group row">
                <label class="form-control-label"></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <button class="btn" type="submit">{$CMS->lang['config_general_edit_submit']}</button>
                </div>
              </div>

          </div>
        </div>
      </figure>

</form>
</section>
<script language="javascript">
$(document).ready(function(){
    validate_form_custom("#config_general");


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

  <section class="add_form main_form">
      <figure class="heading">
        <h3>{$CMS->lang['config_general_add_form']}</h3>
          <a href="{$CMS->vars['root_domain']}/?site=config_general{$CMS->class->search->url_return}" title=""><span class="font-icon font-icon-del"></span></a>
      </figure>

<form method="post" id="config_general" name="config_general" action="{$CMS->vars['root_domain']}/?site=config_general&act=add_do" onSubmit="return check_form(this.id);" enctype="multipart/form-data">

    <figure class="box-typical box-typical box-typical-padding border">
        <div class="row">
          <div class="col-lg-4">
              <div class="form-group row">
                <label class="form-control-label">{$CMS->lang['config_general_name']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <input class="form-control" type="text" name="pos_name" value="{$data['pos_name']}" data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['config_general_incomplete_name']}">
                </div>
              </div>
              <div class="form-group row">
                  <label class="form-control-label">{$CMS->lang['config_general_display']}<font style="margin-left:5px" color="#FF0000">(*)</font></label>
                  <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                    <select class="form-control auto_select select2" name="pos_status" defaultvalue="{$data['pos_status']}"  data-validation="[NOTEMPTY]" data-validation-message="{$CMS->lang['status_incomplete']}">
                        {$CMS->vars['display_status']}
                    </select>
                  </div>
              </div>

              <div class="form-group row">
                <label class="form-control-label"></label>
                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                  <button class="btn" type="submit">{$CMS->lang['config_general_add_submit']}</button>
                </div>
              </div>

          </div>
        </div>
      </figure>

</form>
</section>

<script language="javascript">
$(document).ready(function(){
    validate_form_custom("#config_general");


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
		  <h3>{$CMS->lang['config_general_header']}</h3>
		</div>
	  </div>
	</div>
</header>
<section class="card">

	<section class="box-typical">
		<header class="box-typical-header">
			<div class="tbl-row">
				<div class="tbl-cell tbl-cell-title">
				  <h3>{$CMS->lang['config_general_header']}: <strong>{$data['config_general_name']}</strong></h3>
				</div>
				<div class="tbl-cell tbl-cell-action-bordered">
					<a href="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}{$CMS->class->search->url_return}">
					<button type="button" class="action-btn"><i class="fa fa-mail-reply"></i></button>
					</a>
				</div>                                    
			</div>
		</header>
	</section>



	<div class="card-block">

		<h5 class="with-border">{$CMS->lang['config_general_required_info']}</h5>

		  <div class="form-group row">
			<label class="col-sm-3 form-control-label">{$CMS->lang['config_general_name']}</label>
			<div class="col-sm-9">
				<p class="form-control-static">
				  {$data['config_general_name']}
				  &nbsp; <script type="text/javascript">permission_btn("edit", "config_general", "{$CMS->vars['root_domain']}/?site=config_general&act=edit&id={$data['data_bk']['config_general_id']}");</script>
				  &nbsp; <script type="text/javascript">permission_btn("delete", "project", "{$CMS->vars['root_domain']}/?site=config_general&act=delete&id={$data['data_bk']['config_general_id']}");</script>
				</p>
			</div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['config_general_key']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['config_general_key']}
				  </p>
			  </div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['config_general_display_type']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['config_general_display_type']}
				  </p>
			  </div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['config_general_width']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['config_general_width']}
				  </p>
			  </div>
		  </div>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['config_general_height']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['config_general_height']}
				  </p>
			  </div>
		  </div>
		 
		 <h5 class="with-border">{$CMS->lang['config_general_add_info']}</h5>
		  
		  <div class="form-group row">
			  <label class="col-sm-3 form-control-label">{$CMS->lang['config_general_time']}</label>
			  <div class="col-sm-9">
				  <p class="form-control-static">
					{$data['config_general_time']}
				  </p>
			  </div>
		  </div>
	</div><!-- end .card-block-->
</section><!--card --> 


<script language="javascript" src="{$CMS->vars['js_url']}/public.js"></script>

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
<form method="post" id="config_general" name="config_general" action="{$CMS->vars['root_domain']}/?site=config_general&act=search_do" onSubmit="return check_form(this.id);">
<div class="block_wrapper">
<div class="block_top"><p class="align_right"><a href="{$CMS->vars['root_domain']}/?site=config_general{$CMS->class->search->url_return}">&laquo; {$CMS->lang['config_general_header_back']}</a></p>{$CMS->lang['search_form']}</div>
<div class="block_mid">
<table width="100%" cellspacing="0" cellpadding="4">
  <tr>
    <td class="left25"><b>{$CMS->lang['config_general_name']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="config_general_name" value="{$data['config_general_name']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['config_general_website']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="config_general_website" value="{$data['config_general_website']}"></td>
  </tr>
  <tr>
    <td class="left25"><b>{$CMS->lang['config_general_owner']}</b>:</td>
    <td><input class="input_text" size="45" type="text" name="config_general_owner" value="{$data['config_general_owner']}"></td>
  </tr>
</table>
</div>
</div>
<div class="block_desc_bottom_submit">
	<input class="input_submit" type="submit" name="submit" value=" {$CMS->lang['search_submit']} "> 
</div>
<div class="block_bottom pagination pagination-sm"></div>
</form>
<script language="javascript">rebuild_form("config_general",1,1);</script>
EOF;

        return $output;
    }
    public function GoogleMaps()
    {
        global $CMS;

        $lat = $CMS->vars['company_address_x'] ? $CMS->vars['company_address_x'] : 36.778261;
        $lng = $CMS->vars['company_address_y'] ? $CMS->vars['company_address_y'] : -119.41793239999998;

        if(!$lat || !$lng) return false;

        $output = <<<EOF
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <input type="hidden" name="config[input][company_address_x]" class="gLat" value="{$lat}">
                <input type="hidden" name="config[input][company_address_y]" class="gLng" value="{$lng}">
            </div>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <div id="map" style="height: 500px;display: none"></div>
            </div>
        </div>
<script src="https://maps.googleapis.com/maps/api/js?key={$CMS->vars['google_map_key']}&libraries=places"></script>

<script>
      function updateLatLng(lat, lng)
      {
          $(".gLat").val(lat);
          $(".gLng").val(lng);
      }

      function initAutocomplete() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: {$lat}, lng: {$lng}},
          zoom: 14,
          mapTypeId: 'roadmap'
        });
        
        

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        
        //First marker
        let fMarker = addMarker(map, map.getCenter().lat(), map.getCenter().lng())
        markers.push(fMarker);
        
        
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          
            var places = searchBox.getPlaces();
          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            let marker = addMarker(map, place.geometry.location.lat(), place.geometry.location.lng());
            
            markers.push(marker);
            
            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
        
        return map;
      }
      
      function addMarker(map, lat, lng)
      {
          updateLatLng(lat, lng);
          
          let marker = new google.maps.Marker({
              map: map,
              position: {lat: lat, lng: lng},
              draggable: true
            })
            
            //handle drag marker
            marker.addListener('dragend', function() {
                updateLatLng(marker.getPosition().lat, marker.getPosition().lng);
            });
            
            return marker;
      }
      
      
      var isLoadMap = 0;
      
      function mapToggle(eventObj, mapObj)
      {
          mapObj.toggle();
          eventObj.toggleClass("blue");
          
          if(isLoadMap == 1) return false;

          if(mapObj.is(':visible'))
          {
              isLoadMap = 1;
              initAutocomplete();
          }
      }
      
      function mapEnabled(eventObj, mapObj)
      {
          mapObj.show();
          eventObj.addClass("blue");
          
          if(isLoadMap == 1) return false;

          if(mapObj.is(':visible'))
          {
              isLoadMap = 1;
              initAutocomplete();
          }
      }

</script>
EOF;

        return $output;
    }

    public function GoogleMapsIframe()
    {
        global $CMS;

        $output = <<<EOF
        <div class="row" id="map" style="display: none">
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <textarea id="google_maps_iframe" name="config[input][google_maps_iframe]" class="form-control" placeholder="Embed google map here">{$CMS->vars['google_maps_iframe']}</textarea>
            </div>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                <a class="btn-primary btn" onclick="previewMaps($('.preview-maps'), $('#google_maps_iframe'))">Preview</a>
                <a class="btn-danger btn" onclick="clearMaps($('.preview-maps'), $('#google_maps_iframe'))">Clear map</a>
            </div>
            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12 preview-maps" style="margin-top: 20px">
                    {$CMS->vars['google_maps_iframe']}
            </div>
        </div>
    <script>
      function mapEnabled(eventObj, mapObj)
      {
          mapObj.show();
          eventObj.addClass("blue");
      }
      
      function mapToggle(eventObj, mapObj)
      {
          mapObj.toggle();
          eventObj.toggleClass("blue");
      }
      
      function previewMaps(displayObj, dataObj) {
        displayObj.html(dataObj.val());
      }
      
      function clearMaps(displayObj, dataObj) {
        dataObj.val('');
        displayObj.html('');
      }
    </script>
    
EOF;

        return $output;
    }

    public function seoTab()
    {
        global $CMS;

        $optimize_image_checked = $CMS->vars['optimize_image'] ? 'checked' : '';

        $output = <<<EOF
        <div role="tabpanel" class="tab-pane fade" id="tabs-4-tab-2">
            <div class="row">
                <div class="col-lg-6">
                    <h5 class="m-t-lg with-border">Copy from URL:</h5> 
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">URL: </label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                      <div class="input-group">
                           <input name="config[input][seo_crawling_url]" class="form-control" type="text" id="crawling_url" value="{$CMS->vars['seo_crawling_url']}">
                          <div class="input-group-btn">
                            <button onclick="crawlingSEO($('#crawling_url').val())" class="btn btn-default" type="button"><i class="fa fa-refresh"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <h5 class="m-t-lg with-border">Copy from source:</h5> 
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">Source: </label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                           <textarea rows="10" id="seo_crawling_source" name="seo_crawling_source" class="form-control"></textarea>
                          <button onclick="crawlingSEO($('#seo_crawling_source').val(), 'source')" class="btn btn-default" type="button"><i class="fa fa-refresh"></i></button>
                      </div>
                    </div>
                    <h5 class="m-t-lg with-border">Meta</h5> 
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_seo_keyword']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][seo_keyword]" crawling="name.keywords">{$CMS->vars['seo_keyword']}</textarea>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_seo_description']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][seo_description]" crawling="name.description">{$CMS->vars['seo_description']}</textarea>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_seo_author']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][seo_author]" crawling="name.author">{$CMS->vars['seo_author']}</textarea>
                      </div>
                    </div>
                    
                    <h5 class="m-t-lg with-border">OG</h5>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_og_title']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][og_title]" value="{$CMS->vars['og_title']}" crawling="property.og:title">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_og_description']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][og_description]" crawling="property.og:description">{$CMS->vars['og_description']}</textarea>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_og_image']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="file" class="form-control" name="og_image" value="{$CMS->vars['og_image']}">
EOF;
        if(is_file("{$CMS->vars['upload_dir']}/attach/{$CMS->vars['og_image']}"))
        {
            $output .= <<<EOF
                        <img style="max-width: 300px;" src="{$CMS->vars['upload_url']}/attach/{$CMS->vars['og_image']}">
EOF;
        }


        $output .= <<<EOF
                      </div>
                    </div>
                    
                    <h5 class="m-t-lg with-border">GEO</h5>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_geo_region']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][geo_region]" value="{$CMS->vars['geo_region']}" crawling="name.geo.region">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_geo_placename']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][geo_placename]" value="{$CMS->vars['geo_placename']}" crawling="name.geo.placename">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_geo_position']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][geo_position]" value="{$CMS->vars['geo_position']}" crawling="name.geo.position">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_geo_icbm']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][geo_icbm]" value="{$CMS->vars['geo_icbm']}" crawling="name.ICBM">
                      </div>
                    </div>
                    
                    <h5 class="m-t-lg with-border">Dublin core</h5>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_dc_title']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][dc_title]" value="{$CMS->vars['dc_title']}" crawling="name.DC.title">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_dc_description']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][dc_description]" crawling="name.DC.description">{$CMS->vars['dc_description']}</textarea>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_dc_subject']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][dc_subject]" crawling="name.DC.subject">{$CMS->vars['dc_subject']}</textarea>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_dc_language']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[textarea][dc_language]" value="{$CMS->vars['dc_language']}" crawling="name.DC.language">
                      </div>
                    </div>

                    <h5 class="m-t-lg with-border">SOCIAL</h5>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_google_id_fanpage']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][google_id_fanpage]">{$CMS->vars['google_id_fanpage']}</textarea>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_facebook_id_fanpage']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][facebook_id_fanpage]">{$CMS->vars['facebook_id_fanpage']}</textarea>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_twitter_id_fanpage']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][twitter_id_fanpage]">{$CMS->vars['twitter_id_fanpage']}</textarea>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_facebook']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_facebook]" value="{$CMS->vars['link_facebook']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_google']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_google]" value="{$CMS->vars['link_google']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_twitter']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_twitter]" value="{$CMS->vars['link_twitter']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_youtube']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_youtube]" value="{$CMS->vars['link_youtube']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_instagram']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_instagram]" value="{$CMS->vars['link_instagram']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_yelp']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_yelp]" value="{$CMS->vars['link_yelp']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_vimeo']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_vimeo]" value="{$CMS->vars['link_vimeo']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_blogs']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_blog]" value="{$CMS->vars['link_blog']}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_pinterest']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_pinterest]" value="{$CMS->vars['link_pinterest']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_yellow_page']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_yellowpage]" value="{$CMS->vars['link_yellowpage']}">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_link_foursquare_page']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="config[input][link_foursquare]" value="{$CMS->vars['link_foursquare']}">
                      </div>
                    </div>

                    <h5 class="m-t-lg with-border">OTHERS</h5>
                    <div class="form-group row">
                        <label class="col-xl-4 form-control-label">{$CMS->lang['optimize_image']}</label>
                        <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                          <div class="checkbox-toggle" style="margin-top: 10px;">
                            <input type="checkbox" class="enabled_check" id="optimize_image" name="config[yes_no][optimize_image]" value="1" {$optimize_image_checked}>
                            <label for="optimize_image"></label>
                          </div>
                          <span class="note_info" style="font-size: 12px;color: red;font-style: italic;">{$CMS->lang['optimize_image_note']}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_google_analytic']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][google_analytic]">{$CMS->vars['google_analytic']}</textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_mastertool']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][webmaster_tools]" crawling="name.google-site-verification">{$CMS->vars['webmaster_tools']}</textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_h1_content']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="4" name="config[textarea][h1_content]">{$CMS->vars['h1_content']}</textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_price_range']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input class="form-control" rows="4" name="config[input][price_range]"  value="{$CMS->vars['price_range']}">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_favicon']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="file" class="form-control" name="favicon"/>
EOF;
        if(is_file("{$CMS->vars['upload_dir']}/attach/{$CMS->vars['favicon']}"))
        {
            $output .= <<<EOF
                      <div class="box_favicon">
                        <img src="{$CMS->vars['upload_url']}/attach/{$CMS->vars['favicon']}">
                        <a class="btn_del_favicon"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                      </div>
EOF;
        }


        $output .= <<<EOF
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-xl-4 form-control-label">{$CMS->lang['label_site_map']}</label>
                      <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                        <input type="file" class="form-control" name="site_map"/>
EOF;

        if(is_file("{$CMS->vars['upload_dir']}/sitemap/sitemap.xml"))
        {
            $output .= <<<EOF
            <span class="note_info"><a href="{$CMS->vars['upload_url']}/sitemap/sitemap.xml" target="_blank">sitemap.xml</a></span>
EOF;
        }
        else
        {
            $output .= <<<EOF
            <span class="note_info">Have no sitemap.xml</span>
EOF;
        }

        $output .= <<<EOF
                      </div>
                    </div>
                </div>
              </div>

          </div><!--.tab-pane-->
EOF;
        return $output;
    }

    public function openHours()
    {
        global $CMS;

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday','sunday'];

        $openHours = [
            '6:00 am','6:15 am',
            '6:30 am','6:45 am',
            '7:00 am','7:15 am',
            '7:30 am','7:45 am',
            '8:00 am','8:15 am',
            '8:30 am','8:45 am',
            '9:00 am','9:15 am',
            '9:30 am','9:45 am',
            '10:00 am','10:15 am',
            '10:30 am','10:45 am',
            '11:00 am','11:15 am',
            '11:30 am','11:45 am',
            '12:00 pm','12:15 pm',
            '12:30 pm','12:45 pm',
            '1:00 pm','1:15 pm',
            '1:30 pm','1:45 pm',
            '2:00 pm','2:15 pm',
            '2:30 pm','2:45 pm',
            '3:00 pm',
        ];

        $closeHours = [
            '12:00 pm','12:15 pm',
            '12:30 pm','12:45 pm',
            '1:00 pm','1:15 pm',
            '1:30 pm','1:45 pm',
            '2:00 pm','2:15 pm',
            '2:30 pm','2:45 pm',
            '3:00 pm','3:15 pm',
            '3:30 pm','3:45 pm',
            '4:00 pm','4:15 pm',
            '4:30 pm','4:45 pm',
            '5:00 pm','5:15 pm',
            '5:30 pm','5:45 pm',
            '6:00 pm','6:15 pm',
            '6:30 pm','6:45 pm',
            '7:00 pm','7:15 pm',
            '7:30 pm','7:45 pm',
            '8:00 pm','8:15 pm',
            '8:30 pm','8:45 pm',
            '9:00 pm','9:15 pm',
            '9:30 pm','9:45 pm',
            '10:00 pm','10:15 pm',
            '10:30 pm','10:45 pm',
            '11:00 pm','11:15 pm',
            '11:30 pm','11:45 pm',
        ];

        $openHoursSelect = "";

        foreach ($openHours as $hour)
        {
            $openHoursSelect .= "<option value='{$hour}'>{$hour}</option>";
        }

        $closeHoursSelect = "";

        foreach ($closeHours as $hour)
        {
            $closeHoursSelect .= "<option value='{$hour}'>{$hour}</option>";
        }


        $output = <<<EOF
        <h5 class="m-t-lg with-border">Open hours</h5>
        <div class="form-group row">
          <label class="col-xl-4 form-control-label">{$CMS->lang['label_use_booking_open_hours']}</label>
          <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
             <select class="form-control auto_select" type="text" name="config[yes_no][booking_open_hours]" defaultvalue="{$CMS->vars['booking_open_hours']}">
                <option value="0">{$CMS->lang['title_off']}</option>
                <option value="1">{$CMS->lang['title_on']}</option>
                </select>
          </div>
        </div>
       
EOF;

        foreach ($days as $day)
        {
            $output .= <<<EOF
            <div class="form-group row">
                <label class="col-xl-4 form-control-label"><input name="open_hours[{$day}][checked]" value="1" type="checkbox">&nbsp;{$CMS->lang[$day]}</label>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                    <select name="open_hours[{$day}][open]" class="form-control select2">{$openHoursSelect}</select>
                </div>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                    <select name="open_hours[{$day}][close]" class="form-control select2">{$closeHoursSelect}</select>
                </div>
            </div>
EOF;

        }

        //Script checked
        $openHours = @json_decode($CMS->vars['open_hours'], true);

        $output .= <<<EOF
        <script>
EOF;
        // LHL-2018-06-07, Fix $openhours is not available
        if ( isset($openHours) ) {
            foreach ($openHours as $day => $openHour) {
                if ($openHour['checked']) {
                    $output .= <<<EOF
            $('[name="open_hours[{$day}][checked]"]').prop('checked', true);
EOF;
                }

                $output .= <<<EOF
            $('[name="open_hours[{$day}][open]"]').val('{$openHour['open']}');
            $('[name="open_hours[{$day}][close]"]').val('{$openHour['close']}');
EOF;

            }
        }

        $output .= <<<EOF
        </script>
EOF;


        return $output;
    }

    function smsConfig()
    {
        global $CMS;

        $sms_number_checked = $CMS->vars['sms_enabled'] ? 'checked' : '';
        $sms_subscription_checked = $CMS->vars['sms_subscription'] ? 'checked' : '';
        $output = <<<EOF
        <!-- Config Ngan Luong -->
        <h5 class="m-t-lg with-border">SMS</h5>      
        <div class="form-group row">
            <label class="col-xl-4 form-control-label">Enabled</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <div class="checkbox-toggle" style="margin-top: 10px;">
                <input type="checkbox" class="enabled_check" id="sms_enabled" name="config[yes_no][sms_enabled]" value="1"  {$sms_number_checked}>
                <label for="sms_enabled"></label>
              </div>
            </div>
          </div>
         <div class="form-group row">
            <label class="col-xl-4 form-control-label">Subscription SMS</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <div class="checkbox-toggle" style="margin-top: 10px;">
                <input type="checkbox" class="enabled_check_sms_subscription" id="sms_subscription" name="config[yes_no][sms_subscription]" value="1" onchange="smsgroupCheckToggle();" {$sms_subscription_checked}>
                <label for="sms_subscription"></label>
              </div>
            </div>
          </div>
          <div class="form-group row" config_group="sms_subscription">
            <label class="col-xl-4 form-control-label">SMS number</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <input class="form-control paypal_id" type="text" name="config[input][sms_number]" value="{$CMS->vars['sms_number']}">
            </div>
          </div>

            <script>
                function smsgroupCheckToggle()
                {
                    $("[config_group='sms_subscription']").hide();
                    $.each($(".enabled_check_sms_subscription:checked"), (key, obj) => {
          
                       $("[config_group='"+$(obj).attr('id')+"']").show();
                    })
                }
                
                smsgroupCheckToggle();
            </script>
EOF;

        return $output;
    }


    function marConfig()
    {
        global $CMS;



        $output = <<<EOF
     
        <h5 class="m-t-lg with-border">Marketing</h5>      
      
          <div class="form-group row" config_group="gg_id_place">
            <label class="col-xl-4 form-control-label">Google ID Place</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <input class="form-control" type="text" name="config[input][gg_id_place]" value="{$CMS->vars['gg_id_place']}">
            </div>
          </div>
         <div class="form-group row" config_group="gg_apikey_place">
            <label class="col-xl-4 form-control-label">Google API Key Place</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <input class="form-control " type="text" name="config[input][gg_apikey_place]" value="{$CMS->vars['gg_apikey_place']}">
            </div>
          </div>

          <div class="form-group row" config_group="zd_contact_id">
            <label class="col-xl-4 form-control-label">Zendesk Contact ID</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <input class="form-control" type="text" name="config[input][zd_contact_id]" value="{$CMS->vars['zd_contact_id']}">
            </div>
          </div>

         <div class="form-group row" config_group="gg_analytics_account">
            <label class="col-xl-4 form-control-label">Google Analytics Account</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <span class="btn btn-rounded btn-file">
                      <span>Choose file</span>
                      <input type="file" name="gg_analytics_account" multiple="">
              </span>
EOF;
        if($CMS->vars['gg_analytics_account'] != "")
        {
            $output .= <<<EOF
                <p>File: {$CMS->vars['gg_analytics_account']}  <a alt="Delete file" href="{$CMS->vars['root_domain']}/?site=config_general&act=edit_do&subact=del_key_gg_analytics"><i class="fa fa-trash-o"></i></a></p>
               
EOF;

        }
        $output .= <<<EOF
            </div>
          </div>

        
EOF;

        return $output;
    }


    function freshDeskConfig() {
        global $CMS;
        $out = "";
        if ($CMS->vars['addon_fresh_desk_enable'] == 1) {
            $out = <<<EOF
<!-- Config FreshDesk  -->
<h5 class="m-t-lg with-border">FreshDesk</h5>      
<div class="form-group row">
	<label class="col-xl-4 form-control-label">Domain</label>
	<div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
		<input class="form-control paypal_id" type="text" name="config[input][fd_domain]" value="{$CMS->vars['fd_domain']}">
	</div>
</div>
<div class="form-group row">
	<label class="col-xl-4 form-control-label">Key</label>
	<div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
		<input class="form-control paypal_id" type="text" name="config[input][fd_key]" value="{$CMS->vars['fd_key']}">
	</div>
</div>
EOF;
        }
        return $out;
    }

    function cacheConfig()
    {
        global $CMS;

        $is_cache_checked = $CMS->vars['is_cache'] ? 'checked' : '';
        $cache_type_selected[$CMS->vars['cache_type']] =  'selected';
        $output = <<<EOF
        <!-- Config Ngan Luong -->
        <h5 class="m-t-lg with-border">{$CMS->lang['title_tab_cache']} <a href="?site=config_general&tab=tabs-config-cache&subact=clear_cache" class="btn btn-warning">{$CMS->lang['clear_cache']}</a></h5>      
         <div class="form-group row">
            <label class="col-xl-4 form-control-label">{$CMS->lang['label_enable_cache']}</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <div class="checkbox-toggle" style="margin-top: 10px;">
                <input type="checkbox" class="enabled_check_is_cache" id="is_cache" name="config[yes_no][is_cache]" value="1" onchange="cachegroupCheckToggle();" {$is_cache_checked}>
                <label for="is_cache"></label>
              </div>
            </div>
          </div>
          <div class="form-group row" config_group="is_cache">
            <label class="col-xl-4 form-control-label">{$CMS->lang['label_cache_engine']}</label>
            <div class="col-xl-2 col-lg-2 col-sm-4 col-xs-6">
               <select class="form-control" name="config[select][cache_type]">
                    <option {$cache_type_checked['file']} value="file">File</option>
                    <option {$cache_type_selected['redis']} value="redis">Redis</option>
               </select>
            </div>
          </div>

            <script>
                function cachegroupCheckToggle()
                {
                    $("[config_group='is_cache']").hide();
                    $.each($(".enabled_check_is_cache:checked"), (key, obj) => {
                       $("[config_group='"+$(obj).attr('id')+"']").show();
                    })
                }
                
                cachegroupCheckToggle();
            </script>
EOF;

        return $output;
    }


    function saleConfig()
    {
        global $CMS;

        $is_negative_sale_checked = $CMS->vars['negative_sale'] ? 'checked' : '';
        $is_enabled_commission = $CMS->vars['enabled_commission'] ? 'checked' : '';

        $app_printing_bill_checked = [];
        $app_printing_bill = input::jsonDecode($CMS->vars['app_printing_bill']);

        $app_theme_checked[$CMS->vars['app_theme']] = "selected";
        $app_enabled_multi_booking_checked[$CMS->vars['app_enabled_multi_booking']] = "selected";

        foreach($app_printing_bill as $key => $value)
        {
            $app_printing_bill_checked[$key] = "checked";
        }

        foreach (['stores-info', 'video'] as $value)
        {
            $checkin_welcome_screen_selected[$value] = $value == input::vars('checkin_welcome_screen') ? 'selected' : '';
        }

        foreach ([0, 1] as $value)
        {
            $pos_order_type_default_selected[$value] = $value == input::vars('pos_order_type_default') ? 'selected' : '';
        }

        $output = <<<EOF
<div class="row">
    <div class="col-lg-6">
        <h5 class="m-t-lg with-border">{$CMS->lang['act_config']}</h5> 
         <div class="form-group row">
            <label class="col-xl-4 form-control-label">{$CMS->lang['label_negative_sale']}</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <div class="checkbox-toggle" style="margin-top: 10px;">
                <input type="checkbox" class="enable_negative_sale" id="negative_sale" name="config[yes_no][negative_sale]" value="1" onchange="negative_saleCheckToggle();" {$is_negative_sale_checked}>
                <label for="negative_sale"></label>
              </div>
            </div>
          </div>
           <div class="form-group row">
            <label class="col-xl-4 form-control-label">{$CMS->lang['gcommission']}</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <div class="checkbox-toggle" style="margin-top: 10px;">
                <input type="checkbox" class="enabled_commission" id="enabled_commission" name="config[yes_no][enabled_commission]" value="1" {$is_enabled_commission}>
                <label for="enabled_commission"></label>
              </div>
            </div>
          </div>
          
          <h5 class="m-t-lg with-border">POS & BOOKING</h5>
          <div class="form-group row">
                <label class="col-xl-4 form-control-label">{$CMS->lang['auto_refresh_data']}</label>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                    <div class="input-group">
                      <input type="number" value="{$CMS->vars['app_refresh_data_period']}" name="config[input][app_refresh_data_period]" class="form-control">
                      <span class="input-group-addon">{$CMS->lang['minute']}</span>
                    </div>
                    <span class="note_info" style="font-size: 12px;color: red;font-style: italic;">{$CMS->lang['auto_refresh_data_note']}</span>
                </div>
            </div>
          <div class="form-group row">
                <label class="col-xl-4 form-control-label">{$CMS->lang['printing_bill_info']}</label>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                    <div>
                      <label><input name="config[input][app_printing_bill][name]" type="checkbox" value="1" {$app_printing_bill_checked['name']}> {$CMS->lang['name']}</label>
                    </div>
                    <div>
                      <label><input name="config[input][app_printing_bill][address]" type="checkbox" value="1" {$app_printing_bill_checked['address']}> {$CMS->lang['address']}</label>
                    </div>
                    <div>
                      <label><input name="config[input][app_printing_bill][phone]" type="checkbox" value="1" {$app_printing_bill_checked['phone']}> {$CMS->lang['phone']}</label>
                    </div>
                    <div>
                      <label><input name="config[input][app_printing_bill][logo]" type="checkbox" value="1" {$app_printing_bill_checked['logo']}> Logo</label>
                    </div>
                    <div>
                      <label><input name="config[input][app_printing_bill][website]" type="checkbox" value="1" {$app_printing_bill_checked['website']}> Website</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-xl-4 form-control-label">Loại hóa đơn mặc định</label>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                   <select class="form-control" name="config[input][pos_order_type_default]" id="pos_order_type_default">
                        <option {$pos_order_type_default_selected[0]} value="0">Sản phẩm</option> 
                        <option {$pos_order_type_default_selected[1]} value="1">Dịch vụ</option> 
                   </select>
                </div>
            </div>
EOF;

        if(input::vars('booking_enable')) {
            $output .= <<<EOF
            <div class="form-group row">
                <label class="col-xl-4 form-control-label">Cho phép đặt nhiều dịch vụ trong 1 đơn hàng (Booking)</label>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                   <select class="form-control" name="config[input][app_enabled_multi_booking]" id="app_enabled_multi_booking">
                        <option {$app_enabled_multi_booking_checked[0]} value="0">Không</option> 
                        <option {$app_enabled_multi_booking_checked[1]} value="1">Có</option> 
                   </select>
                </div>
            </div>
EOF;

        }

        if(input::vars('checkin_enabled'))
        {
            $url_image = "";
            $display = $css_logo = "";
            if($CMS->vars['logo_checkin'])
            {
                $display = " display: block; ";
                $url_image = "{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_checkin']}";
                $css_logo = "style='display: none;'";
            }

            $output .= <<<EOF
        <h5 class="m-t-lg with-border">Check-in</h5>
            <div class="form-group row">
              <label class="col-xl-4 form-control-label">Logo </label>
              <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                  <fieldset class="form-group" style="margin-bottom: 0;">
                        <div class="actionButtons pull-right">
                            <ul>
                                <li onclick="return performClick('checkinfile');">
                                    <i tabindex="0" class="fa fa-pencil"></i>
                                </li>
                            </ul>
                            <input type="hidden" id="checkinfile_output_b64" name="base64_image" value="">
                        </div>
                         
                        <div class="drop-zone fileinput-button" style="height: 110px !important; padding-top: 0;">
                                <img id="upload_img_checkin_show" style="margin: 0 auto; max-width: 100%; max-height: 110px; {$display}" src="{$url_image}">
                                <i class="font-icon font-icon-cloud-upload-2" {$css_logo}></i>
                                <div class="drop-zone-caption" {$css_logo}>Drag file to upload</div>
                                <input type="file" name="logo_checkin" id="checkinfile" accept="image/*">
                        </div><!--.drop-zone-->
                    </fieldset>
              </div>
            </div>
            <div class="form-group row">
                <label class="col-xl-4 form-control-label">Màn hình chào</label>
                <div class="col-xl-4 col-lg-12 col-sm-12 col-xs-12">
                   <select onchange="changeCheckinScreenType()" class="form-control" name="config[input][checkin_welcome_screen]" id="checkin_welcome_screen">
                        <option {$checkin_welcome_screen_selected['stores-info']} value="stores-info">Thông tin cửa hàng</option> 
                        <option {$checkin_welcome_screen_selected['video']} value="video">Video Youtube</option> 
                   </select>
                </div>
            </div>
            
            <div class="form-group row checkin-block video" style="display:none">
                <label class="col-xl-4 form-control-label">Mã nhúng videos Youtube</label>
                <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                    <textarea class="form-control" name="config[input][checkin_welcome_screen_video]" rows="5">{$CMS->vars['checkin_welcome_screen_video']}</textarea>
                </div>
            </div>
            <script>
                function changeCheckinScreenType() {
                    let checkinType = $("#checkin_welcome_screen").val();
                    console.log(checkinType);
                    $(".checkin-block").hide();
                    $(".checkin-block." + checkinType).show();
                }
                
                $(document).ready(function() {
                    changeCheckinScreenType();
                })
            </script>
EOF;
        }

        $output .= <<<EOF

        <h5 class="m-t-lg with-border">{$CMS->lang['title_custom_status']}</h5>
        <div class="form-group row">
              <label class="col-xl-4 form-control-label">{$CMS->lang['title_choose_status_custom']}</label>
              <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                <div class="row">
                  <p class="status_title with-border">{$CMS->lang['order_status_0']}</p>
                </div>
                <div class="box_list">
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[0][]" id="check-0" value="0" >
                    <label for="check-0">{$CMS->lang['title_custom_status_0']}</label>
                  </div>
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[0][]" id="check-10" value="10" >
                    <label for="check-10">{$CMS->lang['title_custom_status_10']}</label>
                  </div>
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[0][]" id="check-20" value="20" >
                    <label for="check-20">{$CMS->lang['title_custom_status_20']}</label>
                  </div>
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[0][]" id="check-30" value="30" >
                    <label for="check-30">{$CMS->lang['title_custom_status_30']}</label>
                  </div>

                  <!-- order missed -->
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[0][]" id="check-40" value="40" >
                    <label for="check-40">{$CMS->lang['title_custom_status_40']}</label>
                  </div>
                </div>

                <div class="row">
                  <p class="status_title with-border">{$CMS->lang['order_status_1']}</p>
                </div>
                <div class="box_list">
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[1][]" id="check-11" value="11">
                    <label for="check-11">{$CMS->lang['title_custom_status_11']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[1][]" id="check-12" value="12">
                    <label for="check-12">{$CMS->lang['title_custom_status_12']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[1][]" id="check-13" value="13">
                    <label for="check-13">{$CMS->lang['title_custom_status_13']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[1][]" id="check-14" value="14">
                    <label for="check-14">{$CMS->lang['title_custom_status_14']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[1][]" id="check-15" value="15">
                    <label for="check-15">{$CMS->lang['title_custom_status_15']}</label>
                  </div>

                </div>

                <div class="row clearfix">
                  <p class="status_title with-border">{$CMS->lang['order_status_2']}</p>
                </div>
                <div class="box_list">
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[2][]" id="check-21" value="21" >
                    <label for="check-21">{$CMS->lang['title_custom_status_21']}</label>
                  </div>
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[2][]" id="check-22" value="22" >
                    <label for="check-22">{$CMS->lang['title_custom_status_22']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[2][]" id="check-23" value="23" >
                    <label for="check-23">{$CMS->lang['title_custom_status_23']}</label>
                  </div>
                  
                </div>


                <div class="row">
                  <p class="status_title with-border">{$CMS->lang['order_status_3']}</p>
                </div>
                <div class="box_list">
                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[3][]" id="check-31" value="31" >
                    <label for="check-31">{$CMS->lang['title_custom_status_31']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[3][]" id="check-32" value="32">
                    <label for="check-32">{$CMS->lang['title_custom_status_32']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[3][]" id="check-33" value="33">
                    <label for="check-33">{$CMS->lang['title_custom_status_33']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[3][]" id="check-34" value="34">
                    <label for="check-34">{$CMS->lang['title_custom_status_34']}</label>
                  </div>

                  <div class="checkbox col-lg-6 col-md-6">
                    <input type="checkbox" name="custom_status[3][]" id="check-35" value="35">
                    <label for="check-35">{$CMS->lang['title_custom_status_35']}</label>
                  </div>
                </div>




              </div>
          </div>

          <!-- Custom event after add cart -->
          <h5 class="m-t-lg with-border">{$CMS->lang['title_custom_add_cart']}</h5>
          <div class="form-group row">
              <label class="col-xl-4 form-control-label">{$CMS->lang['title_custom_add_cart_note']}</label>
              <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
                 <select class="form-control" name="config[select][custom_add_cart]">
                      <option value="0">{$CMS->lang['title_jump_to_cart']}</option> 
                      <option value="1">{$CMS->lang['title_stay_on_the_current_page']}</option> 
                 </select>
              </div>
          </div>
          <script>
          $(document).ready(function(){
            $('[name="config[select][custom_add_cart]"] option[value="{$CMS->vars['custom_add_cart']}"]').prop('selected', true);
          });
          </script>

    </div>
</div>
EOF;

        return $output;
    }

    function instagramConfig()
    {
        global $CMS;



        $output = <<<EOF
     
        <h5 class="m-t-lg with-border">Instagram</h5>      
      
          <div class="form-group row">
            <label class="col-xl-4 form-control-label">User ID</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <input class="form-control" type="text" name="config[input][instagram_user_id]" value="{$CMS->vars['instagram_user_id']}">
              <span class="note_info" style="font-size: 12px;color: red;font-style: italic;">{$CMS->lang['config_insta_userid_note']}</span>
            </div>
          </div>
         <div class="form-group row">
            <label class="col-xl-4 form-control-label">Access key</label>
            <div class="col-xl-8 col-lg-12 col-sm-12 col-xs-12">
              <input class="form-control " type="text" name="config[input][instagram_access_token]" value="{$CMS->vars['instagram_access_token']}">
              <span class="note_info" style="font-size: 12px;color: red;font-style: italic;">{$CMS->lang['config_insta_userid_accesskey']}</span>
            </div>
          </div>        
EOF;

        return $output;
    }

}

?>