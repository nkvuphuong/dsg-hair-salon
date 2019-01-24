import {AppCurrencyPipe} from './app-currency.pipe';

describe('AppCurrencyPipe', () => {
    it('create an instance', () => {
        const pipe = new AppCurrencyPipe('vi');
        expect(pipe).toBeTruthy();
    });
});
