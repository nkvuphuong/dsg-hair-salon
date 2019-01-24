import {environment} from "../environments/environment";
import {locale, now} from "moment";

let getApiUrl = () => {
    if(!environment.production) { //development
        return `${location.protocol}//${location.hostname}/pos_data`;
    } else {
        if(location.port == "8080") { //test
            return `${location.protocol}//${location.hostname}/pos_data`;
        } else { //production
            return "/pos_data";
        }
    }
};

let getApiBookingUrl = () => {
    if(!environment.production) { //development
        return `${location.protocol}//${location.hostname}/booking_data`;
    } else {
        if(location.port == "8080") { //test
            return `${location.protocol}//${location.hostname}/booking_data`;
        } else { //production
            return "/booking_data";
        }
    }
};

export const APPGLOBAL = Object.freeze({
    API_URL: getApiUrl(),
    API_BOOKING_URL: getApiBookingUrl(),
    API_SECRET_KEY: "ZbioRz1oub",
    ROOT_DOMAIN: `${location.protocol}//${location.hostname}`,
    COOKIE_DOMAIN: `.${location.hostname}`,
    DATETIMEPICKER_OPTIONS: {
        format: "DD/MM/YYYY HH:mm",
        sideBySide: true,
        showClear: true,
        stepping: 5,
        debug: false,
        minDate: now(),
        ignoreReadonly: true,
        locale: locale()
    }
})