import {Injectable} from '@angular/core';
import {Bill, PrintingBill} from "./bill";
import {BillItem} from "../bill-item/bill-item";
import {APPGLOBAL} from "../../global";
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {Observable} from "rxjs/Observable";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "../error/error.service";
import {Staff} from "../staff/staff";
import {StaffService} from "../staff/staff.service";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {Subscription} from "rxjs/src/Subscription";
import {customDateTimeFormat, customNumberFormat} from "../../lib/script";
import * as moment from "moment";
import {CookieService} from "ngx-cookie-service";
import {Store} from "../store/store";

@Injectable()
export class BillService {

    private httpOptions = {
        headers: new HttpHeaders({'Content-Type': 'application/json'})
    };

    constructor(private http: HttpClient, private errorService: ErrorService, private staffService: StaffService, private idbService: IndexedDbService, private cookieService: CookieService) {
    }

    getBills(store?: Store, productType = 0): Bill[] {
        let session = sessionStorage.getItem("bills");
        if (!session || !JSON.parse(session).length) {
            this.addBill(null, productType, store);
        }
        return JSON.parse(sessionStorage.getItem("bills"));
    }

    addBill(bill?: Bill, productType: number = 0, store: Store = null): Bill {
        let bills = JSON.parse(sessionStorage.getItem("bills"));
        let isDuplicate = false;

        bills = bills ? bills : [];

        if (!bill) {
            bill = new Bill();
            bill.store = store || null;
            bill.productType = productType;
            bill.staff = this.staffService.getCurrentStaff();
        } else {
            //Check duplicate
            bills.forEach((x: Bill, k) => {
                if (x.key == bill.key) {
                    isDuplicate = true;
                    return false;
                }
            })
        }

        if (!isDuplicate) {
            bills.push(bill);
            sessionStorage.setItem("bills", JSON.stringify(bills));
        } else {
            this.saveBill(bill);
        }

        return bill;
    }

    saveBill(bill: Bill) {
        let bills = JSON.parse(sessionStorage.getItem("bills"));
        bills = bills.map(b => b.key == bill.key ? bill : b);
        sessionStorage.setItem("bills", JSON.stringify(bills));
    }

    removeBill(bill: Bill): Bill {
        let bills = JSON.parse(sessionStorage.getItem("bills"));
        bills = bills.filter(b => {
            return b.key != bill.key;
        });
        sessionStorage.setItem("bills", JSON.stringify(bills));
        return bill;
    }

    addItem(item: BillItem, bill: Bill) {
        if (bill.productType == 1) { //Dịch vu
            bill.items.push(this.calculateItem(item));
        } else {
            if (bill.items.find(x => x.product.id == item.product.id)) {
                bill.items = bill.items.map(x => {
                    if (x.product.id == item.product.id) {
                        x.quantity++;
                    }
                    return this.calculateItem(x);
                })
            } else {
                bill.items.push(this.calculateItem(item));
            }
        }
        this.saveBill(bill);
        return bill;
    }

    calculateItem(item: BillItem): BillItem {
        item.subTotal = item.price * item.quantity;

        if (item.taxType == 0) {
            item.taxAmount = (item.price * item.taxValue) / 100;
        } else {
            item.taxAmount = item.taxValue * 1;
        }
        item.taxAmount = item.taxAmount * item.quantity;
        item.taxAmount = item.taxAmount < 0 ? 0 : item.taxAmount;

        if (item.discountType == 0) {
            item.discountValue = item.price ? 100 - ((item.price / item.oldPrice) * 100) : 0;
        } else {
            item.discountValue = item.oldPrice - item.price;
        }
        item.discountValue = item.discountValue < 0 ? 0 : item.discountValue;

        item.discountAmount = (item.oldPrice - item.price) * item.quantity;
        item.discountAmount = item.discountAmount < 0 ? 0 : item.discountAmount;

        item.total = item.subTotal - item.discountAmount;

        item.discountValue = customNumberFormat(item.discountValue);
        item.discountAmount = customNumberFormat(item.discountAmount);
        item.taxAmount = customNumberFormat(item.taxAmount);

        return item;
    }

    calculateBill(bill: Bill): Bill {

        let checkTaxRs = this.checkTaxBillItems(bill.items);

        bill.subTotal = 0;
        bill.total = 0;
        bill.taxAmount = 0;
        bill.paymentAmount = 0;
        bill.discountAmount = 0;
        bill.excessCash = 0;
        bill.items.forEach(x => {
            bill.subTotal += x.subTotal;
            bill.taxAmount += x.taxAmount;
        })

        if (checkTaxRs.status) {
            bill.discountAmount = bill.discountType == 0 ? (bill.discountValue * bill.subTotal) / 100 : bill.discountValue;
            bill.taxAmount = (bill.subTotal - bill.discountAmount) * (checkTaxRs.taxValue / 100);
        } else {
            bill.discountAmount = 0;
            bill.discountValue = 0;
        }

        bill.disabledDiscount = !checkTaxRs.status;


        bill.total = bill.subTotal - bill.discountAmount + bill.taxAmount;
        bill.paymentAmount = bill.total;

        bill.discountValue = customNumberFormat(bill.discountValue);
        bill.discountAmount = customNumberFormat(bill.discountAmount);
        bill.taxAmount = customNumberFormat(bill.taxAmount);
        bill.paymentAmount = customNumberFormat(bill.paymentAmount);
        bill.total = customNumberFormat(bill.total);
        bill.excessCash = customNumberFormat(bill.excessCash);

        return bill;
    }

    doPayment(bill: Bill): Observable<any> {

        let billData: Bill = JSON.parse(JSON.stringify(bill));

        billData.items = billData.items.map(item => {
            item.bookingTime = moment(`${item.date} ${item.hour}`, 'DD/MM/YYYY HH : mm').format("YYYY-MM-DD HH:mm:00");
            return item;
        })

        return this.http.post<any>(APPGLOBAL.API_URL + '/?site=bill&act=payment', billData, this.httpOptions).pipe(
            tap(res => {
                this.errorService.log("BillService: do payment")
                if (res.trx) {
                    let printingBill: PrintingBill = res.trx;
                    printingBill.isOffline = 0;
                    this.idbService.put("printing_bills", printingBill).subscribe();
                }
            }),
            catchError(this.errorService.handleError<any>("BillService.doPayment"))
        )
    }

    doPaymentOffline(): Observable<number> {
        return this.idbService.count("printing_bills", "isOffline_idx", IDBKeyRange.only(1));
    }

    /**
     * Get printing bills from api
     * @returns {Observable<any>}
     */
    getPrintingBills(storeId: number, dataFilter: {} = null, perPage: number = 10, currentPage = 1): Observable<any> {

        let params = "";
        if (dataFilter) {
            params = "&" + $.param(dataFilter);
        }

        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=bill&act=load_bills&per_page=' + perPage + '&page=' + currentPage + '&store=' + storeId + params).pipe(
            tap(res => {
                //save to cache
                res.bills.forEach((bill: PrintingBill, i) => {
                    bill.isOffline = 0;
                    bill.time = customDateTimeFormat(bill.timestamp);
                    this.idbService.put("printing_bills", bill).subscribe();
                })

                this.errorService.log("BillService: fetched bills")
            }),
            catchError(this.errorService.handleError<any>("BillService.loadPrintingBills"))
        )
    }

    /**
     * Get html to print
     * @param {number} id
     * @returns {Observable<any>}
     */
    getPrintingHtml(id: number): Observable<any> {
        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=bill&act=print_bill&id=' + id).pipe(
            tap(res => this.errorService.log("BillService: get printing html")),
            catchError(this.errorService.handleError<any>("BillService.getPrintingHtml"))
        )
    }

    /**
     * Convert bill offline format to printing bill
     * @param {Bill} bill
     * @returns {PrintingBill}
     */
    conver2PrintingBill(bill: Bill): PrintingBill {
        let printingBill = new PrintingBill();

        var d = new Date();

        printingBill.code = "TRXOFF_" + bill.staff.id + "_" + d.getTime() + "_" + Math.round(Math.random() * 1000);
        printingBill.time = null;
        printingBill.status = "N/A";
        printingBill.total = bill.total;
        printingBill.amount = bill.subTotal;
        printingBill.customer = bill.customer;
        printingBill.discountTotal = bill.discountAmount;
        printingBill.invoiceNo = "N/A";
        printingBill.items = bill.items;
        printingBill.tax = bill.taxAmount;
        printingBill.excessCash = bill.excessCash;
        printingBill.paymentAmount = bill.paymentAmount;

        return printingBill;
    }

    /**
     * Count syncable bill
     * @returns {Observable<number>}
     */
    countSyncable(): Observable<number> {
        return this.idbService.count("printing_bills", "isOffline_idx", IDBKeyRange.only(1));
    }

    /**
     * Kiểm tra tính đồng bộ giữa % thuế của các items.
     * + Nếu các items có thuế không giống nhau hoàn toàn thì status = false.
     * + Nếu các items có thuế giống nhau hoàn toàn thì status = true, tax = tax chung
     * @param {BillItem[]} items
     * @returns {any}
     */
    checkTaxBillItems(items: BillItem[]): any {
        let rs = {
            status: true,
            taxValue: 0
        };

        if (!items || items.length == 0) {
            rs.status = false;
        } else {
            let taxValue = items[0].taxValue;

            items.forEach(item => {
                if (item.taxValue != taxValue) {
                    rs.status = false;
                    return false;
                }
            })

            if (rs.status) {
                rs.taxValue = taxValue;
            }
        }

        return rs;
    }

    countQuantity(bill: Bill): number {
        let total = 0;

        bill.items.forEach((item: BillItem, i) => {
            total += +item.quantity;
        })

        return total;
    }

    getBookedSlots(storeId): Observable<any> {
        return this.http.get<any>(APPGLOBAL.API_BOOKING_URL + '/?site=order&act=get_booked_hours&store=' + storeId).pipe(
            tap(res => this.errorService.log("OrderService: getBookedSlots")),
            catchError(this.errorService.handleError<any>("OrderService.getBookedSlots"))
        )
    }
}
