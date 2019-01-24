import {Component, OnInit} from '@angular/core';
import {Order} from "../../models/order";
import {OrderService} from "../../services/order.service";
import {ActivatedRoute, Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";

@Component({
    selector: 'app-complete',
    templateUrl: './complete.component.html',
    styleUrls: ['./complete.component.css']
})
export class CompleteComponent implements OnInit {

    order: Order;
    orderShow: Order;

    constructor(
        private orderService: OrderService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private toastr: ToastrService
    ) {
    }

    ngOnInit() {
        this.orderService.currentOrder.subscribe(
            order => {
                this.orderShow = Object.assign({}, order);
                this.order = order;
                this.order.orderItems = [];
                if (!this.orderShow.orderItems || !this.orderShow.orderItems.length) {
                    this.router.navigateByUrl('/book');
                }
            }
        )
    }

    /**
     * Get total of order
     * @returns {number}
     */
    getTotal(): number {
        return this.orderService.getTotal(this.orderShow);
    }

    cancel(): void {
        let request = this.activeRoute.snapshot.params;

        this.orderService.cancel(request.order, request.phone).subscribe(
            res => {
                if(res.status == 'success') {
                    this.toastr.success(res.msg);
                    this.router.navigateByUrl('/');
                } else {
                    this.toastr.error(res.msg);
                }
            },
            err => {
                this.toastr.error("Có lỗi xảy ra! Không thể thực hiện thao tác");
            },
            () => {}
        );
    }

}
