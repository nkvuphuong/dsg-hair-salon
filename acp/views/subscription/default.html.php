<section class="add_table main_form">
    <figure class="heading">
        <h3><?=$CMS->lang['subscription']?></h3>
    </figure>

    <!-- Start Infomation -->
    <header class="widgets-header box-typical box-typical box-typical-padding border">
        <div class="container-fluid">
            <div class="tbl tbl-outer">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <div class="tbl tbl-item">
                            <div class="tbl-row">
                                <div class="tbl-cell">
                                    <div class="title"><?=$CMS->lang['subscription']?></div>
                                    <div class="amount color-blue"><?=$CMS->vars['siteInfo']['site_license_package']?></div>
                                    <? if($CMS->vars['siteInfo']['site_license_total']) { ?>
                                        <div class="amount-sm"><a onclick="cancelPackage('<?=$CMS->vars['root_domain']?>/?site=subscription&act=cancel_package')"><?=$CMS->lang['sub_cancel']?></a></div>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tbl-cell">
                        <div class="tbl tbl-item">
                            <div class="tbl-row">
                                <div class="tbl-cell">
                                    <div class="title"><?=$CMS->lang['sub_billing']?></div>
                                    <div class="amount"><?=($CMS->vars['siteInfo']['site_license_total'] == 0) ? $CMS->lang['free'] : "{$CMS->class->input->currency($CMS->vars['siteInfo']['site_license_total'])}/{$CMS->vars['siteInfo']['site_license_cycle']} {$CMS->lang['month']}"?></div>
                                    <div class="amount-sm"><a href="<?=$CMS->vars['root_domain']?>/?site=subscription&act=upgrade_package"><?=$CMS->lang['sub_switch_plan']?></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tbl-cell">
                        <div class="tbl tbl-item">
                            <div class="tbl-row">
                                <div class="tbl-cell">
                                    <div class="title"><?=$CMS->lang['sub_next_change']?></div>
                                    <div class="amount"><?=($CMS->vars['siteInfo']['site_license_total'] == 0) ? "N/A" : "{$CMS->class->input->currency($CMS->vars['siteInfo']['site_license_total'])} {$CMS->class->date->date_format($CMS->vars['siteInfo']['site_license_expired'])}"?></div>
                                    <div class="amount-sm">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Infomation -->

    <!-- Start table -->
    <section>
        <div class="data_table">
            <table id="example" class="display table table_cus tbl-typical" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <? if ($_SESSION['is_mobile']) { ?>
                        <th width="2%" data-orderable="false" data-sortable="false"></th>
                    <? } ?>
                    <th width="10%" data-orderable="true"><?=$CMS->lang['sub_date']?></th>
                    <th width="10%" data-orderable="true"><?=$CMS->lang['sub_code']?></th>
                    <th width="10%" data-orderable="false"><?=$CMS->lang['sub_total']?></th>
                    <th width="55%" data-orderable="false"><?=$CMS->lang['sub_desc']?></th>
                    <th width="15%" data-orderable="false"><?=$CMS->lang['sub_status']?></th>
                </tr>
                </thead>
                <tbody>
                <? if ($tpl->data) { ?>
                    <? foreach ($tpl->data as $data) {
                        $data = $CMS->subscription->convertvalue($data); ?>
                        <tr>
                            <? if ($_SESSION['is_mobile']) { ?>
                                <td></td>
                            <? } ?>
                            <td><?= $data['sub_time'] ?></td>
                            <td><?= $data['order_key'] ?></td>
                            <td style="white-space: pre-wrap"><?= $data['sub_total'] ?></td>
                            <td style="white-space: pre-wrap"><?= $data['sub_name'] ?></td>
                            <td style="white-space: pre-wrap"><?= $data['payment_status'] ?></td>
                        </tr>
                    <? } ?>
                <? } else { ?>
                    <tr>
                        <? if ($_SESSION['is_mobile']) { ?>
                            <td></td>
                        <? } ?>
                        <td colspan="5"><?= $CMS->lang['no_data'] ?></td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
    </section>
    <!-- End table -->

    <!-- Start Paging -->
    <div class="block_bottom pagination pagination-sm">
        <?= $CMS->subscription->show_page ?>
    </div>
    <!-- End Paging -->
</section>