import {Injectable} from '@angular/core';
import {Observable} from "rxjs/Observable";
import {Staff} from "./staff";
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "../error/error.service";
import {AuthenticationService} from "../../components/auth/authentication.service";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {isOnline} from "../../lib/script";

@Injectable()
export class StaffService {

    private dbStoreName = "staffs";

    private httpOptions = {
        headers: new HttpHeaders()
    };

    constructor(private http: HttpClient, private errorService: ErrorService, private authService: AuthenticationService, private idbService: IndexedDbService) {
    }

    getStaffs(): Observable<Staff[]> {

        if(isOnline()) {
            return this.http.get<Staff[]>(APPGLOBAL.API_URL + '/?site=staff&act=get_staffs').pipe(
                tap(res => {

                    //Save to cache
                    res.forEach(x => {
                        this.idbService.put(this.dbStoreName,x).subscribe();
                    });

                    return this.errorService.log("StaffService: fetched staff");
                }),
                catchError(this.errorService.handleError<Staff[]>("StaffService.getStaffs", []))
            );
        } else { //Load offline from cache
            return this.idbService.all(this.dbStoreName);
        }
    }

    getCurrentStaff() {
        let staffInfo = this.authService.decodeToken();
        let staff = new Staff();

        if(typeof staffInfo.staff != "undefined" && staffInfo.staff) {
            staff.id = staffInfo.staff.id;
            staff.name = staffInfo.staff.name;
            staff.storeId = staffInfo.staff.storeId;
        }

        return staff;

    }

    getServiceStaffs(): Observable<Staff[]> {
        return this.http.get<Staff[]>(APPGLOBAL.API_BOOKING_URL + '/?site=staff&act=get_staffs').pipe(
            tap(res => {
                return this.errorService.log("StaffService: fetched staff");
            }),
            catchError(this.errorService.handleError<Staff[]>("StaffService.getStaffs", []))
        );
    }

    updateStore(staff: Staff): Observable<any> {
        return this.http.post<any>(APPGLOBAL.API_BOOKING_URL + '/?site=staff&act=update_store', staff, this.httpOptions).pipe(
            tap(res => {
                return this.errorService.log("CustomerService: Update store for staff " + staff.name);
            }),
            catchError(this.errorService.handleError<any>("CustomerService.updateStore"))
        )
    }
}
