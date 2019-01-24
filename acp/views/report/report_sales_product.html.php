<div class="container-fluid2">
    <section class="add_table main_form have_tab">
        <figure class="box-typical box-typical-padding border">
            <div class="row">
                <div class="form-group col-xl-12">
                    <label class="form-label"><?= $CMS->lang['title_quick_view']; ?></label>
                    <div class="form-control-wrapper">
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="yesterday"><?= $CMS->lang['title_yesterday']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="today"><?= $CMS->lang['title_today']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="last_week"><?= $CMS->lang['title_last_week']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="this_week"><?= $CMS->lang['title_this_week']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="last_month"><?= $CMS->lang['title_last_month']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="this_month"><?= $CMS->lang['title_this_month']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="last_year"><?= $CMS->lang['title_last_year']; ?></button>
                        <button type="button" class="btn btn-inline btn-success change_time"
                                btn_type="this_year"><?= $CMS->lang['title_this_year']; ?></button>
                    </div>

                </div>
                <div class="form-group col-xl-12 box_date_change">
                    
                    <div class="row">
                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_time_from']; ?></label>
                            <div class="input-group">
                                <input type="text" name="time_from"
                                       class="form-control datetimepicker-1 change_pick_time" placeholder="Time from"
                                       value="">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_time_to']; ?></label>
                            <div class="input-group">
                                <input type="text" name="time_to" class="form-control datetimepicker-1 change_pick_time"
                                       placeholder="Time to" value="">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_product_group']; ?></label>
                            <div class="form-control-wrapper">
                            <select type="text" name="p_group" id="p_group" class="form-control select2" value="">
                                <option value=''><?= $CMS->lang['title_all']; ?></option>
                                <?= $tpl->ListProductGroup ?>
                            </select>
                            </div>
                        </div>

                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_store']; ?></label>
                            <div class="form-control-wrapper">
                            <select type="text" name="store_id" id="store_id" class="form-control select2" value="">
                                <option value=''><?= $CMS->lang['title_all']; ?></option>
                                <?= $tpl->ListStore ?>
                            </select>
                            </div>
                        </div>
                        <? } ?>

                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_user']; ?></label>
                            <div class="form-control-wrapper">
                            <select type="text" name="user_id" id="user_id" class="form-control select2" value="">
                                <option value=''><?= $CMS->lang['title_all']; ?></option>
                                <?= $tpl->listUser ?>
                            </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group col-xl-12 box_date_change">
                    <div class="row">
                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_product_name']; ?></label>
                            <div class="input-group">
                                <input type="text" name="product_name" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_price_from']; ?></label>
                            <div class="input-group">
                                <input type="text" name="price_from" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_price_to']; ?></label>
                            <div class="input-group">
                                <input type="text" name="price_to" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_supplier']; ?></label>
                            <div class="form-control-wrapper">
                            <select type="text" name="supplier" class="form-control select2">
                                <option value=''><?= $CMS->lang['title_all']; ?></option>
                                <?= $tpl->listSupp ?>
                            </select>
                            </div>
                        </div>
                        
                        <div class="col-xl-2">
                            <label class="form-label"><?= $CMS->lang['title_type_view']; ?></label>
                            <div class="form-control-wrapper">
                                <select class="form-control select2" name="type_view">
                                    <option value="view_day"><?= $CMS->lang['title_date']; ?></option>
                                    <option value="view_month"><?= $CMS->lang['title_month']; ?></option>
                                    <option value="view_year"><?= $CMS->lang['title_year']; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <label class="form-label" style="height: 18px;"></label>
                            <div class="input-group">
                                <button type="button"
                                        class="btn btn-primary btn_view_report"><?= $CMS->lang['title_view']; ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="box_chart">
                        <p class="title_chart"><span><?= $CMS->lang['title_time']; ?></span>: <span
                                    class="time_title"></span></p>
                        <div id="chart_show" class="chart"></div>
                        <div class="chart_name"></div>
                    </div>
                </div>
            </div>
        </figure>

    </section>
    <script>
        google.charts.load('visualization', '1.0', {'packages': ['corechart']});
    </script>
</div>