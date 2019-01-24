import {Component, OnInit, ViewEncapsulation} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {GlobalService} from "../../services/global.service";
import {OrderService} from "../../services/order.service";
import {ToastrService} from "ngx-toastr";
import {RatingService} from "../../services/rating.service";
import {Rating} from "../../models/rating";
import {forkJoin} from "rxjs";
import {Order} from "../../models/order";
import {AuthenticationService} from "../auth/authentication.service";
import {StaffService} from "../../services/staff.service";
import {Staff} from "../../models/staff";
import {Store} from "../../models/store";
import {StoreService} from "../../services/store.service";

@Component({
    selector: 'app-information',
    templateUrl: './information.component.html',
    styleUrls: ['./information.component.css'],
    encapsulation: ViewEncapsulation.None
})
export class InformationComponent implements OnInit {

    globalVariables: any;
    phone: string;
    orders: any[];
    selectedOrdId: number = 0;
    cancelModalLoading: boolean = false;
    rateModalLoading: boolean = false;
    mainLoading: boolean = true;
    ratings: Rating[];
    currentRate: number = 100;
    staff: Staff;
    store: Store;

    constructor(
        private activeRoute: ActivatedRoute,
        private globalService: GlobalService,
        private orderService: OrderService,
        private toastr: ToastrService,
        private ratingService: RatingService,
        private auth: AuthenticationService,
        private staffService: StaffService,
        private storeService: StoreService
    ) {
    }

    ngOnInit() {
        this.phone = this.activeRoute.snapshot.paramMap.get('phone');

        if (this.auth.isAuthenticated()) {
            this.staff = this.staffService.getCurrentStaff();

            this.storeService.stores.subscribe(
                (stores: any[]) => {
                    if (stores && stores.length) {
                        this.store = stores.find(x => x.id == this.staff.storeId);
                    } else {
                        this.store = null;
                    }
                }
            );
        }

        this.globalService.data.subscribe(
            res => {
                this.globalVariables = res;
            },
            err => console.log(err),
        );

        this.index();
    }

    index(): void {
        this.mainLoading = true;

        let reqs = [
            this.ratingService.getRatings(),
            this.orderService.getOrders(this.phone)
        ];

        forkJoin(reqs).subscribe(
            ([ratings, orders]) => {
                this.ratings = ratings;
                this.orders = orders;

                setTimeout(() => {
                    $('.step-info .info-books i').click(function () {
                        var item = $(this).closest('.item');
                        item.toggleClass('opened');
                        item.find('.info-books-more').slideToggle();

                    });
                }, 0)
            },
            err => console.log(err),
            () => this.mainLoading = false
        );
    }

    /**
     * Cancel order
     */
    cancel(): void {
        this.cancelModalLoading = true;
        this.orderService.cancel(this.selectedOrdId, this.phone).subscribe(
            res => {
                if (res.status == 'success') {
                    this.toastr.success(res.msg);
                    this.index();
                } else {
                    this.toastr.error(res.msg);
                }
            },
            err => {
                this.toastr.error("Có lỗi xảy ra! Không thể thực hiện thao tác");
            },
            () => {
                $("#cancelModal").modal('hide');
                this.cancelModalLoading = false;
            }
        );
    }

    /**
     * Rate & complete order
     */
    rateOrder(): void {
        this.rateModalLoading = true;
        this.orderService.rate(this.selectedOrdId, this.currentRate).subscribe(
            res => {
                if(res.status == "success") {
                    this.toastr.success(res.msg);
                } else {
                    this.toastr.error(res.msg);
                }
            },
            err => {
                this.toastr.error("Có lỗi xảy ra! Không thể thực hiện thao tác");
            },
            () => {
                $("#ratingModal").modal('hide');
                this.rateModalLoading = false;
                this.index();
            }
        );
    }
}
