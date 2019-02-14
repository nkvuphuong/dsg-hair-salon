<section class="add_table main_form have_tab">
    <figure class="box-typical box-typical-padding border">
        <div class="row">
            <div class="form-group col-xl-12">
                <div class="form-control-wrapper">
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->yesterday?>" end="<?=$tpl->yesterday?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Hôm qua
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->today?>" end="<?=$tpl->today?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Hôm nay
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->last_week[0]?>" end="<?=$tpl->last_week[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tuần trước
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->this_week[0]?>" end="<?=$tpl->this_week[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tuần này
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->last_month[0]?>" end="<?=$tpl->last_month[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tháng trước
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->this_month[0]?>" end="<?=$tpl->this_month[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Tháng này
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->last_year[0]?>" end="<?=$tpl->last_year[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Năm trước
                    </button>
                    <button type="button" class="btn btn-inline btn-success change_time"
                            start="<?=$tpl->this_year[0]?>" end="<?=$tpl->this_year[1]?>" onclick="LibExt.setDatetimeStartEndInput($(this), $('#time_from'), $('#time_to'))">Năm này
                    </button>
                </div>
            </div>

            <div class="form-group col-xl-12 box_date_change">
                <div class="row">
                    <div class="col-xl-2">
                        <label class="form-label">Từ ngày</label>
                        <div class="input-group">
                            <input type="text" name="time_from" id="time_from"
                                   class="form-control date-picker" placeholder="Time from"
                                   value="">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <label class="form-label">Đến ngày</label>
                        <div class="input-group">
                            <input type="text" name="time_to" id="time_to"  class="form-control date-picker"
                                   placeholder="Time to" value="">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <label class="form-label" style="height: 18px;"></label>
                        <div class="input-group">
                            <button type="button"
                                    class="btn btn-primary btn_view_report">Lọc
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </figure>

</section>

<section class="add_table main_form have_tab">
    <figure class="heading">
        <h3><span id="detail_title">Thông tin</span>: <span class="time_title"></span></h3>
    </figure>
    <figure class="box-typical border">
        <table id="table-edit" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th width="1">
                    #
                </th>
                <th>Name</th>
                <th>Description</th>
                <th class="table-icon-cell">
                    <i class="font-icon font-icon-heart"></i>
                </th>
                <th class="table-icon-cell">
                    <i class="font-icon font-icon-comment"></i>
                </th>
                <th width="120">Date Created</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>Last quarter revene</td>
                <td class="color-blue-grey-lighter">Revene for last quarter in state America for year 2013, whith...</td>
                <td class="table-icon-cell">5</td>
                <td class="table-icon-cell">24</td>
                <td class="table-date">6 minutes ago</td>
                <td class="table-photo">
                    <img src="img/photo-64-1.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Nicholas<br/>Barrett">
                </td>
            </tr>
            <tr>
                <td>1</td>
                <td>Last quarter revene</td>
                <td class="color-blue-grey-lighter">Revene for last quarter in state America for year 2013, whith...</td>
                <td class="table-icon-cell">5</td>
                <td class="table-icon-cell">24</td>
                <td class="table-date">6 minutes ago</td>
                <td class="table-photo">
                    <img src="img/photo-64-1.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Nicholas<br/>Barrett">
                </td>
            </tr>
            <tr>
                <td>1</td>
                <td>Last quarter revene</td>
                <td class="color-blue-grey-lighter">Revene for last quarter in state America for year 2013, whith...</td>
                <td class="table-icon-cell">5</td>
                <td class="table-icon-cell">24</td>
                <td class="table-date">6 minutes ago</td>
                <td class="table-photo">
                    <img src="img/photo-64-1.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Nicholas<br/>Barrett">
                </td>
            </tr>
            <tr>
                <td>1</td>
                <td>Last quarter revene</td>
                <td class="color-blue-grey-lighter">Revene for last quarter in state America for year 2013, whith...</td>
                <td class="table-icon-cell">5</td>
                <td class="table-icon-cell">24</td>
                <td class="table-date">6 minutes ago</td>
                <td class="table-photo">
                    <img src="img/photo-64-1.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Nicholas<br/>Barrett">
                </td>
            </tr>
            <tr>
                <td>1</td>
                <td>Last quarter revene</td>
                <td class="color-blue-grey-lighter">Revene for last quarter in state America for year 2013, whith...</td>
                <td class="table-icon-cell">5</td>
                <td class="table-icon-cell">24</td>
                <td class="table-date">6 minutes ago</td>
                <td class="table-photo">
                    <img src="img/photo-64-1.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Nicholas<br/>Barrett">
                </td>
            </tr>
            <tr>
                <td>1</td>
                <td>Last quarter revene</td>
                <td class="color-blue-grey-lighter">Revene for last quarter in state America for year 2013, whith...</td>
                <td class="table-icon-cell">5</td>
                <td class="table-icon-cell">24</td>
                <td class="table-date">6 minutes ago</td>
                <td class="table-photo">
                    <img src="img/photo-64-1.jpg" alt="" data-toggle="tooltip" data-placement="bottom" title="Nicholas<br/>Barrett">
                </td>
            </tr>
            </tbody>
        </table>
    </figure>
</section>
