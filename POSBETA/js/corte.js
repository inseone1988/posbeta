var ventas;
var corteinfo;
var summ;
var copiasinfo;
var papinfo;
var cyberinfo;
var cashmvm;
function getCorteData(callback){
    $.ajax({
        url:"requesthandler.php",
        type:"POST",
        dataType:"JSON",
        data:{
            "function":"getFullReport",
            "data":{"from":moment().format("YYYY-MM-DD"),"to":moment().add(1,"day").format("YYYY-MM-DD")}
        },
        success : function(r){
            if (r.success){
                corteinfo = r.systemCorte;
                summ = r.details.summary;
                copiasinfo = r.details.copias;
                papinfo = r.details.papeleria;
                cyberinfo = r.details.cyberplanet;
                cashmvm = r.details.cash;
                ventas = r.payload;
                mapSummary();
                mapSystemCorte();
                mapCopies();
                mapPapeleria();
                mapCyber();
                mapCashMvm();
                mapSells();
                //callback();
            }
        }
    })
}

function mapSystemCorte(){
    $("#system-count").text(numeral(corteinfo.ammount).format("$0,0.00"));
    $("#corte-count").text(numeral(corteinfo.count).format("$0,0.00"));
    $("#diference").text(numeral(corteinfo.diference).format("$0,0.00"));
}

function mapSummary(){
    for (let i = 0; i < summ.length; i++) {
        var row = "<tr><td>"+summ[i].category+"</td><td>"+Number(summ[i].quantity)+"</td><td>"+numeral(summ[i].total).format('$0,0.00')+"</td>";
        $("#summary").append(row);
    }
}

function mapCopies() {
    var total = 0;
    for (let i = 0; i < copiasinfo.length; i++) {
        total += Number(copiasinfo[i].total);
        var row = "<tr><td>"+copiasinfo[i].ProductId+"</td><td>"+copiasinfo[i].Name+"</td><td>"+copiasinfo[i].quantity+"</td><td>"+numeral(copiasinfo[i].price).format("$0,0.00")+"</td><td>"+numeral(copiasinfo[i].total).format("$0,0.00")+"</td>";
        $("#copies").append(row);
    }
    var totalrow = "<tr class='font-weight-bold'><td colspan='3'></td><td>Total</td><td>"+numeral(total).format("$0,0.00")+"</td>";
    $("#copies").append(totalrow);
}

function mapPapeleria(){
    var total = 0;
    for (let i = 0; i < papinfo.length; i++) {
        total += Number(papinfo[i].total);
        var row = "<tr><td>"+papinfo[i].ProductId+"</td><td>"+papinfo[i].Name+"</td><td>"+numeral(papinfo[i].price).format("$0,0.00")+"</td><td>"+Number(papinfo[i].quantity)+"</td><td>"+numeral(papinfo[i].total).format("$0,0.00")+"</td>";
        $("#pape").append(row);
    }
    var totalrow = "<tr class='font-weight-bold'><td colspan='3'></td><td>Total</td><td>"+numeral(total).format("$0,0.00")+"</td>";
    $("#pape").append(totalrow);
}

function mapCyber(){
    var total = 0;
    for (let i = 0; i < cyberinfo.length; i++) {
        total += Number(cyberinfo[i].total);
        var row = "<tr><td>"+cyberinfo[i].ProductId+"</td><td>"+cyberinfo[i].Name+"</td><td>"+numeral(cyberinfo[i].price).format("$0,0.00")+"</td><td>"+Number(cyberinfo[i].quantity)+"</td><td>"+numeral(cyberinfo[i].total).format("$0,0.00")+"</td>";
        $("#cyber").append(row);
    }
    var totalrow = "<tr class='font-weight-bold'><td colspan='3'></td><td>Total</td><td>"+numeral(total).format("$0,0.00")+"</td>";
    $("#cyber").append(totalrow);
}

function mapCashMvm(){
    for (let i = 0; i < cashmvm.length; i++) {
        var row = "<tr><td>"+cashmvm[i].timestamp+"</td><td>"+cashmvm[i].description+"</td><td>"+numeral(cashmvm[i].ammount).format("$0,0.00")+"</td>";
        $("#cashmvm").append(row);
    }
}

function mapSells(){
    $.each(ventas,function(index,value){
        var row = "<tr class='font-weight-bold mt-3 bg-light-gray'><td>Ticket no :"+value.OrderId+"</td><td colspan='4'> Fecha y hora : "+value.Timestamp+"</td>";
        $("#sell-by-ticket").append(row);
        for (let i = 0; i < value.details.length; i++) {
            var product = value.details[i];
            var mRow = "<tr><td>Hora y fecha: "+product.fecha+"</td><td>Producto : "+product.Name+"</td><td>Cantidad : "+product.Quantity+"</td><td>Precio : "+numeral(product.price).format("$0,0.00")+"</td><td>Total : "+numeral((product.Quantity * product.price)).format("$0,0.00")+"</td>";
            $("#sell-by-ticket").append(mRow);
        }
        for (let i = 0; i < value.payments.length; i++) {
            payment = value.payments[i];
            var pRow = "<tr><td colspan='2'></td><td class='font-weight-bold bg-info'>Pago : "+numeral(payment.payment).format("$0,0.00")+"</td><td class='font-weight-bold bg-info'>Cambio : "+numeral(payment.Change).format("$0,0.00")+"</td><td class='font-weight-bold bg-info'>Total : "+numeral(payment.Total).format("$0,0.00")+"</td>";
            $("#sell-by-ticket").append(pRow);
        }
    });
}