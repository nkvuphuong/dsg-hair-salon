import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {Observable} from "rxjs/Observable";
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {ErrorService} from "../error/error.service";
import {Customer} from "./customer";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {isOnline} from "../../lib/script";
import {NgbDateStruct} from "@ng-bootstrap/ng-bootstrap";

@Injectable()
export class CustomerService {

    private dbStoreName = "customers";

    private httpOptions = {
        headers: new HttpHeaders()
    };

    constructor(private http: HttpClient, private errorService: ErrorService, private idbService: IndexedDbService) {
        this.httpOptions.headers.append('Content-Type', 'multipart/form-data');
        this.httpOptions.headers.append('Accept', 'application/json');
    }

    getCustomers(term: string, page: number = 1, perPage: number = 10): Observable<Customer[]> {
        if(isOnline()) {
            return this.http.get<Customer[]>(APPGLOBAL.API_URL + '/?site=customer&act=get_customers&term=' + term + '&page=' + page + '&limit=' + perPage).pipe(
                tap(res => {
                    //Save to cache
                    res.forEach((x) => {
                        x.birthday = JSON.parse(x.birthday);
                        this.idbService.put(this.dbStoreName,x).subscribe();
                    })
                    return this.errorService.log("CustomerService: fetched customer");
                }),
                catchError(this.errorService.handleError<Customer[]>("CustomerService.getCustomers"))
            );
        } else {
            return this.idbService.all(this.dbStoreName, "name_idx", false, IDBKeyRange.bound(term, term + '\uffff'));
        }
    }

    submitCustomer(customer: Customer): Observable<any> {

        let data = new FormData();

        for (let obj in customer) {
            if(obj == 'avatar') {
                data.append('files', customer[obj], customer[obj].name);
            } else if(obj == 'city' || obj == 'district') {
                data.append(obj, typeof customer[obj] != "undefined" && customer[obj] ? customer[obj].id.toString() : null);
            } else if(obj == 'birthday') {
                let birthdayObj = customer[obj];
                let birthday = new Date(birthdayObj.year, birthdayObj.month-1, birthdayObj.day);
                data.append(obj, Math.round(+birthday/1000).toString());
            } else {
                data.append(obj, customer[obj]);
            }
        }

        let url;
        if(customer.action == 'edit') {
            url = APPGLOBAL.API_URL + '/?site=customer&act=update_customer';
        } else {
            url = APPGLOBAL.API_URL + '/?site=customer&act=add_customer';
        }

        return this.http.post<any>(url, data, this.httpOptions).pipe(
            tap(res => {
                if(res.data.birthday) {
                    res.data.birthday = JSON.parse(res.data.birthday);
                }
                return this.errorService.log("CustomerService: Add new customer "+ res.data.name);
            }),
            catchError(this.errorService.handleError<any>("CustomerService.addCustomer"))
        )
    }

    uploadImage(e): Observable<any> {
        const file = e.target.files[0];
        const pattern = /image-*/;

        if (!file.type.match(pattern)) {
            alert('You are trying to upload not Image. Please choose image.');
            return;
        }
        const reader = new FileReader();
        reader.readAsDataURL(file);
        return Observable.create(observer => {
            reader.onloadend = () => {
                observer.next(reader.result);
                observer.complete();
            };
        });
    }

}
