import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ErrorService} from "../error/error.service";
import {City} from "../city/city";
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {Observable} from "rxjs/Observable";
import {District} from "./district";
import {of} from "rxjs/observable/of";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {isOnline} from "../../lib/script";

@Injectable()
export class DistrictService {

    private dbStoreName = "districts";

    constructor(private http: HttpClient, private serviceError: ErrorService, private idbService: IndexedDbService) {
    }

    getDistricts(city: City): Observable<District[]> {
        if (!city) return of([]);

        if (isOnline()) {
            return this.http.get<District[]>(APPGLOBAL.API_URL + '/?site=district&act=get_districts&city=' + city.id).pipe(
                tap(res => {
                    res.forEach(x => this.idbService.put(this.dbStoreName, x).subscribe())
                    this.serviceError.log("DistrictService: fetched districts");
                }),
                catchError(this.serviceError.handleError<District[]>("DistrictService.getDistricts"))
            )
        } else { //Offline
            console.log(city.id);
            return this.idbService.all(this.dbStoreName, "cityId_idx", false, IDBKeyRange.only(city.id));
        }
    }
}
