const socket = io.connect(":3000");

socket.on('open_rating_order', function (data) {
    let storeId = +$("#store_id").val();

    if (+data.ord.storeId && +storeId && +data.ord.storeId == +storeId) {
        $(".display-block").hide();
        $("#confirm-screen").show();
        $("#ord_id").val(data.ord.ord_id);

        $("#ord-items").html(function () {
            let html = "";
            data.ord.items.forEach((item) => {
                html += SocketIOClient.itemTpl(item);
            })

            return html;
        });

        $("#ord-amount").html(data.ord.amount);
        $("#ord-total").html(data.ord.total);
        $("#ord-discount").html(data.ord.discount);
        $("#ord-tax").html(data.ord.tax);
        $("#ord-phone").html(data.ord.phone);
        $("#ord-id").val(data.ord.id);
        $("#order-rating").modal("hide");
        // SocketIOClient.initRatingValue();

        socket.emit("opened_rating_order", data);
    }
});

var SocketIOClient = {
    rate: function () {
        let value = +$("#rating-value").val();
        let ordId = +$("#ord-id").val();

        if (!value) {
            PNotify.alert("Chưa chọn đánh giá");
            return false;
        }

        $(".feedback-modal").loading({
            theme: 'dark',
            message: 'Đang xử lý...'
        });

        $.ajax({
            url: "../?act=rate",
            data: {
                id: ordId,
                value: value
            },
            dataType: 'json',
            success: function (res) {
                if (res.status == 'success') {
                    socket.emit("complete_rating_order", res.data);
                } else {
                    PNotify.error(res.msg);
                }

                $("#FeedbackChecksWrap").hide();
                $("#FeedbackthankyouWrap").show();

                setTimeout(function() {
                    $("#order-rating").modal("hide")
                    $("#FeedbackChecksWrap").show();
                    $("#FeedbackthankyouWrap").hide();
                    $(".display-block").hide();
                    $("#welcome-screen").show();
                }, 5000);
            },
            error: function (err) {
                PNotify.error("Có lỗi xảy ra !");
            },
            complete: function () {
                $(".feedback-modal").loading("stop");
            }
        });

        return false;
    },
    itemTpl: function (item) {
        return `
            <div class="present-item">
                <p>Dịch vụ: ${item.name}  <span class="text-green"><b>(${item.price})</b></span></p>
                <p>Thời gian: <b>(${item.bookingTime})</b>  -  Nhân viên: <b>${item.staff ? item.staff.name : 'N/A'}</b></p>
            </div>
        `;
    },
    chooseRatingValue: function(obj) {
        $(".feebback-wrap").removeClass("active");
        $("#FeedbackChecksWrap").show();
        $("#FeedbackthankyouWrap").hide();
        obj.addClass("active");
        $("#rating-value").val(+obj.attr("value"));
    },
    initRatingValue: function(defaultVal = 100) {
        $(".feebback-wrap[value='" + defaultVal + "']").trigger("click");
    },
};

function openFullscreen() {
    if (!document.fullscreenElement &&    // alternative standard method
        !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) {
            document.documentElement.msRequestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    }
}

$(document).ready(function() {
    // SocketIOClient.initRatingValue();
})