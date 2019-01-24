import { Injectable } from '@angular/core';
import {Observable} from "rxjs";
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {ErrorService} from "../error/error.service";

@Injectable()
export class OrderService {

    private httpOptions = {
        headers: new HttpHeaders({'Content-Type': 'application/json'})
    };

  constructor(private http: HttpClient, private errorService: ErrorService) { }

  checkRating(ordId: number): Observable<any> {
        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=order&act=check_rating&id=' + ordId).pipe(
            tap(res => this.errorService.log("OrderService: check rating")),
            catchError(this.errorService.handleError<any>("OrderService.OrderService"))
        )
    }
}
