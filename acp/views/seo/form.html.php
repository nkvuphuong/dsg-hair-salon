<form enctype="multipart/form-data" method="post" id="form_<?=$CMS->input['site']?>" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=$tpl->act?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="return check_form(this.id);">

    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=$tpl->header_title?></h3>
            <a href="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>" title=""><span class="font-icon font-icon-del"></span></a>
        </figure>

        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#tab-url" role="tab" data-toggle="tab" aria-expanded="true">
                                    Copy from URL
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-src" role="tab" data-toggle="tab" aria-expanded="true">
                                    Copy from source HTML
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="tab-url">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="URL" id="crawling_url">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="crawlingSEO($('#crawling_url').val())">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade in" id="tab-src">
                            <div class="form-group">
                                <textarea rows="10" class="form-control" placeholder="Source HTML" id="crawling_src"></textarea>
                                <button class="btn btn-default" type="button" onclick="crawlingSEO($('#crawling_src').val(),'source')">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-inline">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#tab-meta" role="tab" data-toggle="tab" aria-expanded="true">
                                    Meta
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-og" role="tab" data-toggle="tab" aria-expanded="true">
                                    OG
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-dc" role="tab" data-toggle="tab" aria-expanded="true">
                                    Dublin core
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tab-others" role="tab" data-toggle="tab" aria-expanded="true">
                                    Others
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="form-group">
                            <div class="fl-flex-label">
                                <input class="form-control" type="text" name="seo_url" value="<?=$tpl->data['seo_url']?>" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_url']?>" placeholder="<?=$CMS->lang['seo_url']?>">
                            </div>
                        </div>

                        <!-- Tab meta -->
                        <div role="tabpanel" class="tab-pane fade active in" id="tab-meta">
                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <textarea class="form-control content_textarea" name="seo_title" placeholder="<?=$CMS->lang['seo_title']?>" crawling="tag.title"><?=$tpl->data['seo_title']?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <textarea class="form-control content_textarea" name="seo_keywords" placeholder="<?=$CMS->lang['seo_keywords']?>" crawling="name.keywords"><?=$tpl->data['seo_keywords']?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <textarea class="form-control content_textarea" name="seo_description" placeholder="<?=$CMS->lang['seo_description']?>" crawling="name.description"><?=$tpl->data['seo_description']?></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- End - Tab meta -->

                        <!-- Tab og -->
                        <div role="tabpanel" class="tab-pane fade in" id="tab-og">
                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <textarea class="form-control content_textarea" name="seo_og_title" placeholder="<?=$CMS->lang['seo_og_title']?>" crawling="property.og:title"><?=$tpl->data['seo_og_title']?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="fl-flex-label">
                                    <textarea class="form-control content_textarea" name="seo_og_description" placeholder="<?=$CMS->lang['seo_og_description']?>" crawling="property.og:description"><?=$tpl->data['seo_og_description']?></textarea>
                                </div>
                            </div>

                            <fieldset class="form-group">
                                <label class="form-label" for="seo_og_image"><?=$CMS->lang['seo_og_image']?></label>
                                <input type="file" name="seo_og_image" class="form-control" id="seo_og_image">
                                <?if($tpl->data['seo_og_image']) {?>
                                    <img src="<?=$tpl->data['seo_og_image']?>" style="max-width: 300px">
                                <?}?>
                            </fieldset>

                        </div>
                        <!-- End - Tab og -->

                        <!-- Tab dc -->
                        <div role="tabpanel" class="tab-pane fade in" id="tab-dc">
                            <div role="tabpanel" class="tab-pane fade active in" id="tab-meta">
                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <textarea class="form-control content_textarea" name="seo_dc_title" placeholder="<?=$CMS->lang['seo_dc_title']?>" crawling="name.DC.title"><?=$tpl->data['seo_dc_title']?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <textarea class="form-control content_textarea" name="seo_dc_subject" placeholder="<?=$CMS->lang['seo_dc_subject']?>" crawling="name.DC.subject"><?=$tpl->data['seo_dc_subject']?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <textarea class="form-control content_textarea" name="seo_dc_description" placeholder="<?=$CMS->lang['seo_dc_description']?>" crawling="name.DC.description"><?=$tpl->data['seo_dc_description']?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End - Tab dc -->

                        <!-- Tab others -->
                        <div role="tabpanel" class="tab-pane fade in" id="tab-others">
                            <div role="tabpanel" class="tab-pane fade active in" id="tab-meta">
                                <div class="form-group">
                                    <div class="fl-flex-label">
                                        <textarea class="form-control content_textarea" name="seo_h1_content" placeholder="<?=$CMS->lang['seo_h1_content']?>"><?=$tpl->data['seo_h1_content']?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End - Tab dc -->
                    </div>
                </section>
            </div>
        </div>


    </section>

    <section class="add_cart_footer">
            <?=$CMS->global->footer_back(['list' => "{$CMS->vars['root_domain']}/?site={$CMS->input['site']}"])?>
            <?= $CMS->input['act'] == 'add' || $CMS->input['act'] == 'add_do' ? $CMS->global->footer_save("{$CMS->input['site']}") : $CMS->global->footer_edit()?>
    </section>

</form>
<script language="javascript">
    $(document).ready(function () {
        validate_form_custom("#form_<?=$CMS->input['site']?>", "a.act_submit_save, a.act_submit_save, a.act_submit_save, [type='submit']");
    });
</script>