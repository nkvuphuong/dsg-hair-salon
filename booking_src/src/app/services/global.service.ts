import {Injectable} from '@angular/core';
import {Observable} from "rxjs";
import {catchError, tap} from "rxjs/operators";
import {HttpClient} from "@angular/common/http";
import {ErrorService} from "./error.service";
import {APPGLOBAL} from "../global";
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";

@Injectable()
export class GlobalService {

    source = new BehaviorSubject<any>(null);
    data = this.source.asObservable();

    constructor(private http: HttpClient, private serviceError: ErrorService) {
    }

    getVariables(): Observable<any> {
        return this.http.get(APPGLOBAL.API_URL + '/?site=main&act=get_global_variables').pipe(
            tap(res => {
                this.serviceError.log("GlobalService: fetched variables");
            }),
            catchError(this.serviceError.handleError<any>("GlobalService.getVariables"))
        )
    }

    setData(data: any) {
        this.source.next(data);
    }
}
