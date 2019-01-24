import { Pipe, PipeTransform } from '@angular/core';
import {customSec2Human} from "../lib/script";

@Pipe({
  name: 'secToHuman'
})
export class SecToHumanPipe implements PipeTransform {

  transform(value: any): any {
    return customSec2Human(value);
  }

}
