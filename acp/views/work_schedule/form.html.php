<script src="/acp/jsacp/work_schedule.js?v=201808262212"></script>
<form enctype="multipart/form-data" method="post" id="work_schedule_form" action="<?=$CMS->vars['root_domain']?>/?site=<?=$CMS->input['site']?>&act=<?=isset($tpl->act) ? $tpl->act : ''?><?=$CMS->input['id'] ? "&id={$CMS->input['id']}" : "";?>" onsubmit="workScheduleSubmit($(this)); return false;">
    <section class="add_form main_form">
        <figure class="heading">
            <h3><?=isset($tpl->header_title) ? $tpl->header_title : ""?></h3>
        </figure>
        <figure>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="with-border m-t-0"><?=$CMS->lang['work_schedule_information']?></h4>
                    <div class="row">
                        <div class="col-md-3">
                            <input autocomplete="off" id="work_schedule_date" name="work_schedule_date" class="work_schedule_date form-control" onchange="workScheduleLoadData($('#work_schedule_date').val(),$('#store_id').val())" value="<?=$tpl->data['work_schedule_date']?>">
                        </div>
                        <div class="col-md-3">
                            <select name="city_id" id="city_id" class="form-control select2" onchange="workScheduleLoadStores($(this).val(), $('#store_id'))">
                                <?foreach($tpl->cities as $city) {?>
                                    <option value="<?=$city['id']?>"><?=$city['name']?></option>
                                <?}?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="store_id" id="store_id" class="form-control select2" onchange="workScheduleLoadData($('#work_schedule_date').val(),$('#store_id').val())">
                                <?foreach($tpl->store as $store) {?>
                                    <option value="<?=$store['store_id']?>"><?=$store['store_name']?></option>
                                <?}?>
                            </select>
                        </div>
                        <div class="col-md-3"><button type="submit" class="btn btn-primary">Cập nhật</button> | <button type="button" class="btn btn-success bs-tooltip-trigger" data-toggle="modal" title="Sao chép lịch trực từ thời gian khác hoặc cửa hàng khác" data-target="#cloneWorkScheduleModal">Sao chép</button></div>
                    </div>
                    <div class="with-border with-border m-t-0"><br></div>
                    <section class="add_table">
                        <div class="data_table">
                            <div class="table table_cus table-responsive" style="border-top: none;">
                                <table id="work_schedule_tbl" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                                    <thead>
                                    <tr>
                                        <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                                        <th  width="5%" data-sortable="false" data-orderable="false" aria-label="">
                                            <div class="checkbox checkbox-only">
                                                <input type="checkbox" id="checkall" name="checkall" onchange="workScheduleAll($(this).is(':checked'))">
                                                <label for="checkall"></label>
                                            </div>
                                        </th>
                                        <th>Nhân viên</th>
                                        <th width="15%">Slots</th>
                                        <th>
                                            Chi nhánh
                                        </th>
                                        <?foreach ($tpl->shift_work  as $item) {?>
                                            <th><input type="checkbox" onchange="workScheduleColInit(<?=$item['shift_work_id']?>, $(this).is(':checked'))"> <?=$item['shift_work_name']?></th>
                                        <?}?>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td>
                                            <select class="form-control" id="filterByBranchSelect" onchange="workScheduleFilterByBranch()">
                                                <option value="all">Tất cả chi nhánh</option>
                                                <option value="branch" selected>Chi nhánh đang chọn</option>
                                            </select>
                                        </td>
                                        <?foreach ($tpl->shift_work  as $item) {?>
                                            <td><strong><?=\lib\date::sec2Hour($item['shift_work_in'])?> - <?=\lib\date::sec2Hour($item['shift_work_out'])?></strong></td>
                                        <?}?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?
                                            foreach ($tpl->users as $user) {
                                        ?>
                                            <tr class="ws-item ws-item-branch-<?=$user['store_id']?>">
                                                <td style="margin:0px;padding:0px;"></td>
                                                <td>
                                                    <div class="checkbox checkbox-only">
                                                        <input onchange="workScheduleRowInit($(this).parents('tr:first'));" type="checkbox" class="cbx_row"  name="row_<?=$user['user_id']?>" id="row_<?=$user['user_id']?>" value="<?=$user['user_id']?>"/>
                                                        <label for="row_<?=$user['user_id']?>"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?=$user['user_display_name']?>
                                                    <br>
                                                    <span uname="<?=$user['user_display_name']?>" uid="<?=$user['user_id']?>" from="<?=$user['user_busy_from'] ? \lib\date::format($user['user_busy_from'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']].' H:i') : ''?>" to="<?=$user['user_busy_to'] ? \lib\date::format($user['user_busy_to'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']].' H:i') : ''?>" onclick="workScheduleSetUserIsBusyForm($(this))" data-toggle="modal" data-target="#setBusyModal" class="label label-pill label-primary busy_item"> <i class="fa fa-clock-o" aria-hidden="true"></i> Tạm ẩn: <small class="busy_from_display"><?=$user['user_busy_from'] ? \lib\date::format($user['user_busy_from'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']].' H:i') : 'N/A'?></small> - <small class="busy_to_display"><?=$user['user_busy_to'] ? \lib\date::format($user['user_busy_to'], $CMS->vars['dateformat_php'][$CMS->vars['date_format']].' H:i') : 'N/A'?></small></span>
                                                </td>
                                                <td>
                                                    <div class="form-group row">
                                                        <div class="col-xs-12">
                                                            <div class="input-group">
                                                                <input name="slots[<?=$user['user_id']?>][]" class="form-control" type="number" id="slots_<?=$user['user_id']?>" value="<?=$user['user_booking_slots']?>">
                                                                <div class="input-group-btn">
                                                                    <button onclick="workScheduleSetSlotForStaff($(this), '<?=$user['user_id']?>', $('#slots_<?=$user['user_id']?>'))" class="btn btn-default" type="button"><i class="fa fa-save"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?=\lib\input::arrayValue($user['store'],'name', '')?>
                                                </td>
                                                <?foreach ($tpl->shift_work  as $item) {?>
                                                    <td><input class="shift_work_row shift_work_col_<?=$item['shift_work_id']?>" type="checkbox"  name="staff[<?=$user['user_id']?>][]" id="shift_work_<?=$user['user_id']?>_<?=$item['shift_work_id']?>" value="<?=$item['shift_work_id']?>"/></td>
                                                <?}?>
                                            </tr>
                                        <?}?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </figure>
    </section>
</form>

<div class="modal fade"
     id="setBusyModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="setBusyModalFrm">
                <input type="hidden" name="user_id" value="0">
                <div class="modal-header">
                    <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                        <i class="font-icon-close-2"></i>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Tạm ẩn nhân viên <span id="uname"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="busyFrom">Từ: </label>
                                <input type="text" class="form-control" id="busyFrom"  name="user_busy_from" placeholder="Từ">
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="busyFrom">Đến: </label>
                                <input type="text" class="form-control" id="busyTo" name="user_busy_to" placeholder="Đến">
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-rounded btn-primary" onclick="workScheduleSetUserIsBusy();">Đồng ý</button>
                </div>
            </form>
        </div>
    </div>
</div><!--.modal-->

<!-- Clone workschedule-modal -->
<div id="cloneWorkScheduleModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <form id="cloneWorkScheduleForm" action="">
                <div class="modal-header">
                    <h4 class="modal-title">Sao chép dữ liệu từ nguồn</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong>Lưu ý: </strong>
                        <ul>
                            <li>- Tính năng này chỉ tự động điền dữ liệu. Vui lòng kiểm tra dữ liệu sau khi sao chép và nhấn "Cập nhật" để hoàn tất.</li>
                            <li>- Dữ liệu sau khi sao chép có thể không hoàn toàn khớp với dữ liệu gốc. Do ảnh hưởng của lịch trực từ các cửa hàng khác trong cùng khoảng thời gian.</li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ws_date_clone">Ngày</label>
                                <input autocomplete="off" id="ws_date_clone" name="ws_date_clone" class="ws_date_clone date-picker form-control" value="<?=$tpl->data['work_schedule_date']?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ws_city_clone">Tỉnh/thành phố</label>
                                <select name="ws_city_clone" id="ws_city_clone" class="form-control select2"  onchange="workScheduleLoadStores($(this).val(), $('#ws_store_clone'))">
                                    <?foreach($tpl->cities as $city) {?>
                                        <option value="<?=$city['id']?>"><?=$city['name']?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ws_store_clone">Kho/Cửa hàng</label>
                                <select name="ws_store_clone" id="ws_store_clone" class="form-control select2">
                                    <?foreach($tpl->store as $store) {?>
                                        <option value="<?=$store['store_id']?>"><?=$store['store_name']?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="workScheduleClone()">Sao chép</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Clone workschedule-modal -->

<script language="javascript">
    $(document).ready(function () {
        workScheduleTblInit();
        $(".work_schedule_date").datetimepicker({ format:'<?=$CMS->vars['date_format']?>', minDate: 'now'});
        $(".work_schedule_date").mask("<?=$tpl->maskFormat?>", {placeholder: "<?=$tpl->maskPlaceHolder?>"});

        $("#busyFrom, #busyTo").datetimepicker({ format:'<?=$CMS->vars['date_format']?> HH:mm'});
        $("#busyFrom, #busyTo").mask("<?=$tpl->maskFormat?> 00:00", {placeholder: "<?=$tpl->maskPlaceHolder?> __:__"});
        workScheduleLoadData($('#work_schedule_date').val(),$('#store_id').val());

        $("#city_id").trigger("change");
        $("#ws_city_clone").trigger("change");

        $(".bs-tooltip-trigger").tooltip();
    });
</script>