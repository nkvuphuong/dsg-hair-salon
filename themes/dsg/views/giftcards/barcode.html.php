<div class="section">
    <div class="section_wrapper clearfix">
    <!--Infor Barcode-->
        <? if($tpl->data) { ?>
        <div class="box_info_barcode">
            <p>Gift card information</p>
            <ul>
                <li>
                    <label>Giftcard code: </label>
                    <span><?=$tpl->data['giftcard_code'];?></span>
                </li>
                <li>
                    <label>Customer: </label>
                    <span><?=$tpl->data['name'];?></span>
                </li>
                <li>
                    <label>Email: </label>
                    <span><?=$tpl->data['cus_email'];?></span>
                </li>
                <li>
                    <label>Date created: </label>
                    <span><?= \lib\date::format($tpl->data['gitem_time'],"M d, Y g:i A");?></span>
                </li>
                <li>
                    <label>Last used: </label>
                    <span><?= $tpl->data['gitem_time_update'] == $tpl->data['gitem_time'] ? "N/A" : \lib\date::format($tpl->data['gitem_time_update'],"M d, Y g:i A");?></span>
                </li>
                <li>
                    <label>Remaining amount: </label>
                    <span><?=$tpl->data['amount_remain'];?></span>
                </li>
            </ul>
        </div>
        <? } else { ?>
                <p>Code gift card invalid!</p>
        <? } ?>
    </div>
</div>