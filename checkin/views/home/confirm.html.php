<div id="confirm-screen" class="display-block" style="display: none">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <h2 class="mt-05 text-green text-center"><b>Hóa Đơn Dịch vụ</b></h2>
        </div>
        <div class="col-md-2"></div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 mb-10">
            <h4 class=""><b>Cửa hàng <?= \lib\input::arrayValue($tpl->data->store, 'name') ?></b> cảm ơn bạn đã tin tưởng vào dịch vụ của chúng tôi</h4>
            <h4 class="mb-20">Số điện thoại: <b id="ord-phone">###</b></h4>
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 present-item-wrap" id="ord-items">
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="row mb-20">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <table class="table table-borderless present-table">
                <tbody>
                <tr>
                    <td scope="col" width="183"><strong>Số tiền:</strong></td>
                    <td scope="col" id="ord-amount">###</td>
                    <td scope="col"><strong>Giảm giá:</strong></td>
                    <td scope="col" id="ord-discount">###</td>
                    <td scope="col"><strong>Thuế:</strong></td>
                    <td scope="col" id="ord-tax">###</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="row ">
        <div class="col-md-2"></div>
        <div class="col-md-8 present-price-row">
            <div class="present-price">Tổng tiền Dịch vụ: 	<span id="ord-total">###</span></div>
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="row mb-20">
        <div class="col-md-2"></div>
        <div class="col-md-8 text-center mb-20">
            <a class="btn btn-green-md btn-block" href="#" data-toggle="modal" data-target="#order-rating">XÁC NHẬN HÓA ĐƠN</a>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>