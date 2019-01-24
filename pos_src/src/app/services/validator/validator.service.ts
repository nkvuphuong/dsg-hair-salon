import { Injectable } from '@angular/core';

@Injectable()
export class ValidatorService {

  constructor() { }

    isVietnamesePhone(s: string) {
        let regex = new RegExp(/^(01[2689]|0[98753])[0-9]{8}$/);
        return regex.test(s);
    }
}
