<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['abandoned_title'];?></h3>
        <figure class="pull-right right">
            <div class="search" style="margin-right: 15px;">
                <form method="get" id="frm_quickserch_product" action="http://dev.local/acp/" style="display:inline-block"><input name="token" value="a39de364209517df3e8830278f149612" type="hidden">
                    <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
                    <div class="clearfix box_result_find_wrap qs_abandoned_wrap">
                        <input class="fa-input" value="&#xf002;" type="submit">
                        <input class="form-control qs_abandoned qs_abandoned_loading relative" type="text" name="keywords" value="<?=$tpl->keywords;?>" style="width: 100%;" placeholder="<?=$CMS->lang['gsearch_quick'];?>">
                        <div class="box_result_find qs_abandoned_suggestion" style="display:none"></div>
                    </div>
                    <script>
                        $(document).ready(function(){
                            var qsAbandonedTimer = 0; //timer identifier
                            autocompleteNew('.qs_abandoned', qsAbandonedTimer, '<?=$CMS->vars['root_domain'];?>/?site=abandoned&subact=quicksearch');
                            setEventMouseUpOutSize('.qs_abandoned_wrap', '.qs_abandoned_suggestion');
                        });
                    </script>
                </form>
            </div>
            <a class="btn btn-warning" href="<?="{$CMS->vars['root_domain']}/?site={$CMS->input['site']}&subact=clear_cache"?>"><?=$CMS->lang['clear_cache']?></a>
        </figure>
    </figure>

    <form method="post" name="form_abandoned" id="form_abandoned" action="">
        <input type="hidden" name="site" value="<?=$CMS->input['site']?>">
        <input type="hidden" name="act" value="">

        <section class="add_table">
            <div class="data_table">
                <div class="table table-responsive table_cus">
                    <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                        <thead>
                            <tr>
                                <th>
                                    <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_abandoned');" id="checkall">
                                        <input type="checkbox" name="all" onmoudiscountver="on_mouse=0;" onmoudiscountut="on_mouse=1;" id="id_all">
                                        <label for="id_all"></label>
                                    </div>
                                </th>
                                <th>ID</th>
                                <th><?=$CMS->lang['abandoned_email'];?></th>
                                <th><?=$CMS->lang['abandoned_name'];?></th>
                                <th><?=$CMS->lang['abandoned_ip'];?></th>
                                <th><?=$CMS->lang['abandoned_total'];?></th>
                                <th><?=$CMS->lang['abandoned_time'];?></th>
                                <th><?=$CMS->lang['abandoned_sent_email'];?></th>
                                <th><?=$CMS->lang['abandoned_status'];?></th>
                                <th><?=$CMS->lang['abandoned_count_sent'];?></th>
                                <th width="50"></th>
                            </tr>
                            <tr id="search_abandoned">
                                <th></th>
                                <th></th>
                                <th>
                                    <input class="form-control" type="text" name="email" value="<?=$tpl->email;?>" style="width: 100%;" placeholder="">
                                </th>
                                <th>
                                    <input class="form-control" type="text" name="name" value="<?=$tpl->name;?>" style="width: 100%;" placeholder="">
                                </th>
                                <th>
                                    <input class="form-control" type="text" name="ip" value="<?=$tpl->ip;?>" style="width: 100%;" placeholder="">
                                </th>
                                <th>
                                    <input class="form-control" type="text" name="total" value="<?=$tpl->total;?>" style="width: 100%;" placeholder="From-To">
                                </th>
                                <th>
                                    <div class="daterange-1">
                                        <input class="form-control" type="text" name="time" value="<?=$tpl->time_from_to;?>" style="width: 100%;" placeholder="From-To">
                                    </div>
                                </th>
                                <th>
                                    <select class="form-control" name="sent_email"><?=$tpl->option_sent_email;?></select>
                                </th>
                                <th>
                                    <select class="form-control" name="status"><?=$tpl->option_status;?></select>
                                </th>
                                <th>
                                    <input class="form-control" type="text" name="count_sent" value="<?=$tpl->count_sent;?>" style="width: 100%;" placeholder="From-To">
                                </th>
                                <th width="100">
                                    <a class="pointer btn btn-gray" onclick="doGeneralSearch('#search_abandoned', '<?=$CMS->vars['root_domain'];?>/?site=abandoned');" style="width: 100%;"><i class="fa fa-search" style="color: #fff;"></i></a>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?
                            if( !empty($tpl->data) AND is_array($tpl->data) )
                            {
                                foreach( $tpl->data as $result )
                                {
                            ?>
                            <tr>
                                <td>
                                    <div class="checkbox checkbox-only">
                                        <input type="checkbox"  name="id_<?=$result['record_cnt']?>" id="id_<?=$result['record_cnt']?>" value="<?=$result['id']?>"/>
                                        <label for="id_<?=$result['record_cnt']?>"></label>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?=$CMS->vars['root_domain'];?>/?site=abandoned&act=show&id=<?=$result['id'];?>">#<?=$result['id'];?></a>
                                </td>
                                <td>
                                    <a href="<?=$CMS->vars['root_domain'];?>/?site=abandoned&act=show&id=<?=$result['id'];?>"><?=$result['bill_email'];?></a>
                                </td>
                                <td><?=$result['bill_full_name'];?></td>
                                <td><?=$result['ip'];?></td>
                                <td><?=$result['total_c'];?></td>
                                <td><?=$result['time_c'];?></td>
                                <td><?=$result['sent_email_label'];?></td>
                                <td><?=$result['status_label'];?></td>
                                <td><?=$result['count_sent'];?></td>
                                <td style="text-align:center"><?=$result['btn_delete'];?></td>
                            </tr>
                            <?}}?>
                        </tbody>
                    </table>
                </div>
                <div class="fuction_table">
                    <div class="pull-left">
                        <p class="form-control-static">
                            <select class="form-control" onchange="return submit_action_control(this, $(this).parents('form:first').attr('id'));"><?=$tpl->option_action;?></select>
                        </p>
                    </div>
                    <nav class="pull-right">
                        <div class="block_bottom pagination pagination-sm"><?=$CMS->show_page;?></div>
                    </nav>
                </div>
            </div>
        </section>
        <input type="hidden" name="data_cnt" value="<?=$tpl->record_cnt;?>">
    </form>
</section>
<script></script>