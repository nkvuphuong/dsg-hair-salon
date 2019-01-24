<div class="row">
    <div class="col-xl-6" style="float:none;margin:0px auto;">
        <div class="box-typical prices-page steps-icon-block">
            <?= \core\ezy::render("header"); ?>
            <form id="delivery_form" name="delivery_form" action="<?=$CMS->vars['root_domain']?>/?site=subscription&act=payment" method="post">
                <header class="steps-numeric-title"><?$CMS->lang['form_package_upgrade_info']?></header>
                <section class="add_table">
                    <table id="example" class="display table table_cus dataTable no-footer" cellspacing="0" width="100%" role="grid" style="width: 100%;">
                        <thead>
                        <tr role="row">
                            <th><?=$CMS->lang['form_package_name']?></th>
                            <th><?=$CMS->lang['price']?>/<?=$CMS->lang['month']?></th>
                            <th><?=$CMS->lang['period']?></th>
                            <th><?=$CMS->lang['title_number_money']?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr role="row" class="odd">
                            <td>
                                <div class="form-group"><?=$tpl->cart['info']['package_name']?></div>
                            </td>
                            <td>
                                <div class="form-group"><?=$CMS->class->input->currency($tpl->cart['info']['package_price'])?></div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control change_package_cycle" style="margin:0px;" name="package_cycle" id="package_cycle">
                                    <? foreach ($tpl->cart['info']['payment_cycle'] as $payment_cycle) { ?>
                                        <option value="<?=$payment_cycle?>" <?=$payment_cycle == $tpl->cart['info']['package_cycle'] ? 'selected' : ''?> ><?=$payment_cycle?> <?=$CMS->lang['month']?></option>
                                    <? } ?>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <span style="color:red;" id="package_total_price"><?=$CMS->class->input->currency($tpl->cart['total_amount'])?></span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </section>
                <a href="<?=$CMS->vars['root_domain']?>/?site=subscription&act=upgrade_package" class="btn btn-rounded btn-grey float-left"/>← <?=$CMS->lang['back']?></a>
                <input type="hidden" name="payment_method_do" value="1"/>
                <button type="submit" class="btn btn-rounded float-right go_to_payment_form"><?=$CMS->lang['next']?> →</button>
            </form>
            <script>
                $(document).on('change','.change_package_cycle', function(){
                    var package_cycle = $(this).val();
                    var _that = $('#package_total_price');
                    $.ajax({
                        type: "POST",
                        url: site_root_domain + "/?site=subscription&act=change_cycle_package",
                        data: {
                            package_cycle:package_cycle
                        },
                        beforeSend: function (xhr) {
                            _that.html('loading...');
                        },
                        success: function (html)
                        {
                            _that.html(html);
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>