<script src="/acp/jsacp/discount.js"></script>
<div class="modal fade" id="addDiscountCodePopup" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <form enctype="multipart/form-data" method="post" id="addDiscountCodePopupFrm">
        <input type="hidden" name="discount_id" value="0">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="addDiscountCodePopupHeader">
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="margin-right: 5px">
                        <?=$tpl->form_items_popup_body;?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="closeDiscountCodePopupBtn" class="btn btn-default"
                            data-dismiss="modal"><?= $CMS->lang['close'] ?></button>
                    <button type="submit" class="btn btn-primary"><?= $CMS->lang['act_add'] ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<script language="javascript">
    addDiscountCodePopupFrmValidate();
</script>
