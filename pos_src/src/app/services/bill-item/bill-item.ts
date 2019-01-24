import {Product} from "../product/product";
import {Md5} from "ts-md5";
import * as moment from "moment";
import {Staff} from "../staff/staff";
import {BookingHour} from "../booking-hour/booking-hour";

export class BillItem {
    key: any;
    productName: string;
    productId: number;
    quantity: number;
    price: number;
    oldPrice: number;
    subTotal: number;
    total: number;
    taxValue: number;
    taxType: number;
    taxAmount: number;
    discountAmount: number;
    discountType: number;
    discountValue: number;
    bookingTime: any; //Use for services
    staff: Staff; //use for services
    staffs: Staff[]; //use for services
    date: string;
    hour: string;
    bookingHours: BookingHour[];
    oriBookingHours: BookingHour[];

    constructor(public product: Product) {
        let m = moment().format("x");
        this.key = Md5.hashStr(m);
        this.productName = this.product.name;
        this.productId = this.product.id;
        this.oldPrice = this.product.price * 1;
        this.price = this.oldPrice;
        this.taxValue = this.product.tax;
        this.taxType = 0; //0: percent; 1: amount
        this.taxAmount = 0;
        this.discountAmount = 0;
        this.discountValue = 0;
        this.discountType = 0; //0: percent; 1: amount
        this.quantity = 1;
        this.total = 0;
        this.subTotal = 0;
        this.bookingTime = null;
        this.staff = new Staff();
        this.date = moment().set({'h':0, 'm':0, 's':0}).format("DD/MM/YYYY");
        this.bookingHours = [];
        this.oriBookingHours = [];
        this.hour = null;
    }
}