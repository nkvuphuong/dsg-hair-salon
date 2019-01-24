<div class="box-typical box-typical-info" style="min-height: 500px;padding: 0 15px 15px;">
  <h5 class="m-t-md with-border"><?=$CMS->lang['title_manage_ship_fee'];?></h5>
  <div class="row">
    <div class="col-lg-12 col-md-12">
      <section class="box-typical scrollable" style="border: none;">
        <div class="box-typical-body">
            <div class="table-responsive add_table" style="margin: 0;">
                <div class="clearfix ship_fee_loading" style="display: none;"><img src="/acp/images/fb-loading.gif"> Loading...</div>
                <div id="ship_fee_content" class="clearfix">
                  <?=\core\ezy::render('show_shipping_fee_data', 'product');?>
                </div>
                <div class="fuction_table" style="border: none;">
                  <div class="pull-left">
                      <a class="btn btn-green pointer" onclick="openNewShipFee('<?=$tpl->data['product_id'];?>');"><?=$CMS->lang['ship_fee_new'];?></a>
                  </div>
              </div>
            </div>
        </div><!--.box-typical-body-->
      </section>
    </div>
  </div>
</div>

<script type="text/javascript">
  /*
  * List ship fee ajax
  */
  if( typeof loadShipFee != 'function' )
  {
    function loadShipFee(productId)
    {
      $('.ship_fee_loading').show();
      $('#ship_fee_content').html('');
      
      // Ajax
      $.ajax({
        type: "post",
        url: site_root_domain +'/?site=product&subact=list_ship_fee&product_id='+productId,
        data: {'product_id': productId, 'is_ajax': 1},
        dataType: 'JSON',
        berforsend: function()
        {
          waitingDialog.show('Please wait...');
        }, 
        success: function (obj) 
        {
          if( obj.status == 'success' )
          {
            $('#ship_fee_content').html(obj.data);
          }
          else
          {
            $('#ship_fee_content').html('...');
          }

          $('.ship_fee_loading').hide();
        },
        error: function (html) 
        {
          $('.ship_fee_loading').hide();
          $('#ship_fee_content').html('...');
        },
      });
    }
  }

  /*
  * Delete ship fee ajax
  */
  if( typeof openDeleteShipFee != 'function' )
  {
    function openDeleteShipFee(shipId, productId, textShow)
    {
      if( !shipId )
      {
        return false;
      }
      var textShow = textShow ? textShow : 'Do you want to delete it?';
      swal({
        title: confirm_alert_title,
        text: textShow,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: cms_lang['gnotice_ok'],
        cancelButtonText: cms_lang['gnotice_cancel'],
        closeOnConfirm: true,
        closeOnCancel: true
      }).then(function(){
        // Ajax
        $.ajax({
          type: "post",
          url: site_root_domain +'/?site=product&subact=delete_ship_fee&ship_id='+shipId+'&is_ajax=1',
          data: {'ship_id': shipId, 'is_ajax': 1},
          dataType: 'JSON',
          berforsend: function()
          {
            waitingDialog.show('Please wait...');
          }, 
          success: function (obj) 
          {
            swal('Notice!', obj.msg, obj.status == 'success' ? 'success' : 'warning');
            loadShipFee(productId);
          },
          error: function (html) 
          {
            swal('Notice!', 'Error, please try again', 'warning');
            loadShipFee(productId);
          },
        });
      });
    }
  }

  /*
  * Edit ship fee ajax
  */
  if( typeof openEditShipFee != 'function' )
  {
    function openEditShipFee(shipId, productId)
    {
      // Show modal
      var shipFee = $('#editShipFeeModalCenter');
          shipFee.find('.loading_content').show();
          shipFee.find('.main_content').hide();
          shipFee.find('#ship_id').val(shipId).attr('value', shipId);
          shipFee.find('#product_id').val(productId).attr('value', productId);
          shipFee.find('.btn_edit_shipfee').prop('disabled', true);
          shipFee.modal('show');

      // Ajax
      $.ajax({
        type: "post",
        url: site_root_domain +'/?site=product&subact=load_ship_fee&ship_id='+shipId,
        data: {'ship_id': shipId, 'is_ajax': 1},
        dataType: 'JSON',
        berforsend: function()
        {
          waitingDialog.show('Please wait...');
        }, 
        success: function (obj) 
        {
          if( obj.status == 'success' )
          {
            shipFee.find('#ship_type_service').text(obj.data.shipping_type);
            shipFee.find('#ship_location').text(obj.data.shipping_location);
            shipFee.find('#ship_price').val(obj.data.ship_price).attr('ship_price', obj.data.ship_price);
            shipFee.find('#ship_price_extra').val(obj.data.ship_price_extra).attr('ship_price_extra', obj.data.ship_price_extra);
          }
          else
          {
            alertText('Error, please try again', 'warning');
            shipFee.modal('hide');
          }

          shipFee.find('.loading_content').hide();
          shipFee.find('.main_content').show();
          shipFee.find('.btn_edit_shipfee').prop('disabled', false);
        },
        error: function (html) 
        {
          alertText('Error, please try again', 'warning');
          shipFee.modal('hide');
        },
      });
    }
  }

  if( typeof doEditShipFee != 'function' )
  {
    function doEditShipFee()
    {
      // Inputs
      var shipFee = $('#editShipFeeModalCenter');
          shipFee.modal('hide');
      var data = {};
          data.ship_id = shipFee.find('#ship_id').val();
          data.ship_price = shipFee.find('#ship_price').val();
          data.ship_price_extra = shipFee.find('#ship_price_extra').val();
          data.is_ajax = 1;
      var productId = shipFee.find('#product_id').val();

      // Ajax
      $.ajax({
        type: "post",
        url: site_root_domain +'/?site=product&subact=edit_ship_fee&ship_id='+data.ship_id+'&is_ajax=1',
        data: data,
        dataType: 'JSON',
        berforsend: function()
        {
          waitingDialog.show('Please wait...');
        }, 
        success: function (obj) 
        {
          alertText(obj.msg, obj.status == 'success' ? 'success' : 'warning');
          loadShipFee(productId);
        },
        error: function (html) 
        {
          alertText('Error, please try again', 'warning');
          loadShipFee(productId);
        },
      });
    }
  }

  /*
  * New ship fee ajax
  */
  if( typeof openNewShipFee != 'function' )
  {
    function openNewShipFee(productId)
    {
      // Show modal
      var shipFee = $('#addShipFeeModalCenter');
          shipFee.find('.loading_content').show();
          shipFee.find('.main_content').hide();
          shipFee.find('#product_id').val(productId).attr('value', productId);
          shipFee.find('.btn_edit_shipfee').prop('disabled', true);
          shipFee.modal('show');
    }
  }
  if( typeof doAddShipFee != 'function' )
  {
    function doAddShipFee()
    {
      // Inputs
      var shipFee = $('#addShipFeeModalCenter');
          shipFee.modal('hide');
      var data = shipFee.find('#form_add_ship_fee').serializeArray();
          data.push({name: "is_ajax", value: "1"});
      var productId = shipFee.find('#product_id').val();

      // Ajax
      $.ajax({
        type: "post",
        url: site_root_domain +'/?site=product&subact=add_ship_fee&product_id='+productId+'&is_ajax=1',
        data: data,
        dataType: 'JSON',
        berforsend: function()
        {
          waitingDialog.show('Please wait...');
        }, 
        success: function (obj) 
        {
          alertText(obj.msg, obj.status == 'success' ? 'success' : 'warning');
          loadShipFee(productId);
        },
        error: function (html) 
        {
          alertText('Error, please try again', 'warning');
          loadShipFee(productId);
        },
      });
    }
  }

  $(document).ready(function(){
  });
</script>