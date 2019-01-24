import {Injectable} from '@angular/core';
import {Observable} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {catchError, tap} from "rxjs/operators";
import {APPGLOBAL} from "../global";
import {ErrorService} from "./error.service";
import {Product} from "../models/product";
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";

@Injectable()
export class ProductService {

    private apiUrl = APPGLOBAL.API_URL + '/?site=product';
    productSource = new BehaviorSubject<Product[]>(new Array());
    products = this.productSource.asObservable();

    constructor(private errorService: ErrorService, private http: HttpClient) {
    }

    getProducts(order: string = "id", by: string = "asc"): Observable<Product[]> {
        const url = `${this.apiUrl}&act=get_products&order=${order}&by=${by}`;
        return this.http.get<Product[]>(url).pipe(
            tap(res => {
                return this.errorService.log("ProductService: fetched products");
            }),
            catchError(this.errorService.handleError<Product[]>("ProductService.getProducts", []))
        );
    }

    setProducts(products: Product[]) {
        this.productSource.next(products);
    }
}
