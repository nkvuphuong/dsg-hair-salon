import {ChangeDetectorRef, Component, EventEmitter, Input, OnInit, ViewChild} from '@angular/core';
import {Product} from "../../services/product/product";
import {ProductService} from "../../services/product/product.service";
import {Bill, PrintingBill} from "../../services/bill/bill";
import {BillService} from "../../services/bill/bill.service";
import {BillItem} from "../../services/bill-item/bill-item";
import {StaffService} from "../../services/staff/staff.service";
import {Staff} from "../../services/staff/staff";
import {Customer} from "../../services/customer/customer";
import {CustomerService} from "../../services/customer/customer.service";
import {debounceTime, switchMap} from "rxjs/operators";
import {MatTabChangeEvent} from "@angular/material";
import {CityService} from "../../services/city/city.service";
import {City} from "../../services/city/city";
import {District} from "../../services/district/district";
import {Ward} from "../../services/ward/ward";
import {DistrictService} from "../../services/district/district.service";
import {WardService} from "../../services/ward/ward.service";
import {FormControl, FormGroup, NgForm, Validators} from "@angular/forms";
import {ToastrService} from "ngx-toastr";
import {APPGLOBAL} from "../../global";
import {PrintBillComponent} from "../print-bill/print-bill.component";
import {AuthenticationService} from "../auth/authentication.service";
import {Router} from "@angular/router";
import {ProductCategoryService} from "../../services/product-category/product-category.service";
import {ProductCategory} from "../../services/product-category/product-category";
import {
    convertVietnamese,
    customDateTimeFormat,
    customNumberFormat,
    isOnline, onlyNumber,
    setMaxLengPhoneInput
} from "../../lib/script";
import {emailCustomValidator} from "../../shared/custom-validators.directive";
import * as screenfull from "screenfull";

import 'rxjs/Rx';
import {ConnectNetworkService} from "../../services/connect-network/connect-network.service";
import {GlobalService} from "../../services/global/global.service";
import {IndexedDbService} from "../../services/indexed-db/indexed-db.service";
import * as moment from 'moment'
import {StoreService} from "../../services/store/store.service";
import {Store} from "../../services/store/store";
import {BookingHourService} from "../../services/booking-hour/booking-hour.service";
import {BookingHour} from "../../services/booking-hour/booking-hour";
import {forkJoin} from "rxjs/observable/forkJoin";
import {CookieService} from "ngx-cookie-service";
import {Price} from "../../services/product/price";
import {ValidatorService} from "../../services/validator/validator.service";
import {Socket} from 'ng-socket-io';

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.component.html',
    styleUrls: ['./dashboard.component.css']
})

export class DashboardComponent implements OnInit {

    @ViewChild(PrintBillComponent) printBillComponent: PrintBillComponent;
    customerFrm: FormGroup;
    productSlide: any;
    productSlideSort: string = 'desc';
    products: Product[];
    oriProducts: Product[];
    productCategories: ProductCategory[] = [];
    oriProductCategories: ProductCategory[] = [];
    checkedCategories: ProductCategory[] = [];
    bills: Bill[];
    staffs: Staff[];
    cities: City[];
    districts: District[];
    wards: Ward[];
    @Input() selectedBill: Bill;
    @Input() selectedItem: BillItem;
    @Input() customer: Customer = new Customer();
    currentStaff: Staff;
    customerFlag: boolean = false;
    customerPage: number = 1;
    customerPerPage: number = 10;
    customerTerm: string = "";
    typeaheadCustomer = new EventEmitter<string>();
    customerAvatar: string;
    noAvatarurl: string = "assets/images/avartar.png";

    //Loading flag
    customerLoading: boolean = false;
    productLoading: boolean = true;
    districtLoading: boolean = false;
    billPaymentLoading: boolean = false;

    isFullscreen: boolean = false;

    appConfig: any = APPGLOBAL;

    globalVariables: any;
    selectedPrintingBill: PrintingBill;

    countSyncableBills: number = 0;

    //Booking
    daysOfWeek: string[];
    serviceStaffs: Staff[];

    //Reloader
    reloader: boolean = true;

    //Choose store
    storeCities: City[];
    selectedCity: City;
    stores: Store[];
    selectedStore: Store;
    oriStores: Store[];
    loadingChooseStore: boolean = false;

    //Prices
    prices: Price[];
    oriPrices: Price[];

    now: Date;


    constructor(private productService: ProductService,
                private productCategoryService: ProductCategoryService,
                private billService: BillService,
                private staffService: StaffService,
                private customerService: CustomerService,
                private cityService: CityService,
                private districtService: DistrictService,
                private wardService: WardService,
                private cd: ChangeDetectorRef,
                private toastr: ToastrService,
                private authSevice: AuthenticationService,
                private router: Router,
                private globalService: GlobalService,
                public connectNetworkService: ConnectNetworkService,
                private idbService: IndexedDbService,
                private cookieService: CookieService,
                private bookingHourService: BookingHourService,
                private storeService: StoreService,
                private validatorService: ValidatorService,
                public socket: Socket,
    ) {
    }

    ngOnInit() {

        this.socket.disconnect();

        this.now = new Date();

        this.currentStaff = this.staffService.getCurrentStaff();
        this.reloader = true;

        if (!this.currentStaff.storeId) {
            this.getCitiesHaveStores();
        } else {
            this.getStores(() => {
                let stores = this.oriStores.filter(x => x.id == this.currentStaff.storeId);
                if (stores && stores[0]) {
                    this.currentStaff.store = stores[0];
                    this.daysOfWeek = [];
                    for (let i = 0; i <= 6; i++) {
                        this.daysOfWeek.push(moment().add(i, 'd').set({"h": 0, "m": 0, "s": 0}).format('DD/MM/YYYY'));
                    }

                    this.getGlobalVariables();
                    /*this.getBills();
                    this.getProducts(true);
                    this.getPrices(() => {
                        this.setPriceBookForBill()
                    });
                    this.getProductCategories();
                    this.getStaffs();
                    this.getCustomers(this.selectedBill.customers);
                    this.getCities();
                    this.customerFrmValidate();
                    this.printBillComponent.countSyncable();*/
                } else {
                    this.toastr.error("This store is not available");
                    this.getCitiesHaveStores();
                }
            })
        }
    }

    getGlobalVariables() {
        this.globalService.getVariables().subscribe(
            variables => {

                //Check enabled
                if (!variables.posEnabled) {
                    document.location.href = APPGLOBAL.ROOT_DOMAIN;
                    this.toastr.warning("App is disabled");
                    return;
                }

                //Check checkin
                if (variables.checkinEnabled) {
                    console.log("Enabled SocketIO");
                    this.socket.connect();
                } else {
                    console.log("Disabled SocketIO");
                }

                setTimeout(() => {
                    this.reloader = false;
                }, 1000)

                //Custom css by theme
                document.getElementById('theme-css').setAttribute('href', 'assets/css/style' + (variables.theme && variables.theme !== null ? variables.theme : '') + '.css')

                this.globalVariables = variables;

                this.getBills(variables.productTypeDefault);
                this.getProducts(true);
                this.getPrices(() => {
                    this.setPriceBookForBill()
                });
                this.getProductCategories();
                this.getStaffs();
                this.getCustomers(this.selectedBill.customers);
                this.getCities();
                this.customerFrmValidate();
                this.printBillComponent.countSyncable();

                //Auto reload
                if (this.globalVariables.appRefreshDataPeriod) {
                    window.setInterval(() => {
                        console.log("Auto reload ....");
                        this.getProducts(true);
                        this.getPrices();
                        this.getProductCategories(true);
                        this.getStaffs();
                        this.getCustomers(this.selectedBill.customers);
                        this.getCities();
                    }, this.globalVariables.appRefreshDataPeriod * 60 * 1000)
                }
            },
            (err) => {
                console.log(err);
                setTimeout(() => {
                    this.reloader = false;
                }, 1000)
            }
        )
    }

    getProducts(reload: boolean = false): void {

        if (!reload) {
            this.productSlideSort = this.productSlideSort == 'asc' ? 'desc' : 'asc';
        }

        this.productLoading = true;

        this.productService.getProducts('price', this.productSlideSort).subscribe(
            products => {
                this.productLoading = false;
                if (this.productSlide) {
                    if (!this.productSlide.destroyed) {
                        this.productSlide.destroy();
                    }
                }
                return this.oriProducts = products;
                // return this.products = products;
            },
            (err) => {
                this.productLoading = false;
                console.log(err)
            },
            () => {
                this.productLoading = false;
                this.overwriteProductByPrice();
                this.filterProducts();
            }
        );
    }

    productSetSlide(): void {
        setTimeout(() => {
            this.productSlide = new Swiper('.swiper-container', {
                slidesPerView: 10,
                slidesPerGroup: 10,
                slidesPerColumn: 2,
                spaceBetween: 0,
                pagination: {
                    el: '#product-swiper-pagination',
                    type: 'fraction'
                },
                navigation: {
                    nextEl: '#product-swiper-button-next',
                    prevEl: '#product-swiper-button-prev',
                },
                breakpoints: {
                    800: {
                        slidesPerView: 2,
                        slidesPerGroup: 2,
                    },
                    900: {
                        slidesPerView: 3,
                        slidesPerGroup: 3,
                    },
                    1000: {
                        slidesPerView: 4,
                        slidesPerGroup: 4,
                    },
                    1100: {
                        slidesPerView: 5,
                        slidesPerGroup: 5,
                    },
                    1200: {
                        slidesPerView: 6,
                        slidesPerGroup: 6,
                    },
                    1300: {
                        slidesPerView: 7,
                        slidesPerGroup: 7,
                    },
                    1400: {
                        slidesPerView: 8,
                        slidesPerGroup: 8,
                    },
                    1500: {
                        slidesPerView: 9,
                        slidesPerGroup: 9,
                    },
                    1600: {
                        slidesPerView: 10,
                        slidesPerGroup: 10,
                    },
                }
            });
        }, 0);
    }

    getProductCategories(reload: boolean = false): void {
        this.productCategoryService.getProductCategories('name').subscribe(
            data => {
                this.oriProductCategories = data;
                if (!reload) {
                    this.productCategories = data;
                    this.productCategories.map((cat, index) => cat.checked = false)
                }
            },
            err => {
                console.log(err)
            },
            () => {
                this.filterCheckedCategories();
            }
        )
    }

    filterProducts(): void {

        this.checkedCategories = [];

        if (this.productSlide) {
            if (!this.productSlide.destroyed) {
                this.productSlide.destroy();
            }
        }

        this.productCategories.forEach(x => {
            if (x.checked) {
                this.checkedCategories.push(x);
            }
        })

        //Filter by category
        if (this.checkedCategories.length) {
            this.products = this.oriProducts.filter(x => {
                return this.checkedCategories.find((y) => x.group * 1 == y.id * 1);
            })
        } else {
            this.products = this.oriProducts;
        }

        //Filter by type
        if (this.products) {
            this.products = this.products.filter(x => x.type == this.selectedBill.productType);
        }
        this.productSetSlide();
    }

    removeCheckedCategory(catId) {
        this.productCategories.map(x => x.checked = (x.id * 1 == catId * 1) ? false : x.checked);
        this.filterProducts();
    }

    checkedAllCategories(e) {
        this.productCategories.map(x => x.checked = e.target.checked);
    }

    changeCheckedCategory(e, catId) {
        this.productCategories.map(x => x.checked = (x.id * 1 == catId * 1) ? e.target.checked : x.checked);
    }

    filterCheckedCategories(term: string = "") {
        term = convertVietnamese(term);

        this.productCategories = this.oriProductCategories;
        //Filter by name
        if (term.length > 0) {
            this.productCategories = this.productCategories.filter(x => {
                let name = convertVietnamese(x.name);
                return name.includes(term);
            });
        }

        //Filter by type
        this.productCategories = this.productCategories.filter(x => x.type == this.selectedBill.productType);
    }

    getStaffs(): void {
        this.staffService.getStaffs().subscribe(
            staffs => this.staffs = staffs
        );
    }

    getBills(productType = 0): void {
        this.bills = this.billService.getBills(this.currentStaff.store, productType);
        this.selectedBill = this.bills[0];
    }

    addBill(productType: number = 0): void {
        this.billService.addBill(null, productType, this.currentStaff.store);
        this.bills = this.billService.getBills(this.currentStaff.store);
        this.selectedBill = this.bills[this.bills.length - 1];
        this.setPriceBookForBill();
        this.selectedBill.customers = [...this.selectedBill.customers];
        this.filterCheckedCategories();
        this.filterProducts();
    }

    removeBill(bill: Bill): void {
        this.billService.removeBill(bill);
        this.bills = this.billService.getBills(this.currentStaff.store, this.globalVariables.productTypeDefault);
        let selectedBillIndex = this.getIndexSelectedBill();
        selectedBillIndex = selectedBillIndex < 0 ? 0 : selectedBillIndex;
        this.selectedBill = this.bills[selectedBillIndex] ? this.bills[selectedBillIndex] : this.bills[0];
        this.setPriceBookForBill();
        this.selectedBill.customers = [...this.selectedBill.customers];
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.filterCheckedCategories();
        this.filterProducts();
    }

    selectBill(bill: Bill): void {
        this.selectedBill = bill;
        this.getCustomers(this.selectedBill.customers);
        this.filterPriceBooks();
        this.filterCheckedCategories();
        this.overwriteProductByPrice();
        this.filterProducts();
    }

    addItem(product: Product): void {
        let item = new BillItem(product);

        if (product.type == 1) { //dich vu
            if (this.selectedBill.store && this.selectedBill.store.id) {
                item.date = this.daysOfWeek[0];
            } else {
                this.toastr.warning("Vui lòng chọn cửa hàng trước khi chọn dịch vụ");
                return;
            }
        }

        // Kiểm tra item cuối để gán nhân viên mặc định cho item vừa add
        let lastItem = this.selectedBill.items[this.selectedBill.items.length-1];

        this.selectedBill = this.billService.addItem(item, this.selectedBill);

        if (product.type == 1) { //dich vu
            let addedItem = this.selectedBill.items[this.selectedBill.items.length - 1];
            let __this = this;
            // this.getBookingHours(addedItem);
            this.getBookingHours(addedItem, function () {
              //Gán nhân viên mặc định cho item mới
              if (lastItem.staff) {
                __this.addStaffToItem(lastItem.staff.id, addedItem);
              }
            });
        }

        this.selectedBill = this.billService.calculateBill(this.selectedBill);
    }

    removeItem(item: BillItem): void {
        this.selectedBill.items = this.selectedBill.items.filter(x => x.key != item.key);
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.setBookedHoursAllItems();
        this.billService.saveBill(this.selectedBill);
    }

    selectItem(item: BillItem): void {
        this.selectedItem = item;
    }

    updateQuantity(item: BillItem): void {
        item.quantity *= 1;
        item.quantity = item.quantity <= 0 ? 1 : item.quantity;
        item = this.billService.calculateItem(item);
        this.selectedBill.items.map(x => x.key == item.key ? item : x);
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.billService.saveBill(this.selectedBill);
    }

    adjustQuantity(item: BillItem, type: string = "+"): void {
        item.quantity *= 1;
        if (type == '-') {
            item.quantity -= 1;
        } else {
            item.quantity += 1;
        }

        this.updateQuantity(item);
    }

    updatePrice(item: BillItem): void {
        item.price *= 1;
        item.price = item.price < 0 ? 0 : item.price;

        item = this.billService.calculateItem(item);

        this.selectedBill.items.map(x => x.key == item.key ? item : x);
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.billService.saveBill(this.selectedBill);
    }

    updatePaymentAmount(): void {
        this.selectedBill.paymentAmount *= 1;
        this.selectedBill.paymentAmount = this.selectedBill.paymentAmount <= 0 ? 0 : this.selectedBill.paymentAmount;
        this.selectedBill.excessCash = this.selectedBill.paymentAmount - this.selectedBill.total;
        this.billService.saveBill(this.selectedBill);
    }

    changeItemDiscountType(discountType: number): void {
        if (discountType != this.selectedItem.discountType) {
            this.selectedItem.discountType = discountType;
            this.updatePrice(this.selectedItem);
        }
    }

    changeBillDiscountType(discountType: number): void {
        if (discountType != this.selectedBill.discountType) {
            this.selectedBill.discountType = discountType;
            this.billService.calculateBill(this.selectedBill);
            this.billService.saveBill(this.selectedBill);
        }
    }

    changDiscountValue(): void {
        if (this.selectedItem.discountType == 0) {
            this.selectedItem.price = this.selectedItem.product.price - ((this.selectedItem.product.price * this.selectedItem.discountValue) / 100);
        } else {
            this.selectedItem.price = this.selectedItem.product.price - this.selectedItem.discountValue;
        }

        this.selectedItem = this.billService.calculateItem(this.selectedItem);
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.billService.saveBill(this.selectedBill);
    }

    changeBillDiscountValue(): void {
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.billService.saveBill(this.selectedBill);
    }

    changeBillDiscountAmount(): void {
        this.selectedBill.discountAmount = this.selectedBill.discountAmount > this.selectedBill.subTotal ? this.selectedBill.subTotal : this.selectedBill.discountAmount;
        this.selectedBill.discountValue = this.selectedBill.discountType == 0 ? (this.selectedBill.discountAmount / this.selectedBill.subTotal) * 100 : this.selectedBill.discountAmount;

        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.billService.saveBill(this.selectedBill);
    }

    updateTax(): void {
        this.selectedItem = this.billService.calculateItem(this.selectedItem);
        this.selectedBill = this.billService.calculateBill(this.selectedBill);
        this.billService.saveBill(this.selectedBill);
    }

    changeTaxType(taxType: number): void {
        if (this.selectedItem.taxType == taxType) return;

        this.selectedItem.taxType = taxType;
        if (taxType == 0) {
            this.selectedItem.taxValue = 100 * (this.selectedItem.taxAmount / this.selectedItem.quantity / this.selectedItem.price);
        } else {
            this.selectedItem.taxValue = (this.selectedItem.taxValue * this.selectedItem.price) / 100;
        }

        this.selectedItem.taxValue = customNumberFormat(this.selectedItem.taxValue);

        this.updateTax();
    }

    getCustomers(customers: Customer[] = []): void {
        if (!this.customerFlag) {
            this.customerFlag = true;
            this.typeaheadCustomer
                .pipe(
                    debounceTime(500),
                    switchMap(term => {
                        this.customerPage = 1;
                        this.customerTerm = term;
                        return this.customerService.getCustomers(term, this.customerPage, this.customerPerPage);
                    })
                )
                .subscribe(customers => {
                    this.selectedBill.customers = customers;
                    this.billService.saveBill(this.selectedBill);
                    if (this.selectedBill.customers.length < this.customerPerPage) {
                        this.customerPage = 0; //To disable load more
                    }
                    this.cd.markForCheck();
                }, (err) => {
                    console.log('error', err);
                    this.selectedBill.customers = [];
                    this.cd.markForCheck();
                });
        }

        //Load customers
        if (typeof customers != "undefined" && customers.length > 0) {
            this.selectedBill.customers = customers;
            this.selectedBill.customers = [...this.selectedBill.customers];
            this.customerPage = 0; //To disable load more
        }
    }

    loadMoreCustomers(): void {
        if (!this.customerPage || !isOnline()) return;

        this.customerPage++;
        this.customerService.getCustomers(this.customerTerm, this.customerPage, this.customerPerPage).subscribe(
            customers => {
                customers.forEach(x => {
                    this.selectedBill.customers.push(x);
                })
                this.selectedBill.customers = [...this.selectedBill.customers];
                this.billService.saveBill(this.selectedBill);

                if (this.selectedBill.customers.length < this.customerPerPage) {
                    this.customerPage = 0; //To disable load more
                }
            }
        );
    }

    addCustomerToBill(customer: Customer): void {
        this.selectedBill.customer = customer;
        if (this.selectedBill.productType) {
            this.selectedBill.phone = customer.phone;
        } else {
            this.selectedBill.phone = "";
        }

        this.filterPriceBooks();
        this.billService.saveBill(this.selectedBill);
    }

    addStaffToBill(staff: Staff): void {
        this.selectedBill.staff = staff;
        this.billService.saveBill(this.selectedBill);
    }

    addStaffToItem(staffId: number, item: BillItem): void {
        item.staff = item.staffs.find(x => x.id == staffId) || null;
        item.hour = null;
        this.filterBookingHours(item);
        this.setBookedHoursAllItems();
        this.billService.saveBill(this.selectedBill);
    }

    onTabClick(event: MatTabChangeEvent): void {
        this.selectBill(this.bills[event.index]);
    }

    getIndexSelectedBill(): number {
        return this.bills.findIndex(x => x.key == this.selectedBill.key);
    }

    clearSelectedStaff(): void {
        this.selectedBill.staff = new Staff();
        this.billService.saveBill(this.selectedBill);
    }

    clearStaffSelectedItem(item: BillItem): void {
        this.selectedBill.items.map(x => {
            if (item.key == x.key) {
                x.staff = new Staff();
                x.hour = null;
                this.filterBookingHours(item);
                return;
            }
        })
        this.setBookedHoursAllItems();
        this.billService.saveBill(this.selectedBill);
    }

    clearSelectedCustomer(): void {
        this.selectedBill.customer = new Customer();
        this.selectedBill.phone = "";
        this.billService.saveBill(this.selectedBill);
    }

    getCities(): void {
        this.cityService.getCities().subscribe(
            cities => this.cities = cities
        );
    }

    chooseCity(city: City): void {
        this.customer.city = city;
        this.getDistricts(city);
    }

    getDistricts(city: City, callback?): void {
        this.districts = [];
        this.districts = [...this.districts];
        this.customer.district = null;
        this.districtLoading = true;
        this.districtService.getDistricts(city as City).subscribe(
            districts => {
                this.districtLoading = false;
                if (callback instanceof Function) {
                    callback()
                }
                return this.districts = districts;
            }
        );
    }

    chooseDistrict(district: District): void {
        this.customer.district = district;
    }

    getWards(district: District): void {
        this.wardService.getWards(district as District).subscribe(
            wards => this.wards = wards
        )
    }

    submitCustomer(frm: NgForm): void {
        if (!frm.valid) return;
        this.customerLoading = true;
        this.customerService.submitCustomer(this.customer).subscribe(
            res => {
                this.customerLoading = false;
                if (res.status == 'success') {
                    this.selectedBill.customers.unshift(res.data);
                    this.selectedBill.customers = [...this.selectedBill.customers];
                    this.selectedBill.customer = res.data;
                    this.selectedBill.phone = res.data.phone;
                    this.billService.saveBill(this.selectedBill);
                    this.toastr.success(res.msg);
                    $('.add-customer-modal-sm').modal('hide');
                    frm.form.reset();
                    setTimeout(() => {
                        this.customer = new Customer();
                        this.customerAvatar = null;
                    }, 0);
                } else {
                    this.toastr.error(res.msg);
                }
            },
            err => {
                this.customerLoading = false;
                this.toastr.error("Error!");
                console.log(err);
            }
        )
    }

    loadCustomerToAdd() {
        this.customer = new Customer();
        this.customer.action = "add";
        this.customerAvatar = null;
    }

    loadCustomerToEdit() {
        this.customer = new Customer();
        this.customer.action = "edit";
        this.getDistricts(this.selectedBill.customer.city, () => {
            this.customer = this.selectedBill.customer;
            this.customer.action = "edit";
            this.customerAvatar = this.customer.avatarStr;
        });
    }

    changeAvatar(e): void {
        this.customerService.uploadImage(e).subscribe(img => {
            this.customer.avatar = e.target.files[0];
            this.customerAvatar = img;
        })
    }

    doPaymentBill(): void {
        if (isOnline()) {
            this.billPaymentLoading = true;
            this.billService.doPayment(this.selectedBill).subscribe(
                res => {
                    this.billPaymentLoading = false;
                    if (res.status == 'success') {


                        if (typeof this.selectedBill.isOffline != "undefined" && this.selectedBill.isOffline && this.selectedBill.printingBillId) {
                            //Remove offline bill
                            this.idbService.remove("printing_bills", this.selectedBill.printingBillId * 1).subscribe(
                                _ => {
                                    this.printBillComponent.countSyncable();
                                },
                                err => console.log(err)
                            );

                        }

                        this.removeBill(this.selectedBill);

                        this.toastr.success(res.msg);
                        //In hóa đơn
                        res.trx.time = customDateTimeFormat(res.trx.timestamp);
                        this.selectedPrintingBill = res.trx;
                        this.printBillComponent.print();
                    } else {
                        this.toastr.error(res.msg);

                        //Load lại khung giờ booking cho các item service
                        if (res.naItems.length) {
                            this.reloadBookingHours();
                        }
                    }
                },
                err => {
                    this.billPaymentLoading = false;
                    this.toastr.error("Error!");
                    console.log(err);
                }
            );
        } else {
            if (this.selectedBill.isOffline) {
                this.removeBill(this.selectedBill);
            } else {
                this.billService.doPaymentOffline().subscribe(
                    cnt => {
                        let d = new Date();
                        let printingBill: PrintingBill = this.billService.conver2PrintingBill(this.selectedBill);
                        printingBill.isOffline = 1;
                        printingBill.timestamp = Math.round(d.getTime() / 1000);
                        printingBill.time = customDateTimeFormat(printingBill.timestamp);
                        printingBill.id = (cnt * (-1)) - 1;
                        printingBill.bill = this.selectedBill;
                        printingBill.bill.printingBillId = printingBill.id;
                        this.idbService.put("printing_bills", printingBill).subscribe(
                            x => {
                                this.removeBill(this.selectedBill);
                                this.toastr.success("Không thể kết nối Internet: hóa đơn được lưu offline");

                                this.selectedPrintingBill = printingBill;
                                this.printBillComponent.print();
                                this.printBillComponent.countSyncable();
                            },
                            err => {
                                this.toastr.error("Lỗi lưu hóa đơn offline: (err613)");
                                console.log(err);
                            }
                        );
                    },
                    err => {
                        this.toastr.error("Lỗi lưu hóa đơn offline: (err618)");
                    }
                );
            }
        }

    }

    logout(): void {
        this.authSevice.logout();
        // this.router.navigate(['/login']);
        window.location.href = APPGLOBAL.ROOT_DOMAIN + "/acp/?returnUrl=" + APPGLOBAL.ROOT_DOMAIN + "/pos";
    }

    customerFrmValidate() {
        this.customerFrm = new FormGroup({
            'name': new FormControl(this.customer.name, [
                Validators.required,
                Validators.minLength(4),
                emailCustomValidator()
            ]),
        })
    }

    saveBill(bill: Bill): void {
        this.billService.saveBill(bill);
    }

    fullscreenToggle(): void {
        if (screenfull.enabled) {
            screenfull.toggle();
            this.isFullscreen = !screenfull.isFullscreen;
        }
    }

    onSyncBill(bill: PrintingBill) {
        bill.bill.isOffline = 1;
        let currentBill = this.billService.addBill(bill.bill as Bill);
        this.bills = this.billService.getBills(this.currentStaff.store);
        this.selectedBill = currentBill;

        $("#printBillModalNeedSync").modal("hide");
    }

    chooseBillStore(store: Store) {
        if (this.selectedBill.items.length) {
            this.selectedBill.items = [];
            this.selectedBill = this.billService.calculateBill(this.selectedBill);
            this.toastr.info("Bạn đã thay đổi cửa hàng. Thông tin đơn hàng sẽ được làm mới lại", "Thông báo");
        }

        this.selectedBill.store = store;
        this.billService.saveBill(this.selectedBill);
    }

    /**
     * Get hours to booking
     * @param {BillItem} item
     */
    getBookingHours(item: BillItem, callback = null) {
        let day = moment(item.date, "DD/MM/YYYY");
        this.bookingHourService.getBookingHours(this.selectedBill.store.id, day.format('YYYY-MM-DD')).subscribe(
            (res: BookingHour[]) => {
                res.sort((a, b) => a['second'] - b['second']);
                item.oriBookingHours = res;
                this.filterStaffByBookingHoursAndService(item);
                this.filterBookingHours(item);
                this.setBookedHoursAllItems();
                this.billService.saveBill(this.selectedBill);

                if (callback) {
                  callback();
                }
            }
        )
    }

    /**
     * @param {BillItem} item
     */
    filterBookingHours(item: BillItem) {
        let now = +moment().format('x') / 1000;
        let date = +moment(item.date, "DD/MM/YYYY").format('x') / 1000;
        let newBookingHours: BookingHour[] = JSON.parse(JSON.stringify(item.oriBookingHours));

        if (newBookingHours) {

            //Filter by staffs list
            newBookingHours = newBookingHours.map(bookingHour => {
                bookingHour.slots = bookingHour.slots.filter(slot => item.staffs && item.staffs.findIndex(x => x.id == slot) > -1);
                return bookingHour;
            });


            //Filter by choosen staff
            if (item.staff && item.staff.id) {
                newBookingHours = newBookingHours.map(bookingHour => {
                    bookingHour.booked = bookingHour.booked.filter(booked => booked === item.staff.id);
                    bookingHour.slots = bookingHour.slots.filter(slot => slot == item.staff.id || slot == null || typeof slot == "undefined")
                    return bookingHour;
                });
            }

            //Filter by time
            newBookingHours = newBookingHours.filter(bookingHour => {
                let time = date + +bookingHour.second;
                return time > now;
            })
        }

        item.bookingHours = [...newBookingHours];
    }

    /**
     * Lọc ra những nhân viên có thể đặt lịch
     * @param {BookingHour[]} bookingHours
     * @param {Product} service
     */
    filterStaffByBookingHoursAndService(item: BillItem) {

        let bookingHours = item.oriBookingHours;
        item.staffs = [];
        let existed = []; //Lưu giư các staffId đã push vào (Duplicate)

        //Lọc theo booking hours
        if (bookingHours.length) {
            bookingHours.forEach(bookingHour => {
                if (bookingHour.slots.length - bookingHour.booked.length > 0) {
                    bookingHour.booked.forEach(booked => {
                        bookingHour.slots.map(slot => slot !== booked);
                    })
                    bookingHour.slots.forEach(slot => {
                            if (existed.indexOf(slot) < 0) {
                                item.staffs.push(this.staffs.filter(staff => staff.id == slot)[0]);
                                existed.push(slot);
                            }
                        }
                    )
                }
            })
        }

        //Lọc theo product
        let product = item.product;
        if (product.staffIds && item.staffs) {
            item.staffs = item.staffs.filter(staff => {
                return product.staffIds.indexOf(staff.id) > -1;
            });
        } else {
            item.staffs = [];
        }
        item.staff = null;
    }

    /**
     * Set booked hours for all items of bill
     * @param {string} date
     */
    setBookedHoursAllItems() {
        if (this.selectedBill.productType != 1) return;
        let bookedOthers = [];
        this.selectedBill.items.forEach(x => {
            bookedOthers.push({
                'date': x.date,
                'hour': x.hour,
                'staffId': x.staff && x.staff.id ? x.staff.id : null,
                'itemKey': x.key
            });
        });

        this.selectedBill.items = this.selectedBill.items.map(item => {
            item.bookingHours = item.bookingHours.map(bookingHour => {
                bookingHour.bookedOthers = [];
                bookedOthers.forEach(booked => {
                    if (booked.date == item.date && booked.hour == bookingHour.hour && booked.itemKey != item.key) {
                        if (item.staff && item.staff.id) {
                            if (booked.staffId == item.staff.id || typeof booked.staffId == "undefined" || booked.staffId == null) {
                                bookingHour.bookedOthers.push(booked.staffId);
                            }
                        } else {
                            bookingHour.bookedOthers.push(booked.staffId);
                        }
                    }
                });
                return bookingHour;
            })
            return item;
        });
    }

    setHourItem() {
        this.setBookedHoursAllItems();
        this.billService.saveBill(this.selectedBill);
    }

    clearAllServiceItems(city: City) {
        if (this.selectedBill.items.length) {
            this.selectedBill.items = [];
            this.selectedBill = this.billService.calculateBill(this.selectedBill);
            this.toastr.info("Bạn đã thay đổi tỉnh / thành phố. Thông tin đơn hàng sẽ được làm mới lại", "Thông báo");
        }
        this.billService.saveBill(this.selectedBill);
    }

    reloadBookingHours() {
        let requests = [];
        this.selectedBill.items.forEach((item) => {
            let day = moment(item.date, "DD/MM/YYYY");
            requests.push(this.bookingHourService.getBookingHours(this.selectedBill.store.id, day.format('YYYY-MM-DD')));
        })

        forkJoin(requests).subscribe(results => {
            this.selectedBill.items.map((item, i) => {
                let bookingHours: BookingHour[] = Object.keys(results[i]).map(key => results[i][key]);
                item.oriBookingHours = bookingHours.sort((a, b) => a['second'] - b['second']);
                this.filterBookingHours(item);

                let checkHour = item.bookingHours.filter(x => x.hour == item.hour);
                if (checkHour) {
                    if (checkHour[0].slots.length - checkHour[0].booked.length - checkHour[0].bookedOthers.length <= 0) {
                        item.hour = null;
                    }
                } else {
                    item.hour = null;
                }
                return item;
            });

            this.setBookedHoursAllItems();
            this.billService.saveBill(this.selectedBill);
        })
    }

    checkBookingHourIncomplete(): boolean {
        let rs = true;

        if (this.selectedBill.productType == 0) return rs;

        this.selectedBill.items.forEach(item => {
            if (typeof item.hour == "undefined" || item.hour == null || item.hour == "" || item.hour == "null") {
                rs = false;
            }
        })
        return rs;
    }

    getCitiesHaveStores() {
        this.cityService.getCitiesHaveStores().subscribe(
            cities => {
                this.storeCities = cities;
                this.selectedCity = this.storeCities && this.storeCities[0] ? this.storeCities[0] : null;
                this.getStores(() => {
                    this.filterStoresByCity(this.selectedCity);
                    //Check store
                    $("#chooseStoreModal").modal({'backdrop': 'static', 'keyboard': false});
                    $("#chooseStoreModal").modal('show');

                    this.reloader = false;
                });
            },
            err => console.log(err)
        )
    }

    getStores(callback?) {
        this.storeService.getStores().subscribe(
            stores => {
                this.oriStores = stores;
                this.selectedStore = stores && stores[0] ? stores[0] : null;
                if (typeof callback == "function") {
                    callback();
                }
            },
            err => console.log(err)
        )
    }

    filterStoresByCity(city: City) {
        if (!city) {
            this.stores = [];
            this.selectedCity = null;
            this.selectedStore = null;
            return;
        }

        this.selectedCity = city;
        if (this.oriStores) {
            this.stores = this.oriStores.filter(x => x.cityId == city.id);
        }

        this.selectedStore = this.stores && this.stores[0] ? this.stores[0] : null;

        if (this.selectedStore && this.selectedStore.cityId != this.selectedCity.id) {
            this.selectedStore = null;
        }
    }

    chooseStore() {
        if (this.bills) {
            this.bills.forEach(bill => {
                bill.city = bill.city || this.selectedCity;
                bill.store = bill.store || this.selectedStore;
                this.billService.saveBill(bill);
            })
        }

        //Update store for staff
        let staffData = JSON.parse(JSON.stringify(this.currentStaff));
        staffData.store = this.selectedStore || null;
        staffData.storeId = this.selectedStore && this.selectedStore.id ? this.selectedStore.id : null;

        this.loadingChooseStore = true;
        this.staffService.updateStore(staffData).subscribe(
            res => {
                if (res.status == 'success') {
                    this.cookieService.set("accessToken", res.accessToken, null, '/', APPGLOBAL.COOKIE_DOMAIN);
                    this.toastr.success("Updated success");
                    this.currentStaff = staffData;
                    this.bills = this.billService.getBills(this.currentStaff.store);
                    this.selectedBill = this.bills[0];
                    this.ngOnInit();
                    $("#chooseStoreModal").modal("hide");
                } else {
                    this.toastr.error("Updated fail");
                }
            },
            err => {
                this.toastr.error("Error!");
                console.log(err);
            },
            () => {
                this.loadingChooseStore = false;
            }
        );
    }

    getPrices(callback?): void {
        this.productService.getPrices().subscribe(
            prices => {
                this.oriPrices = this.prices = prices;
                this.filterPriceBooks();

                if (callback instanceof Function) {
                    callback();
                }
            }
        );
    }

    filterPriceBooks(): void {
        //Filter by stores
        this.prices = this.oriPrices.filter(x => {
            if (x.storeIds.length == 0) return true;
            if (x.storeIds.indexOf(this.selectedBill.store.id) != -1) return true;
            return false;
        });

        //Filter by customer group
        if (this.selectedBill.customer && this.selectedBill.customer.groupIds) {
            //Lấy những bảng giá chung + bảng giá dành riêng chi mỗi nhóm khách hàng
            this.prices = this.prices.filter(x => {
                //Bảng giá chung
                if (x.cusGroupIds.length == 0) return true;

                //Bảng giá theo nhóm KH
                if (this.selectedBill.customer.groupIds.length > 0) {
                    let check = this.selectedBill.customer.groupIds.some(function (groupId) {
                        if (x.cusGroupIds.indexOf(groupId) != -1) return true;
                        return false;
                    })
                    return check;
                }
                return false;
            });
        } else {
            //Chỉ lấy những bảng giá chung
            this.prices = this.prices.filter(x => {
                if (x.cusGroupIds.length == 0) return true;
                return false;
            });
        }
    }

    changePriceBook(price: Price): void {
        this.selectedBill.price = price || null;
        this.selectedBill.isSetPrice = 0;

        /**
         * Update for product
         */
        this.overwriteProductByPrice();
        this.filterProducts();

        /**
         * Update for bill items
         */
        /*this.selectedBill.items.map((x: BillItem) => {
            let newPrice = this.selectedBill.price ? this.selectedBill.price.items.filter(y => y.productId == x.productId) : null;
            x.oldPrice = x.price = newPrice && newPrice[0] ? newPrice[0].price : x.product.commonPrice;
            this.updatePrice(x);

            return x;
        });

        this.billService.saveBill(this.selectedBill);*/
    }

    overwriteProductByPrice() {
        if (this.oriProducts) {
            this.oriProducts.map(x => {
                let newPrice = this.selectedBill.price ? this.selectedBill.price.items.filter(y => y.productId == x.id) : null;
                x.price = newPrice && newPrice[0] ? newPrice[0].price : x.commonPrice;
                return x;
            });
        }
    }

    setPriceBookForBill() {
        let priceBook;

        if (this.currentStaff.store.priceId && this.prices && this.prices.length) {
            priceBook = this.prices.find(x => +x.id == +this.currentStaff.store.priceId);
        }

        if (this.selectedBill.isSetPrice) {
            this.changePriceBook(priceBook);
        }
    }

    setMaxLengPhoneInput(phone?: string) {
        return setMaxLengPhoneInput(phone);
    }

    checkInvalidPhone() {
        return this.validatorService.isVietnamesePhone(this.selectedBill.phone);
    }

    setBillPhone(value: string) {
        this.selectedBill.phone = onlyNumber(value);
        this.billService.saveBill(this.selectedBill);
    }

    setCustomerPhone(value: string) {
        this.customer.phone = onlyNumber(value);
    }
}
