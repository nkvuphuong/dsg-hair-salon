var ezyEditorName = "";

/**
 * Init editor
 */

function ezyEditorInit()
{
    $(document).ready(function() {
        // Active line
        $(".files-manager-side-list a").click(function(){
            $(".files-manager-side-list a").removeClass("active");
            $(this).addClass("active");
        });

        // Listen something i need
        ezyEditorListen();

        // Open default file //
        //ezyEditorOpen();
    });
}

/**
 * Open a template
 */

function ezyEditorOpen(htmlId)
{
    ezyEditorName = typeof htmlId != "undefined" ? htmlId : $('#ezyEditorSelect option:selected').val();
    url = site_root_domain+"/?site=interface&act=show&file="+ezyEditorName;
    $("#page_interface").show();
    $("#page_html").hide();
    $('#ezyEditor').hide();
    $('#ezyEditorIntro').show();
    $('#ezyEditorIntro').html("Loading...");

    $.ajax(url).done(function(response)
    {
        $('#ezyEditor').show();
        $('#ezyEditorIntro').hide();
     
        // $('#ezyEditorYour').summernote('code', $.parseJSON(response).htmlEditable);
        tinymce.get('ezyEditorYour').setContent($.parseJSON(response).htmlEditable);
        ezyEditorCheckChanges();


        // $('#ezyEditorOur').summernote('code', $.parseJSON(response).htmlOriginal);
        tinymce.get('ezyEditorOur').setContent($.parseJSON(response).htmlOriginal);
    });
}

/**
 * Save a template
 */

function ezyEditorSave()
{
    // Check exist selector name
    if ( !ezyEditorName )
    {
        alert("Something was wrong! Please refresh (F5) the page");
    }

    
        // var content = $('#ezyEditorYour').summernote('code');
        var content = tinymce.get('ezyEditorYour').getContent();

        // Try to save
        url = site_root_domain+"/?site=interface&act=edit&file="+ezyEditorName+"&revert="+ezyEditorCheckChanges();

        $.post(url, {content:content}).done(function(response)
        {
            $.notify({
                icon: 'font-icon font-icon-check-circle',
                title: '<strong>Notification</strong>',
                message: response.replace("%filename%", ezyEditorName)
            },{
                type: 'success'
            });

            ezyEditorCheckChanges();
        });
    
}

/**
 * Revert to default
 */

function ezyEditorRevert()
{
    url = site_root_domain+"/?site=interface&act=show&file="+ezyEditorName;

    $.ajax(url).done(function(response)
    {
        
            // $('#ezyEditorYour').summernote('code', $.parseJSON(response).htmlOriginal);
            tinymce.get('ezyEditorYour').setContent($.parseJSON(response).htmlOriginal)
         
        // $('#ezyEditorOur').summernote('code', $.parseJSON(response).htmlOriginal);
        tinymce.get('ezyEditorOur').setContent($.parseJSON(response).htmlOriginal)

        ezyEditorSave();
    });
}

/**
 * LHL, Listen the music :))
 */

function ezyEditorListen()
{
    $('.swal-btn-revert').click(function(e){
        e.preventDefault();
        swal({
            title: "Are you sure?",
            text: "We will revert your changes into default code!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, do it now!",
            cancelButtonText: "No, cancel!",
        }).then(function() {
            ezyEditorRevert();
            /*
             swal(
             'Reverted!',
             'Your change has been reverted',
             'success'
             )*/
        }, function(dismiss) {
            // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
            if (dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'We dont do anything :)',
                    'error'
                )
            }
        });
    });
}

/**
 * Check chanages
 */

function ezyEditorCheckChanges(langCode='')
{
    // let selector = langCode ? '#ezyEditorYour'+langCode : '#ezyEditorYour';
    let selector = langCode ? 'ezyEditorYour'+langCode : 'ezyEditorYour';

    // if ( $(selector).summernote('code') != $('#ezyEditorOur').summernote('code') )
    if ( tinymce.get(selector).getContent() != tinymce.get('ezyEditorOur').getContent() )
    {
        $('.swal-btn-revert').show();
        $('#recertIntro').hide();
        return false;
    }
    else {
        $('.swal-btn-revert').hide();
        $('#recertIntro').show();
        return true;
    }
}

var imageFilePicker = function (field_name, url, type, win) {

    $("#"+field_name).attr('mce-type', type);

    if(type == 'image')
    {
        type =  1;
    }
    else if(type == 'file')
    {
        type =  2;
    }
    else if(type == 'media')
    {
        type =  3;
    }
    else
    {
        type = 0;
    }

    $.fancybox.open({
        href: '/whm/tools/filemanager/dialog.php?field_id='+field_name+'&type='+type+'&relative_url=0&akey='+filemanager_access_key,
        type: 'iframe',
        autoSize : false,
        width    : win.innerWidth-100 + "px",
        height   : win.innerHeight-100 + "px",
    });

    /*tinymce.activeEditor.windowManager.open({
     url: '/filemanager/dialog.php?field_id='+field_name+'&type='+type+'&relative_url=0',
     width: win.innerWidth-100,
     height: win.innerHeight-100,
     buttons: [
     {
     text: 'Close',
     onclick: 'close'
     }],
     });*/
};

var tinyMCESettings = []; //Dùng để overwrite lên cấu hình mặc định khi cần
function tinyMCEInit(settings = []){
    tinymce.init({
        selector: '.editor_texarea',
        height: 500,
        theme: 'modern',
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons paste textcolor responsivefilemanager code fullscreen"
        ],
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        toolbar2: "| responsivefilemanager | fontsizeselect | fontselect | link unlink anchor | image media | forecolor backcolor  | print preview code | fullscreen | codemirror ",
        image_advtab: true ,
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css'
        ],
        fontsize_formats: '8px 10px 12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 40px',
        external_filemanager_path: "/acp/tools/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        filemanager_access_key:filemanager_access_key,
        relative_urls: typeof settings['relative_urls'] !== 'undefined' ? settings['relative_urls'] : false,
        external_plugins: { "filemanager" :  "/acp/assets/js/lib/tinymce/plugins/responsivefilemanager/plugin.min.js"},
        valid_elements: '*[*]',
        // extended_valid_elements: 'section[*],&[*],i[*],div[*],a[*],iframe[src|frameborder=0|scrolling=no|alt|title|width|height|align|name],container[*]',
        forced_root_block : '',
        valid_children : '+a[div|img],+head[style],+body[style]',
        /*codemirror: {
         indentOnInit: true, // Whether or not to indent code on init.
         // path: 'CodeMirror', // Path to CodeMirror distribution
         config: {           // CodeMirror config object
         mode: 'htmlmixed', //application/x-httpd-php,
         theme: 'default',
         lineNumbers: false,
         },
         saveCursorPosition: true,    // Insert caret marker
         jsFiles: [          // Additional JS files to load
         'mode/clike/clike.js',
         'mode/php/php.js'
         ]
         },*/
        cleanup_on_startup: false,
        trim_span_elements: false,
        verify_html: false,
        cleanup: false,
        convert_urls: typeof settings['convert_urls'] !== 'undefined' ? settings['convert_urls'] : true,
        branding: false,
        file_browser_callback: function(field_name, url, type, win) {
            imageFilePicker(field_name, url, type, win);
        },
        setup: function (editor) {
            editor.on('init', function(args) {
                editor = args.target;
                editor.on('NodeChange', function(e) {
                    if (e && e.element.nodeName.toLowerCase() == 'img') {

                        let w = $(e.element).attr('width');
                        let h = $(e.element).attr('height');
                        let url = $(e.element).attr('src');

                        getMetaImg(url, (width, height) => {

                            w = typeof w == "undefined" ? 0 : parseInt(w);
                            h = typeof h == "undefined" ? 0 : parseInt(h);

                            if((w == width || w+1 == width) && h == height)
                            {
                                w = 0;
                                h = 0;
                            }

                            if(w == 0 && h == 0)
                            {
                                tinyMCE.DOM.setAttribs(e.element, {'width': null, 'height': null});
                            }
                            else
                            {
                                tinyMCE.DOM.setAttribs(e.element, {'width': (w==0 ? null : w), 'height': (h==0 ? null : h)});
                            }
                        })
                    }
                });
            })
        }
    });
}


/**
 * START EDITOR
 */

$(document).ready(function(){ // Start autorun
    // console.log(tinyMCESettings);
    tinyMCEInit(tinyMCESettings);
// upload summernote
    $(function(){
        $('[data-toggle="tooltip"]').tooltip();
    })

    $('body').on('click', ".insert-image", function() {

        var image = $(this).data('image');
        $(objSummernote).summernote('editor.insertImage', image);
        $('#modal-image').modal('hide');
    })

    $("body").on("click", ".thumb",function() {
        $('#imagepreview').attr('src', $(this).data('image'));
        $('#imagemodal').modal('show');
    });

    $("body").on("click", ".close-modal", function() {
        $('#imagepreview').attr('src', '');
        $('#imagemodal').modal('hide');
        $('#imagemodaldelete').modal('hide');
    });

    var image_to_delete;
    var image_id;

    $("body").on("click",".delete-image", function() {
        $('#imagemodaldelete').modal('show');
        image_to_delete = $(this).data('image');
        image_id = $(this).data('image_id');
    })

    $('body').on('click', "#delete_image", function() {
        $.ajax({
            type: "POST",
            data:({'image':image_to_delete}),
            url: "/acp/?site=dashboard&subact=delfile",
            success: function(data){
                $("#image_"+image_id).fadeOut()
                $('#imagemodaldelete').modal('hide');
            }
        })

    })

    $('body').on('click', "#button-upload", function() {
        $('#form-upload').remove();

        $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value="" /></form>');

        $('#form-upload input[name="file"]').trigger('click');

        if (typeof timer != 'undefined') {
            clearInterval(timer);
        }

        timer = setInterval(function() {
            if ($('#form-upload input[name="file"]').val() != '') {
                clearInterval(timer);

                $.ajax({
                    url: '/acp/?site=dashboard&subact=savefile',
                    type: 'post',
                    data: new FormData($('#form-upload')[0]),
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#button-upload').html('<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;&nbsp;UPLOADING');
                        $('#button-upload').prop('disabled', true);
                    },
                    complete: function() {
                        $('#button-upload').html('<i class="fa fa-upload"></i>&nbsp;&nbsp;UPLOAD IMAGE');
                        $('#button-upload').prop('disabled', false);
                    },
                    success: function(json) {
                        $(objSummernote).summernote('editor.insertImage', json['content']);
                        $('#modal-image').modal('hide');
                    },

                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        }, 500);
    });

    /********Start editor*********/

    var objSummernote="";
    /*$('.editor_texarea').summernote({
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'image', 'video']],
            ['view', ['fullscreen', 'codeview']]
        ],
        buttons: {
            image: function() {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: '<i class="fa fa-image" />',
                    tooltip: "Image manager",
                    click: function (editor) {
                        // console.log();
                        objSummernote = $(this).parents(".note-editor.note-frame.panel.panel-default").prev();
                        $('#modal-image').remove();
                        $.ajax({
                            url: '/acp/?site=dashboard&subact=file_management',
                            dataType: 'html',
                            success: function(html) {
                                // console.log(html);
                                $('body').append('<div id="modal-image" class="modal">' + html + '</div>');
                                $('#modal-image').modal('show');
                            }
                        });
                    }
                });
                // return false;
                return button.render();
            }
        }
        // callbacks: {
        // onImageUpload: function(files, editor, welEditable) {
        //         sendFileUpload(files[0], editor, welEditable);
        //     }
        // }

    });*/






        
}); // End autorun


/**
 * Get info image
 * @param url
 * @param callback
 */
function getMetaImg(url, callback) {
    var img = new Image();
    img.src = url;
    img.onload = function() { callback(this.width, this.height); }
}

function responsive_filemanager_callback(field_id){
    var url=jQuery('#'+field_id).val();
    let type =  $('#'+field_id).attr('mce-type');

    if(type != 'image') return false;

    let mceFrm = $('#'+field_id).parents(".mce-form:first");
    let mceW = mceFrm.find(".mce-textbox[aria-label='Width']");
    let mceH = mceFrm.find(".mce-textbox[aria-label='Height']");
    getMetaImg(url, (width, height) => {
        mceW.val(width);
        mceH.val(height);
    })
}

/**
 * Send File upload
 */

function sendFileUpload(file, editor, welEditable)
{
    // console.log("da toi day");
    data = new FormData();
    data.append("upload_img", file);
    $.ajax({
        data: data,
        type: "POST",
        url: "/?site=dashboard&subact=upload_image_summernote",
        cache: false,
        contentType: false,
        processData: false,
        success: function(url) {
            editor.insertImage(welEditable, url);
        }
    });
}

function elfinderDialog()
{
    var fm = $('<div/>').dialogelfinder({
        url : '/?site=dashboard&subact=upload_image_summernote', // change with the url of your connector
        lang : 'en',
        width : 840,
        height: 450,
        destroyOnClose : true,
        getFileCallback : function(files, fm) {
            // console.log(files);
            $('.editor').summernote('editor.insertImage', files.url);
        },
        commandsOptions : {
            getfile : {
                oncomplete : 'close',
                folders : false
            }
        }
    }).dialogelfinder('instance');
}



/**
 * ************************** Area for html page *********************
 */

function addnewpage()
{
    $('#page_html').show();
    $('#page_interface').hide();
    $("input[name='pages_id']").val("");
    $(".listInput").val("");
    $(".title_tab").html(cms_lang.title_add_html);

    default_language = "vn";
        $(".box_url_"+default_language).css("display","none");
        $("input[name='page_name["+default_language+"]']").val('');
        // $('#htmlEditorYour').summernote('code', '');
        tinymce.get('htmlEditorYour').setContent("");
        $("input[name='url_["+default_language+"]']").val('');
   

    
}

function htmlEditorSave()
{
    var pages_id = $("input[name='pages_id']").val();
    if(pages_id)
    {
        var urls = site_root_domain+"/?site=pages&act=edit_do&subact=ajax&id="+pages_id;
    }else
    {
        var urls = site_root_domain+"/?site=pages&act=add_do&subact=ajax";
    }

    tinymce.triggerSave();

    var data = $("form#formHtml").serialize();
    
    $.ajax({
        type: "post",
        url: urls,
        data: data,
        })
    .done(function(response)
    {
        var obj = JSON.parse(response);
     
        if(obj.status == "error")
        {
            $.notify({
                icon: 'font-icon font-icon-check-circle',
                title: '<strong>Notification</strong>',
                message: obj.msg,
            },{
                type: 'danger'
            });

            return false;
        }else
        {
            $.notify({
                icon: 'font-icon font-icon-check-circle',
                title: '<strong>Notification</strong>',
                message: obj.msg,
            },{
                type: 'success'
            });

            if(!pages_id)
            {
                $(".list_page").prepend("<li><a p_id='"+obj.data.pages_id+"' onclick=\"open_page('"+obj.data.pages_id+"');\">"+obj.data.pages_name[default_language]+"</a></li>");
            }else
            {
                $(".list_page li").find("a[p_id='"+obj.data.pages_id+"']").html(obj.data.pages_name[default_language]);
            }

             
                $(".box_url_"+default_language).css("display","block");
                $("input[name='url_["+default_language+"]']").val("/p/"+obj.data.pages_shorturl[default_language]);
             
        }
        
    });
}

function open_page(pages_id=0)
{
    if(pages_id)
    {
        url = site_root_domain+"/?site=pages&act=show&subact=ajax&id="+pages_id;
        $('#page_interface').hide();
        $('#page_html').hide();
        $('.page_loading').show();
        $('.page_loading').html("Loading...");
        $(".title_tab").html(cms_lang.title_edit_html);

        $.ajax(url).done(function(response)
        {
            $('#page_html').show();
            $('.page_loading').hide();
            var obj = JSON.parse(response);
            $("input[name='pages_id']").val(obj.pages_id);
            
                $(".box_url_"+default_language).css("display","block");
                $("input[name='page_name["+default_language+"]']").val(obj.pages_name[default_language]);
                $("input[name='url_["+default_language+"]']").val("/p/"+obj.pages_shorturl[default_language]);
                // $('#htmlEditorYour').summernote('code', obj.pages_content[default_language]);
                tinymce.get('htmlEditorYour').setContent(decodeHTMLEntities(obj.pages_content[default_language]));
            
        });
    }
}

// Use decode htmlentities javascript
function decodeHTMLEntities(text) 
{
    var entities = [
        ['amp', '&'],
        ['apos', '\''],
        ['#x27', '\''],
        ['#x2F', '/'],
        ['#39', '\''],
        ['#47', '/'],
        ['lt', '<'],
        ['gt', '>'],
        ['nbsp', ' '],
        ['quot', '"']
    ];

    for (var i = 0, max = entities.length; i < max; ++i) 
        text = text.replace(new RegExp('&'+entities[i][0]+';', 'g'), entities[i][1]);

    return text;
}

function clear_page(page_id=0, obj)
{
    swal({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then(function () {
            // Ajax del image
            waitingDialog.show(cms_lang.waiting_dialog_msg);
            $.ajax({
                type: "post",
                url: site_root_domain+ "/?site=pages&act=delete&subact=ajax",
                data: {id:page_id},
                success: function(responsive)
                {
                    waitingDialog.hide();
                    if(responsive == 1)
                    {
                        swal(
                          'Successful!',
                          'Delete page successful!',
                          'success'
                        ).then(function () {
                        window.location.href = "/acp/?site=interface";
                        })
                    }
                }
            });

            // xoa item
            $(obj).parent("li").remove();
          });
}