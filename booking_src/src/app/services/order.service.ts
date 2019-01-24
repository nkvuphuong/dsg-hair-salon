import {Injectable} from '@angular/core';
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {Order} from "../models/order";
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {ErrorService} from "./error.service";
import {Observable} from "rxjs/internal/Observable";
import {catchError, tap} from "rxjs/operators";
import {APPGLOBAL} from "../global";

@Injectable({
    providedIn: 'root'
})
export class OrderService {

    private httpOptions = {
        headers: new HttpHeaders({'Content-Type': 'application/json'})
    };
    private orderSource = new BehaviorSubject<Order>(new Order());
    currentOrder = this.orderSource.asObservable();

    constructor(private http: HttpClient, private errorService: ErrorService) {
    }

    setOrder(order: Order) {
        this.orderSource.next(order);
    }

    addOrder(order: Order): Observable<any> {
        return this.http.post<any>(APPGLOBAL.API_URL + '/?site=order&act=add', order, this.httpOptions).pipe(
            tap(res => {
                this.errorService.log("OrderService: addOrder")
            }),
            catchError(this.errorService.handleError<any>("OrderService.addOrder"))
        )
    }

    getBookedSlots(storeId): Observable<any> {
        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=order&act=get_booked_hours&store=' + storeId).pipe(
            tap(res => this.errorService.log("OrderService: getBookedSlots")),
            catchError(this.errorService.handleError<any>("OrderService.getBookedSlots"))
        )
    }

    /**
     * Get total of order
     * @returns {number}
     */
    getTotal(order: Order): number {
        let total = 0;
        order.orderItems.forEach(x => {
            if (x.product) {
                let totalItem = x.product.price * ((100 - x.product.tax) / 100);
                total += totalItem;
            }
        });
        return total;
    }

    getEstimatedTime(order: Order): number {
        let total = 0;
        order.orderItems.forEach(x => {
            if (x.product) {
                total += +x.product.estimatedTime;
            }
        });
        return total;
    }

    cancel(orderId: number, phone: string): Observable<any> {
        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=order&act=cancel&order=' + orderId + "&phone=" + phone).pipe(
            tap(res => this.errorService.log("OrderService: cancelled order")),
            catchError(this.errorService.handleError<any>("OrderService.cancel"))
        )
    }

    getOrders(phone?: string): Observable<any> {
        let params = [];

        if (phone) {
            params.push(`phone=${phone}`);
        }

        let urlParams = params.length ? "&" + params.join("&") : '';

        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=order&act=get_orders' + urlParams).pipe(
            tap(res => this.errorService.log("OrderService: fetch order")),
            catchError(this.errorService.handleError<any>("OrderService.getOrders"))
        )
    }

    rate(orderId: number, rate: number): Observable<any> {

        orderId = +orderId;
        rate = +rate;

        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=order&act=rate&order=' + orderId + "&rate=" + rate).pipe(
            tap(res => this.errorService.log("OrderService: rated order")),
            catchError(this.errorService.handleError<any>("OrderService.rate"))
        )
    }
}
