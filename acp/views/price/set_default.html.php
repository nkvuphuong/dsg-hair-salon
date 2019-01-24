<section class="add_table main_form">
    <figure class="heading">
        <h3><?= $CMS->lang['set_default_header']; ?></h3>

        <figure class="pull-right right">
            <a href="<?=$CMS->vars['root_domain']?>/?site=price" title="" class="add_bill"><?=$CMS->lang['menu_price']?></a>
        </figure>
    </figure>

    <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-icons">
            <div class="tbl">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tab-stores" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										<i class="fa fa-map-marker" aria-hidden="true"></i>
										<?= \lib\input::lang('price_stores') ?>
									</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tab-customer-groups" role="tab" data-toggle="tab">
									<span class="nav-link-in">
										<i class="fa fa-users" aria-hidden="true"></i>
										<?= \lib\input::lang('price_cus_groups') ?>
									</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div><!--.tabs-section-nav-->

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active show" id="tab-stores">
                <form method="post" name="form_price" id="form_price" action="">
                    <section class="add_table">
                        <div class="data_table">
                            <div class="table table_cus table-responsive">
                                <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                                    <thead>
                                    <tr>
                                        <th class="form-inline">
                                            <?= $CMS->lang['price_stores'] ?>
                                            <input style="min-width: 70%" type="text" class="form-control" onkeyup="PriceStore.filterStores($(this).val())" placeholder="Filter">
                                        </th>
                                        <th class="form-inline">
                                            <?= $CMS->lang['price_store_address'] ?>
                                            <input style="min-width: 70%" type="text" class="form-control" onkeyup="PriceStore.filterStores($(this).val(), 'address')" placeholder="Filter">
                                        </th>
                                        <th width="30%" class="three-dots"><?= $CMS->lang['price_name'] ?></th>
                                    </tr>
                                    </thead>
                                    <tbody id="priceStoreDefault">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </form>
            </div><!--.tab-pane-->


            <div role="tabpanel" class="tab-pane fade" id="tab-customer-groups">
                <form method="post" name="form_price2" id="form_price2" action="">
                    <section class="add_table">
                        <div class="data_table">
                            <div class="table table_cus table-responsive">
                                <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                                    <thead>
                                    <tr>
                                        <th class="form-inline">
                                            <?= $CMS->lang['price_cus_groups'] ?>
                                            <input style="min-width: 70%" type="text" class="form-control" onkeyup="PriceCusGroup.filterGroups($(this).val())" placeholder="Filter">
                                        </th>
                                        <th width="50%" class="three-dots"><?= $CMS->lang['price_name'] ?></th>
                                    </tr>
                                    </thead>
                                    <tbody id="priceCusGroupDefault">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </form>
            </div><!--.tab-pane-->
        </div><!--.tab-content-->
    </section><!--.tabs-section-->
</section>

<script src="/acp/jsacp/price.js"></script>
<script>
    PriceStore.setPrices(<?=\lib\input::jsonEncode($tpl->prices, 0)?>);
    PriceStore.setStores(<?=\lib\input::jsonEncode($tpl->stores, 0)?>);
    PriceStore.render();

    PriceCusGroup.setPrices(<?=\lib\input::jsonEncode($tpl->prices, 0)?>);
    PriceCusGroup.setGroups(<?=\lib\input::jsonEncode($tpl->cusgroups, 0)?>);
    PriceCusGroup.render();
</script>
