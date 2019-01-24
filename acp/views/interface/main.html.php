<script language="javascript" type="text/javascript">
    ezyEditorInit();
</script>

<header class="section-header">
    <div class="tbl">
        <h3><?=$CMS->lang['int_head'];?></h3>
        <ol class="breadcrumb breadcrumb-simple">
            <li><a href="?"><?=$CMS->lang['int_home'];?></a></li>
            <li class="active"><?=$CMS->lang['int_editor'];?></li>
        </ol>
    </div>
</header>

<section class="box-typical box-typical-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row">
                <div class="col-xl-3 block_left_file">
                        <? foreach ($tpl->dateTemplateTree as $folder => $files) { ?>
                            <header class="files-manager-side-title"><?=$folder;?></header>
                            <ul class="files-manager-side-list">
                                <? foreach ($files as $value => $text) { ?>
                                    <li><a onclick="ezyEditorOpen('<?=$value?>');"><?=$text;?></a></li>
                                <? } ?>
                            </ul>
                        <? } ?>

                        <header class="files-manager-side-title"><?=$CMS->lang['title_list_html'];?></header>
                        <ul class="files-manager-side-list list_page">
                            <? foreach ($tpl->listPage  as $key => $data) { ?>

                                <li><a class="pull-left a_show" p_id='<?=$data['pages_id'];?>' onclick="open_page('<?=$data['pages_id'];?>', this);"><?=$data['pages_name'][$CMS->vars['default_language']];?></a>
                                    <span class="pull-right a_del" p_id='<?=$data['pages_id'];?>' onclick="clear_page(<?=$data['pages_id'];?>, this)"><i class="fa fa-trash"></i></span>

                                </li>
                            <? } ?>
                            <li><a onclick="addnewpage();"><?=$CMS->lang['title_add_page'];?></a></li>
                        </ul>

                    <!--.files-manager-side-->
                    <!--
                    <select class="select2" id="ezyEditorSelect" onchange="ezyEditorOpen();">
                    <? foreach ($tpl->dateTemplateTree as $folder => $files) { ?>
                        <optgroup label="<?=$folder;?>">
                        <label class="form-label semibold"><?=$folder;?></label>
                        <? foreach ($files as $value => $text) { ?>
                            <option value="<?=$value?>"><?=$text;?></option>
                        <? } ?>
                        </optgroup>
                    <? } ?>
                    </select>-->
                </div>
                <div id="page_interface" class="col-xl-9 col-lg-9 col-sm-12 col-xs-12">
                    <div class="summernote-theme-1">
                        <div id="ezyEditorIntro"><?=$CMS->lang['int_intro'];?></div>
                        <div id="ezyEditor" style="display: none;">

                            <section class="tabs-section">
                                <div class="tabs-section-nav tabs-section-nav-icons">
                                    <div class="tbl">
                                        <ul class="nav" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
                                                <span class="nav-link-in">
                                                    <i class="font-icon  font-icon-pencil"></i>
                                                    <?=$CMS->lang['int_customize'];?>
                                                </span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab">
                                                <span class="nav-link-in">
                                                    <i class="font-icon  font-icon-server"></i>
                                                    <?=$CMS->lang['int_default'];?>
                                                </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!--.tabs-section-nav-->

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1">
                                        <?=$CMS->global->languageTab('change-language-tab', $CMS->vars['default_language']);?>

                                        <?if($CMS->vars['translations']){?>
                                            <?foreach ($CMS->vars['translations'] as $langCode => $langName){?>
                                                <div class=" change-language-tab" lang="<?=$langCode?>">
                                                    <textarea class="editor_texarea" id="ezyEditorYour<?=$langCode?>" rows="10"><?=$CMS->lang['int_revert'];?></textarea>
                                                </div>

                                            <?}?>
                                        <?} else {?>
                                            <textarea class="editor_texarea" id="ezyEditorYour" rows="10"><?=$CMS->lang['int_revert'];?></textarea>
                                        <?}?>

                                        <div class="box_control_save" style="padding:10px;clear: both;overflow: hidden;padding-bottom: 0;">
                                            <input class="btn btn-rounded pull-left" type="submit" name="submit" value="<?=$CMS->lang['int_savebtn'];?>" id="submit" onclick="ezyEditorSave()">
                                            <span id="recertIntro" style="line-height: 35px;margin-left: 20px;"><?=$CMS->lang['int_save'];?></span>
                                            <button class="btn btn-rounded swal-btn-revert pull-right"><?=$CMS->lang['int_revert'];?></button>
                                        </div>
                                    </div><!--.tab-pane-->
                                    <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2">
                                        <textarea class="editor_texarea" id="ezyEditorOur" rows="10"><?=$CMS->lang['int_ourtheme'];?></textarea>
                                    </div><!--.tab-pane-->
                                </div><!--.tab-content-->
                            </section><!--.tabs-section-->

                        </div>
                    </div>

                </div>

                <div class="col-xl-9 col-lg-9 col-sm-12 col-xs-12">
                    <div class="page_loading"></div>
                    <div id="page_html" style="display: none;">
                        <section class="tabs-section">
                            <div class="tabs-section-nav tabs-section-nav-icons">
                                <div class="tbl">
                                    <ul class="nav" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab">
                                                <span class="nav-link-in title_tab">
                                                    <?=$CMS->lang['title_add_html'];?>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div><!--.tabs-section-nav-->

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="tabs-1-tab-1" style="clear: both; overflow: hidden;">
                                <form name="formHtml" id="formHtml" method="post">
                                    <?=$CMS->global->languageTab('change-language-tab', $CMS->vars['default_language']);?>

                                    <?if($CMS->vars['translations']){?>
                                        <?foreach ($CMS->vars['translations'] as $langCode => $langName){?>
                                            <div class=" change-language-tab" lang="<?=$langCode?>">
                                                <div class="col-lg-6 col-md-6 col-xs-12 col-ms12">
                                                    <div class="form-group">
                                                        <label class="form-label"><?=$CMS->lang['title_page'];?></label>
                                                        <input type="text" name="page_name[<?=$langCode?>]" class="form-control listInput" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-xs-12 col-ms12 box_url_<?=$langCode?>" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label">URL:</label>
                                                        <input type="text" name="url_[<?=$langCode;?>]" class="form-control" readonly="readonly" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-xs-12 col-ms12">
                                                    <div class="form-group">
                                                        <label class="form-label"><?=$CMS->lang['title_content'];?></label>
                                                        <textarea class="editor_texarea listTextarea" name="htmlEditorYour[<?=$langCode?>]" id="htmlEditorYour<?=$langCode?>" rows="10"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        <?}?>
                                    <?} else {?>
                                        <div class="col-lg-6 col-md-6 col-xs-12 col-ms12">
                                            <div class="form-group">
                                                <label class="form-label"><?=$CMS->lang['title_page'];?></label>
                                                <input type="text" name="page_name[<?=$CMS->vars['default_language'];?>]" class="form-control listInput" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-xs-12 col-ms12 box_url_<?=$CMS->vars['default_language'];?>" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label">URL: </label>
                                                <input type="text" name="url_[<?=$CMS->vars['default_language'];?>]" class="form-control" readonly="readonly" />
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-xs-12 col-ms12">
                                            <div class="form-group">
                                                <label class="form-label"><?=$CMS->lang['title_content'];?></label>
                                                <textarea class="editor_texarea listTextarea" name="htmlEditorYour[<?=$CMS->vars['default_language'];?>]" id="htmlEditorYour" rows="10"></textarea>
                                            </div>
                                        </div>
                                    <?}?>
                                    
                                    <input type="hidden" name="pages_id" value="" />
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-ms12">
                                        <div class="checkbox">
                                            <input type="checkbox" id="use_layouts" name="use_layouts" value="1" checked="checked">
                                            <label for="use_layouts">Use layouts</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-ms12">
                                        <input class="btn btn-rounded" type="button" name="submit" value="<?=$CMS->lang['int_savebtn'];?>" id="submit" onclick="htmlEditorSave()">
                                        <span id="recertIntro"><?=$CMS->lang['int_save'];?></span>
                                    </div>
                                    
                                </form>
                                </div><!--.tab-pane-->
                                
                                </div><!--.tab-pane-->
                            </div><!--.tab-content-->
                        </section><!--.tabs-section-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

