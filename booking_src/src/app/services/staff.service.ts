import {Injectable} from '@angular/core';
import {Observable} from "rxjs";
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "./error.service";
import {Staff} from "../models/staff";
import {APPGLOBAL} from "../global";
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {AuthenticationService} from "../components/auth/authentication.service";
import {Store} from "../models/store";

@Injectable()
export class StaffService {
    private httpOptions = {
        headers: new HttpHeaders({'Content-Type': 'application/json'})
    };
    staffSource = new BehaviorSubject<Staff[]>(new Array);
    staffs = this.staffSource.asObservable();

    constructor(private http: HttpClient, private errorService: ErrorService, private authService: AuthenticationService) {
    }

    getStaffs(): Observable<Staff[]> {
        return this.http.get<Staff[]>(APPGLOBAL.API_URL + '/?site=staff&act=get_staffs').pipe(
            tap(res => {
                return this.errorService.log("StaffService: fetched staff");
            }),
            catchError(this.errorService.handleError<Staff[]>("StaffService.getStaffs", []))
        );
    }

    setStaffs(staffs: Staff[]) {
        this.staffSource.next(staffs);
    }

    getCurrentStaff() {
        let staffInfo = this.authService.decodeToken();
        let staff = new Staff();

        if(typeof staffInfo.staff != "undefined" && staffInfo.staff) {
            staff = staffInfo.staff;
        }

        return staff;
    }

    updateStore(store: Store): Observable<any> {
        let staff = this.getCurrentStaff();
        let payload = {
            storeId: store.id,
            userId: staff.id,
        };

        return this.http.post<any>(APPGLOBAL.API_URL + '/?site=staff&act=update_store', payload, this.httpOptions).pipe(
            tap(res => {
                return this.errorService.log("StaffService: update store for staff");
            }),
            catchError(this.errorService.handleError<any>("StaffService.updateStore", null))
        );
    }
}
