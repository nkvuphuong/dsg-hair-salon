<!-- Popup preview -->
<div id="box_preview" class="modal fade" style="padding-top: 30px;">
    <div class="modal-dialog modal-lg revert" role="document">
        <form method="post" id="sendEmail">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="float:left" class="modal-title">Preview</h5>
                    <button style="float:right" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="clearfix loading_content" id=""><img src="/acp/images/fb-loading.gif"> Loading...</div>
                    <div class="clearfix main_content">
                        <div class="tabs-section-nav">
                            <div class="tbl">
                                <ul class="nav" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active send_email" group="tab_box_review" href="#tab_box_review" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">Preview & print</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link send_email" group="tab_send_email" href="#tab_send_email" role="tab" data-toggle="tab">
                                            <span class="nav-link-in">Send email</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!--.tabs-section-nav-->
                        <div class="tab-content box clearfix">
                            <div id="tab_box_review" class="tab-pane fade active in">
                                <div class="pdfContent" data-dojo-attach-point="_pdfContent" style="width:100%;" height="100%">
                                    <iframe src="" width="100%" height="400px" frameborder="0"></iframe>
                                </div>
                            </div>
                            <div id="tab_send_email" class="tab-pane fade">
                                <div class="rows" style="margin-top: 15px">
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">Email from (*):</label>
                                            <div class="typeahead-field">
                                                <span class="typeahead-query">
                                                    <input data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['trx_cus_email_err'];?>" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="<?=$CMS->lang['invalid_email'];?>" type="text" class="form-control" name="email_from">
                                                </span>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">Email to (*):</label>
                                            <div class="typeahead-field">
                                                <span class="typeahead-query">
                                                    <input data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['trx_cus_email_err'];?>" type="text" class="form-control" name="email_to">
                                                </span>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">CC:</label>
                                            <div class="typeahead-field">
                                                <span class="typeahead-query">
                                                    <input type="text" class="form-control" name="email_cc">
                                                </span>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">BCC:</label>
                                            <div class="typeahead-field">
                                                <span class="typeahead-query">
                                                    <input type="text" class="form-control" name="email_bcc">
                                                </span>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">Title (*):</label>
                                            <div class="typeahead-field">
                                                <span class="typeahead-query">
                                                    <input data-validation="[NOTEMPTY]" type="text" class="form-control" name="email_title">
                                                </span>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <label class="form-label">Content (*):</label>
                                            <div class="typeahead-field">
                                                <span class="typeahead-query">
                                                    <textarea id="send_file_email_content" name="email_content" class="editor_texarea"></textarea>
                                                </span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input name="backTo" value="" type="hidden">
                    <input id="url_preview" name="url_preview" value="" type="hidden">
                    <input id="url_send_email" name="url_send_email" value="" type="hidden">

                    <div id="sendEmailBtn" style="cursor: pointer; display:none;" class="btn btn-primary tab_send_email group_tab btn_send_email"> Send email <img style="display:none; height: 60%" id="sendEmailLoading" src="/acp/images/fb-loading.gif"></div>
                    <button type="button" class="btn btn-primary tab_box_review group_tab btn_do_print" onclick="doPrint()">Print</button>
                    <button type="button" class="btn btn-warning" onclick="backToComposePrint()">Edit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
        <script>
            $(document).ready(function(){
                validate_form_custom2("#sendEmail", function(){return sendEmailPrint()},"#sendEmailBtn");
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    if($(e.target).hasClass("send_email"))
                    {
                        $(".group_tab").hide();
                        let group = $(e.target).attr("group");
                        $(".group_tab."+group).show();
                    }
                });
            });
        </script>
    </div>
</div>

<!-- Popup compose -->
<div id="box_compose" class="modal fade" style="padding-top: 30px;">
    <div class="modal-dialog modal-lg revert" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="float:left" class="modal-title"><?=$CMS->lang['compose'];?></h5>
                <button style="float:right" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="clearfix loading_content" id=""><img src="/acp/images/fb-loading.gif"> Loading...</div>
                <div class="clearfix main_content">
                    <textarea class="editor_texarea" id="content_compose"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <input id="url_compose" name="url_compose" value="" type="hidden">

                <button type="button" class="btn btn-primary btn_preview_print" onclick="previewPrint()">
                    <span><?=$CMS->lang['create_pdf'];?> & <?=$CMS->lang['preview'];?></span>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$CMS->lang['close'];?></button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
.modal-lg.revert .modal-header {
    position: relative;
}
.modal-lg.revert .modal-header .close{
    position: absolute;
    right: 15px;
    left: auto;
}
@media (min-width:768px) {
    .modal-lg.revert, .modal.fade .modal-dialog.modal-lg.revert {
        width: 900px;
        max-width: 100%;
        margin-top: 40px;
    }
}
</style>