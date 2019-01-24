import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ErrorService} from "../error/error.service";
import {District} from "../district/district";
import {Observable} from "rxjs/Observable";
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {of} from "rxjs/observable/of";

@Injectable()
export class WardService {

  constructor(private http: HttpClient, private errorService: ErrorService) { }

  getWards(district: District): Observable<District[]> {
    if(!district) return of([]);
    return this.http.get<District[]>(APPGLOBAL.API_URL+'/?site=ward&act=get_wards&district='+district.id).pipe(
        tap(_ => this.errorService.log("WardService: fetched wards")),
        catchError(this.errorService.handleError<District[]>("WardService.getWards"))
    )
  }

}
