import {Injectable} from '@angular/core';
import {Observable} from "rxjs/Observable";
import {HttpClient} from "@angular/common/http";
import {Product} from "./product";
import {ErrorService} from "../error/error.service";
import {catchError, tap} from "rxjs/operators";
import {APPGLOBAL} from "../../global";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {isOnline} from "../../lib/script";
import {Price} from "./price";

@Injectable()
export class ProductService {

    private apiUrl = APPGLOBAL.API_URL + '/?site=product';
    private dbStoreName = "products";

    constructor(private errorService: ErrorService, private http: HttpClient, private idbService: IndexedDbService) {
    }

    getProducts(order: string = "id", by: string = "asc"): Observable<Product[]> {
        if(isOnline()) {
            const url = `${this.apiUrl}&act=get_products&order=${order}&by=${by}`;
            return this.http.get<Product[]>(url).pipe(
                tap(res => {
                    //Save to cache
                    res.forEach(x => {
                        this.idbService.put(this.dbStoreName,x).subscribe();
                    });

                    return this.errorService.log("ProductService: fetched products");
                }),
                catchError(this.errorService.handleError<Product[]>("ProductService.getProducts", []))
            );
        } else { //Offline
            return this.idbService.all(this.dbStoreName, order+"_idx", by == "asc");
        }

    }

    getPrices(order: string = "id", by: string = "asc"): Observable<Price[]> {
        if(isOnline()) {
            const url = `${this.apiUrl}&act=get_prices&order=${order}&by=${by}`;
            return this.http.get<Price[]>(url).pipe(
                tap(res => {
                    //Save to cache
                    res.forEach(x => {
                        this.idbService.put("prices",x).subscribe();
                    });

                    return this.errorService.log("ProductService: fetched prices");
                }),
                catchError(this.errorService.handleError<Price[]>("ProductService.getPrices", []))
            );
        } else { //Offline
            return this.idbService.all(this.dbStoreName, order+"_idx", by == "asc");
        }

    }
}
