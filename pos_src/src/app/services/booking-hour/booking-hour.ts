export class BookingHour {

    slots: number[];
    booked: number[];
    bookedOthers: number[]; //Chỗ đã đặt tại các item khác cùng bill
    hour: string;
    second: number;

    constructor(){
        this.slots = [];
        this.booked = [];
        this.bookedOthers = [];
    }

}