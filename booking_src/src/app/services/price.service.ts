import { Injectable } from '@angular/core';
import {APPGLOBAL} from "../global";
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {ErrorService} from "./error.service";
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {catchError, tap} from "rxjs/operators";
import {Price} from "../models/price";

@Injectable({
  providedIn: 'root'
})
export class PriceService {

    private apiUrl = APPGLOBAL.API_URL + '/?site=price';
    productSource = new BehaviorSubject<Price[]>(new Array());
    prices = this.productSource.asObservable();

    constructor(private errorService: ErrorService, private http: HttpClient) {
    }

    getPrices(storeId: number, phone?: string, order: string = "id", by: string = "asc"): Observable<Price[]> {
        const url = `${this.apiUrl}&act=get_prices&store_id=${storeId}&phone=${phone}&order=${order}&by=${by}`;
        return this.http.get<Price[]>(url).pipe(
            tap(res => {
                return this.errorService.log("PriceService: fetched prices");
            }),
            catchError(this.errorService.handleError<Price[]>("PriceService.getPrices", []))
        );
    }

    setPrices(prices: Price[]) {
        this.productSource.next(prices);
    }
}
