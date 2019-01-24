<? //= \core\ezy::render("scheduler"); ?>
<script>
    var trxDashboardOptions = {
        height: 300,
        vAxis: {
            format: `#<?=$CMS->vars['currency_separate']?>###<?=$CMS->vars['currency_type']?>`
        }
    };
</script>
    <div class="row match-height">
        <? if(isset($CMS->vars['web_free']) and $CMS->vars['web_free'] == 1) { ?>
        <div class="col-lg-12">
            <section class="box-typical box-typical-dashboard mb30 dashboard-trx-sales-block">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3>Overview</h3>
                        </div>
                        
                    </div>
                </header>

                
                <div class="info-box">
                    <div class="clr_box">
                        <h3 class="box_title">Statistic</h3>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <div class="box_quick green_title">
                                <span class="box_icon"><i class="font-icon font-icon-users-two"></i></span>
                                <p class="box_des">
                                    <span class="label cnumber"><?=$CMS->vars['number_call_now'];?></span>
                                     Click "Call now"
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <div class="box_quick orange_title">
                                <span class="box_icon"><i class="font-icon font-icon-notebook-lines"></i></span>
                                <p class="box_des">
                                    <span class="label cnumber"><?=$tpl->report['order']['number_new'] + $tpl->report['order']['number_old'];?></span> Total Order (month)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="clr_box">
                        <h3 class="box_title">Quick link</h3>
                        <?if($CMS->permit['service_add']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=service&act=add">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-plus-circle"></i></span>
                                    <p class="box_des">Add Service</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['order_add']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=order&act=add">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-cart-plus"></i></span>
                                    <p class="box_des">Add Order</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['interface_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=interface">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-laptop"></i></span>
                                    <p class="box_des">Edit Interface</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['gallery_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=gallery">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-image"></i></span>
                                    <p class="box_des">Photo Gallery</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['coupons_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=coupons">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-gift"></i></span>
                                    <p class="box_des">Coupons</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['logos_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=logos">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-fire"></i></span>
                                    <p class="box_des">Banner</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                    </div>


                    <div class="clr_box">
                        <?if($CMS->permit['config_general_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=config_general">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-cogs"></i></span>
                                    <p class="box_des">Genneral Settings</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['email_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=email">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-envelope"></i></span>
                                    <p class="box_des">Email</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['contact_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=contact">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-at"></i></span>
                                    <p class="box_des">Contact</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['emailtpl_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=emailtpl">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-envelope-square"></i></span>
                                    <p class="box_des">Email Template</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['sms_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=sms">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-comments"></i></span>
                                    <p class="box_des">SMS</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        <?if($CMS->permit['embed_read']){?>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                            <a href="?site=embed">
                                <div class="box_quick">
                                    <span class="box_icon"><i class="fa fa-plug"></i></span>
                                    <p class="box_des">Embed</p>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                    </div>

                </div>
            </section><!--.box-typical-dashboard-->
        </div>
    <? }// Use for web free  
        else {?>

        <div class="col-xl-6">
            <section class="box-typical box-typical-dashboard mb30 dashboard-trx-sales-block">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?=$CMS->lang['title_total_sales']?></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-actions">
                            <div class="dropdown show sale-dashboard-dropdown">
                                <a class="btn btn-secondary dropdown-toggle btn-sm" href="" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                    <? for($timeType=1; $timeType<=4; $timeType++){ ?>
                                        <a class="dropdown-item" onclick="trxStatsDashBoard(<?=$timeType?>, trxDashboardOptions)"><?=$CMS->lang['dashboard_income_time_'.$timeType]?></a>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-typical-header" style="margin-bottom: 15px">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text" id="dashboard-trx-sales-time-type"><?=$CMS->lang['title_total_sales']?></span>
                                        <span class="info-box-number" id="dashboard-trx-sales-total"><?= $CMS->class->input->currency($tpl->trx['income']); ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($tpl->trx['stats'] as $key => $item) { ?>
                        <div class="col-md-6">
                            <div class="callout" style="border-color: <?= $item['color'] ?>">
                                <div class="sub_report_title"><?= $CMS->lang['trx_tab_' . $key]; ?></div>
                                <div class="sub_report_money" id="dashboard-trx-sales-<?= $key; ?>"><?= $CMS->class->input->currency($item['data']['total']); ?></div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </section><!--.box-typical-dashboard-->
        </div>
        <div class="col-xl-6">
            <div class="row">
                <div class="col-sm-6">
                    <article class="statistic-box red">
                        <div>
                            <a href="<?= $tpl->url_customer; ?>" style="color: #FFF;">
                                <div class="number"><?= $tpl->report['customer']['number_new']; ?></div>
                                <div class="caption">
                                    <div><?= $tpl->report['customer']['caption']; ?></div>
                                </div>
                                <div class="percent">
                                    <?= $tpl->report['customer']['icon']; ?>
                                    <p><?= $tpl->report['customer']['percent']; ?></p>
                                </div>
                            </a>
                        </div>
                    </article>
                </div><!--.col-->
                <div class="col-sm-6">
                    <article class="statistic-box purple">
                        <div>
                            <a href="<?= $tpl->url_order; ?>" style="color: #FFF;">
                                <div class="number"><?= $tpl->report['order']['number_new']; ?></div>
                                <div class="caption">
                                    <div><?= $tpl->report['order']['caption']; ?></div>
                                </div>
                                <div class="percent">
                                    <?= $tpl->report['order']['icon']; ?>
                                    <p><?= $tpl->report['order']['percent']; ?></p>
                                </div>
                            </a>
                        </div>
                    </article>
                </div><!--.col-->
                <div class="col-sm-6">
                    <article class="statistic-box yellow">
                        <div>
                            <a href="<?= $tpl->url_revenue; ?>" style="color: #FFF;">
                                <div class="number" data-toggle="tooltip" data-placement="right"
                                     title="<?= $tpl->report['revenue']['title_number']; ?>"><?= $tpl->report['revenue']['number_new']; ?></div>
                                <div class="caption">
                                    <div><?= $tpl->report['revenue']['caption']; ?></div>
                                </div>
                                <div class="percent">
                                    <?= $tpl->report['revenue']['icon']; ?>
                                    <p><?= $tpl->report['revenue']['percent']; ?></p>
                                </div>
                            </a>
                        </div>
                    </article>
                </div><!--.col-->
                <div class="col-sm-6">
                    <article class="statistic-box green">
                        <div>
                            <a href="<?= $tpl->url_costs; ?>" style="color: #FFF;">
                                <div class="number" data-toggle="tooltip" data-placement="right"
                                     title="<?= $tpl->report['costs']['title_number']; ?>"><?= $tpl->report['costs']['number_new']; ?></div>
                                <div class="caption">
                                    <div><?= $tpl->report['costs']['caption']; ?></div>
                                </div>
                                <div class="percent">
                                    <?= $tpl->report['costs']['icon']; ?>
                                    <p><?= $tpl->report['costs']['percent']; ?></p>
                                </div>
                            </a>
                        </div>
                    </article>
                </div><!--.col-->
            </div><!--.row-->
        </div>
        <div class="col-xl-6">
            <section class="box-typical box-typical-dashboard mb30  dashboard-order-sales-block">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?= $CMS->lang['sales_chart'] ?></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-actions">
                                <div class="dropdown show sale-dashboard-dropdown">
                                <a class="btn btn-secondary dropdown-toggle btn-sm" href="https://example.com" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                    <? for($timeType=1; $timeType<=4; $timeType++){ ?>
                                        <a class="dropdown-item" onclick="orderSalesChartDashBoard(<?=$timeType?>, trxDashboardOptions)"><?=$CMS->lang['dashboard_income_time_'.$timeType]?></a>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="box-typical-body" style="height: 339px;">
                    <div class="chart-statistic-box">
                        <div class="chart-container-in">
                            <div id="chart_ord_sales_div"></div>
                        </div>
                    </div><!--.chart-statistic-box-->
                </div>
            </section>
        </div><!--.col-->

        <div class="col-xl-6">
            <section class="box-typical box-typical-dashboard mb30 dashboard-trx-revenue-costs-block">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?= $CMS->lang['title_sales_expenses'] ?></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-actions">
                            <div class="dropdown show sale-dashboard-dropdown">
                                <a class="btn btn-secondary dropdown-toggle btn-sm" href="https://example.com" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                    <? for($timeType=1; $timeType<=4; $timeType++){ ?>
                                        <a class="dropdown-item" onclick="trxRevenueCostsChartDashBoard(<?=$timeType?>, trxDashboardOptions)"><?=$CMS->lang['report_time_'.$timeType]?></a>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="box-typical-body" style="height: 339px;">
                    <section class="widget-chart-combo" style="border:none;">
                        <div class="widget-chart-combo-content">
                            <div class="widget-chart-combo-content-in" style="margin-right: 0;">
                                <div id="chart_revenue_div" class="chart"></div>
                            </div>
                        </div>
                        <script language="javascript">
                            $(document).ready(function(){
                                dropdownInit($(".sale-dashboard-dropdown"),1);
                            })
                        </script>
                    </section><!--.widget-chart-combo-->
                </div><!--.box-typical-body-->
            </section><!--.box-typical-dashboard-->
        </div><!--.col-->

        <div class="col-xl-6">
            <section class="box-typical box-typical-dashboard mb30">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?= $tpl->chart['status_order']['chart_title']; ?></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-actions">
                            <a href="<?= $CMS->vars['root_domain']; ?>/?site=order" class="action-btn">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </header>
                <div class="box-typical-body" style="height: 335px;">
                    <section class="card">
                        <div id="donut-chart"></div>
                    </section>
                    <script>
                        var donutChart = c3.generate({
                            bindto: '#donut-chart',
                            data: {
                                columns: <?= $tpl->chart['status_order']['str_chart']; ?>,
                                type: 'donut',
                                onclick: function (d, element) {
                                    if (d.index == 0) {
                                        window.location.href = "<?= $CMS->vars['root_domain']?>/?site=order&status=0";
                                    } else if (d.index == 1) {
                                        window.location.href = "<?= $CMS->vars['root_domain']?>/?site=order&status=1";
                                    } else if (d.index == 2) {
                                        window.location.href = "<?= $CMS->vars['root_domain']?>/?site=order&status=2";
                                    }
                                }
                            },
                            donut: {
                                title: "<?= $tpl->chart['status_order']['chart_title']; ?>"
                            }
                        });
                    </script>
                </div><!--.box-typical-body-->
            </section><!--.box-typical-dashboard-->
        </div><!--.col-->

        <div class="col-xl-6">
            <section class="box-typical box-typical-dashboard mb30">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?= $tpl->getList['order']['caption']; ?></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-actions">
                            <a href="<?= $CMS->vars['root_domain']; ?>/?site=order" class="action-btn">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </header>
                <div class="box-typical-body" style=" height: 335px;">
                    <table class="tbl-typical">
                        <!--<tr>
                            <th><div>Trạng thái</div></th>
                            <th><div>Đơn hàng</div></th>
                            <th align="center"><div>Tổng tiền</div></th>
                            <th align="center"><div>Thời gian</div></th>
                        </tr>-->
                        <?= $tpl->getList['order']['output']; ?>
                    </table>
                </div><!--.box-typical-body-->
            </section><!--.box-typical-dashboard-->
        </div><!--.col-->

        <div class="col-xl-6">
            <section class="box-typical box-typical-dashboard mb30">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?= $tpl->getList['customer']['caption']; ?></h3>
                        </div>
                        <div class="tbl-cell tbl-cell-actions">
                            <a href="<?= $CMS->vars['root_domain']; ?>/?site=customer" class="action-btn">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </header>
                <div class="box-typical-body" style=" height: 326px;">
                    <div class="contact-row-list">
                        <?= $tpl->getList['customer']['output']; ?>
                    </div>
                </div><!--.box-typical-body-->
            </section><!--.box-typical-dashboard-->

        </div><!--.col-->

        <div class="col-xl-6 scrollable">
            <section class="box-typical box-typical-dashboard mb30">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h3><?= $tpl->getList['activity']['caption']; ?></h3>
                        </div>

                        <div class="tbl-cell tbl-cell-actions">
                            <a href="<?= $CMS->vars['root_domain']; ?>/?site=logs" class="action-btn">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </header>
                <div class="box-typical-body" style=" height: 326px;">
                    <div class="widget widget-activity" style="margin: 0; border: none;">
                        <?= $tpl->getList['activity']['output']; ?>
                    </div>
                </div>
            </section>
        </div>
    <? } ?>
        <!--<div class="col-xl-6 scrollable">
        <section class="box-typical box-typical-dashboard mb30" style="height: 305px;">
            <header class="box-typical-header">
                <div class="tbl-row">
                    <div class="tbl-cell tbl-cell-title">
                        <h3 style="display: inline-block;"><? /*= $tpl->getList['issues']['caption'];*/ ?>: </h3>
                        <header class="widget-header" style="display: inline-block;">
                            <?php
        /*                                echo $tpl->getList['issues']['status'][0];
                                        echo $tpl->getList['issues']['status'][1];
                                    */ ?>
                        </header>
                    </div>
                    <div class="tbl-cell tbl-cell-actions">
                        <a href="<? /*= $CMS->vars['root_domain']; */ ?>/?site=issues" class="action-btn">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                    </div>
                   
                </div>
            </header>
            <div class="box-typical-body">
                <div class="widget widget-activity" style="margin: 0; border: none;">
                    <? /*= $tpl->getList['issues']['output'];*/ ?>
                </div>
            </div>
        </section>
    </div>-->

    </div>
<? /*= \core\ezy::render("taskbar"); */ ?>
<?php
if( $CMS->vars['site_routed_app'] == "web" and !isset($CMS->vars['web_free']))
{
?>
<div class="control-panel-container">
    <ul>
        <li class="tasks">
            <div class="control-item-header">
                <a href="<?=$CMS->vars['root_domain']?>/?site=config_general&tab=tabs-4-tab-7" class="icon-toggle" data-toggle="tooltip" title="<?=$CMS->lang['quick_booking_hours']?>">
                    <span class="icon fa fa-clock-o"></span>
                </a>
                <span class="text"><?=$CMS->lang['quick_booking_hours']?></span>
            </div>
        </li>
        <li class="tasks">
            <div class="control-item-header">
                <a href="<?=$CMS->vars['root_domain']?>/?site=config_general&tab=tabs-4-tab-6" class="icon-toggle" data-toggle="tooltip" title="<?=$CMS->lang['quick_switch_sms']?>">
                    <span class="icon fa fa-commenting-o"></span>
                </a>
                <span class="text"><?=$CMS->lang['quick_switch_sms']?></span>
            </div>
        </li>
        <li class="tasks">
            <div class="control-item-header">
                <a href="<?=$CMS->vars['root_domain']?>/?site=config_general&tab=tabs-4-tab-5" class="icon-toggle" data-toggle="tooltip" title="<?=$CMS->lang['quick_datetime']?>">
                    <span class="icon fa fa-calendar"></span>
                </a>
                <span class="text"><?=$CMS->lang['quick_datetime']?></span>
            </div>
        </li>
        <li class="tasks">
            <div class="control-item-header">
                <a href="<?=$CMS->vars['root_domain']?>/?site=config_general&tab=tabs-4-tab-3" class="icon-toggle" data-toggle="tooltip" title="<?=$CMS->lang['quick_email']?>">
                    <span class="icon fa fa-envelope"></span>
                </a>
                <span class="text"><?=$CMS->lang['quick_email']?></span>
            </div>
        </li>
        <li class="tasks">
            <div class="control-item-header">
                <a href="<?=$CMS->vars['root_domain']?>/?site=config_general" class="icon-toggle" data-toggle="tooltip" title="<?=$CMS->lang['quick_title']?>">
                    <span class="icon fa fa-text-height"></span>
                </a>
                <span class="text"><?=$CMS->lang['quick_title']?></span>
            </div>
        </li>
    </ul>
    <a class="control-panel-toggle" id="control-panel-toggle-open">
        <span class="fa fa-angle-double-left"></span>
    </a>
</div>
<? } ?>