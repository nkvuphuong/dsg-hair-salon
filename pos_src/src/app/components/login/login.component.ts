import {Component, OnInit} from '@angular/core';
import {AuthenticationService} from "../auth/authentication.service";
import {Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";
import {CookieService} from "ngx-cookie-service";
import {Moment} from "moment";
import {APPGLOBAL} from "../../global";
import {City} from "../../services/city/city";
import {Store} from "../../services/store/store";
import {StoreService} from "../../services/store/store.service";
import {CityService} from "../../services/city/city.service";

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

    username: string = "";
    password: string = "";
    loading: boolean = false;
    storeCities: City[];
    selectedCity: City;
    stores: Store[];
    selectedStore: Store;
    oriStores: Store[];

    constructor(
        private authenticationService: AuthenticationService,
        private router: Router,
        private toastr: ToastrService,
        private cookieService: CookieService,
        private storeService: StoreService,
        private cityService: CityService) {
    }

    ngOnInit() {
        this.getCitiesHaveStores();
    }

    login(): void {
        this.loading = true;
        this.authenticationService.login(this.username, this.password).subscribe(
            res => {
                if (res.status == 'success') {
                    if (res.cookie.accessToken) {
                        //Save token
                        // localStorage.setItem('accessToken', res.cookie.accessToken);

                        //Set cookie login ACP
                        this.cookieService.set('crmuser_session', res.cookie.crmuser_session, null, '/acp', APPGLOBAL.COOKIE_DOMAIN);
                        this.cookieService.set('crmuser_id', res.cookie.crmuser_id, null,'/acp',  APPGLOBAL.COOKIE_DOMAIN);
                        this.cookieService.set('crmuser_hash', res.cookie.crmuser_hash, null, '/acp', APPGLOBAL.COOKIE_DOMAIN);
                        this.cookieService.set('accessToken', res.cookie.accessToken, null, '/', APPGLOBAL.COOKIE_DOMAIN);

                        this.cookieService.set('currentCity', JSON.stringify(this.selectedCity), null, '/', APPGLOBAL.COOKIE_DOMAIN);
                        this.cookieService.set('currentStore',  JSON.stringify(this.selectedStore) , null, '/', APPGLOBAL.COOKIE_DOMAIN);

                        this.router.navigate(['/dashboard']);
                        this.loading = false;
                    } else {
                        this.toastr.error("Invalid access");
                        this.loading = false;
                    }
                } else {
                    this.toastr.error(res.msg);
                    this.loading = false;
                }
            },
            err => {
                this.toastr.error("Error while login");
                this.loading = false;
                console.log(err);
            }
        )
    }

    getCitiesHaveStores() {
        this.cityService.getCitiesHaveStores().subscribe(
            cities => {
                this.storeCities = cities;
                this.selectedCity = this.storeCities && this.storeCities[0] ? this.storeCities[0] : null;
                this.getStores(() => {this.filterStoresByCity(this.selectedCity)});
            },
            err => console.log(err)
        )
    }

    getStores(callback?) {
        this.storeService.getStores().subscribe(
            stores => {
                this.oriStores = stores;
                this.selectedStore = stores && stores[0] ? stores[0] : null;
                if(typeof callback == "function") {
                    callback();
                }
            },
            err => console.log(err)
        )
    }

    filterStoresByCity(city: City) {
        if (!city) {
            this.stores = [];
            this.selectedCity = null;
            this.selectedStore = null;
            return;
        }

        this.selectedCity = city;
        if (this.oriStores) {
            this.stores = this.oriStores.filter(x => x.cityId == city.id);
        }

        this.selectedStore = this.stores && this.stores[0] ? this.stores[0] :  null;

        if (this.selectedStore && this.selectedStore.cityId != this.selectedCity.id) {
            this.selectedStore = null;
        }
    }

}
