import {OrderItem} from "./orderItem";
import {Store} from "./store";

export class Order {
    phone: string;
    orderItems: OrderItem[];
    store: Store;
    note: string;
}