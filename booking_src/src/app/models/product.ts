export class Product {
    id: number;
    name: string;
    code: number;
    group: number;
    oldPrice: number;
    price: number;
    image: string;
    imageAlt: string;
    tax: number;
    slug: string;
    gallery: any;
    oriData: any;
    image_S: string;
    type: number = 0; //0: SP, 1: DV
    staffIds: Array<number>;
    estimatedTime: number;
}