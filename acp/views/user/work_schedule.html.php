<section class="add_table">
    <div class="data_table">
        <div class="table table_cus table-responsive" style="border-top: none;">
            <table id="example" class="display" cellspacing="0" width="100%" style="margin-top:0px;">
                <thead>
                <tr>
                    <th data-orderable="false" data-sortable="false" aria-label="" style="margin:0px;padding:0px;"></th>
                    <th  width="5%" data-sortable="false" data-orderable="false" aria-label="">
                        <div class="checkbox checkbox-only" onclick="javascript:form_checkall('form_user_group');" id="checkall">
                            <input type="checkbox" name="all" onmouseover="on_mouse=0;" onmouseout="on_mouse=1;">
                            <label for="checkall"></label>
                        </div>
                    </th>
                    <th>Name</th>
                    <th>Root</th>
                    <th>Admin</th>
                    <th>User</th>
                    <th style="text-align:center" data-sortable="false" data-orderable="false">
                        <button class="btn btn-success"><?=$CMS->lang['act_create_shift_work']?></button>
                    </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</section>