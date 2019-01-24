import { Injectable } from '@angular/core';
import { Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import {AuthenticationService} from "./authentication.service";
import {APPGLOBAL} from "../../global";

@Injectable()
export class AuthGuard implements CanActivate {

    constructor(private router: Router, private authenicationService: AuthenticationService) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        let component = route.url[0].path;
        if (this.authenicationService.isAuthenticated()) {
            if(component == 'login') {
                this.router.navigate(['/dashboard']);
                return false;
            } else {
                return true;
            }
        } else {
            if(component == 'login') {
                return true;
            } else {
                // not logged in so redirect to login page with the return url
                // this.router.navigate(['/login'], { queryParams: { returnUrl: state.url }});
                window.location.href = APPGLOBAL.ROOT_DOMAIN + "/acp/?returnUrl=" + APPGLOBAL.ROOT_DOMAIN + "/pos";
                return false;
            }
        }
    }
}