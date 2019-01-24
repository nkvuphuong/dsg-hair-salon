import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "../error/error.service";
import {Observable} from "rxjs/Observable";
import {Store} from "./store";
import {APPGLOBAL} from "../../global";


@Injectable()
export class StoreService {
    constructor(private http: HttpClient, private errorService: ErrorService) {
    }

    getStores(): Observable<Store[]> {
        return this.http.get<Store[]>(APPGLOBAL.API_BOOKING_URL + '/?site=store&act=get_stores').pipe(
            tap(res => {
                this.errorService.log("StoreService: fetched service")
            }),
            catchError(this.errorService.handleError<Store[]>("StoreService.getStores"))
        )
    }
}
