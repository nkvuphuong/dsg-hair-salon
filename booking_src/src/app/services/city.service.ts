import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {APPGLOBAL} from "../global";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "./error.service";
import {City} from "../models/city";
import {Observable, BehaviorSubject} from "rxjs";

@Injectable()
export class CityService {

    private citiesSource = new BehaviorSubject<City[]>(new Array());
    cities = this.citiesSource.asObservable();

    constructor(private http: HttpClient, private errorService: ErrorService) {
    }

    getCities(): Observable<City[]> {
        return this.http.get<City[]>(APPGLOBAL.API_URL + '/?site=city&act=get_cities').pipe(
            tap(res => {
                this.errorService.log("CityService: fetched service");
            }),
            catchError(this.errorService.handleError<City[]>("CiyService.getCities"))
        )
    }

    setCities(cities: City[]) {
        this.citiesSource.next(cities);
    }
}
