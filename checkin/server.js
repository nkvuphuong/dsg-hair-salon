const express = require("express")();
const http = require("http").Server(express);
const io = require("socket.io")(http);

io.on("connection", function (socket) {
    console.log("User connected");

    socket.on("disconnect", function () {
        console.log("User disconnected");
    });

    //Kích hoạt khi có đơn hàng cần đánh giá (Từ acp, hoặc booking)
    socket.on("open_rating_order", function (data) {
        //Phát sự kiện đến trang checkin để mở giao diện đánh giá
        socket.broadcast.volatile.emit("open_rating_order", data);
    });

    //Kích hoạt khi hoàn tất đánh giá
    socket.on("complete_rating_order", function (data) {
        //Phát sự kiện đến acp && pos
        io.emit("complete_rating_order", data);
    });

    //Check để biết trang checkin đã nhận được yêu cầu mở đánh giá chưa
    socket.on("opened_rating_order", function (data) {
        socket.broadcast.volatile.emit("opened_rating_order", data);
    })

    //Khi có đơn hàng mới
    socket.on("new_order", function (ordId) {
        socket.broadcast.volatile.emit("new_order", ordId);
    })
});

http.listen(3000);