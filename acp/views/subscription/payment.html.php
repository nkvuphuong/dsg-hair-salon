<div class="row">
    <div class="col-xl-6" style="float:none;margin:0px auto;">
        <section class="box-typical steps-icon-block">
            <?= \core\ezy::render("header"); ?>
            <form id="delivery_form" name="delivery_form" action="<?=$CMS->vars['root_domain']?>/?site=subscription&act=payment" method="post">
                <header class="steps-numeric-title"><?=$CMS->lang['form_package_upgrade_info']?></header>
                <div class="form-group">
                    <fieldset class="form-control" style="border:none;text-align:left;">
                        <div class="form-group">
                            <span class="form-control-label2"><?=$CMS->lang['form_package_name']?>:</span>
                            <span class="form-control-span2"><?=$tpl->cart['info']['package_name']?></span>
                        </div>
                        <div class="form-group">
                            <span class="form-control-label2"><?=$CMS->lang['period']?>:</span>
                            <span class="form-control-span2"><?=$tpl->cart['info']['package_cycle']?> <?=$CMS->lang['month']?></span>
                        </div>
                        <div class="form-group">
                            <span class="form-control-label2"><?=$CMS->lang['price']?>:</span>
                            <span class="form-control-span2"><?=$CMS->class->input->currency($tpl->cart['total_amount'])?></span>
                        </div>
                    </fieldset>
                </div>


                <? if($tpl->cart['info']['package_name'] == 'basic') { ?>
                    <input type="hidden" name="payment_method" value="free" />
                <? } else {?>
                    <style>
                        i.VISA, i.MASTE, i.AMREX, i.JCB, i.VCB, i.TCB, i.MB, i.VIB, i.ICB, i.EXB, i.ACB, i.HDB, i.MSB, i.NVB, i.DAB, i.SHB, i.OJB, i.SEA, i.TPB, i.PGB, i.BIDV, i.AGB, i.SCB, i.VPB, i.VAB, i.GPB, i.SGB,i.NAB,i.BAB { width:80px; height:30px; display:block; background:url(<?=$CMS->vars['img_url']?>/bank_logo.png) no-repeat; }
                        i.MASTE { background-position:0px -31px}
                        i.AMREX { background-position:0px -62px}
                        i.JCB { background-position:0px -93px;}
                        i.VCB { background-position:0px -124px;}
                        i.TCB { background-position:0px -155px;}
                        i.MB { background-position:0px -186px;}
                        i.VIB { background-position:0px -217px;}
                        i.ICB { background-position:0px -248px;}
                        i.EXB { background-position:0px -279px;}
                        i.ACB { background-position:0px -310px;}
                        i.HDB { background-position:0px -341px;}
                        i.MSB { background-position:0px -372px;}
                        i.NVB { background-position:0px -403px;}
                        i.DAB { background-position:0px -434px;}
                        i.SHB { background-position:0px -465px;}
                        i.OJB { background-position:0px -496px;}
                        i.SEA { background-position:0px -527px;}
                        i.TPB { background-position:0px -558px;}
                        i.PGB { background-position:0px -589px;}
                        i.BIDV { background-position:0px -620px;}
                        i.AGB { background-position:0px -651px;}
                        i.SCB { background-position:0px -682px;}
                        i.VPB { background-position:0px -713px;}
                        i.VAB { background-position:0px -744px;}
                        i.GPB { background-position:0px -775px;}
                        i.SGB { background-position:0px -806px;}
                        i.NAB { background-position:0px -837px;}
                        i.BAB { background-position:0px -868px;}
                        ul.cardList li { cursor: pointer;float: left;margin-right: 0;padding: 5px 4px;text-align: center;width: 100px;}
                        div.boxContent {display:none;}
                    </style>

                    <header class="steps-numeric-title"><?=$CMS->lang['form_choose_payment_method']?></header>

                    <div class="form-group" style="margin-bottom:0px;">
                        <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                            <div class="radio w25" style="width:100%;">
                                <input type="radio" checked="checked" name="payment_method" id="radio-show-11" value="11">
                                <label for="radio-show-11"><?=$CMS->lang['payment_method_11']?></label>
                            </div>
                            <div class="boxContent paymentMethod11">
                                <p><i><?=$CMS->lang['payment_method_note_11']?></i></p>
                                <ul class="cardList clearfix">
                                    <li class="bank-online-methods">
                                        <label for="vcb_ck_on">
                                            <i class="BIDV" title="<?=$CMS->lang['BIDV']?>"></i>
                                            <input type="radio" value="BIDV"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="vcb_ck_on">
                                            <i class="VCB" title="<?=$CMS->lang['VCB']?>"></i>
                                            <input type="radio" value="VCB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="vnbc_ck_on">
                                            <i class="DAB" title="<?=$CMS->lang['DAB']?>"></i>
                                            <input type="radio" value="DAB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="tcb_ck_on">
                                            <i class="TCB" title="<?=$CMS->lang['TCB']?>"></i>
                                            <input type="radio" value="TCB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="sml_atm_mb_ck_on">
                                            <i class="MB" title="<?=$CMS->lang['MB']?>"></i>
                                            <input type="radio" value="MB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="sml_atm_vib_ck_on">
                                            <i class="VIB" title="<?=$CMS->lang['VIB']?>"></i>
                                            <input type="radio" value="VIB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="sml_atm_vtb_ck_on">
                                            <i class="ICB" title="<?=$CMS->lang['ICB']?>"></i>
                                            <input type="radio" value="ICB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="sml_atm_exb_ck_on">
                                            <i class="EXB" title="<?=$CMS->lang['EXB']?>"></i>
                                            <input type="radio" value="EXB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods">
                                        <label for="sml_atm_acb_ck_on">
                                            <i class="ACB" title="<?=$CMS->lang['ACB']?>"></i>
                                            <input type="radio" value="ACB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_hdb_ck_on">
                                            <i class="HDB" title="<?=$CMS->lang['HDB']?>"></i>
                                            <input type="radio" value="HDB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_msb_ck_on">
                                            <i class="MSB" title="<?=$CMS->lang['MSB']?>"></i>
                                            <input type="radio" value="MSB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_nvb_ck_on">
                                            <i class="NVB" title="<?=$CMS->lang['NVB']?>"></i>
                                            <input type="radio" value="NVB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_vab_ck_on">
                                            <i class="VAB" title="<?=$CMS->lang['VAB']?>"></i>
                                            <input type="radio" value="VAB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_vpb_ck_on">
                                            <i class="VPB" title="<?=$CMS->lang['VPB']?>"></i>
                                            <input type="radio" value="VPB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_scb_ck_on">
                                            <i class="SCB" title="<?=$CMS->lang['SCB']?>"></i>
                                            <input type="radio" value="SCB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="bnt_atm_pgb_ck_on">
                                            <i class="PGB" title="<?=$CMS->lang['PGB']?>"></i>
                                            <input type="radio" value="PGB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="bnt_atm_gpb_ck_on">
                                            <i class="GPB" title="<?=$CMS->lang['GPB']?>"></i>
                                            <input type="radio" value="GPB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="bnt_atm_agb_ck_on">
                                            <i class="AGB" title="<?=$CMS->lang['AGB']?>"></i>
                                            <input type="radio" value="AGB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="bnt_atm_sgb_ck_on">
                                            <i class="SGB" title="Ngân hàng Sài Gòn Công Thương"></i>
                                            <input type="radio" value="SGB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_bab_ck_on">
                                            <i class="BAB" title="<?=$CMS->lang['BAB']?>"></i>
                                            <input type="radio" value="BAB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_bab_ck_on">
                                            <i class="TPB" title="<?=$CMS->lang['TPB']?>"></i>
                                            <input type="radio" value="TPB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_bab_ck_on">
                                            <i class="NAB" title="<?=$CMS->lang['NAB']?>"></i>
                                            <input type="radio" value="NAB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_bab_ck_on">
                                            <i class="SHB" title="<?=$CMS->lang['SHB']?>"></i>
                                            <input type="radio" value="SHB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="sml_atm_bab_ck_on">
                                            <i class="OJB" title="<?=$CMS->lang['OJB']?>"></i>
                                            <input type="radio" value="OJB"  name="bankcode" >
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                    </div>

                    <div class="form-group" style="margin-bottom:0px;">
                        <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                            <div class="radio w25" style="width:100%;">
                                <input type="radio" name="payment_method" id="radio-show-12" value="12">
                                <label for="radio-show-12"><?=$CMS->lang['payment_method_12']?></label>
                            </div>
                            <div class="boxContent paymentMethod12">
                                <p><i><?=$CMS->lang['payment_method_note_12']?></i></p>
                                <ul class="cardList clearfix">
                                    <li class="bank-online-methods ">
                                        <label for="vcb_ck_on">
                                            <i class="BIDV" title="<?=$CMS->lang['BIDV']?>"></i>
                                            <input type="radio" value="BIDV"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="vcb_ck_on">
                                            <i class="VCB" title="<?=$CMS->lang['VCB']?>"></i>
                                            <input type="radio" value="VCB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="vnbc_ck_on">
                                            <i class="DAB" title="<?=$CMS->lang['DAB']?>"></i>
                                            <input type="radio" value="DAB"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="tcb_ck_on">
                                            <i class="TCB" title="<?=$CMS->lang['TCB']?>"></i>
                                            <input type="radio" value="TCB"  name="bankcode" >
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                    </div>

                    <div class="form-group" style="margin-bottom:0px;">
                        <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                            <div class="radio w25" style="width:100%;">
                                <input type="radio" name="payment_method" id="radio-show-13" value="13">
                                <label for="radio-show-13"><?=$CMS->lang['payment_method_13']?></label>
                            </div>
                            <div class="boxContent paymentMethod13">
                                <p><i><?=$CMS->lang['payment_method_note_13']?></i></p>
                                <ul class="cardList clearfix">
                                    <li class="bank-online-methods ">
                                        <label for="vcb_ck_on">
                                            Visa: <input type="radio" value="VISA"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="vnbc_ck_on">
                                            Master: <input type="radio" value="MASTER"  name="bankcode" >
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                    </div>

                    <div class="form-group" style="margin-bottom:0px;">
                        <fieldset class="form-control" style="max-width:unset;border:none;text-align:left;">
                            <div class="radio w25" style="width:100%;">
                                <input type="radio" name="payment_method" id="radio-show-14" value="14">
                                <label for="radio-show-14"><?=$CMS->lang['payment_method_14']?></label>
                            </div>
                            <div class="boxContent paymentMethod14">
                                <p><i><?=$CMS->lang['payment_method_note_14']?></i></p>
                                <ul class="cardList clearfix">
                                    <li class="bank-online-methods ">
                                        <label for="vcb_ck_on">
                                            Visa: <input type="radio" value="VISA"  name="bankcode" >
                                        </label>
                                    </li>
                                    <li class="bank-online-methods ">
                                        <label for="vnbc_ck_on">
                                            Master: <input type="radio" value="MASTER"  name="bankcode" >
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                    </div>

                    <script language="javascript">
                        $('input[name="payment_method"]').bind('click', function() {
                            $('.boxContent').hide();
                            $('.paymentMethod'+$(this).val()).show();
                            $('input[name=bankcode]').prop('checked', false);
                            $($('.paymentMethod'+$(this).val()+' ul li').find('input[name=bankcode]')[0]).prop('checked', true);
                        });

                        $("[name='payment_method']:checked").trigger("click");
                    </script>
                <? } ?>

                <!-- Start payment infomation -->
                <header class="steps-numeric-title"><?=$CMS->lang['payer_info']?></header>
                <div class="form-group">
                    <fieldset class="form-control" style="border:none;text-align:left;">
                        <fieldset class="form-group" style="position: relative"
                            <label class="form-label"><?=$CMS->lang['payer_name']?>:</label>
                                <input type="text" name="payer_name" value="<?=$tpl->payer['name']?>" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_name']?>"/>
                        </fieldset>
                        <fieldset class="form-group" style="position: relative">
                            <label class="form-label"><?=$CMS->lang['payer_phone']?>:</label>
                            <input type="text" name="payer_phone" value="<?=$tpl->payer['phone']?>" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_phone']?>"/>
                        </fieldset>
                        <fieldset class="form-group" style="position: relative">
                            <label class="form-label"><?=$CMS->lang['payer_email']?>:</label>
                            <input type="email" name="payer_email" value="<?=$tpl->payer['email']?>" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_email']?>" data-validation-regex="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/" data-validation-regex-message="<?=$CMS->lang['invalid_email']?>"/>
                        </fieldset>
                        <fieldset class="form-group" style="position: relative">
                            <label class="form-label"><?=$CMS->lang['payer_address']?>:</label>
                            <input type="text" name="payer_address" value="<?=$tpl->payer['address']?>" class="form-control" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['incomplete_address']?>"/>
                        </fieldset>
                        <fieldset class="form-group" style="position: relative">
                            <button onclick="clearForm($('#delivery_form'))" type="button" class="btn btn-rounded btn-inline btn-danger"><?=$CMS->lang['clear_form']?></button>
                        </fieldset>
                    </fieldset>
                </div>
                <!-- End infomation -->

                <a href="<?=$CMS->vars['root_domain']?>/?site=subscription&act=payment&back_to_cart=1" class="btn btn-rounded btn-grey float-left"/>← <?=$CMS->lang['back']?></a>
                <input type="hidden" name="payment_do" value="1"/>
                <button type="submit" class="btn btn-rounded float-right go_to_payment_form"><?=$CMS->lang['next']?> →</button>
            </form>
        </section>
    </div>
</div>

<script>
    $(document).ready(()=>{
        validate_form_custom('#delivery_form');
    })
</script>