import { Directive, Input, OnChanges, SimpleChanges } from '@angular/core';
import { AbstractControl, NG_VALIDATORS, Validator, ValidatorFn, Validators } from '@angular/forms';

export function emailCustomValidator(): ValidatorFn {
    return (control: AbstractControl): {[key: string]: any} => {
        let rs = false;
        if(typeof control.value == "undefined" || typeof control.value == null || control.value == "") {
            return null;
        } else {
            let regex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
            rs = regex.test(control.value);
            return !rs ? {'emailCustom': {value: control.value}} : null;
        }
    };
}

@Directive({
    selector: '[appEmailCustom]',
    providers: [{provide: NG_VALIDATORS, useExisting: emailCustomDirective, multi: true}]
})
export class emailCustomDirective implements Validator {
    validate(control: AbstractControl): {[key: string]: any} {
        return emailCustomValidator()(control);
    }
}