let embedMirrorJS = null;
let embedMirrorCSS = null;
let embedSelectorIdRandom = 'embed_' + Date.now() + "_" + Math.round(Math.random()*1000);

let embedInstagramUserId = (typeof instagramUserId != 'undefined' && instagramUserId) ? instagramUserId : 'userId';
let embedInstagramAccessToken = (typeof instagramAccessToken != 'undefined' && instagramAccessToken) ? instagramAccessToken : 'accessToken';

let sampleEmbledData = {
    'instagram_v1': {
        'html' : `
        <section class="clearfix" id="instagram_${embedSelectorIdRandom}">
            <!-- instagram template -->
            <div class="instagram_tpl" style="display:none">
                <div class="col-xs-6 col-sm-6 col-md-3">
                    <div class="instagram-item" username="{model.user.username}" profile_picture="{model.user.profile_picture}" full_name="{model.user.full_name}">
                        <a itemprop="url" title="{caption}" href="{link}" target="_blank">
                            <div class="gallery-box">
                                <div class="image-bg" style="background-image: url('{image}');">
                                    <img itemprop="image" src="{image}" title="{caption}">
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- instagram header -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="sb_instagram instagram_header">
                        <div class="sb_instagram_header">
                            <a class="instagram_header_link" target="_blank" href="https://www.instagram.com">
                                <div class="sbi_header_text">
                                    <h3 class="instagram_header_text">@Instagram</h3>
                                    <p class="instagram_header_info">© Instagram</p>
                                </div>
                                <div class="sbi_header_img">
                                    <div class="sbi_header_img_hover"><i class="fa fa-instagram"></i></div>
                                    <img class="instagram_header_img" width="50" height="50" src="instagram/instagram.png"/>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- instagram content -->
            <div class="clearfix instagram-gbox-wrap">
                <div class="row" id="instagram_${embedSelectorIdRandom}_feed">
                    <!-- important: instagram_${embedSelectorIdRandom}_feed use for render instagram -->
                </div>
            </div>

            <!-- instagram footer -->
            <div class="row">
                <div class="col-xs-12 text-center" style="padding-bottom: 15px;">
                    <input type="button" class="btn btn-success instagram_loadmore" value="Load more"/>
                    <a class="btn btn-primary instagram_link" target="_blank" href="https://www.instagram.com">
                        <i class="fa fa-instagram" aria-hidden="true"></i> Follow on Us Instagram
                    </a>
                </div>
            </div>
        </section>
        `,
        'js' : `
        $(document).ready(function(){
            /* Your API client id from Instagram */
            var ${embedSelectorIdRandom}_userId = '${embedInstagramUserId}';

            /* A valid oAuth token. Can be used in place of a client ID */
            var ${embedSelectorIdRandom}_accessToken = '${embedInstagramAccessToken}';

            /* element contain html instagram */
            var ${embedSelectorIdRandom}_instagramWrap = '#instagram_${embedSelectorIdRandom}';

            /* instagramFeedInit( userId, accessToken, instagramWrap ) */
            instagramFeedInit(${embedSelectorIdRandom}_userId, ${embedSelectorIdRandom}_accessToken, ${embedSelectorIdRandom}_instagramWrap);
        });
        `,
        'css' : `
        /* Some example CSS */ /* @import url("something.css"); */
        .instagram-gbox-wrap {padding: 0 10px 0;}
        .instagram-gbox-wrap .gallery-box {margin: 0 -10px 10px;}
        .instagram-gbox-wrap .gallery-box .image-bg {-webkit-transition: all .5s;-moz-transition: all .5s;-o-transition: all .5s;transition: all .5s;position: relative;z-index: 1;}
        .instagram-gbox-wrap .gallery-box:hover .image-bg {-webkit-transform: scale(1.1);-ms-transform: scale(1.1);-o-transform: scale(1.1);transform: scale(1.1);z-index: 2;}
        .instagram-gbox-wrap .gallery-box .gallery-title {position: absolute;bottom:0;left:0;width: 100%;color: #FFF;font-weight: 500;background: rgba(3, 3, 3, 0.3);filter: alpha(opacity=30);max-height: 60px;padding: 5px;
            margin: 0;overflow: hidden;
        }
        .instagram-gbox-wrap .gl-wrap .gallery-box .image-bg {border: 3px solid #f07398;}
        `
    },

    'magnific_popup_first_closed_then_open_second': {
        'html' : `
        <!-- popup 1 -->
        <div id="${embedSelectorIdRandom}_1" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
                <p style="color: #000;">popup 1</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>

        <!-- popup 2 -->
        <div id="${embedSelectorIdRandom}_2" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
            <p style="color: #000;">popup 2</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>
        `,
        'js' : `
        $(document).ready(function(){
            /* function call popup */
            function openMagic_${embedSelectorIdRandom} ( element_id, element_id2 ) {
                if( $(element_id).length <= 0 ) { return false; }
                /* closed first */ $.magnificPopup.close();
                /* later open second */ $.magnificPopup.open({items: {src: element_id,type: 'inline'}, callbacks: { afterClose: function() { openMagic_${embedSelectorIdRandom}(element_id2); } } });
                /* debug */ console.log(element_id);
            }

            /* call popup */
            openMagic_${embedSelectorIdRandom}('#${embedSelectorIdRandom}_1', '#${embedSelectorIdRandom}_2');
        });
        `,
        'css' : `
        /* Some example CSS */ /* @import url("something.css"); */
        #${embedSelectorIdRandom}_1 img { } #${embedSelectorIdRandom}_2 img { } 
        `
    },

    'magnific_popup_first_closed_then_open_second_5_times': {
        'html' : `
        <!-- popup 1 -->
        <div id="popup_1_${embedSelectorIdRandom}" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
                <p style="color: #000;">popup 1</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>

        <!-- popup 2 -->
        <div id="popup_2_${embedSelectorIdRandom}" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
            <p style="color: #000;">popup 2</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>

        <!-- popup 3 -->
        <div id="popup_3_${embedSelectorIdRandom}" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
            <p style="color: #000;">popup 3</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>

        <!-- popup 4 -->
        <div id="popup_4_${embedSelectorIdRandom}" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
            <p style="color: #000;">popup 4</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>

        <!-- popup 5 -->
        <div id="popup_5_${embedSelectorIdRandom}" class="white-popup popup-style mfp-hide">
            <div class="clearfix">
            <p style="color: #000;">popup 5</p><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
        </div>
        `,
        'js' : `
        $(document).ready(function(){
            /* numbers of popup need show */
            var popup_cnt_${embedSelectorIdRandom} = 5;

            /* data popup and max loop = numbers of popup need show for error */
            var popup_arr_${embedSelectorIdRandom} = [];
            for ( var i=0; i < popup_cnt_${embedSelectorIdRandom}; i++ ) { popup_arr_${embedSelectorIdRandom}[i] = '#popup_' + (i + 1) + '_${embedSelectorIdRandom}'; }
            
            /* function popup */
            function openMagic_${embedSelectorIdRandom} () 
            {
                /* check loop and exist popup */
                if( popup_cnt_${embedSelectorIdRandom} < 0 || popup_arr_${embedSelectorIdRandom}.length <= 0 ) { return false; }
                popup_cnt_${embedSelectorIdRandom} --;

                /* get id popup */
                var arr = popup_arr_${embedSelectorIdRandom}; 
                var id = '';
                for( i in arr ) { if( $(arr[i]).length > 0 ) { id = arr[i]; arr.splice(i, 1); break; } else { console.log(arr[i] +  ' missed'); } }
                /* open popup */
                if( $(id).length > 0 ) { /* closed first */  $.magnificPopup.close(); /* later open second */ $.magnificPopup.open({ items: { src: id,  type: 'inline' },  callbacks: {  afterClose: function() {  openMagic_${embedSelectorIdRandom}();  }  }  }); /* debug */ console.log(id); } 
            }
            
            /* call popup */
            openMagic_${embedSelectorIdRandom} ();
        });
        `,
        'css' : `
        /* Some example CSS */ /* @import url("something.css"); */
        #popup_1_${embedSelectorIdRandom} img { } #popup_2_${embedSelectorIdRandom} img { } #popup_3_${embedSelectorIdRandom} img { } #popup_4_${embedSelectorIdRandom} img { } #popup_5_${embedSelectorIdRandom} img { }
        `
    },

    'magnific_popup': {
        'html' : `<div id="${embedSelectorIdRandom}" class="white-popup popup-style mfp-hide">
<div><img src="https://www.google.com.vn/logos/doodles/2017/mid-autumn-festival-2017-vietnam-6247281559863296-l.png" alt="" /></div>
</div>
        `,
        'js' : `$.magnificPopup.open({
            items: {
                src: '#${embedSelectorIdRandom}',
                type: 'inline'
            }
        });
        `,
        'css' : `/* Some example CSS */
            /* @import url("something.css"); */
            #${embedSelectorIdRandom} img {
            }
        `
    },

    'subcribe_form' : {
        'html' : `<form enctype="multipart/form-data" class="wpcf7-form" method="post" name="send_contact" id="subcribe_frm_${embedSelectorIdRandom}" action="/contact/subscribe">
    <div class="form-group">
        <label for="contactemail_${embedSelectorIdRandom}">Your Email (required)</label>
        <div class="form-control-wrapper">
            <input class="form-control" type="text" data-validation="[EMAIL]" data-validation-message="Invalid email" autocomplete="off" placeholder="Your Email" name="contactemail" id="contactemail_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="contactname_${embedSelectorIdRandom}">Your name (required)</label>
        <div class="form-control-wrapper">
            <input class="form-control" type="text" data-validation="[NOTEMPTY]" data-validation-message="Please enter your name!" autocomplete="off" autocomplete="off" placeholder="Your name" name="contactname" id="contactname_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="firstname_${embedSelectorIdRandom}">First name</label>
        <div class="form-control-wrapper">
            <input class="form-control" type="text" autocomplete="off" placeholder="First name" name="firstname" id="firstname_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="lastname_${embedSelectorIdRandom}">Last name</label>
        <div class="form-control-wrapper">
            <input class="form-control" type="text" autocomplete="off" placeholder="Last name" name="lastname" id="lastname_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="location_1_${embedSelectorIdRandom}">Location 1:</label>
        <div class="form-control-wrapper">
            <input type="hidden" class="form-control" type="text" autocomplete="off" name="add[location_1_label]" value="Location 1">
            <input class="form-control" type="text" autocomplete="off" name="add[location_1_value]" id="location_1_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="location_2_${embedSelectorIdRandom}">Location 2:</label>
        <div class="form-control-wrapper">
            <input type="hidden" class="form-control" type="text" autocomplete="off" name="add[location_2_label]" value="Location 2">
            <input class="form-control" type="text" autocomplete="off" name="add[location_2_value]" id="location_2_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="contactdate_${embedSelectorIdRandom}">Date</label>
        <div class="form-control-wrapper">
            <input readonly class="form-control" type="text" autocomplete="off" name="contactdate" id="contactdate_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="contactbirthday_${embedSelectorIdRandom}">Birthday</label>
        <div class="form-control-wrapper">
            <input readonly class="form-control" type="text" autocomplete="off" name="contactbirthday" id="contactbirthday_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="contactphone_${embedSelectorIdRandom}">Phone</label>
        <div class="form-control-wrapper">
            <input class="form-control inputPhone" type="text" autocomplete="off" placeholder="Phone number" name="contactphone"  id="contactphone_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="contactservice_${embedSelectorIdRandom}">Service</label>
        <div class="form-control-wrapper">
            <select class="form-control" name="contactservice" id="contactservice_${embedSelectorIdRandom}">
                <option value="Manicure">Manicure</option>
                <option value="Pedicure">Pedicure</option>
                <option value="Manicure &amp; Pedicure">Manicure &amp; Pedicure</option>
                <option value="Gel Manicure">Gel Manicure</option>
                <option value="Acrylic Fill">Acrylic Fill</option>
                <option value="Acrylic Fullset">Acrylic Fullset</option>
                <option value="Gel Fill">Gel Fill</option>
                <option value="Gel Fullset">Gel Fullset</option>
                <option value="Eyebrow Waxing">Eyebrow Waxing</option>
                <option value="Body Waxing">Body Waxing</option>
                <option value="Individual eyelash extension">Individual eyelash extension</option>
                <option value="Volume(cluster) eyelash extension">Volume(cluster) eyelash extension</option>
                <option value="Brow tinting">Brow tinting</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="contactsubject_${embedSelectorIdRandom}">Subject</label>
        <div class="form-control-wrapper">
            <input class="form-control" type="text" autocomplete="off" placeholder="Your Subject" name="contactsubject" id="contactsubject_${embedSelectorIdRandom}">
        </div>
    </div>
    <div class="form-group">
        <label for="contactcontent_${embedSelectorIdRandom}">Your Message</label>
        <div class="form-control-wrapper">
            <textarea class="form-control" rows="7" autocomplete="off" placeholder="Your Message" name="contactcontent" id="contactcontent_${embedSelectorIdRandom}"></textarea>
        </div>
    </div>
    <div class="form-group text-right">
        <input class="btn btn-primary btn_contact_${embedSelectorIdRandom} " type="submit" value="SUBSCRIBE">
    </div>
</form>`,
        'js' : `
$(document).ready(function(){
    let contactDateFormat = "YYYY/MM/DD hh:mm a";
    let contactBirthday = "YYYY/MM/DD";
    $('#contactdate_${embedSelectorIdRandom}').mask("0000/00/00 00:00", {placeholder: contactDateFormat});
    $('#contactdate_${embedSelectorIdRandom}').datetimepicker({
        format: contactDateFormat,
        sideBySide: true,
        ignoreReadonly: true,
    });
    $('#contactbirthday_${embedSelectorIdRandom}').mask("0000/00/00", {placeholder: contactBirthday});
    $('#contactbirthday_${embedSelectorIdRandom}').datetimepicker({
        format: contactBirthday,
        ignoreReadonly: true,
    });
    $("#subcribe_frm_${embedSelectorIdRandom}").validate({
        submit: {
            settings: {
                button: ".btn_contact_${embedSelectorIdRandom}",
                inputContainer: '.form-group',
                errorListClass: 'form-tooltip-error',
            }
        }
    });
})
        `,
        'css' : '',
    },
};

function chooseSampleEmbled(sample_code){
    let data = eval('sampleEmbledData.'+sample_code);
    if(data)
    {
        embedMirrorJS.setValue(data.js);
        embedMirrorCSS.setValue(data.css);
        tinymce.activeEditor.setContent(data.html);
    }
}

function codeMirrorTextAreaCustom(eleID, mode, theme = 'default', value=null){
    return CodeMirror.fromTextArea(document.getElementById(eleID),{
        lineNumbers: true,
        value: value,
        mode: {name: mode, globalVars: true},
        theme: theme,
        matchBrackets: true,
        autoCloseTags: true,
        lineWrapping: true,
        styleActiveLine: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "F11": function(cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
            },
            "Esc": function(cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
            }
        }
    });
}

function codeMirrorCustom(eleID, mode, theme = 'default', value=null){
    // console.log(eleID);
    return CodeMirror(document.getElementById(eleID),{
        lineNumbers: true,
        value: value,
        mode: {name: mode, globalVars: true},
        theme: theme,
        matchBrackets: true,
        autoCloseTags: true,
        lineWrapping: true,
        styleActiveLine: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "F11": function(cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
            },
            "Esc": function(cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
            }
        }
    });
}