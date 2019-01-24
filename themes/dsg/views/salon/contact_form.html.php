<div class="box-msg box-msg-<?=isset($tpl->msg['status']) ? $tpl->msg['status'] : null;?>" style="<?=isset($tpl->msg['hidden']) ? $tpl->msg['hidden'] : null;?>"><?=isset($tpl->msg['message']) ? $tpl->msg['message'] : null;?></div>
<?=\views\layouts::google_recaptcha_header("send_contact",0);?>
<form enctype="multipart/form-data" class="contact-form" method="post" name="send_contact" id="send_contact" action="/contact/send" accept-charset="UTF-8">
    <div class="clearfix row">
        <div class="col-md-6">
            <div class="form-group">
                <input class="form-control" name="contactname" type="text" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng nhập tên" autocomplete="off" placeholder="Họ và tên">
            </div>
            <div class="form-group">
                <input class="form-control" name="contactemail" type="text" data-validation="[EMAIL]" data-validation-message="Vui lòng nhập email" autocomplete="off" placeholder="Email">
            </div>
            <div class="form-group">
                <input class="form-control" name="contactsubject" type="text" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng nhập tiêu đề" autocomplete="off" placeholder="Tiêu đề">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <textarea class="form-control" name="contactcontent" rows="3" data-validation="[NOTEMPTY]" data-validation-message="Vui lòng nhập nội dung" autocomplete="off" placeholder="Nội dụng"></textarea>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <p><i class="fa fa-info-circle"></i>Tất cả các thông tin điều bắt buộc nhập</p>
        <div class="">
            <button class="btn btn-md btn-primary btn_contact <?=\views\layouts::google_recaptcha_form("send_contact");?>" type="submit">Gửi liên hệ</button>
        </div>
    </div>
</form>