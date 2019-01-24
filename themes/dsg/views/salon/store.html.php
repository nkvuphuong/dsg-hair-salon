<section class="p-contact-us">
    <!-- tpl main -->
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2 style="margin-bottom: 15px;"><?=\core\ezy::tpl("title_header");?></h2>
            </div>
        </div>
        <? if($tpl->store) {?>
            <div class="row section-title-wrapper">
                <div class="visible-md visible-lg col-md-4 col-lg-3">	</div>
                <div class="col-md-10 col-lg-8">

                    <div class="row">
                        <div class="col-sm-4 col-md-4">
                            <div class="form-group">
                                <select class="form-control input-with-right-icon" id="city_id">
                                    <?if( !empty($tpl->optionCity_by_salon) ){?>
                                        <?foreach( $tpl->optionCity_by_salon as $data ){?>
                                            <option <?= $tpl->store['city_id']==$data['city_id'] ? "selected" : "" ?> value="<?=$data['city_id'];?>"><?=$data['city_name'];?></option>
                                        <?}  } ?>
                                </select>
                                <span class="fa fa-caret-down right-icon-input"></span>
                            </div>
                        </div>

                        <div class="col-sm-4 col-md-4">
                            <div class="form-group">

                                <select class="form-control input-with-right-icon" name="salon_select" value="" id="liststore">
                                    <option>Hệ thống Salon</option>

                                </select>
                                <span class="fa fa-caret-down right-icon-input"></span>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="liststore_html">
                <div class="row">
                    <div class="col-md-12">
                        <div class="contact-brand-container">
                            <div class="row">
                                <div class="col-md-6 col-lg-5 contact-brand-item">
                                    <img class="full-width" src="images/brand-1.jpg">

                                    <h3><?=$tpl->store['store_name']?></h3>
                                    <div class="icon-div">
                                        <p>
                                            <b><?=$tpl->store['store_address']?></b>
                                        </p>
                                    </div>
                                    <div class="icon-div phone">
                                        <p>ĐT : <a href="tel:<?=$tpl->store['store_phone']?>"><?=$tpl->store['store_phone']?></a></p>
                                    </div>
                                    <a href="/booking/" class="btn btn-md btn-primary" style="margin-left:25px;margin-top:10px;height: 40px;line-height: 40px;font-size: 16px">đặt lịch hẹn</a>
                                </div>
                                <div class="col-md-6 col-lg-7 contact-brand-item">
                                    <?=html_entity_decode($tpl->store['googlemap_code'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?} else {?>

        <?}?>





    </div>
</section>
<script type="text/javascript">

    $(document).ready(function(){
        var city_id = $("#city_id").val();
        get_storebycity(city_id, "<?=$tpl->selected_store;?>");

    });
    $("#city_id").on('change',function(){
        var city_id  = $("#city_id").val();
        get_storebycity(city_id);
    });
    function get_storebycity(id, storeId = null)
    {
        console.log(storeId);
        $("#liststore").html("");
        $.ajax({
            type: "post",
            url: "/salon/optionstore/typeshow-1/id-"+id,
            data: {
                storeId: storeId
            },
            success: function(response)
            {
                $("#liststore").html(response);
            }
        });
    }
    $("#liststore").on("change",function(){
        var store_id = $(this).val();
        if(store_id) {
            var slug = $(this).find("option[value='" + store_id + "']").attr("slug");
            window.location.href = "/"+ slug +"-sl"+store_id;
        } else {
            alert("Vui lòng chọn salon");
        }

    });


</script>



