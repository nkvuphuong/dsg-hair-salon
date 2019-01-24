<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        .clearfix:after { content: ""; display: table; clear: both; }
        a { color: #333; text-decoration: underline; }
        body { position: relative; width: 21cm; height: 29.7cm; margin: 0 auto; color: #001028; background: #FFFFFF; font-family: Arial, sans-serif; font-size: 12px; font-family: Arial; font-size: 0.9em;}
        header { padding: 10px 0; margin-bottom: 30px; border-bottom: 1px solid #C1CED9; }
        #logo { text-align: center; margin-bottom: 10px; }
        #logo img { width: 90px; }
        h1 { color: #333;font-size: 2.1em;line-height: 1.4em;margin: 0; }
        h2, h5 { margin: 5px 0;  }
        #project { float: left; }
        #project span { text-align: left; width: 52px; margin-right: 35px; display: inline-block; font-size:0.9em; }
        #company { float: right; text-align: right; }
        #project div, #company div { white-space: nowrap; }
        table { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px; }
        table th, table td { text-align: left; }
        table th.center, table td.center { text-align: center; }
        table th { padding: 10px 20px; color: #333; border: 1px solid #C1CED9; white-space: nowrap; font-weight: normal; }
        table .service, table .desc { text-align: left; }
        table td { padding: 10px; text-align: left; border: #333; border: 1px solid #C1CED9; vertical-align: middle;}
        table td.service, table td.desc { vertical-align: top; }
        table td.unit, table td.qty, table td.total { font-size: 1.2em; }
        table td.grand { border-top: 1px solid #333; }
        #notices .notice { color: #333; font-size: 1.2em; }
        footer { color: #333; width: 100%; height: 30px; position: absolute; bottom: 0; border-top: 1px solid #C1CED9;padding: 8px 0; text-align: center; }
        #container { height: 500px; width: 600px; position: relative; }
        #image { position: absolute; left: 0; top: 0; }
        #text { z-index: 100; position: absolute; font-size: 30px; font-weight: bold; left: 250px; top: -50px;/* Safari */ -webkit-transform: rotate(-50deg); /* Firefox */ -moz-transform: rotate(-50deg); /* IE */ -ms-transform: rotate(-50deg); /* Opera */ -o-transform: rotate(-50deg); /* Internet Explorer */ filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3); }
        .trx_status { position: absolute; top: 25%; left: 50%; /* Rotate div */ -ms-transform: rotate(-45deg); /* IE 9 */ -webkit-transform: rotate(-45deg); /* Chrome, Safari, Opera */ transform: rotate(-45deg); z-index: 100; font-size: 50px; color: black; -webkit-text-fill-color: white; /* Will override color (regardless of order) */ -webkit-text-stroke-width: 1px; -webkit-text-stroke-color: black; }
    </style>
</head>
<body>
    <header class="clearfix">
        <table style="width: 100%; max-width: 100%; margin-bottom: 0;border: 0;" width="100%" cellspacing="0" cellpadding="55%">
            <body>
                <tr style="background-color: #fff; padding:10px 5px;">
                    <td style="border: 0; vertical-align: middle;padding: 0;text-align: left;">
                        <h1 style="color: #333;font-size: 2.1em;line-height: 1.4em;margin: 0;font-family: Arial, sans-serif;text-align: left;"><?=$CMS->lang['preview_title_order_uppercase'];?> <span style="font-weight: normal;">#<?=$tpl->data['ord_name'];?></span></h1>
                    </td>
                    <td style="border: 0; vertical-align: middle;padding: 0;text-align: right">
                        <div style="font-size:0.9em; text-align: right;"><?=$CMS->class->date->date_format(time(), 1);?></div>
                    </td>
                </tr>
            </body>
        </table>
    </header>
    <main>
        <div style="padding-top: 15px;"></div>
        <table style="width: 100%; max-width: 100%;" width="100%" cellspacing="0" cellpadding="55%">
            <thead>
                <tr style="background-color: #f8f8f8; padding:10px 5px; text-align: left;">
                    <th colspan="2" style="padding: 10px 20px;color: #333;border: 1px solid #C1CED9;white-space: nowrap;font-weight: normal;">
                        <h2 style="color: #333;white-space: nowrap;font-weight: bold;margin: 5px 0;font-size: 1.3em;font-family: Arial, sans-serif;""><?=$CMS->lang['preview_title_shipping_detail'];?></h2>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50%;vertical-align: top;">
                        <table style="width: 100%; max-width: 100%;" width="100%" cellspacing="0" cellpadding="55%">
                            <tr>
                                <td style="vertical-align: top; border: none;padding: 0;width: 80px;">
                                    <span><strong><?=$CMS->lang['preview_title_shipping_name'];?>: </strong></span>
                                </td>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <div style="margin-bottom: 5px;">
                                        <span><?=$tpl->ship['full_name'];?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <span><strong><?=$CMS->lang['preview_title_address'];?>:</strong></span>
                                </td>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <div style="margin-bottom: 5px;">
                                        <span><?=$tpl->ship['address_full_us'];?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <span><strong><?=$CMS->lang['preview_title_phone'];?>:</strong></span>
                                </td>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <div style="margin-bottom: 5px;">
                                        <span><a href="tell:<?=$tpl->ship['phone'];?>"><?=$tpl->ship['phone'];?></a></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <span><strong><?=$CMS->lang['preview_title_email'];?>:</strong></span>
                                </td>
                                <td style="vertical-align: top; border: none;padding: 0;">
                                    <div style="margin-bottom: 5px;">
                                        <span><a href="mailto:<?=$tpl->ship['email'];?>"><?=$tpl->ship['email'];?></a></span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: top;">
                        <div style="margin-bottom: 5px;">
                            <span><strong><?=$CMS->lang['preview_title_note'];?>:</strong></span>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <span><?=$tpl->data['ord_note'];?></span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
    <footer>@ <?=$CMS->vars['company_name'];?></footer>
</body>
</html>