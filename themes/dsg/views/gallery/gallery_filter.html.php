
<div class="row">
    <div class="col-sm-3 col-md-3">
        <div class="form-group">
            <select class="form-control input-with-right-icon" id="cate_hair" defaultvalue="<?=$tpl->cat_cate;?>">
                <option value="">Loại tóc</option>
                <option value="1">Nam</option>
                <option value="0">Nữ</option>
            </select>
            <span class="fa fa-caret-down right-icon-input"></span>
        </div>
    </div>
    <div class="col-sm-3 col-md-3">
        <div class="form-group">
            <select class="form-control input-with-right-icon" id="style_hair" defaultvalue="<?=$tpl->cat_id;?>">
                <option value="">Kiểu tóc</option>

            </select>
            <span class="fa fa-caret-down right-icon-input"></span>
        </div>
    </div>
    <div class="col-sm-3 col-md-3">
        <div class="form-group">
            <select class="form-control input-with-right-icon" id="color_hair"  defaultvalue="<?=$tpl->color_hair;?>">
                <option value="">Màu tóc</option>
                <?=$CMS->vars['list_hair_color_html'];?>
            </select>
            <span class="fa fa-caret-down right-icon-input"></span>
        </div>
    </div>
</div>

<script type="text/javascript">
    var def_cat_cate = $("#cate_hair").attr("defaultvalue");
    var def_style_hair = $("#style_hair").attr("defaultvalue");
    var def_color_hair = $("#color_hair").attr("defaultvalue");

    $(document).ready(function(){
        if(def_cat_cate != "")
        {

            $("#cate_hair").find("option[value="+def_cat_cate+"]").attr("selected",true);
        }
        if(def_color_hair != "")
        {

            $("#color_hair").find("option[value="+def_color_hair+"]").attr("selected",true);
        }


        get_stylehair(def_cat_cate);
    });
    $("#cate_hair").on("change",function(){
        var cate_hair = $("#cate_hair").val();
        get_stylehair(cate_hair);

    });
    $("#color_hair, #style_hair").on("change",function(){
        list_hair(cate_hair);
    });


    $("#search_hair").on("click",function(){
        var style_hair = $("#style_hair").val();
        if(style_hair > 0)
        {
            window.location = "/gallery/list/"+style_hair;
        }

    });

    function list_hair()
    {
        var style_hair = $("#style_hair").val();
        var color_hair = $("#color_hair").val();
        window.location = "/gallery/list_dsg/style-hair-"+style_hair+"/color-hair-"+color_hair;

    }

    function get_stylehair(id) {
        $("#style_hair").html("<option>Loading ....</option>");
        var color_hair = $("#color_hair").val();
        $.ajax({
            type: "post",
            url: "/gallery/get_stylehair/id-" + id,            data: {},
            success: function (response) {
                $("#style_hair").html(response);

                if (def_style_hair != "") {
                    $("#style_hair option[value='" + def_style_hair + "']").prop("selected", true);
                }
            },
            complete: function () {
            }
        });
    }

</script>