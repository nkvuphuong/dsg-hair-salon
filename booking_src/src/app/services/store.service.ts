import {Injectable} from '@angular/core';
import {Store} from "../models/store";
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {HttpClient} from "@angular/common/http";
import {APPGLOBAL} from "../global";
import {ErrorService} from "./error.service";
import {Observable} from "rxjs/index";
import {catchError, tap} from "rxjs/operators";


@Injectable({
    providedIn: 'root'
})
export class StoreService {
    private storesSource = new BehaviorSubject<Store[]>(new Array());
    stores = this.storesSource.asObservable();

    constructor(private http: HttpClient, private errorService: ErrorService) {
    }

    getStores(): Observable<Store[]> {
        return this.http.get<Store[]>(APPGLOBAL.API_URL + '/?site=store&act=get_stores').pipe(
            tap(res => {
                this.errorService.log("StoreService: fetched stores")
            }),
            catchError(this.errorService.handleError<Store[]>("CiyService.getStores"))
        )
    }

    setStores(stores: Store[]) {
        this.storesSource.next(stores);
    }
}
