
  <!-- Font Awesome 3.0 -->
        <link href="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/css/font-awesome.css" rel="stylesheet">
        <!-- Annimate -->
        <link href="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/css/animate.css" rel="stylesheet">

        <!-- app Style -->
        <link  href="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/css/app.css" rel="stylesheet">
        <!-- app Responsive Style -->
        <link href="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/css/jquery-ui-1.8.17.custom.css" rel="stylesheet">

        <!-- Roboto Google font-->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
        <!-- google font Loader API -->

        <!-- color picker Style -->
        <link href="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/css/pick-a-color-1.1.7.min.css" rel="stylesheet">

        <!-- Modernizr-->
        <script type="text/javascript" src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/modernizr.custom.28468.js"></script>


<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" >

    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>

        <figure class="box-typical box-typical box-typical-padding border">
            <div class="row">
                <div class="col-lg-6">
                    <div class="row match-height">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['logo_position']?><font style="margin-left:5px" color="#FF0000">(*)</font></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <select class="form-control select2" name="logo_position" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_position']?>">
                                        <?=\core\ezy::render('position_options');?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['logo_name']?><!--font style="margin-left:5px" color="#FF0000">(*)</font     data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>" --></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control" type="tel" name="logo_name" value="<?=$tpl->data['logo_name']?>" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['logo_start_time']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control date-picker" type="tel" name="logo_start_time" value="<?=$tpl->data['logo_start_time']?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['logo_end_time']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <input class="form-control date-picker" type="tel" name="logo_end_time" value="<?=$tpl->data['logo_end_time']?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label"><?=$CMS->lang['logo_src_alt']?></label>
                            <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                <input class="form-control" type="tel" name="logo_src_alt" value="<?=$tpl->data['logo_src_alt']?>">
                            </div>
                         </div>
                          <div class="form-group ">
                             <label class="form-control-label"><?=$CMS->lang['upload_option']?></label>
                              <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                               
                                <div class="radio w25">
                                  <input type="radio" name="upload_option" id="radio-show-0"  checked="checked" value="0">
                                  <label for="radio-show-0"><?=$CMS->lang['upload_option_0']?></label>
                                </div>
                                  <div class="radio w25">
                                  <input type="radio" name="upload_option" id="radio-show-1" value="1">
                                  <label for="radio-show-1"><?=$CMS->lang['upload_option_1']?></label>
                                </div>  
                               </div>  
                        </div>
                        
                    </div>
                </div> <!--End col-lg-4-->

                <div class="col-lg-6">

                    <div class="form-group row">
                        <label class="form-control-label"><?=$CMS->lang['logo_link']?></label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <input class="form-control" type="tel" name="logo_link" value="<?=$tpl->data['logo_link']?>"> <!-- data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_link']?>" -->
                        </div>
                    </div>

                  
                  
                          <div class="form-group row">
                                <label class="form-control-label"><?=$CMS->lang['logo_desc']?></label>
                                <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                                    <textarea class="form-control" rows="6" name="logo_desc"><?=$tpl->data['logo_desc']?></textarea>
                                </div>
                            </div>
                </div>
            </div>



     <div class="row" id="option_upload_image_0">
         <div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
               <div class="form-group row">
                        <label class="form-control-label">
                            <span class="pull-left"><?=$CMS->lang['logo_src']?></span>
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
                                <input  type="hidden" id="ufile_output_b64" name="base64_image" value="{$base64_image}" />
                            </div>
                        </label>
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <div class="drop-zone fileinput-button" style="height: 110px !important">
                                <i class="font-icon font-icon-cloud-upload-2"></i>
                                <div class="drop-zone-caption">Drag file to upload</div>
                                <input type="file" name="upload_file" id="ufile" accept="image/*">
                            </div><!--.drop-zone-->
                            <img  id="upload_img_show" class="img-responsive" src="<?=$tpl->data['logo_src']?>" style="max-width: 100%;" alt="">
                        </div>
                    </div>

           </div>
       </div><!-- end class row opption 0 -->  
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
                               <?=$tpl->data['designtext']?>
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

                            <label style="margin-top:13px"><i class="icon-magic"></i>Shadow </label>
                              
                             <div class="slider">
                                <div class='size_hshadow'>0px</div>
                                <div id="slider_hshadow"></div>
                            </div>
                            <div class="slider">
                                <div class='size_vshadow'>0px</div>
                                <div id="slider_vshadow"></div>
                            </div>
                             <div class="slider">
                                <div class='size_blurshadow'>0px</div>
                                <div id="slider_blurshadow"></div>
                            </div>
                                 <!-- input colors -->
                                <input id='shadow_color' type="text" style="border: 1px solid rgba(197,214,222,.7); box-shadow: none;font-size: 13px;" class="pick-a-color span8">

                            <!-- section to change Fonts -->
                            <label style="margin-top:10px"><i class="icon-beaker"></i> Fonts</label>
                            <!-- btn groupe using bootstrap - dropup : to show menu up -->
                            <div class="btn-group dropup">
                                
                            
                            <div class="dropdown">
                              <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Select font
                              </button>
                              <div class="dropdown-menu" id="font" aria-labelledby="dd-header-add">
                                   <li><a class="dropdown-item" data-font = 'Cantora+One'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/1.jpg" alt=""></a></li>
                                    <li><a href="#" data-font ='Londrina+Outline'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/2.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Raleway'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/3.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Kavoon'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/4.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Kotta+One'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/5.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Parisienne'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/6.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Amarante'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/7.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Caesar+Dressing'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/8.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Spirax'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/9.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Indie+Flower'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/10.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Erica+One'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/11.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='UnifrakturMaguntia'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/12.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Shojumaru'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/13.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Finger+Paint'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/14.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Sigmar+One'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/15.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Petit+Formal+Script'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/16.jpg" alt=""></a></li>

                                <li><a href="#" data-font ='Monoton'><img src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/img/fonts/17.jpg" alt=""></a></li>
                                  
                              </div>
                          </div>
                              <!-- google fonts  - images - data-font contain name of font-->
 
                            </div>
                        </div><!-- widget content -->
                    </div><!-- widget -->

                </div><!-- Span3 -->
               </div><!--end col 6 -->
               
               <div class="col-md-8" style="margin-bottom:5px">           
           
                 <div class="row">
                   <div class="span6">
                  
                    <div class='designContainer' id='printable' style="float:left;max-height:400px;"  >
                        
                        <!-- Text container  - class ='no-delete' we can't delete it (the originale text) -->

                        <?=$tpl->data['designstyle']?>
      
                        <!-- default T-shirt -->
                        <img id='Tshirtsrc'   style="max-height:400px" src="<?=$tpl->data['logo_image_original_src']?>" alt="">
                     

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
                                              
                                               
                                                  <span class="btn btn-rounded btn-file">
                                                        <span>Choose file</span>
                                                        <input type="file" name="upload_image_method_1"  onchange="uploadPhotos(this)" >
                                                    </span>
                                                    <input type="hidden" name="style_push" id="style_push"  >
                                                    <input type="hidden" name="style_p_push" id="style_p_push" >
                                                    <input type="hidden" name="list_image" id="list_image"  >

                                               </div>


                                        </li>

                                        <li class="divider-vertical"></li>
 

                                    </ul>
                                </div><!-- /.navbar-inner -->
                            </div><!-- navbar -->
                 
                </div><!-- end row -->   
            </div><!-- end col 8 -->
              

       </div><!-- end row -->   







        </figure>
    </section>

    <section class="add_cart_footer">
            <?=$CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"])?>
           <div class="btn-group dropup pull-right">
                <button type="button" name="add_logos" class="btn btn-inline btn-primary ladda-button pull-right add_cart" data-style="expand-right" data-size="xs"><span class="ladda-label"><?=$CMS->lang['logo_button_'.$CMS->input['act']];?></span><span class="ladda-spinner"></span><span class="ladda-spinner"></span></button>
            </div>      
    </section>

</form>
    <script type="text/javascript" src="jsacp/logos.js"></script>

 <!-- Jquery UI -->
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/jquery-ui-1.10.3.custom.min.js"></script>  
        <!-- Color picker Scripts --> 
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/pick-a-color-1.1.7.min.js"></script>   
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/tinycolor-0.9.15.min.js"></script>
        <!-- Print Script -->
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/jquery.print-preview.js"></script> 
        <!-- Html2canvas script -->
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/html2canvas.js"></script> 
        <!-- Convas To image script-->
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/Canvas2Image.js"></script> 
        <script type="text/javascript"  src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/base64.js"></script> 
     

        <!-- Default Script call -->
        <script type="text/javascript" src="<?=$CMS->vars['root_domain']?>/assets/t_shirt_designer/js/app.js"></script>

           
      
<script>
    $(document).ready(function(){
      validate_form_custom("#form_logos", "button[name='add_logos']", "logos");
    });
  </script>
<script>
      
window.uploadPhotos = function(onthis){
    // Read in file
 

    var file = onthis.files[0];

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
 

 $(document).ready(function(){

    var upload_option = "<?=$tpl->data['logo_upload_option']?>";
    $("#radio-show-"+upload_option).prop("checked",true);
    if(upload_option == 0)
    {

        $("#option_upload_image_0").show();
        $("#option_upload_image_1").hide();
    }
    else
    {
        $("#option_upload_image_1").show();
        $("#option_upload_image_0").hide();
    }
 });
</script>