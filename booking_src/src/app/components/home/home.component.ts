import {Component, OnInit} from '@angular/core';
import {Order} from "../../models/order";
import {OrderService} from "../../services/order.service";
import {ToastrService} from "ngx-toastr";
import {ValidatorService} from "../../services/validator.service";
import {Router} from "@angular/router";
import {GlobalService} from "../../services/global.service";
import {AuthenticationService} from "../auth/authentication.service";
import {Staff} from "../../models/staff";
import {StaffService} from "../../services/staff.service";
import {City} from "../../models/city";
import {Store} from "../../models/store";
import {CityService} from "../../services/city.service";
import {StoreService} from "../../services/store.service";
import {CookieService} from "ngx-cookie-service";
import {APPGLOBAL} from "../../global";
import {DeviceDetectorService} from "ngx-device-detector";

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

    order: Order;
    mode: string = "booking"; //booking | search
    globalVariables: any;
    isLogin: boolean = false;
    staff: Staff;
    cities: City[];
    stores: Store[];
    storesByLocation: Store[];
    selectedCity: City;
    chooseBrandLoading: boolean = false;
    device: any;

    constructor(
        private orderService: OrderService,
        private validatorService: ValidatorService,
        private toastr: ToastrService,
        private router: Router,
        private globalService: GlobalService,
        private auth: AuthenticationService,
        private staffService: StaffService,
        private cityService: CityService,
        private storeService: StoreService,
        private cookieService: CookieService,
        private deviceService: DeviceDetectorService
    ) {
    }

    ngOnInit() {
        this.device = this.deviceService;

        this.isLogin = this.auth.isAuthenticated();

        if(this.isLogin) {
            this.staff = this.staffService.getCurrentStaff();
        }

        this.globalService.data.subscribe(
            res => this.globalVariables = res,
            err => console.log(err),
        );

        this.orderService.currentOrder.subscribe(order => this.order = order);
        this.cityService.cities.subscribe(cities => {
            this.cities = cities;
            this.storeService.stores.subscribe(stores => {
                this.stores = stores;
                this.filterByLocation(this.cities[0]);
            });
        });

        if(this.staff && !this.staff.storeId) {
            setTimeout(() => {
                $('#myModal').modal({
                    backdrop: false,
                    keyboard: false,
                    show: true,
                });
            },0)
        }
    }

    setPhone(value: string) {
        this.order.phone = value.replace(new RegExp(/\./g), '');
    }

    clickNumber(value: string) {
        this.order.phone = typeof this.order.phone != "undefined" && this.order.phone ? this.order.phone : "";

        if (this.order.phone.length < this.setMaxLengPhoneInput(this.order.phone)-2) {
            this.order.phone += value;
        }
    }

    checkInvalidPhone() {
        return this.validatorService.isVietnamesePhone(this.order.phone);
    }

    submitPhoneFrm() {
        if(this.checkInvalidPhone()) {
            if(this.mode == 'search') {
                this.router.navigateByUrl("/information/" + this.order.phone);
            } else {
                if(this.staff && this.staff.storeId) {
                    let staffStore = this.stores.filter(x => x.id == this.staff.storeId)[0];

                    if(staffStore) {
                        this.order.store = staffStore;
                        this.router.navigateByUrl("/book");
                    } else {
                        this.router.navigateByUrl("/store");
                    }

                } else {
                    this.router.navigateByUrl("/store");
                }
            }
        } else {
            this.toastr.error("Số điện thoại không hợp lệ");
        }
    }

    filterByLocation(city: City) {
        if (!city || !this.stores) return;
        this.selectedCity = city;
        this.storesByLocation = this.stores.filter((x: Store) => x.cityId == this.selectedCity.id);
        this.order.store = this.storesByLocation[0];
    }

    filterByLocationIndex(i: number) {
        this.filterByLocation(this.cities[i] || null);
    }

    updateStoreForStaff(){
        if(!this.order.store) {
            this.toastr.error("Bạn chưa chọn cửa hàng");
            return false;
        }

        this.chooseBrandLoading = true;

        this.staffService.updateStore(this.order.store).subscribe(
            res => {
                if(res.status == 'success') {
                    $('#myModal').modal('hide');
                    this.cookieService.set("accessToken", res.accessToken, null, '/', APPGLOBAL.COOKIE_DOMAIN);
                    this.toastr.success(res.msg);
                    this.ngOnInit();
                } else {
                    this.toastr.error(res.msg);
                }
            },
            error1 => {
                this.toastr.error("Có lỗi xảy ra!");
                console.log(error1);
            },
            () => {
                this.chooseBrandLoading = false;
            }
        );
    }

    setMaxLengPhoneInput(phone?: string) {
        let reg = new RegExp(/^01/);
        return reg.test(phone) ? 13 : 12;
    }

    removeLastNumberOfPhone() {
        this.order.phone = this.order.phone.slice(0, -1);
    }
}
