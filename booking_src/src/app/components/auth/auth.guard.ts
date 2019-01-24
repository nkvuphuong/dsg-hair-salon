import {Injectable} from '@angular/core';
import {Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot} from '@angular/router';
import {AuthenticationService} from "./authentication.service";
import {APPGLOBAL} from "../../global";

@Injectable()
export class AuthGuard implements CanActivate {

    constructor(private router: Router, private authenicationService: AuthenticationService) {
    }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        if (!this.authenicationService.isAuthenticated()) {
            window.location.href = APPGLOBAL.ROOT_DOMAIN + "/acp/?site=login&returnUrl=" + encodeURI(window.location.href);
            return false;
        }

        return true;
    }
}