<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        body {
            margin-top: 20px;
        }

        .noborder{
            border: none !important;
        }

        .padding-init {
            padding: initial !important;
        }
    </style>
</head>
<body onload="window.print();window.close()">
<!--onload="window.print();window.close()"-->
<!------ Include the above in your HEAD tag ---------->
<div class="container">
    <div class="row">
        <div class="well col-xs-12">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <address>
                        <strong><?=$CMS->vars['print_company_name']?></strong>
                        <br>
                        <?=$CMS->vars['print_company_address']?>
                        <br>
                        <?=$CMS->vars['print_company_phone']?>
                        <br>
                        <?=$CMS->vars['print_website']?>
                    </address>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                    <p>
                        <em>Ngày: <?=$tpl->data['trx']['trx_time_c']?></em>
                    </p>
                    <p>
                        <em>Hóa đơn: #<?=$tpl->data['trx']['trx_invoice_no_c']?></em>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <img src="<?="{$CMS->vars['upload_url']}/attach/{$CMS->vars['logo_website']}"?>" alt="allytees-500-trans.png" width="180">
                    <h1>Hóa đơn</h1>
                </div>
                </span>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>#</th>
                        <th class="text-center">Giá</th>
                        <th class="text-center">Tổng tiền</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($tpl->data['items'] as $item) { ?>
                        <tr>
                            <td class="col-md-9"><em><?=$item['tri_name']?></em></h4></td>
                            <td class="col-md-1" style="text-align: center"><?=$item['tri_quantity']?></td>
                            <td class="col-md-1 text-center"><?=$CMS->class->input->currency($item['tri_price'])?></td>
                            <td class="col-md-1 text-center"><?=$CMS->class->input->currency($item['tri_subtotal'])?></td>
                        </tr>
                    <? } ?>
                    <tr>
                        <td class=" padding-init">  </td>
                        <td colspan="2" class="text-right padding-init">
                            <p>
                                <strong>Tổng tiền: </strong>
                            </p>
                        </td>
                        <td class="text-center padding-init">
                            <p>
                                <strong><?=$CMS->class->input->currency($tpl->data['trx']['trx_amount'])?></strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="noborder padding-init">  </td>
                        <td colspan="2" class="text-right noborder padding-init">
                            <p>
                                <strong>Giảm giá: </strong>
                            </p>
                        </td>
                        <td class="text-center noborder padding-init">
                            <p>
                                <strong><?=$tpl->data['trx']['trx_discount_value_c']?></strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="noborder padding-init">  </td>
                        <td colspan="2" class="text-right noborder padding-init">
                            <p>
                                <strong>Tax: </strong>
                            </p>
                        </td>
                        <td class="text-center noborder padding-init">
                            <p>
                                <strong><?=$tpl->data['trx']['trx_tax_c']?></strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>  </td>
                        <td colspan="2" class="text-right"><h4><strong>Tổng: </strong></h4></td>
                        <td class="text-center text-danger"><h4><strong><?=$CMS->class->input->currency($tpl->data['trx']['trx_total'])?></strong></h4></td>
                    </tr>
                    </tbody>
                </table>
                <div>
                    <h4 style="text-align:center;">
                        Cảm ơn quý khách.
                    </h4>
                </div>
            </div>
        </div>
    </div>
</body>

</html>