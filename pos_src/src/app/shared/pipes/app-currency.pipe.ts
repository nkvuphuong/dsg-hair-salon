import {Pipe} from '@angular/core';
import {CurrencyPipe} from "@angular/common";

@Pipe({
    name: 'appCurrency'
})
export class AppCurrencyPipe extends CurrencyPipe {
    transform(value: any, args?: any): any {
        return super.transform(value, "VND", "symbol", "1.0-0", "vi");
    }
}
