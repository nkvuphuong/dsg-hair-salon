<? $option_m_manufacture_parent = "<option value=''>{$CMS->lang['select']}</option>";
        $manufacture = $CMS->manufacture->getParent();
        foreach ($manufacture as $m) {
            if ($m['manufacture_id'] == $m_manufacture_parent) {
                $option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' selected>{$m['manufacture_name']}</option>";
            } else {
                $option_m_manufacture_parent .= "<option value='{$m['manufacture_id']}' >{$m['manufacture_name']}</option>";
            }
        }
        $option_m_status = "";
        $m_status = 1;
        for ($i = 1; $i >= 0; $i--) {
            if ($i == $m_status) {
                $option_m_status .= "<option value='{$i}' selected>{$CMS->lang['m_status_0'.$i]}</option>";
            } else {
                $option_m_status .= "<option value='{$i}'>{$CMS->lang['m_status_0'.$i]}</option>";
            }
        }
?>

    <div id="box_add_manufacture" class="popup_add_manufacture mfp-hide">
      <p class="title_add_manufacture roboto_bold" style="font-size: 20px; text-align: center; text-transform: uppercase;"><?=$CMS->lang['manufacture_add'];?></p>
      <form id="add_manufacture_form" name="add_manufacture_form">
        <ul class="list_field_supplier">
            <li style="min-height: 0px;"><p class="manu_error_msg" style="display:none;"></p></li>
       
            <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
         <fieldset class="form-group">
            <label class="form-label" ><?=$CMS->lang['m_manufacture_parent'];?> </label>
              <div class="typeahead-field"> 
                <span class="typeahead-query">
                 
                <select class="form-control select2" name="m_manufacture_parent" id="m_manufacture_parent" >
                  <?=$option_m_manufacture_parent;?>
                </select>
              </span>
            </div>
          </fieldset>
            </li>

        <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12">
          <fieldset class="form-group">
            <label class="form-label"  ><?=$CMS->lang['m_name'];?> <span style="color:red">(*)</span></label>
             <div class="typeahead-field"> 
                <span class="typeahead-query">
                 
                <input class="form-control " type="text" name="m_name" id="m_name" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['m_name_err'];?>" value="<?=$m_name;?>">
                <input type="hidden" name="m_manufacture_id" id="m_manufacture_id" />
              </span>
            </div>
          </fieldset>
        </li>

        <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
          <fieldset class="form-group ">
            <label class="form-label"><?=$CMS->lang['m_code'];?></label>
             <div class="typeahead-field"> 
                <span class="typeahead-query">
                <input class="form-control " type="text" name="m_code" id="m_code"   value="<?=$m_code;?>">
              </span>
            </div>
          </fieldset>

        </li>
       
        <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
          <fieldset class="form-group ">
            <label class="form-label"><?=$CMS->lang['m_status'];?></label>
             <div class="typeahead-field"> 
               <span class="typeahead-query">
                <select class="form-control select2" name="m_status" id="m_status" data-validation="[NOTEMPTY]" data-validation-message="<?=$CMS->lang['m_status_err'];?>">
                  <?=$option_m_status;?>
                </select>
              </span>
            </div>
          </fieldset>
        </li>
        <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
          <fieldset class="form-group ">
            <label class="form-label"><?=$CMS->lang['m_description'];?> <span style="color:red">(*)</span></label>
               <div class="typeahead-field"> 
               <span class="typeahead-query">
                <textarea rows="3" class="form-control" name="m_description" id="m_description"><?=$m_description;?></textarea>
              
            </div>
          </fieldset>
        </li> 

        <li class="col-xl-6 col-lg-6 col-sm-6 col-xs-12"> 
          <li class="col-xl-12 col-lg-12 col-sm-12 col-xs-12" style="min-height: auto;">
            <fieldset class="form-group">
              <div class="typeahead-field"> 
                <span class="typeahead-query change_action_manufacture">
                  <input class="btn btn_add_manufacture" type="button" value="<?=$CMS->lang['manufacture_add'];?>"/>
                </span>
              </div>
            </fieldset>
          </li>
        
        </ul>
          <a class="btn_add_manufacture" style="display:none">btn_add_manufacture</a>
                  <a class="btn_edit_do_manufacture" style="display:none">btn_edit_do_manufacture</a>
          
      </form>
    </div>
    <script>
                 $(document).ready(function(){
                    validate_form_custom("#box_add_manufacture",".act_popup_btn_validate","box_custom");
                });
    </script>  