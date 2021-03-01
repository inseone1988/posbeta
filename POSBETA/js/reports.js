var reportData;
var cTotals;
var cajas;
var categories;
var totalCatogories;
function getReport(from, to) {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "getFullReport",
            "data": {"from": from, "to": to}
        },
        success: function (r) {
            if (r.success) {
                reportData = r.payload;
                cTotals = r.systemCorte;
                displayCajaReport(from);
            }
        }
    });
}

function displayCajaReport(day){
    $("#report-date").text(moment(day).format("dddd DD MMMM YYYY"));
    displayCorteTotals();
    filterCajas();
    filterCategories();
    sumCategoriesTotal();
}

function displayCorteTotals(){
    $("#report-total").text(numeral(cTotals.ammount).format("$0,0.00"));
    $("#report-diference").text(numeral(cTotals.diference).format("$0,0.00"));
    $("#report-cash").text(numeral(cTotals.count).format("$0,0.00"));
}

function filterCajas(){
    cajas = [];
    var mCajas = 0;
    for (let i = 0; i < reportData.length; i++) {
        current = reportData[i];
        if (current.cajaid !== mCajas){
            cajas.push(current.cajaid);
        }
        mCajas = current.cajaid;
    }
}

function filterCategories(){
    categories = [];
    var ccat = "";
    for (let i = 0; i < reportData.length; i++) {
        current = reportData[i];
        for (let j = 0; j < current.details.length; j++) {
            var cat = current.details[j].category;
            if (cat !== ccat) {
                categories.push(cat);
            }
            ccat = cat;
        } 
    }
}

function sumCategoriesTotal(category){
    totalCatogories = [];
    for (let i = 0; i < categories.length; i++) {
        var cat = categories[i];
        for (let j = 0; j < reportData.length; j++) {
            var curr = reportData[j];
            var indexExists = totalCatogories[cat] !== undefined;
            if (indexExists) {
                var paym = curr.payments;
                for (let k = 0; k < paym.length; k++) {
                    if (paym[k].Total > paym[k].payment){
                        totalCatogories[cat] += paym[k].payment;
                    }else{
                        totalCatogories[cat] += paym[k].Total;
                    }
                }
            }else{
                totalCatogories[cat] = 0;
                var paym = curr.payments;
                for (let k = 0; k < paym.length; k++) {
                    if (paym[k].Total > paym[k].payment){
                        totalCatogories[cat] += paym[k].payment;
                    }else{
                        totalCatogories[cat] += paym[k].Total;
                    }
                }
            }
        }
    }
}

function printReport(){
    var prtContent = document.getElementById("day-report");
    var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
}