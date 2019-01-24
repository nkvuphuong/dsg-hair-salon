import {Component, OnInit, HostListener} from '@angular/core';
import {OrderService} from "../../services/order.service";
import {Order} from "../../models/order";
import {Router} from "@angular/router";
import * as moment from "moment";
import {Product} from "../../models/product";
import {ProductService} from "../../services/product.service";
import {StaffService} from "../../services/staff.service";
import {Staff} from "../../models/staff";
import {OrderItem} from "../../models/orderItem";
import {customHour2Sec, staffOwlCarousel} from "../../shared/lib/script";
import {ShiftWork} from "../../models/shift-work";
import {ShiftWorkService} from "../../services/shift-work.service";
import {WorkSchedule} from "../../models/work-schedule";
import {WorkScheduleService} from "../../services/work-schedule.service";
import {ToastrService} from "ngx-toastr";
import {GlobalService} from "../../services/global.service";
import {NgbDate} from "@ng-bootstrap/ng-bootstrap/datepicker/ngb-date";
import {Price} from "../../models/price";
import {PriceService} from "../../services/price.service";
import {DeviceDetectorService} from "ngx-device-detector";
import {Socket} from 'ngx-socket-io';

@Component({
    selector: 'app-book',
    templateUrl: './book.component.html',
    styleUrls: ['./book.component.css']
})
export class BookComponent implements OnInit {

    loading: boolean = false;
    order: Order;
    selectedItem: OrderItem;
    daysOfWeek: moment.Moment[] = [];
    products: Product[];
    staffs: Staff[];
    allStaffs: Staff[];
    orderStaffs: Staff[];
    orderStaffIds: number[];
    shiftworks: ShiftWork[];
    shiftworksTable: any[];
    workSchedules: WorkSchedule[];
    bookedSlots: any[];
    globalVariables: any;
    selectedCalendar: NgbDate;
    calendarMindate: any;
    step: number = 3;
    showFixedBtn: boolean = true;
    showFixedBtnFlag: boolean = false;
    prices: Price[];
    oriPrices: Price[];

    constructor(
        private orderService: OrderService,
        private router: Router,
        private productService: ProductService,
        private staffService: StaffService,
        private shiftWorkService: ShiftWorkService,
        private workScheduleService: WorkScheduleService,
        private toaStr: ToastrService,
        private globalService: GlobalService,
        private priceService: PriceService,
        private deviceService: DeviceDetectorService,
        private socket: Socket
    ) {
    }

    ngOnInit() {
        this.selectedItem = null;
        this.daysOfWeek = [];
        this.products = [];
        this.staffs = [];
        this.allStaffs = [];
        this.orderStaffs = [];
        this.orderStaffIds = [];
        this.shiftworks = [];
        this.shiftworksTable = null;
        this.workSchedules = [];
        this.bookedSlots = [];
        this.globalVariables = null;
        this.calendarMindate = {
            year: moment().get('year'),
            month: moment().get('month') + 1,
            day: moment().get('date')
        };
        this.loading = true;

        this.globalService.data.subscribe(
            res => this.globalVariables = res,
            err => console.log(err),
        );

        this.orderService.currentOrder.subscribe(
            order => {
                this.order = this.order ? this.order : order;

                if (!this.order.store) {
                    this.router.navigateByUrl("/store");
                } else {
                    this.priceService.getPrices(this.order.store.id, this.order.phone).subscribe(
                        (res: Price[]) => {
                            this.oriPrices = res;

                            this.productService.getProducts().subscribe(
                                (res: Product[]) => {
                                    this.products = res;

                                    this.staffService.getStaffs().subscribe(
                                        (res: Staff[]) => {
                                            this.allStaffs = res;
                                            for (let i = 0; i <= (this.deviceService.isMobile() ? 2 : 6); i++) {
                                                this.daysOfWeek.push(moment().add(i, 'd'));
                                            }

                                            this.shiftWorkService.getShiftWorks().subscribe(
                                                (res: ShiftWork[]) => {
                                                    this.shiftworks = res;
                                                    this.shiftworksTable = this.shiftWorkService.getShiftWorkTable(res, this.globalVariables.bookingStep);

                                                    this.workScheduleService.getWorkSchedules(this.order.store.id).subscribe(
                                                        (res: WorkSchedule[]) => {
                                                            this.workSchedules = res;
                                                            this.staffs = this.filterStaffsByWS(res);

                                                            //Get booked slots from order
                                                            this.orderService.getBookedSlots(this.order.store.id).subscribe(
                                                                res => {
                                                                    this.bookedSlots = res;
                                                                    if (this.order.orderItems && this.order.orderItems.length) { //Gán giá trị có sẵn cho các item
                                                                        this.order.orderItems.map(x => {
                                                                            let products = this.products.filter(p => x.product && x.product.id == p.id);
                                                                            x.product = products.length ? products[0] : null;
                                                                            x.price = null;
                                                                        });

                                                                        this.selectedItem = this.order.orderItems[0];
                                                                        this.setSelectedCalendar();
                                                                        this.loadOrderStaffs();
                                                                        this.filterPrices();
                                                                    } else {
                                                                        this.order.orderItems = [];
                                                                        this.addOrderItem();
                                                                    }
                                                                },
                                                                err => console.log(err),
                                                                () => this.loading = false
                                                            )
                                                        },
                                                        err => console.log(err)
                                                    );
                                                },
                                                err => console.log(err),
                                            );
                                        },
                                        err => console.log(err),
                                    );
                                },
                                err => console.log(err)
                            );
                        }
                    )
                }
            }
        )
    }

    /**
     * Update data of items
     * @param {OrderItem} item
     */
    updateOrderItem(item: OrderItem) {
        this.order.orderItems.map(x => item.key == this.selectedItem.key ? this.selectedItem : x);
    }

    /**
     * Add new order item
     */
    addOrderItem() {
        let lastIndex = this.order.orderItems[this.order.orderItems.length - 1] ? +this.order.orderItems[this.order.orderItems.length - 1].index : 0;
        let item = new OrderItem();
        item.date = this.daysOfWeek[0];
        item.index = lastIndex + 1;
        this.order.orderItems.push(item);

        setTimeout(() => {
            this.selectedItem = this.order.orderItems[this.order.orderItems.length - 1];
            this.setSelectedCalendar();
        }, 0);

    }

    /**
     * Remove order item
     * @param {OrderItem} item
     */
    removeOrderItem(item: OrderItem) {
        this.order.orderItems = this.order.orderItems.filter(x => x.key != item.key);
        if (item.key == this.selectedItem.key) {
            this.selectedItem = this.order.orderItems[this.order.orderItems.length - 1];
        }
        this.setSelectedCalendar();
    }

    /**
     * Get staffs by work schedule
     * @param {WorkSchedule[]} workSchedules
     * @returns {Staff[]}
     */
    filterStaffsByWS(workSchedules: WorkSchedule[]): Staff[] {
        let rs = [];
        workSchedules.forEach(x => {
            let staff = this.allStaffs.filter(s => s.id == x.staffId);
            if (staff && rs.indexOf(staff[0]) < 0) {
                rs.push(staff[0]);
            }
        })
        return rs;
    }

    /**
     * Load and show staff after filter
     */
    loadOrderStaffs() {
        this.orderStaffs = [];
        this.orderStaffIds = [];

        let staffByDate = [];
        //Lấy danh sách nhân viên làm việc trong ngày đang chọn
        this.workSchedules.forEach(x => {
            if (x.date == this.selectedItem.date.format('YYYY-MM-DD')) {
                staffByDate.push(x.staffId);
            }
        })

        //Lọc theo dịch vụ
        if (this.selectedItem.product && this.selectedItem.product.staffIds) {
            this.selectedItem.product.staffIds.forEach(staffId => {
                staffId = +staffId;
                this.staffs.forEach(staff => {
                    if (staff.id == staffId) {
                        this.orderStaffs.push(staff);
                        return;
                    }
                })
            });

            //Lọc lại theo ngày làm việc đang chọn
            this.orderStaffs = this.orderStaffs.filter(x => staffByDate.indexOf(x.id) > -1)

            this.orderStaffs.forEach(x => {
                this.orderStaffIds.push(+x.id);
            })
        }

        let defaultStaff = new Staff();
        defaultStaff.name = "Mặc định";
        defaultStaff.avatar = this.globalVariables.noAvatar ? this.globalVariables.noAvatar : null;
        this.orderStaffs.unshift(defaultStaff);

        //Check if selects staff not available then remove it
        if (this.selectedItem.staff && this.orderStaffIds.indexOf(this.selectedItem.staff.id) < 0) {
            this.selectedItem.staff = new Staff();
        }

        this.filterWorkSchedules();

        if (this.orderStaffs.length) {
            staffOwlCarousel();
        }
    }

    /**
     * Filter work schedule by date and staff then rebuild shift work table
     */
    filterWorkSchedules() {
        let workSchedules = this.workSchedules
            .filter(
                x => {
                    return x.storeId == this.order.store.id && x.date == this.selectedItem.date.format("YYYY-MM-DD") && this.orderStaffIds.indexOf(x.staffId) > -1;
                }
            );

        //Convert schedule to steps then group
        let slots = [];

        if (workSchedules) {
            workSchedules.forEach(x => {
                x.swSteps.forEach(step => {
                    slots[step] = slots[step] != null ? slots[step] : [];
                    let maxSlots = x.maxSlots > 1 ? x.maxSlots : 1; //Mỗi nhân viên có 1 maxSlots riêng
                    if (slots[step].indexOf(x.staffId) < 0) {
                        for (let i = 1; i <= maxSlots; i++) {
                            slots[step].push(x.staffId);
                        }
                    }
                })
            });
        }

        //remove slot in other items
        let bookedSlots = [];
        this.order.orderItems.forEach(x => {
            if (x.date == this.selectedItem.date) {
                if (x.hour) {
                    bookedSlots[x.hour] = bookedSlots[x.hour] ? bookedSlots[x.hour] : [];
                    if (x.key != this.selectedItem.key) {
                        bookedSlots[x.hour].push(x.staff ? x.staff.id : null);
                    }
                }
            }
        });

        this.bookedSlots.forEach(x => {
            if (x.date == this.selectedItem.date.format('YYYY-MM-DD')) {
                if (x.hour) {
                    bookedSlots[x.hour] = bookedSlots[x.hour] ? bookedSlots[x.hour] : [];
                    bookedSlots[x.hour].push(x.staff ? x.staff.id : null);
                }
            }
        })

        //rebuild table
        if (this.shiftworksTable) {
            this.shiftworksTable.map(x => {
                x.slots = slots[x.hour] ? slots[x.hour].length : 0;
                x.slots = bookedSlots[x.hour] ? x.slots - bookedSlots[x.hour].length : x.slots;

                if (this.selectedItem.staff && this.selectedItem.staff.id) { //Khi chon nhan vien cu the
                    if (slots[x.hour] && slots[x.hour].indexOf(this.selectedItem.staff.id) < 0) {
                        x.slots = 0;
                    }

                    if (x.slots > 0) {
                        if (bookedSlots[x.hour]) {
                            //Check maxSlots by staff
                            let checkMaxSlots = slots[x.hour].filter(slot => slot == this.selectedItem.staff.id);
                            //Check bookedSlots by staff
                            let checkBookedSlots = bookedSlots[x.hour].filter(slot => slot == this.selectedItem.staff.id);
                            x.slots = checkMaxSlots.length - checkBookedSlots.length;
                        }
                    }
                }

                //If slots hour is in the past then set slots = 0
                let today = moment().set({'hour': 0, 'minute': 0, 'second': 0, 'millisecond': 0});
                let now = moment();
                let selectedDate = this.selectedItem.date.set({'hour': 0, 'minute': 0, 'second': 0, 'millisecond': 0});

                if (today.format('x') == selectedDate.format('x')) {
                    if (customHour2Sec(x.hour) < customHour2Sec(now.format('H : m'))) {
                        x.slots = 0;
                    }
                } else if (today.format('x') > selectedDate.format('x')) {
                    x.slots = 0;
                }


                x.slots = x.slots < 0 ? 0 : x.slots;
                return x;
            });
        }


        if (!slots[this.selectedItem.hour] || (this.selectedItem.staff && this.selectedItem.staff.id && (slots[this.selectedItem.hour].indexOf(this.selectedItem.staff.id) < 0))) {
            this.selectedItem.hour = null;
        }

        this.getTotal();
    }

    /**
     * Check item is valid ?
     * @param {OrderItem} item
     * @returns {boolean}
     */
    itemIsValid(item: OrderItem): boolean {
        if (!item.product || !item.hour || item.hour == '') return false;
        return true;
    }

    orderIsValid(order: Order): boolean {
        let rs = true;

        if (!order.orderItems || order.orderItems.length == 0) rs = false;

        order.orderItems.forEach(x => {
            if (!this.itemIsValid(x)) {
                rs = false;
                return;
            }
        });

        return rs;
    }

    /**
     * Get total of order
     * @returns {number}
     */
    getTotal(): number {
        return this.orderService.getTotal(this.order);
    }

    getTotalEstimatedTime(): number {
        return this.orderService.getEstimatedTime(this.order);
    }

    /**
     * Add order to DB
     */
    add(): void {
        this.loading = true;

        this.orderService.addOrder(this.order).subscribe(
            res => {
                if (res.status == 'success') {
                    this.toaStr.success(res.msg);

                    //Remove items
                    // this.order.note = null;
                    // this.ngOnInit();

                    //Gửi thông báo có đơn hàng mới tới ACP và POS
                    this.socket.emit('new_order', res.orderId);

                    this.router.navigateByUrl(`/complete/${res.payload.phone}/${res.orderId}`);
                } else {
                    this.toaStr.error(res.msg);

                    if (res.naItems) { //Những item khong khả dụng
                        res.naItems.forEach(keyItem => {
                            this.order.orderItems.map(x => {
                                if (x.key == keyItem) {
                                    x.hour = null; //Bỏ khung giờ không khả dụng
                                }
                            })
                        })
                        this.ngOnInit();
                    }
                }
            },
            err => {
                this.toaStr.error("Có lỗi xảy ra. Vui lòng liên hệ hotline để được hỗ trợ.");
                console.log(err);
            },
            () => this.loading = false
        );
    }

    selectCalendar() {
        this.selectedItem.date = moment().set({
            'year': this.selectedCalendar.year,
            'month': this.selectedCalendar.month - 1,
            'date': this.selectedCalendar.day
        });
        this.loadOrderStaffs();
        this.updateOrderItem(this.selectedItem);
        this.filterWorkSchedules();
    }

    setSelectedCalendar() {
        // this.selectedCalendar = new NgbDate(this.selectedItem.date.get('year'), this.selectedItem.date.get('month')+1, this.selectedItem.date.get('date'));
    }

    filterPrices(defaultPriceId = null) {
        defaultPriceId = +defaultPriceId;
        if (!this.selectedItem) return false;
        this.selectedItem.price = null;
        this.selectedItem.prices = null;
        //Filter by product
        if (this.oriPrices && this.selectedItem.product) {
            let oriPrice = JSON.parse(JSON.stringify(this.oriPrices));
            this.selectedItem.prices = oriPrice.filter(x => {
                if (x.items.length == 0) return false;
                let check = x.items.some(item => {
                    if (item.productId == this.selectedItem.product.id) return true;
                    return false;
                })
                return check;
            });
            this.selectedItem.prices = this.selectedItem.prices.length ? this.selectedItem.prices : null;

            if (this.selectedItem.prices) {
                if (defaultPriceId) {
                    let priceBook = this.selectedItem.prices.find(x => +x.id == +defaultPriceId);
                    if (priceBook) {
                        this.selectedItem.price = priceBook;
                    }
                }
                this.selectedItem.price = this.selectedItem.price ? this.selectedItem.price : this.selectedItem.prices[0];

                if (this.selectedItem.price) {
                    this.overwriteProductByPrice();
                }
            }
        }
        ;
        this.updateOrderItem(this.selectedItem);
    }

    overwriteProductByPrice() {
        let price = this.selectedItem.price;
        this.selectedItem.product.price = this.selectedItem.product.oriData.price;
        if (price && price.items) {
            let priceItems = price.items.filter(x => x.productId == this.selectedItem.product.id);
            if (priceItems && priceItems[0]) {
                this.selectedItem.product.price = priceItems[0].price;
            }
        }
        this.updateOrderItem(this.selectedItem);
    }

    showPriceOption(price?: Price): number {
        let priceRs = this.selectedItem.product.oriData.price;

        if (price && price.items) {
            price.items.some(item => {
                if (item.productId == this.selectedItem.product.id) {
                    priceRs = item.price;
                    return true;
                }
                return false;
            })
        }

        return priceRs;
    }

    getDocHeight() {
        var D = document;
        return Math.max(
            D.body.scrollHeight, D.documentElement.scrollHeight,
            D.body.offsetHeight, D.documentElement.offsetHeight,
            D.body.clientHeight, D.documentElement.clientHeight
        );
    }

    onLinkClick(e) {
        if (!this.order.orderItems[e.index]) return;
        let item = this.order.orderItems[e.index];
        this.selectedItem = item;
        this.setSelectedCalendar();
        this.loadOrderStaffs();
    }

    getIndexSelectedItem(): number {
        return this.order.orderItems.findIndex(x => {
            if (!x || !this.selectedItem) return false;
            return x.key == this.selectedItem.key;
        });
    }
}
