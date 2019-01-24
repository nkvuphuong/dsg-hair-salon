import {Injectable} from '@angular/core';
import {
    HttpRequest,
    HttpHandler,
    HttpEvent,
    HttpInterceptor, HttpResponse, HttpErrorResponse
} from '@angular/common/http';
import {Observable} from 'rxjs/Observable';
import {tap} from "rxjs/operators";
import {Router} from "@angular/router";
import {AuthenticationService} from "./authentication.service";

@Injectable()
export class TokenInterceptor implements HttpInterceptor {
    constructor(public auth: AuthenticationService, private router: Router) {
    }

    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {

        if(!navigator.onLine) {
            return Observable.throw(new HttpErrorResponse({ error: 'Internet is not connected.' }));
        }

        request = request.clone({
            setHeaders: {
                Authorization: `Bearer ${this.auth.getToken()}`
            }
        });

        return next.handle(request).pipe(
            tap(
                (event: HttpEvent<any>) =>  {
                    if (event instanceof HttpResponse) {
                        // do stuff with response if you want
                    }
                },
                err => {
                    if (err instanceof HttpErrorResponse) {
                        if (err.status === 401) {
                            // redirect to the login route
                            this.router.navigate(['/login']);
                        }
                    }
                },
                () => {

                })
        );
    }
}