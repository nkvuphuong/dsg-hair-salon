import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {Bill, PrintingBill} from "../../services/bill/bill";
import {BillService} from "../../services/bill/bill.service";
import {ToastrService} from "ngx-toastr";
import {customDateTimeFormat, isOnline, onlyNumber, setMaxLengPhoneInput} from "../../lib/script";
import {IndexedDbService} from "../../services/indexed-db/indexed-db.service";
import {ConnectNetworkService} from "../../services/connect-network/connect-network.service";
import {Observable} from "rxjs/Observable";
import {APPGLOBAL} from "../../global";
import {Staff} from "../../services/staff/staff";
import {Socket} from 'ng-socket-io';
import {CookieService} from "ngx-cookie-service";
import {OrderService} from "../../services/order/order.service";

@Component({
    selector: 'app-print-bill',
    templateUrl: './print-bill.component.html',
    styleUrls: ['./print-bill.component.css']
})
export class PrintBillComponent implements OnInit {

    bills: PrintingBill[] = [];
    @Input() selectedBill: PrintingBill;
    page: number = 1;
    pageSize: number = 10;
    totalRows: number = 0;
    printBillLoading: boolean = false;
    @Input() globalVariables: any;
    @Output() onSyncBill = new EventEmitter<PrintingBill>();
    @Input() countSyncableBills: number = 0;
    appConfig: any = APPGLOBAL;
    @Input() currentStaff: Staff;
    filterData: {
        id: string;
        phone: string;
        time: string;
        amount: string;
        status: string;
    };
    openRatingTimeout;
    socketToken;
    @Input() socket: Socket;

    constructor(
        public billService: BillService,
        private toastr: ToastrService,
        private idbService: IndexedDbService,
        public connectNetworkService: ConnectNetworkService,
        private cookieService: CookieService,
        private orderService: OrderService
    ) {
    }

    ngOnInit() {
        var _self = this;

        this.filterData = {
            id: "",
            phone: "",
            time: "",
            amount: "",
            status: "",
        };

        $("#printBillModal").on('hide.bs.modal', function () {
            _self.getPrintingBills();
        });

        $("#printBillModal").on('show.bs.modal', function () {
            _self.getPrintingBills();
        });

        $("#printBillModalNeedSync").on('hide.bs.modal', function () {
            _self.getPrintingBills(1, true);
        });

        $("#printBillModalNeedSync").on('show.bs.modal', function () {
            _self.getPrintingBills(1, true);
        });

        this.socketToken = this.cookieService.get('PHPSESSID');

        //Check để biết trang checkin đã nhận được yêu cầu mở đánh giá chưa
        this.socket.fromEvent<any>("opened_rating_order").subscribe(data => {

            this.printBillLoading = false;

            if (this.openRatingTimeout) {
                clearTimeout(this.openRatingTimeout);
            }

            if (data.token == this.socketToken) {
                this.toastr.info("Đã mở giao diện đánh giá dịch vụ " + data.ord.name);
            }
        });

        this.socket.fromEvent<any>("new_order").subscribe(ordId => {
            this.toastr.info("<a href='" + APPGLOBAL.ROOT_DOMAIN + "/acp/?site=order&act=show&id=" + ordId + "'>Có đơn hàng mới #" + ordId + "</a>", null, {enableHtml: true});
        });

        //Khi co don hang da danh gia xong
        this.socket.fromEvent<any>("complete_rating_order").subscribe(data => {
            this.toastr.info("<a href='" + APPGLOBAL.ROOT_DOMAIN + "/acp/?site=order&act=show&id=" + data.id + "'>Đã đánh giá đơn hàng " + data.name + "</a>", null, {enableHtml: true});
            _self.getPrintingBills(this.page, false, false);
        });
    }

    getPrintingBills(page: number = 1, loadOffline: boolean = false, loading = true): void {
        this.printBillLoading = loading ? true : false;
        if (isOnline() && !loadOffline) {
            this.billService.getPrintingBills(this.currentStaff.storeId, this.filterData, this.pageSize, page).subscribe(
                res => {
                    this.totalRows = res.totalRows;
                    this.page = page;
                    return this.bills = res.bills;
                },
                err => {
                    this.toastr.error("Lỗi khi tải hóa đơn");
                    console.log(err);
                },
                () => {
                    this.printBillLoading = false;
                }
            );
        } else {

            let range = null;
            let indexName = "timestamp_idx";
            let direction = false;

            if (loadOffline) {
                range = IDBKeyRange.upperBound(-1);
                indexName = "id_idx";
                direction = true;
            }

            this.idbService.all("printing_bills", indexName, direction, range, this.pageSize, page).subscribe(
                res => {
                    this.bills = res;
                    this.page = page;
                },
                err => {
                    this.toastr.error("Lỗi khi tải hóa đơn offline");
                    console.log(err);
                },
                () => {
                    this.printBillLoading = false;
                }
            )

            this.idbService.count("printing_bills", indexName, range).subscribe(
                totalRows => {
                    this.totalRows = totalRows * 1;
                },
                err => {
                    this.toastr.error("Lỗi tính toán phân trang");
                    console.log(err);
                }
            )
        }

    }

    pageChange(e, loadOffline: boolean = false): void {
        this.getPrintingBills(e, loadOffline);
    }

    print(bill: PrintingBill = null): void {

        if (bill) {
            this.selectedBill = bill
        }

        setTimeout(() => {
            let popupWin;
            let printContents = document.getElementById('printing-content').innerHTML;
            let html = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        body {
            margin-top: 20px;
            font-size: 4vw !important;
        }

        .noborder{
            border: none !important;
        }

        .padding-init {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(function() {
  window.close();
}, 0)">
    ${printContents}
</body>
</html>
        `;
            popupWin = window.open('', '_blank');
            popupWin.document.open();
            popupWin.document.write(html);
            popupWin.document.close();
        }, 100);
    }

    syncBill(bill: PrintingBill) {
        this.onSyncBill.emit(bill);
    }

    syncAll() {
        this.printBillLoading = true;
        this.idbService.all("printing_bills", "id_idx", true, IDBKeyRange.upperBound(-1)).subscribe(
            res => {
                if (res.length > 0) {
                    let join = [];
                    res.forEach((x: PrintingBill) => {
                        if (x.bill) {
                            join.push(this.billService.doPayment(x.bill));
                        }
                    })

                    Observable.forkJoin(join).subscribe(
                        res => {
                            this.printBillLoading = false;
                            if (res.length) {
                                let error = false;
                                res.forEach((x: any) => {
                                    let payload: Bill = x.payload;
                                    if (x.status == 'success') {
                                        //Remove offline bill
                                        this.idbService.remove("printing_bills", payload.printingBillId).subscribe(
                                            _ => {
                                                this.countSyncable();
                                            },
                                            err => console.log(err)
                                        );
                                        // this.toastr.success(x.msg);
                                    } else {
                                        error = true;
                                        this.toastr.error(x.msg);
                                    }
                                })

                                if (!error) {
                                    this.toastr.success("Đã đồng bộ hóa đơn thành công");
                                }

                                this.getPrintingBills(1, true);
                            }
                        },
                        err => {
                            this.toastr.error("Lỗi đồng bộ hóa đơn");
                            console.log(err);
                        }
                    );
                }
            }
        )
    }

    countSyncable() {
        this.billService.countSyncable().subscribe(
            cnt => this.countSyncableBills = cnt,
            err => console.log(err)
        );
    }

    cleanFilterPhone() {
        this.filterData.phone = onlyNumber(this.filterData.phone);
    }

    setMaxLengPhoneInput(phone?: string) {
        return setMaxLengPhoneInput(phone);
    }

    openRating(bill: PrintingBill) {
        let __this = this;
        this.printBillLoading = true;
        this.orderService.checkRating(bill.ordId).subscribe(
            res => {
                if(res.status == 'success') {
                    let req = {
                        'ord': res.data,
                        'token': this.socketToken
                    };
                    this.socket.emit("open_rating_order", req);

                    this.openRatingTimeout = setTimeout(function () {
                        __this.printBillLoading = false;
                        __this.toastr.error("Không kết nối được thiết bị đánh giá");
                    }, 5000);
                } else {
                    this.printBillLoading = false;
                    this.toastr.error(res.msg);
                }
            },
            err => {
                this.printBillLoading = false;
                this.toastr.error("Lỗi khi đánh giá đơn hàng");
                console.log(err);
            },
            () => {
            }
        );
    }
}
