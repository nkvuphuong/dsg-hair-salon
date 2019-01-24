<!-- use only add class 'educate_register_submit' innner element for event click submit form -->
<div class="box-msg box-msg-<?=isset($tpl->msg['status']) ? $tpl->msg['status'] : null;?>" style="<?=isset($tpl->msg['hidden']) ? $tpl->msg['hidden'] : null;?>"><?=isset($tpl->msg['message']) ? $tpl->msg['message'] : null;?></div>
<?=\views\layouts::google_recaptcha_header("send_educate_register",0);?>
<form class="register-form educate_register_form" id="send_educate_register" enctype="multipart/form-data" method="post" name="send_educate_register" action="/contact/send">
    <input type="hidden" name="contacttype" value="1">
    <input type="hidden" name="contactsubject" value="Đăng ký học nghề">
    <input type="hidden" name="contactcontent" value="Đăng ký học nghề từ website">
    <?=\core\ezy::tpl("form_educate_register","pages");?>
    <input class="btn btn-md btn-primary btn_educate_register <?=\views\layouts::google_recaptcha_form('send_educate_register');?>" type="submit" value="ĐĂNG KÝ HỌC" style="display: none;">
</form>