var machineCounters;
var sheetCounters;

var cartacounter;
var oficioCounter;
var dblCartacounter;

var sharp1;
var sharp2;
var sharp3;
var sharp4;

function getCounters(callback){
    $.ajax({
        url: "requesthandler.php",
        type : "POST",
        dataType : "JSON",
        data : {
            "function":"getCounters"
        },
        success : function(r){
            if (r.success){
                machineCounters = r.machineCounters;
                sheetCounters = r.sheetCounters;
                initCounters(r.sheetCounters);
                initMachineCounters(r.machineCounters);
                callback();
            }
        }
    })
}

function updateInitCounters(data) {
    cartacounter.update(data[0].currval);
    oficioCounter.update(data[1].currval);
    dblCartacounter.update(data[1].currval);
}

function initCounters(data){
    if (data == null){
        window.location.reload();
    }
    cartacounter = new CountUp("cartacounter",0,data[0].currval,0,5);
    oficioCounter = new CountUp("oficiocounter",0,data[1].currval,0,5);
    dblCartacounter = new CountUp("doblecartacounter",0,data[2].currval,0,5);
    if (!cartacounter.error) {
        cartacounter.start();
    }else{
        console.log(cartacounter.error);
    }
    if (!oficioCounter.error) {
        oficioCounter.start();
    }else{
        console.log(oficioCounter.error);
    }
    if (!dblCartacounter.error) {
        dblCartacounter.start();
    }else{
        console.log(dblCartacounter.error);
    }
}

function initMachineCounters(mCounter){
    sharp1 = new CountUp("sharp1",0,mCounter[0].currentcount,0,10);
    sharp2 = new CountUp("sharp2",0,mCounter[1].currentcount,0,10);
    sharp3 = new CountUp("sharp3",0,mCounter[2].currentcount,0,10);
    sharp4 = new CountUp("sharp4",0,mCounter[3].currentcount,0,10);
    if (!cartacounter.error) {
        sharp1.start();
    }else{
        console.log(cartacounter.error);
    }
    if (!oficioCounter.error) {
        sharp2.start();
    }else{
        console.log(oficioCounter.error);
    }
    if (!dblCartacounter.error) {
        sharp3.start();
    }else{
        console.log(dblCartacounter.error);
    }
    if (!dblCartacounter.error) {
        sharp4.start();
    }else{
        console.log(dblCartacounter.error);
    }
}



function calculateSheet(method,item,ammount){
    if (method === "add") {
        switch (Number(item)){
            case 5:
                addSingleSheet(cartacounter,ammount);
                break;
            case 7:
                addDuplexSheet(cartacounter,ammount);
                break;
            case 6:
                addSingleSheet(oficioCounter,ammount);
                break;
            case 8:
                addDuplexSheet(oficioCounter,ammount);
                break;
            case 10:
                addSingleSheet(dblCartacounter,ammount);
                break;
            case 11:
                addDuplexSheet(dblCartacounter,ammount);
                break;
            case 331:
                addSingleSheet(cartacounter,ammount);
                break;
        }
    }
    if (method === "sub"){
        switch (Number(item)){
            case 5:
                subSingleSheet(cartacounter,ammount);
                break;
            case 7:
                subDuplexSheet(cartacounter,ammount);
                break;
            case 6:
                subSingleSheet(oficioCounter,ammount);
                break;
            case 8:
                subDuplexSheet(oficioCounter,ammount);
                break;
            case 10:
                subSingleSheet(dblCartacounter,ammount);
                break;
            case 11:
                subDuplexSheet(dblCartacounter,ammount);
                break;
            case 331:
                subSingleSheet(cartacounter,ammount);
                break;
        }
    }
}

function isPair(ammount){
    return !(ammount % 2);
}

function addSingleSheet(counter,ammount){
    var newCounter = counter.endVal + Number(ammount);
    updateCounter(counter,newCounter);
}

function addDuplexSheet(counter,ammount){
    var newCounter = 0;
    if (!(ammount % 2)) {
        newCounter = counter.endVal + (Number(ammount) / 2);
        updateCounter(counter,newCounter);
    }else{
        ammount += 1;
        newCounter = counter.endVal + (Number(ammount) / 2);
        updateCounter(counter,newCounter);
    }
}

function subSingleSheet(counter,ammount){
    var newCounter = counter.endVal - Number(ammount);
    updateCounter(counter,newCounter);
}

function subDuplexSheet(counter,ammount){
    var newCounter = 0;
    if (!(ammount % 2)){
        newCounter = counter.endVal - (Number(ammount) / 2);
        updateCounter(counter,newCounter);
    }else{
        ammount += 1;
        newCounter = counter.endVal - (Number(ammount) / 2);
        updateCounter(counter,newCounter);
    }
}

function updateCounter(counter,ammount){
    counter.update(ammount);
}

function updateMachineCounters(){
    let s1 = Number($("#sharp1CounterCapture").val());
    let s2 = Number($("#sharp2CounterCapture").val());
    let s3 = Number($("#sharp3CounterCapture").val());
    let s4 = Number($("#sharp4CounterCapture").val());
    let s1cc = Number(machineCounters[0].currentcount);
    let s2cc = Number(machineCounters[1].currentcount);
    let s3cc = Number(machineCounters[2].currentcount);
    let s4cc = Number(machineCounters[3].currentcount);
    if (s1 >= s1cc) {
        machineCounters[0].currentcount = s1;
    }else{
        alert("El valor no puede ser inferior al contador actual");
        $("#sharp1CounterCapture").focus();
        return;
    }
    if (s2 >= s2cc) {
        machineCounters[1].currentcount = s2;
    }else{
        alert("El valor no puede ser inferior al contador actual");
        $("#sharp2CounterCapture").focus();
        return;
    }
    if (s3 >= s3cc) {
        machineCounters[2].currentcount = s3;
    }else{
        alert("El valor no puede ser inferior al contador actual");
        $("#sharp3CounterCapture").focus();
        return;
    }
    if (s4 >= s4cc) {
        machineCounters[3].currentcount = s4;
    }else{
        alert("El valor no puede ser inferior al contador actual");
        $("#sharp4CounterCapture").focus();
        return;
    }
    $("#contadoresCapture").modal("hide");
}
