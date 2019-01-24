import {Injectable} from '@angular/core';
import {ErrorService} from "../error/error.service";
import {HttpClient} from "@angular/common/http";
import {APPGLOBAL} from "../../global";
import {catchError, tap} from "rxjs/operators";
import {Observable} from "rxjs/Observable";
import {ProductCategory} from "./product-category";
import {IndexedDbService} from "../indexed-db/indexed-db.service";
import {isOnline} from "../../lib/script";

@Injectable()
export class ProductCategoryService {

    private dbStoreName = "product-categories";
    private apiUrl = APPGLOBAL.API_URL + '/?site=product_category';

    constructor(private errorService: ErrorService, private http: HttpClient, private idbService: IndexedDbService) {
    }

    getProductCategories(order: string = "id", by: string = "asc"): Observable<ProductCategory[]> {
        const url = `${this.apiUrl}&act=get_product_categories&order=${order}&by=${by}`;

        if(isOnline()) {
            return this.http.get<ProductCategory[]>(url).pipe(
                tap(res => {
                    res.forEach(x => this.idbService.put(this.dbStoreName, x).subscribe());
                    return this.errorService.log("ProductService: fetched product categories");
                }),
                catchError(this.errorService.handleError<ProductCategory[]>("ProductCategoryService.getProductCategories", []))
            );
        } else {
            return this.idbService.all(this.dbStoreName);
        }
    }
}
