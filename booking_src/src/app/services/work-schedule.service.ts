import {Injectable} from '@angular/core';
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {WorkSchedule} from "../models/work-schedule";
import {catchError, tap} from "rxjs/operators";
import {Observable} from "rxjs/index";
import {APPGLOBAL} from "../global";
import {ErrorService} from "./error.service";
import {HttpClient} from "@angular/common/http";
@Injectable({
    providedIn: 'root'
})
export class WorkScheduleService {
    private workScheduleSource = new BehaviorSubject<WorkSchedule[]>(new Array());
    public workSchedules = this.workScheduleSource.asObservable();

    constructor(private http: HttpClient, private errorService: ErrorService) {
    }

    getWorkSchedules(store_id: number): Observable<{} | WorkSchedule[]> {
        return this.http.get<WorkSchedule[]>(APPGLOBAL.API_URL + '/?site=work_schedule&act=get_work_schedules&store=' + store_id).pipe(
            tap(res => {
                this.errorService.log("WorkScheduleService: fetched schedules")
            }),
            catchError(this.errorService.handleError<WorkSchedule[]>("WorkScheduleService.getWorkSchedules"))
        )
    }

    setWorkSchedules(workSchedules: WorkSchedule[]) {
        this.workScheduleSource.next(workSchedules);
    }
}
