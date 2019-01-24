import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'vietnamesePhoneNumber'
})
export class VietnamesePhoneNumberPipe implements PipeTransform {

  transform(value: string): any {
    return value.replace(/(\d{4})(\d{3})(\d{3,4})/, '$1.$2.$3');
  }

}
