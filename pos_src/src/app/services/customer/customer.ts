import {NgbDateStruct} from "@ng-bootstrap/ng-bootstrap";
import {District} from "../district/district";
import {Ward} from "../ward/ward";
import {City} from "../city/city";

export class Customer {
    id: number = null;
    name: string;
    email: string;
    phone: string;
    birthday: any;
    address: string;
    city: City;
    district: District;
    ward: Ward;
    store: number;
    taxcode: string;
    gender: number = 1;
    group: number;
    note: string;
    avatar: File;
    avatarStr: string;
    action: string; //add | edit
    groupIds: number[];

    constructor() {
    }
}