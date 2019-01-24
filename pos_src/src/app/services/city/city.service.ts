import { Injectable } from '@angular/core';
import {Observable} from "rxjs/Observable";
import {City} from "./city";
import {HttpClient} from "@angular/common/http";
import {APPGLOBAL} from "../../global";
import {ErrorService} from "../error/error.service";
import {catchError, tap} from "rxjs/operators";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {isOnline} from "../../lib/script";

@Injectable()
export class CityService {

  private dbStoreName = "cities";

  constructor(private http: HttpClient, private errorService: ErrorService, private idbService: IndexedDbService) { }

  getCities(): Observable<City[]> {
    if(isOnline()) {
        return this.http.get<City[]>(APPGLOBAL.API_URL + '/?site=city&act=get_cities').pipe(
            tap(res => {
                //Save to cache
                res.forEach(x => this.idbService.put(this.dbStoreName, x).subscribe());

                this.errorService.log("CityService: fetched cities")
            }),
            catchError(this.errorService.handleError<City[]>("CiyService.getCities"))
        )
    } else { //Offline
      return this.idbService.all(this.dbStoreName);
    }
  }

  getCitiesHaveStores(): Observable<City[]> {
      return this.http.get<City[]>(APPGLOBAL.API_BOOKING_URL + "/?site=city&act=get_cities").pipe(
          tap(res => {
              //Save to cache
              res.forEach(x => this.idbService.put(this.dbStoreName, x).subscribe());

              this.errorService.log("CityService: fetched cities have stores")
          }),
          catchError(this.errorService.handleError<City[]>("CiyService.getCitiesHaveStores"))
      )
  }

}
