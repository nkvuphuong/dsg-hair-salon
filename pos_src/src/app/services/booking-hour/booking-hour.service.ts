import {Injectable} from '@angular/core';
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {Observable} from "rxjs/Observable";
import {BookingHour} from "./booking-hour";
import {HttpClient} from "@angular/common/http";
import {ErrorService} from "../error/error.service";

@Injectable()
export class BookingHourService {

    constructor(
        private http: HttpClient,
        private errorService: ErrorService
    ) {
    }

    getBookingHours(storeId:number, date:string, staffId?: number): Observable<{} | BookingHour[]> {
        return this.http.get<BookingHour[]>(APPGLOBAL.API_BOOKING_URL + `/?site=work_schedule&act=get_booking_hours&store=${storeId}&date=${date}&staff=${staffId}`).pipe(
            tap(res => {
                return this.errorService.log("BookingHourService: fetched booking hours");
            }),
            catchError(this.errorService.handleError<BookingHour[]>("BookingHourService.getBookingHours", []))
        );
    }
}
