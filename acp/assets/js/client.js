const socket = io.connect(":3000");
const socketToken = LibExt.getCookie('PHPSESSID');
var openRatingTimeout;

var SocketIOClient = {
    openRating: function (ordId) {
        $.ajax({
            url: site_root_domain + "/?site=order&subact=get_info_rating&id="+ordId,
            dataType: 'json',
            success: function(res) {
                if(res.status == 'success') {
                    socket.emit("open_rating_order", {ord: res.data, token: socketToken});

                    openRatingTimeout = setTimeout(function () {
                        pNotifyACP("Không kết nối được thiết bị đánh giá");
                    }, 5000);
                }
                else {
                    alert(res.msg);
                }
            }
        });
    },
    openedRating: function (data) {
        pNotifyACP("Đã mở đánh giá " + data.name, "info");
    }
};

//Check để biết trang checkin đã nhận được yêu cầu mở đánh giá chưa
socket.on("opened_rating_order", function (data) {
    if (openRatingTimeout) {
        clearTimeout(openRatingTimeout);
    }

    if (data.token == socketToken) {
        SocketIOClient.openedRating(data.ord);
    }
});


//Có đơn hàng mới
socket.on("new_order", function (ordId) {
    pNotifyACP("<a href='" + site_root_domain + "/?site=order&act=show&id=" + ordId + "'>Có đơn hàng mới #" + ordId + "</a>", "info");
});

//Có đánh giá từ khách hàng
socket.on("complete_rating_order", function (data) {
    pNotifyACP("Đã đánh giá đơn hàng " + data.name, "info");
    $(".rating-action-" + data.id).remove();
});