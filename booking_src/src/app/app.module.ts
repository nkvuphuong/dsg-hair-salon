import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';

import {AppComponent} from './app.component';
import {StoreComponent} from './components/store/store.component';
import {BookComponent} from './components/book/book.component';
import {MessageComponent} from './components/message/message.component';
import {AppRoutingModule} from './/app-routing.module';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {HomeComponent} from './components/home/home.component';
import {FormsModule} from "@angular/forms";
import {ToastrModule} from "ngx-toastr";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {NgxMaskModule} from "ngx-mask";
import {CityService} from "./services/city.service";
import {HttpClientModule} from "@angular/common/http";
import {ErrorService} from "./services/error.service";
import {IndexedDbService} from "./services/indexed-db.service";
import {GlobalService} from "./services/global.service";
import {StepMenuComponent} from './components/layout/step-menu/step-menu.component';
import {ProductService} from "./services/product.service";
import {StaffService} from "./services/staff.service";
import {Sec2HourPipe} from './shared/pipes/sec-2-hour.pipe';
import {LoadingModule} from "ngx-loading";
import {VietnamesePhoneNumber} from "./shared/directives/vietnamese-phone-number.directive";
import {CompleteComponent} from './components/complete/complete.component';
import {MatchHeightDirective} from "./shared/directives/match-height.directive";
import {MatTabsModule} from "@angular/material";
import {InformationComponent} from './components/information/information.component';
import {VietnamesePhoneNumberPipe} from './shared/pipes/vietnamese-phone-number.pipe';
import {AuthenticationService} from "./components/auth/authentication.service";
import {JwtModule} from '@auth0/angular-jwt';
import {AuthGuard} from "./components/auth/auth.guard";
import {CookieService} from "ngx-cookie-service";
import {SecToHumanPipe} from './shared/pipes/sec-to-human.pipe';
import {MinToHumanPipe} from './shared/pipes/min-to-human.pipe';
import {RatingService} from "./services/rating.service";
import {SafeHtmlPipe} from './shared/pipes/safe-html.pipe';
import {DeviceDetectorModule} from "ngx-device-detector";
import {SocketIoModule, SocketIoConfig} from 'ngx-socket-io';

const config: SocketIoConfig = {url: 'http://localhost:3000', options: {}};

export function jwtTokenGetter() {
    var name = "accessToken=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

@NgModule({
    declarations: [
        AppComponent,
        StoreComponent,
        BookComponent,
        MessageComponent,
        HomeComponent,
        StepMenuComponent,
        Sec2HourPipe,
        VietnamesePhoneNumber,
        CompleteComponent,
        MatchHeightDirective,
        InformationComponent,
        VietnamesePhoneNumberPipe,
        SecToHumanPipe,
        MinToHumanPipe,
        SafeHtmlPipe
    ],
    imports: [
        BrowserModule,
        BrowserAnimationsModule,
        FormsModule,
        AppRoutingModule,
        HttpClientModule,
        NgbModule.forRoot(),
        ToastrModule.forRoot({
            enableHtml: true,
            progressBar: true,
            closeButton: true,
            tapToDismiss: true
        }),
        NgxMaskModule.forRoot(),
        LoadingModule,
        MatTabsModule,
        JwtModule.forRoot({
            config: {
                headerName: 'Authorization',
                authScheme: 'Bearer ',
                whitelistedDomains: [location.hostname],
                tokenGetter: jwtTokenGetter,
            }
        }),
        DeviceDetectorModule.forRoot(),
        SocketIoModule.forRoot(config)
    ],
    providers: [
        CityService,
        ProductService,
        StaffService,
        ErrorService,
        IndexedDbService,
        GlobalService,
        AuthenticationService,
        AuthGuard,
        CookieService,
        RatingService
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
