import {BillItem} from "../bill-item/bill-item";
import {Md5} from 'ts-md5/dist/md5';
import * as moment from 'moment';
import {Customer} from "../customer/customer";
import {Staff} from "../staff/staff";
import {City} from "../city/city";
import {Store} from "../store/store";
import {Price} from "../product/price";

export class Bill {
    key: any;
    items: BillItem[] = [];
    subTotal: number = 0;
    total: number = 0;
    taxAmount: number = 0;
    discountAmount: number = 0;
    discountType: number = 0; //0: phần trăm, 1: số tiền
    discountValue: number = 0;
    paymentAmount: number = 0;
    excessCash: number = 0; //tiền thối
    customer: Customer = new Customer();
    customers: Customer[] = [];
    paymentMethod: number = 0;
    paymentMethods: any[] = [
        {
            id: 0,
            name: "Tiền mặt",
        },
        {
            id: 4,
            name: "ATM",
        },
        {
            id: 5,
            name: "VISA / MASTER",
        },
    ];
    staff: Staff = new Staff();
    isOffline: number = 0;
    printingBillId: number;
    disabledDiscount: boolean = false;
    productType: number = 0; //0: SP, 1: DV
    city: City;
    store: Store;
    note: string;
    price: Price;
    isSetPrice: number = 1; //Để đánh dấu bill cần được set bảng giá dùng cho DashboardComponent.setPriceBookForBill()
    phone: string = "";

    constructor() {
        let m = moment().format("x");
        this.key = Md5.hashStr(m);
    }
}

export class PrintingBill {
    id: number;
    code: string;
    time: string;
    timestamp: number;
    discountTotal: number;
    tax: number;
    total: number;
    amount: number;
    status: string;
    invoiceNo: string;
    items: BillItem[];
    customer: Customer;
    isOffline: number = 0;
    bill: Bill;
    excessCash: number;
    paymentAmount: number;
    phone: string;
    storeId: number;
    ordId: number;
    ratingStatus: number;
}