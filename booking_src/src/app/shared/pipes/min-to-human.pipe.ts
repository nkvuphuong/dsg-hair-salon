import { Pipe, PipeTransform } from '@angular/core';
import {customMin2Human} from "../lib/script";

@Pipe({
  name: 'minToHuman'
})
export class MinToHumanPipe implements PipeTransform {

  transform(value: any, args?: any): any {
    return customMin2Human(value);
  }

}
