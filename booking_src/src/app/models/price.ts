import {PriceItem} from "./price-item";

export class Price {
    id: number;
    name: string;
    storeIds: number[];
    cusGroupIds: number[];
    items: PriceItem[];
}