import { Pipe, PipeTransform } from '@angular/core';
import {customSec2Hour} from "../lib/script";

@Pipe({
  name: 'sec2Hour'
})
export class Sec2HourPipe implements PipeTransform {

  transform(value: number): string {
    return customSec2Hour(+value);
  }

}
