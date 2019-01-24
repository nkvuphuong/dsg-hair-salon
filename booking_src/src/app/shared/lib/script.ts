import {Md5} from "ts-md5";
import * as moment from "moment";

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

export function cutomRandomHashMd5(): string {
    let m = moment().format('x');
    let r = Math.round(Math.random()*1000);
    return Md5.hashStr(m + '.' + r).toString();
}

export function staffOwlCarousel() {
    $(".list-members").owlCarousel('destroy');
    setTimeout(()=>{
        $(".list-members").owlCarousel({
            autoWidth: true,
            items: 4,
            loop: false,
            margin: 23,
            dots: false,
            responsive: {
                0: {items: 3,},
                600: {items: 4,},
                1000: {items: 4}
            }
        });
    },0)
}

export function customSec2Hour(s: number): string {
    let m = Math.floor(s/60);
    let h = Math.floor(m/60);
    m = m - (h * 60);

    let M = '0' + m.toString();
    let H = '0' + h.toString();

    M = M.substr(-2);
    H = H.substr(-2);

    return `${H} : ${M}`;
}

export function customHour2Sec(s: string): number {

    var explode = s.split(":");

    let h = explode[0].toString().trim();
    let m = explode[1].toString().trim();

    return +h*3600 + +m*60;
}

export function customSec2Human(s: number): string {
    s = +s;
    let m = Math.floor(s/60);
    let h = Math.floor(m/60);
    m = m - (h * 60);

    let rsArr = [];
    if(h) {
        let H = '0' + h.toString();
        H = H.substr(-2);
        rsArr.push(`${H} giờ`);
    }

    if(m) {
        let M = '0' + m.toString();
        M = M.substr(-2);
        rsArr.push(`${M} phút`);
    }

    return rsArr.join(" ");
}

export function customMin2Human(m: number): string {
    m = +m;
    let h = Math.floor(m/60);
    m = m - (h * 60);

    let rsArr = [];
    if(h) {
        let H = '0' + h.toString();
        H = H.substr(-2);
        rsArr.push(`${H} giờ`);
    }

    if(m) {
        let M = '0' + m.toString();
        M = M.substr(-2);
        rsArr.push(`${M} phút`);
    }

    return rsArr.join(" ");
}