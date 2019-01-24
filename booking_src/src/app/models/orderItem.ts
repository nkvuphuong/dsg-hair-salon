import {Product} from "./product";
import {cutomRandomHashMd5} from "../shared/lib/script";
import {Staff} from "./staff";
import * as moment from "moment";
import {Price} from "./price";

export class OrderItem {
    index: number = 1;
    key: string = cutomRandomHashMd5();
    product: Product = null;
    staff: Staff;
    date: moment.Moment;
    hour: string;
    price: Price;
    prices: Price[];
}