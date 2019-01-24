import {Component, OnInit} from '@angular/core';
import {Order} from "../../models/order";
import {OrderService} from "../../services/order.service";
import {ValidatorService} from "../../services/validator.service";
import {City} from "../../models/city";
import {CityService} from "../../services/city.service";
import {Router} from "@angular/router";
import {Store} from "../../models/store";
import {StoreService} from "../../services/store.service";
import {GlobalService} from "../../services/global.service";
import {DeviceDetectorService} from "ngx-device-detector";

@Component({
    selector: 'app-store',
    templateUrl: './store.component.html',
    styleUrls: ['./store.component.css']
})
export class StoreComponent implements OnInit {

    step: number = 2;
    order: Order;
    cities: City[];
    selectedCity: City;
    stores: Store[];
    storesByLocation: Store[];
    loading: boolean = false;
    globalVariables: any;
    device: any

    constructor(private orderService: OrderService, private validatorService: ValidatorService, private cityService: CityService, private router: Router, private storeService: StoreService, private globalService: GlobalService, private deviceService: DeviceDetectorService) {
    }

    ngOnInit() {
        this.device = this.deviceService;

        $(".list-members").owlCarousel('destroy');

        this.loading = true;

        this.globalService.data.subscribe(
            res => {
                return this.globalVariables = res;
            },
            err => console.log(err),
        );

        this.orderService.currentOrder.subscribe(
            order => {
                this.order = order;
                if (!this.validatorService.isVietnamesePhone(this.order.phone)) {
                    this.router.navigateByUrl("/");
                } else {
                    this.cityService.cities.subscribe(
                        (res: City[]) => {
                            this.cities = res;
                            this.selectedCity = this.cities[0];
                            this.storeService.stores.subscribe(
                                (res: Store[]) => {
                                    this.stores = res;
                                    this.filterByLocation(this.selectedCity);
                                    this.loading = false;
                                }
                            );
                        }
                    )
                }
            }
        )
    }

    filterByLocation(city: City) {
        if (!city || !this.stores) return;
        this.selectedCity = city;
        this.storesByLocation = this.stores.filter((x: Store) => x.cityId == this.selectedCity.id);
        this.order.store = this.storesByLocation[0];
    }

    filterByLocationIndex(i: number) {
        this.filterByLocation(this.cities[i] || null);
    }
}
