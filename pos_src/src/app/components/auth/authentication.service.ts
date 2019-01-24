import {Injectable} from '@angular/core';

import {HttpClient, HttpHeaders} from "@angular/common/http";
import {catchError, tap} from "rxjs/operators";
import {Observable} from "rxjs/Observable";
import {ErrorService} from "../../services/error/error.service";
import {APPGLOBAL} from "../../global";
import {CookieService} from "ngx-cookie-service";
import {JwtHelperService} from "@auth0/angular-jwt";

@Injectable()
export class AuthenticationService {

    public token: string;
    private jwt: JwtHelperService;

    constructor(private http: HttpClient, private errorService: ErrorService, private cookieService: CookieService) {
        this.jwt = new JwtHelperService();
    }

    login(username: string, password: string): Observable<any> {
        return this.http.get<any>(APPGLOBAL.API_URL + '/?site=staff&act=login&username=' + username + "&password=" + password).pipe(
            tap(res => {
                this.errorService.log("AuthenticationService: Login for staff " +  username);
                // this.token = res.token;
            }),
            catchError(this.errorService.handleError<any>("AuthenticationService.login", null))
        )
    }

    logout(): void {
        // clear token remove user from local storage to log user out
        // this.token = null;
        // localStorage.removeItem('accessToken');
        //Remove cookie
        this.cookieService.delete("crmuser_session", "/acp", APPGLOBAL.COOKIE_DOMAIN);
        this.cookieService.delete("crmuser_id", "/acp", APPGLOBAL.COOKIE_DOMAIN);
        this.cookieService.delete("crmuser_hash", "/acp", APPGLOBAL.COOKIE_DOMAIN);
        this.cookieService.delete("accessToken", "/", APPGLOBAL.COOKIE_DOMAIN);
    }

    public getToken(): string {
        // return localStorage.getItem('accessToken');
        return this.cookieService.get("accessToken");
    }

    public decodeToken(): any {
        return this.jwt.decodeToken(this.getToken());
    }

    public isAuthenticated(): boolean {
        // get the token
        const token = this.getToken();

        if(!token) return false;

        try {
            return this.jwt.isTokenExpired(token);
        } catch (e) {
            this.errorService.handleError("AuthenticationService.isAuthenticated", false);
            return false;
        }
    }
}
