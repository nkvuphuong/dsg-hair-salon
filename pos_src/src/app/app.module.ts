import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {registerLocaleData} from '@angular/common';
import {HTTP_INTERCEPTORS, HttpClientModule} from "@angular/common/http";
import {PopoverModule} from "ngx-popover";
import {NG_SELECT_DEFAULT_CONFIG, NgSelectModule} from "@ng-select/ng-select";
import {ToastrModule} from "ngx-toastr";

import {ErrorService} from './services/error/error.service';
import {ProductService} from "./services/product/product.service";
import {BillService} from './services/bill/bill.service';

import {AppComponent} from './app.component';
import {MessagesComponent} from './components/messages/messages.component';
import {MessageService} from './services/message/message.service';
import {AppRoutingModule} from './/app-routing.module';
import {DashboardComponent} from './components/dashboard/dashboard.component';
import {StaffService} from "./services/staff/staff.service";
import {CustomerService} from './services/customer/customer.service';
import {MatTabsModule} from "@angular/material";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {NgbModule, NgbDatepickerModule, NgbDateParserFormatter} from "@ng-bootstrap/ng-bootstrap";
import {NgbDateVNParserFormatter} from "../assets/ts/ngb-date-vn-parser-formatter";
import {CityService} from './services/city/city.service';
import {DistrictService} from './services/district/district.service';
import {WardService} from './services/ward/ward.service';
import {LoadingModule} from "ngx-loading";
import {EssenceNg2PrintModule} from "essence-ng2-print";
import {PrintBillComponent} from './components/print-bill/print-bill.component';
import {LoginComponent} from './components/login/login.component';
import {AuthenticationService} from "./components/auth/authentication.service";
import {TokenInterceptor} from "./components/auth/token.interceptor";
import {AuthGuard} from "./components/auth/auth.guard";
import {JwtHelperService} from '@auth0/angular-jwt';
import {ProductCategoryService} from "./services/product-category/product-category.service";
import {emailCustomDirective} from "./shared/custom-validators.directive";
import {MatchHeightDirective} from "./shared/match-height.directive";
import {ServiceWorkerModule} from "@angular/service-worker";
import {environment} from "../environments/environment";
import {ConnectNetworkService} from "./services/connect-network/connect-network.service";
import {IndexedDbService} from "./services/indexed-db/indexed-db.service";
import {GlobalService} from "./services/global/global.service";
import {SqDatetimepickerModule} from 'ngx-eonasdan-datetimepicker';
import {CookieService} from "ngx-cookie-service";
import {StoreService} from "./services/store/store.service";
import {BookingHourService} from "./services/booking-hour/booking-hour.service";
import {MomentModule} from "angular2-moment";
import {AppCurrencyPipe} from './shared/pipes/app-currency.pipe';
import localesVi from '@angular/common/locales/vi';
import localesViExtra from '@angular/common/locales/extra/vi';
import {NgxMaskModule} from "ngx-mask";
import {ValidatorService} from "./services/validator/validator.service";
import {SocketIoModule, SocketIoConfig} from 'ng-socket-io';
import {OrderService} from "./services/order/order.service";

const config: SocketIoConfig = {url: ":3000", options: {}};

registerLocaleData(localesVi, localesViExtra);

@NgModule({
    declarations: [
        AppComponent,
        emailCustomDirective,
        MatchHeightDirective,
        MessagesComponent,
        DashboardComponent,
        PrintBillComponent,
        LoginComponent,
        AppCurrencyPipe
    ],
    imports: [
        BrowserModule,
        BrowserAnimationsModule,
        FormsModule,
        ReactiveFormsModule,
        AppRoutingModule,
        HttpClientModule,
        PopoverModule,
        NgSelectModule,
        MatTabsModule,
        NgbModule.forRoot(),
        NgbDatepickerModule,
        ToastrModule.forRoot(),
        LoadingModule,
        EssenceNg2PrintModule,
        ServiceWorkerModule.register('./ngsw-worker.js', {enabled: environment.production}),
        SqDatetimepickerModule,
        MomentModule,
        NgxMaskModule.forRoot(),
        SocketIoModule.forRoot(config)
    ],
    providers: [
        MessageService,
        ErrorService,
        ProductService,
        BillService,
        StaffService,
        CustomerService,
        CityService,
        DistrictService,
        WardService,
        AuthenticationService,
        AuthGuard,
        JwtHelperService,
        ProductCategoryService,
        ConnectNetworkService,
        IndexedDbService,
        GlobalService,
        CookieService,
        StoreService,
        BookingHourService,
        ValidatorService,
        OrderService,
        {
            provide: NgbDateParserFormatter,
            useClass: NgbDateVNParserFormatter
        },
        {
            provide: HTTP_INTERCEPTORS,
            useClass: TokenInterceptor,
            multi: true
        },
        {
            provide: NG_SELECT_DEFAULT_CONFIG,
            useValue: {
                notFoundText: 'Không tìm thấy dữ liệu'
            }
        }
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
