import {environment} from "../environments/environment";

let getApiUrl = () => {
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
    API_SECRET_KEY: "ZbioRz1oub",
    ROOT_DOMAIN: `${location.protocol}//${location.hostname}`,
    COOKIE_DOMAIN: `.${location.hostname}`
})