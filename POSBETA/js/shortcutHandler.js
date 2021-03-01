var paymentconfirmed = false;
$(document).keydown(function(event){
    switch (context) {
        case "window":
            switch (event.which) {
                case 113:
                    cobrar();
                    break;
                case 116:
                if (sell.orderid !== 0) {
                    alert ("Hay una venta pendiente");
                }else{
                    location.reload();
                }
                    break;
            }
            break;
        case "pago":
            switch (event.which) {
                case 13:
                    if (paymentconfirmed){
                        confirmSellAndPrint();
                        paymentconfirmed = false;
                    }else{
                     paymentconfirmed = true;
                    }
                    break;
            }
        case "retiro":
            switch (event.which) {
                case 13 :
                    saveretiro();
                    break;
            }
    }
    console.log(event.which);
});