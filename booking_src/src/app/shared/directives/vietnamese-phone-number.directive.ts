import {Directive} from '@angular/core';
import {AbstractControl, NG_VALIDATORS, Validator, ValidatorFn} from '@angular/forms';

export function vietnamesePhoneNumberValidator(): ValidatorFn {
    return (control: AbstractControl): { [key: string]: any } => {
        let rs = false;
        if (typeof control.value == "undefined" || typeof control.value == null || control.value == "") {
            return null;
        } else {
            let regex = new RegExp(/^(01[2689]|09|08)[0-9]{8}$/);
            rs = regex.test(control.value);
            return !rs ? {'VietnamesePhoneNumber': {value: control.value}} : null;
        }
    };
}

@Directive({
    selector: '[appVietnamesePhoneNumber]',
    providers: [{provide: NG_VALIDATORS, useExisting: VietnamesePhoneNumber, multi: true}]
})
export class VietnamesePhoneNumber implements Validator {
    validate(control: AbstractControl): { [key: string]: any } {
        return vietnamesePhoneNumberValidator()(control);
    }
}