var myCountdown1 = new Countdown({
    year : 2018,
    month:1, 
    day:24,
    width:270, 
    height:50,
    style:"flip",
    rangeHi:"day",
    timezone    : 7,
    labelText   : {
       ms       : "MS",
       second : "GIÂY",
       minute : "PHÚT",
       hour     : "GIỜ",
       day      : "NGÀY",
       month  : "THÁNG",
       year     : "NĂM" 
    },
    labels  :   {
        font    : "Arial",
        color   : "#f2f2f2",
        offset : 0, 
        textScale   : 1,
        weight  : "normal"
    }
});