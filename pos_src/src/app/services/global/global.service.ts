import {Injectable} from '@angular/core';
import {APPGLOBAL} from "../../global";
import {District} from "../district/district";
import {Observable} from "rxjs/Observable";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "../error/error.service";
import {HttpClient} from "@angular/common/http";
import {isOnline} from "../../lib/script";

@Injectable()
export class GlobalService {

    constructor(private http: HttpClient, private serviceError: ErrorService) {
    }

    getVariables(): Observable<any> {
        if(isOnline()) {
            return this.http.get(APPGLOBAL.API_URL + '/?site=main&act=get_global_variables').pipe(
                tap(res => {
                    this.serviceError.log("GlobalService: fetched variables");

                    //Save to cache
                    window.sessionStorage.setItem("config", JSON.stringify(res));
                }),
                catchError(this.serviceError.handleError<any>("GlobalService.getVariables"))
            )
        } else {
            return Observable.create((observer: any) => {
                let res = window.sessionStorage.getItem("config");
                res = JSON.parse(res);
                observer.next(res);
                observer.complete();
            });
        }
    }
}
