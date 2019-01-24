import {Injectable} from '@angular/core';
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {ShiftWork} from "../models/shift-work";
import {catchError, tap} from "rxjs/operators";
import {customSec2Hour} from "../shared/lib/script";
import {Observable} from "rxjs/index";
import {APPGLOBAL} from "../global";
import {ErrorService} from "./error.service";
import {HttpClient} from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})
export class ShiftWorkService {
    private shiftWorksSource = new BehaviorSubject<ShiftWork[]>(new Array());
    public shiftWorks = this.shiftWorksSource.asObservable();

    constructor(private http: HttpClient, private errorService: ErrorService) {
    }

    getShiftWorks(): Observable<{} | ShiftWork[]> {
        return this.http.get<ShiftWork[]>(APPGLOBAL.API_URL + '/?site=shift_work&act=get_shift_works').pipe(
            tap(res => {
                this.errorService.log("ShiftWorkService: fetched shift works");
            }),
            catchError(this.errorService.handleError<ShiftWork[]>("ShiftWorkService.getShiftWorks"))
        )
    }

    setShiftWorks(shiftWorks: ShiftWork[]) {
        this.shiftWorksSource.next(shiftWorks);
    }

    getShiftWorkTable(shiftWorks: ShiftWork[], step: number): any[] {
        let rs = [];
        step = step * 60;

        shiftWorks.forEach(x => {
            x.out = x.out == 0 ? x.out = 24 * 3600 : x.out;
            for (let i = x.in; i <= x.out; i = i = i + step) {
                let item = {
                    'hour': customSec2Hour(i),
                    'sec': i,
                    'slots': 0
                };

                if(rs.findIndex(x => x.sec == item.sec) == -1) {
                    rs.push(item);
                }
            }
        })
        rs.sort((a, b) => a['sec'] - b['sec']);
        return rs;
    }
}
