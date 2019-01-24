export function convertVietnamese(str: string): string {
    str= str.toLowerCase();
    str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
    str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
    str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
    str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
    str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
    str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
    str= str.replace(/đ/g,"d");
    return str;
}

export function isOnline(): boolean {
    // return false; //test
    return navigator.onLine;
}

export function customDateTimeFormat(timestamp: number): string {
    let date = new Date(timestamp * 1000);
    return `${("0"+date.getDate()).toString().slice(-2)}/${("0"+(date.getMonth()+1)).toString().slice(-2)}/${date.getFullYear()} ${("0"+date.getHours()).slice(-2)}:${("0" + date.getMinutes()).slice(-2)}:${("0" + date.getSeconds()).slice(-2)}`;
}

export function customNumberFormat(x: number): number {
    x *= 1;
    return +x.toFixed(2);
}

export function setMaxLengPhoneInput(phone?: string) {
    let reg = new RegExp(/^01/);
    return reg.test(phone) ? 13 : 12;
}

export function onlyNumber(s?: string) {
    let reg = new RegExp(/\D/);
    return s.replace(reg,'');
}