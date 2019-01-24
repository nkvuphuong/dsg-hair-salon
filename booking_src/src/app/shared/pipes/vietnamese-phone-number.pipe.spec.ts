import { VietnamesePhoneNumberPipe } from './vietnamese-phone-number.pipe';

describe('VietnamesePhoneNumberPipe', () => {
  it('create an instance', () => {
    const pipe = new VietnamesePhoneNumberPipe();
    expect(pipe).toBeTruthy();
  });
});
