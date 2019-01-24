import {Component} from '@angular/core';
import {CityService} from "./services/city.service";
import {IndexedDbService} from "./services/indexed-db.service";
import {GlobalService} from "./services/global.service";
import * as moment from "moment";
import {APPGLOBAL} from "../../../pos_src/src/app/global";
import {ToastrService} from "ngx-toastr";
import {StoreService} from "./services/store.service";
import {forkJoin} from "rxjs/internal/observable/forkJoin";

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    title = 'Booking';
    reloader: boolean = true;

    constructor(private cityService: CityService, private idbService: IndexedDbService, private globalService: GlobalService, private toastr: ToastrService, private storeService: StoreService) {
        moment.locale('vi');

        forkJoin(
            this.globalService.getVariables(),
            this.cityService.getCities(),
            this.storeService.getStores(),
        ).subscribe(([globalVariables, cities, stores]) => {
            //Check enabled
            if(!globalVariables.bookingEnabled) {
                document.location.href = APPGLOBAL.ROOT_DOMAIN;
                this.toastr.warning("App is disabled");
                return;
            }

            setTimeout(() => {
                this.reloader = false;
            }, 1000)

            //Custom css by theme
            document.getElementById('theme-css').setAttribute('href','assets/css/style' + (globalVariables.theme && globalVariables.theme !== null ? globalVariables.theme : '') + '.css')
            this.globalService.setData(globalVariables);
            this.cityService.setCities(cities);
            this.storeService.setStores(stores);
        });
    }
}
