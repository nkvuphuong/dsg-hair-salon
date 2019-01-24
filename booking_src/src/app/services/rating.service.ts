import {Injectable} from '@angular/core';
import {Observable} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {catchError, tap} from "rxjs/operators";
import {APPGLOBAL} from "../global";
import {ErrorService} from "./error.service";
import {BehaviorSubject} from "rxjs/internal/BehaviorSubject";
import {Rating} from "../models/rating";

@Injectable()
export class RatingService {

    private apiUrl = APPGLOBAL.API_URL + '/?site=rating';
    ratingSource = new BehaviorSubject<Rating[]>(new Array());
    ratings = this.ratingSource.asObservable();

    constructor(private errorService: ErrorService, private http: HttpClient) {
    }

    getRatings(order: string = "id", by: string = "asc"): Observable<Rating[]> {
        const url = `${this.apiUrl}&act=get_ratings&order=${order}&by=${by}`;
        return this.http.get<Rating[]>(url).pipe(
            tap(res => {
                return this.errorService.log("RatingService: fetched ratings");
            }),
            catchError(this.errorService.handleError<Rating[]>("RatingService.getRatings", []))
        );
    }

    setProducts(ratings: Rating[]) {
        this.ratingSource.next(ratings);
    }
}
