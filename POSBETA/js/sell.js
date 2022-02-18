var context = "window"; //Default context is window//
corteGrandTotal = 0;
var mode = "Normal";
var tickets;
var printed = false;
var corte = {
    ammount: 0,
    count: 0,
    diference: 0
};

var sell = {
    orderid: 0,
    hasIva: false,
    isPartialPayment: false,
    IVA: 0,
    change: 0,
    isLocked: false,
    order: {
        OrderId: 0,
        branch: $("#branchid").val() === undefined ? 1 : $("#branchid").val(),
        Timestamp: undefined,
        DatetoFinish: undefined,
        JobType: "Venta de papeleria",
        EmployeeId: null,
        CustomerId: null,
        jobName: null,
        OrderStatus: "Captura",
        obs: null,
        prioridad: "Normal",
        tax: 0
    },
    orderdetails: [],
    payment: [],
    items: [],
    grandTotal: 0,
    iva: 0,
    discounts: [],
    hasPayments: this.payment.length !== 0,
    isEditable: function () {
        return this.order.OrderStatus !== "Finished";
    },
    addItem: function (itemdata) {
        this.items.push(itemdata)
    },
    deleteItem: function (position) {
        var q = this.items[position].Quantity;
        calculateSheet("sub", this.items[position].ItemId, q);
        this.orderdetails[position].Status = 0;
        this.items[position].Status = 0;
        this.calculateSellTotal();
        redrawSellTable();
    },
    changeQuantity: function (position, quantity, init) {
        var currentQuantity = this.items[position].Quantity;
        if (quantity >= Number(currentQuantity)) {
            var dif = quantity - currentQuantity;
            if (init) {
                dif = quantity;
            }
            calculateSheet("add", this.items[position].ItemId, dif);
        } else {
            var dif = currentQuantity - quantity;
            calculateSheet("sub", this.items[position].ItemId, dif);
        }
        this.orderdetails[position].Quantity = quantity;
        this.items[position].Quantity = quantity;
        this.calculateitemTotal(position);
        this.calculateSellTotal(position);
        redrawSellTable();
    },
    subQuantity: function (position, quantity) {
        this.orderdetails[position].Quantity = (this.orderdetails[position].Quantity - quantity)
    },
    calculateitemTotal: function (position) {
        var item = this.items[position];
        item.Total = (item.Price * item.Quantity);
    },
    calculateSellTotal: function () {
        var total = 0;
        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].Status !== 0) {
                total += (this.items[i].Total);
            }
        }
        this.grandTotal = total;
        if (this.hasIva) {
            var iva = (16 * total) / 100;
            total = this.grandTotal + iva;
            sell.IVA = iva;
            addTaxRow(iva);
        } else {
            deleteTaxRow();
        }
        if (this.hasPayments) {
            for (let i = 0; i < this.payment.length; i++) {
                total -= Number(this.payment[i].payment);
            }
            addAnticipoRow();
        }
        this.grandTotal = total;
        changeLcdTotal(total);
    },
    changePrice: function (position, price) {
        this.orderdetails[position].price = price;
        this.items[position].Price = price;
        this.calculateitemTotal(position);
        this.calculateSellTotal();
    },
    hasMayoreo: function (position) {
        if (this.items[position].mayoreo !== undefined) {
            return this.items[position].mayoreo.length !== 0;
        }
        return false;
    },
    getPrice: function (position) {
        var item = this.items[position];
        if (this.hasMayoreo(position)) {
            for (var i = 0; i < item.mayoreo.length; i++) {
                var limit = Number(item.mayoreo[i].itemlimit);
                var next = i + 1 >= item.mayoreo.length ? i : i + 1;
                if (item.Quantity >= limit && item.Quantity <= Number(item.mayoreo[next].itemlimit)) {
                    this.changePrice(position, item.mayoreo[i].price);
                    break;
                } else {
                    this.changePrice(position, item.mayoreo[i].price);
                }
            }
        } else {
            this.changePrice(position, item.Price);
        }
    },
    addOrderDetails: function (data) {
        this.orderdetails.push({
            orderdetailsid: data.orderdetailsid !== undefined ? data.orderdetailsid : undefined,
            OrderId: data.OrderId,
            fecha: moment().format("YYYY-MM-DD HH:mm:ss"),
            ProductId: data.ProductId,
            Quantity: data.Quantity,
            Status: 1,
            PackageJob: data.packagejob !== undefined ? data.packagejob : "Venta de mostrador",
            price: data.price
        });
    },
    toggleTax: function () {
        if (!sell.hasIva) {
            sell.hasIva = true;
            sell.order.tax = 1;
            this.calculateSellTotal();
        } else {
            sell.hasIva = false;
            sell.order.tax = 0;
            this.calculateSellTotal();
        }
    },
    initItem: function (data, init) {
        this.addItem(data);
        var position = this.items.length - 1;
        var item = this.items[position];
        item.OrderId = this.orderid;
        if (data.orderdetailsid != undefined) {
            item.orderdetailsid = data.orderdetailsid;
        }
        if (data.Status != undefined) {
            item.Status = data.Status;
        }
        item.ProductId = data.ItemId;
        if (item.Quantity === undefined) {
            var q = prompt("Cantidad", 1);
            item.Quantity = q;
        }
        if (item.Quantity != null) {
            this.addOrderDetails(item);
            this.getPrice(position);
            this.calculateitemTotal(position);
            this.calculateSellTotal();
            this.changeQuantity(position, Number(item.Quantity), init);
        } else {
            this.items.splice(position, 1);
        }
    }
};

function getItem(itemid, orderId,sbc) {
    // var mFunction = "getItem";
    // if (searchByCode) {
    //     mFunction = "getItemByCode";
    // }
    // $.ajax({
    //     url: "requesthandler.php",
    //     type: "POST",
    //     dataType: "JSON",
    //     data: {"function": mFunction, "itemid": itemid},
    //     success: function (r) {
    //         if (r.success) {
    //             $("#item-search").val("");
    //             if (r.payload.length > 0) {
    //                 sell.initItem(r.payload[0], true);
    //             } else {
    //                 alert("No se ha encontrado el articulo");
    //             }
    //         }
    //     }
    // });
    let data = {"itemId" : itemid,"orderId" : orderId};
    if (sbc) data.sbc = sbc;
    $.post("/api/public/v0/orders/add",data,r=>{
        console.log(r);
        sell = r;
        console.log(sell);
    });
}

function getSellFolio(callback) {
    // $.ajax({
    //     url: "requesthandler.php",
    //     type: "POST",
    //     dataType: "JSON",
    //     data: {"function": "newOrder",},
    //     success: function (r) {
    //         if (r.success) {
    //             sell.orderid = r.orderid;
    //             sell.order.OrderId = r.orderid;
    //             sell.order.Timestamp = moment().format("YYYY-MM-DD HH:mm:ss");
    //             callback(r.orderid);
    //         }
    //     }
    // });
    $.getJSON("/api/public/v0/newOrder",data => {
        if (data.success){
            callback(data.payload.orderId);
        }else{
            alert(data.message);
        }
    })
}

function selectRow(element) {
    $("tr").removeClass("selected-row");
    element.addClass("selected-row");
    $("#delete-item").data("position", element.data("position"));
    console.log(element.data("position"));
}

function deleteRow(position) {
    sell.deleteItem(position);
    $("#sell-details tr").eq(position).addClass("d-none");
}

function redrawSellTable() {
    $("#sell-details").empty();
    for (let i = 0; i < sell.items.length; i++) {
        addRow(sell.items[i], i);
    }
}

function addAnticipoRow() {
    $("#anticipo").remove();
    if (sell.hasPayments) {
        var pagado = 0;
        for (let i = 0; i < sell.payment.length; i++) {
            pagado += Number(sell.payment[i].payment);
        }
    }
    var row = "<tr id='anticipo'><td colspan='3'></td><td>Anticipo</td><td>" + numeral(pagado).format("$0,0.00") + "</td>";
    $("#misc").prepend(row);
}

function addRow(itemdata, position) {
    if (position === undefined) {
        position = (sell.items.length - 1);
    }
    var visible = itemdata.Status === 0 ? "d-none" : "";
    var row = "<tr class='" + visible + "' data-position=" + position + " onclick='selectRow($(this))'><td>" + itemdata.ItemId + "</td><td>" + itemdata.Name + "</td><td data-position='" + position + "' data-quantity='" + itemdata.Quantity + "' onclick='changeQuantity($(this))'>" + itemdata.Quantity + "</td><td data-position='" + position + "' data-unitprice='" + itemdata.Price + "' onclick='changePrice($(this))'>" + numeral(itemdata.Price).format("$0,0.00") + "</td><td>" + numeral(itemdata.Total).format("$0,0.00") + "</td></tr>";
    row = $(row);
    $("#sell-details").append(row);
}

function changeLcdTotal(ammount) {
    $("#lcd-money-ammount").text(numeral(ammount).format("0,00.00"));
}

function setItemSearchListener() {
    $("#search-form").on("submit", function (event) {
        event.preventDefault();
        var item = $("#item-search").val();
        if (isNaN(item)) {
            goSearch(item, true);
        } else {
            if (item !== "") {
                goSearch(item);
            }
        }

    });
}

function goSearch(itemid,sbc) {
    // var sbc = searchByCode !== undefined;
    getItem(itemid,sell.orderid,sbc);
    // if (sell.orderid === 0) {
    //     getSellFolio(function (folio) {
    //         $("#orderidfolio").text("Folio : " + folio);
    //         getItem(itemid, sbc);
    //     });
    // } else {
    //     getItem(itemid, sbc);
    // }
}

function getEngargolado(size) {
    switch (size) {
        case 10:
            goSearch(58);
            break;
        case 13:
            goSearch(59);
            break;
        case 15:
            goSearch(61);
            break;
        case 18:
            goSearch(63);
            break;
        case 21:
            goSearch(64)
            break;
        case 25:
            goSearch(65);
            break;
        case 30:
            goSearch(66);
            break;
        case 38:
            goSearch(333);
            break;
        case 45:
            goSearch(334);
            break;
    }
}

function searchByButton(functionality) {
    switch (functionality) {
        case "eng":
            var size = prompt("¿Que tamaño de espiral?", 13);
            if (size != 0 || size != undefined) {
                getEngargolado(Number(size));
            }
            break;
    }
}

function addTaxRow(ammount) {
    $("#misc").removeClass("hide");
    $("#ivatotal").text(numeral(ammount).format("$0,0.00"));
}

function deleteTaxRow() {
    $("#misc").addClass("hide");
    $("#ivatotal").text(numeral(0).format("$0,0.00"));
}

function calculteChg(payment) {
    var change = (payment - sell.grandTotal);
    if (change < 0) {
        change = 0;
    }
    return change;
}

function cobrar(element) {
    sell.isLocked = true;
    if (sell.orderid !== 0) {
        $("#cobrar-total").text(numeral(sell.grandTotal).format("$0,0.00"));
        $("#payment").on("change", function () {
            var payment = Number($("#payment").val());
            var change = calculteChg(payment);
            sell.change = change;
            if (payment < sell.grandTotal) {
                if (!sell.isPartialPayment) {
                    $("#pagoerrortext").text("El pago no puede ser menor al total.");
                    setTimeout(function () {
                        $("#pagoerrortext").text("");
                    }, 5000);
                    sell.isLocked = true;
                } else {
                    if (payment < sell.grandTotal && sell.isPartialPayment && sell.payment.length >= 1) {
                        $("#pagoerrortext").text("Debe cobrar el total del ticket.");
                        setTimeout(function () {
                            $("#pagoerrortext").text("");
                        }, 5000);
                        sell.isLocked = true;
                    } else {
                        sell.isLocked = false;
                        updatePayments();
                    }
                }
                //$("#partialpayment").attr("checked", true);
                //sell.isPartialPayment = true;
                //sell.order.OrderStatus = "Pendiente";
            } else {
                //$("#partialpayment").attr("checked", false);
                sell.isLocked = false;
                sell.order.OrderStatus = "Finished";
                sell.isPartialPayment = false;
            }
            updatePayments();
            $("#cobrar-total").text(numeral(sell.grandTotal).format("$0,0.00"));
            $("#cobrar-pago").text(numeral(payment).format("$0,0.00"));
            $("#cobrar-cambio").text(numeral(change).format("$0,0.00"));
        });
        $("#cobrar-modal").modal("show");
        $("#cobrar-modal").on("hidden.bs.modal", function (e) {
            context = "window";
        });
        $("#cobrar-modal").on("shown.bs.modal", function (e) {
            context = "pago";
            $("#payment").focus();
            $("#payment").select();
        });
    } else {
        alert("No hay nada por cobrar");
    }
}

function updatePayments() {
    if (sell.hasPayments) {
        var last = (sell.payment.length) - 1;
        for (let i = 0; i < sell.payment.length; i++) {
            var curr = sell.payment[i];
            sell.payment[i].PaymentDescription = "Reference Payment";
        }
        if (sell.payment.length > 1) {
            sell.payment[last].PaymentDescription = "Total de nota";
        }
    }
}

function removeItem(element) {
    $("#delete-item").addClass("disabled");
    var position = element.data("position");
    if (position !== -1) {
        var mDelete = confirm("Eliminar producto?");
        if (mDelete) {
            deleteRow(position);
            element.data("position", -1);
            element.attr("data-position", -1);
            $("#delete-item").removeClass("disabled");
        }
    } else {
        alert("Seleccione item");
    }
}

function resetSell() {
    var reset = confirm("Esta seguro de inicializar la venta");
    if (reset) {
    }
}

function addPayment(paymentdescription, ammount) {
    var payment = {
        Timestamp: moment().format("YYYY-MM-DD HH:mm:ss"),
        OrderId: sell.orderid,
        PaymentDescription: paymentdescription,
        PaymentMethod: "Efectivo",
        Ammount: 0,
        discount: 0,
        payment: ammount,
        Total: sell.grandTotal,
        Credit: ammount < sell.grandTotal ? (sell.grandTotal - ammount) : 0,
        change: sell.change,
        IVA: sell.IVA
    };
    sell.payment.push(payment);
}

function switchOpTypeText() {
    var switchlever = $("#customSwitch1");
    var enabled = switchlever.prop("checked");
    var optypeText = $("#optype");
    optypeText.addClass("text-danger");
    (enabled ? optypeText.text("ingresar") : optypeText.text("retirar"));
    switchlever.on("change", function () {
        enabled = switchlever.prop("checked");
        if (!enabled) {
            optypeText.removeClass("text-success");
            optypeText.addClass("text-danger");
            optypeText.text("retirar");
        } else {
            optypeText.removeClass("text-danger");
            optypeText.addClass("text-success");
            optypeText.text("ingresar");
        }
    })
}

function saveretiro() {
    var switchlever = $("#customSwitch1");
    var cant = Number($("#withdraw-amm").val());
    if (cant !== 0) {
        var retiro = {
            timestamp: moment().format("YYYY-MM-DD HH:mm:ss"),
            description: $("#withdraw-desc").val(),
            ammount: cant,
            type: switchlever.prop("checked") ? "entrada" : "retiro"
        };
        $.ajax({
            url: "requesthandler.php",
            type: "POST",
            data: {
                "function": "saveRetiro",
                "data": retiro
            },
            success: function (r) {
                if (r !== 0) {
                    alert("Movimiento guardado");
                    var printw = window.open("retiroticket.php", "_blank");
                    printw.addEventListener("DOMContentLoaded", function () {
                        printw.print();
                        printw.close();
                        location.reload();
                    });
                }
            }
        });
    }
}

function makeWithdraw() {
    $("#retiro-modal").modal("show");
    $("#retiro-modal").on("shown.bs.modal", function (event) {
        $("#withdraw-amm").focus();
        $("#withdraw-amm").select();
        context = "retiro";
    });
    $("#retiro-modal").on("hide.bs.modal", function (event) {
        context = "window";
    });
}

function saveOrderToServer(callback) {
    updateSheetCounterData();
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "saveOrder",
            "orderdata": sell.order,
            "orderdetails": sell.orderdetails,
            "payment": sell.payment,
            "sheetCounters": sheetCounters
        },
        success: function (r) {
            if (r.success) {
                callback();
            }
        }
    });
}

function updateSheetCounterData() {
    sheetCounters[0].currval = cartacounter.endVal;
    sheetCounters[1].currval = oficioCounter.endVal;
    sheetCounters[2].currval = dblCartacounter.endVal;
}

function newWork() {
    getSellFolio(function (folio) {
        var ant = prompt("Anticipo : ", 0);
        if (!isNaN(ant)) {
            if (folio !== 0) {
                saveAnticipo(folio, ant, function () {
                    var mWindow = window.open("newjob.php?orderid=" + folio + "&ant=" + ant);
                    mWindow.addEventListener("DOMContentLoaded", function () {
                        mWindow.print();
                        mWindow.close();
                        location.reload();
                    });
                });
            }
        } else {
            alert("Cantidad invalida");
        }
    })
}

function saveAnticipo(oid, qty, callback) {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "saveJobAnticipo",
            "orderid": oid,
            "qty": qty
        },
        success: function (r) {
            if (r.success) {
                callback();
            }
        }
    });
}

function confirmSellAndPrint(element) {
    if (sell.orderid !== 0) {
        if (!sell.isLocked) {
            $("#candp").attr("disabled", true);
            var payment = Number($("#payment").val());
            var pd = sell.isPartialPayment ? "Pago parcial / Anticipo" : "Total de nota";
            if (payment === 0) {
                pd = "Reference payment";
            }
            addPayment(pd, payment);
            if (payment > sell.grandTotal && pd === "Total de nota" && sell.hasPayments) {
                updatePayments();
            }
            saveOrderToServer(function () {
                var mWindow = window.open("ticket.php?orderid=" + sell.orderid, "_blank");
                mWindow.addEventListener("DOMContentLoaded", function () {
                    mWindow.print();
                    mWindow.close();
                    printed = true;
                    location.reload();
                });
            });
        }
    }

}

function displayCorteModal() {
    $("#corte-modal").modal("show");
    getCajaReport();
}

function getCajaReport(callback) {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {"function": "getCajaReport"},
        success: function (r) {
            if (r.success) {
                displayCajaReport(r.payload, r.anticipos, r.totals, r.cashMovements);
                if (callback !== undefined) {
                    callback(r.payload);
                }
            }
        }
    });
}

function displayCajaReport(data, anticipos, totals, cashmvm) {
    corteGrandTotal = 0;
    $("#caja-details").empty();
    var row1 = "<tr class='font-weight-bold'><td></td><td>Totales</td><td>" + numeral(totals.totalcantidad).format("0,0") + "</td><td>" + numeral(totals.payment).format("$0,0.00") + "</td><td></td></tr>";
    var row2 = "<tr class='font-weight-bold'><td></td><td colspan='2'>Anticipos</td><td>" + numeral(anticipos.anticipo).format("$0,0.00") + "</td><td></td>";
    for (let i = 0; i < data.length; i++) {
        var row = "<tr><td>" + data[i].cajaid + "</td><td>" + data[i].category + "</td><td>" + numeral(data[i].total).format("0,0") + "</td><td>" + numeral(data[i].payment).format("$0,0.00") + "</td><td>" + numeral(data[i].IVA).format("$0,0.00") + "</td></tr>";
        //corteGrandTotal += Number(data[i]["payment"]);
        $("#caja-details").append(row);
    }
    $("#caja-details").append(row1);
    $("#caja-details").append(row2);
    corteGrandTotal += Number(anticipos.anticipo);
    for (let i = 0; i < cashmvm.length; i++) {
        var title = cashmvm[i].type === "entrada" ? "Entradas de efectivo" : "Retiros de efectivo";
        var row = "<tr class='font-weight-bold'><td></td><td colspan='2'>" + title + "</td><td>" + numeral(cashmvm[i].cajamvmts).format("$0,0.00") + "</td><td></td></tr>";
        $("#caja-details").append(row);
    }
    if (cashmvm.length > 0) {
        var totalEntradas = 0;
        var total_salidas = 0;
        for (let i = 0; i < cashmvm.length; i++) {
            switch (cashmvm[i].type) {
                case "retiro":
                    total_salidas += Number(cashmvm[i].cajamvmts);
                    break;
                case "entrada":
                    totalEntradas += Number(cashmvm[0].cajamvmts);
                    break;
            }
        }
        //var totalEntradas = cashmvm[0] !== undefined ? Number(cashmvm[0].cajamvmts) : 0;
        //var total_salidas = cashmvm[1] !== undefined ? Number(cashmvm[1].cajamvmts) : 0;
        corteGrandTotal += ((Number(totals.payment) + totalEntradas) - total_salidas);
    }
    var lastRow = "<tr class='font-weight-bold'><td></td><td colspan='2'>Grand Total</td><td>" + numeral(corteGrandTotal).format("$0,0.00") + "</td><td></td></tr>";
    $("#amm-system").text(numeral(corteGrandTotal).format("$0,0.00"));
    $("#caja-details").append(lastRow);
}

function sumMoney() {
    var total = 0;
    $(".money").each(function (index, element) {
        var value = $(element).val() === "" ? 0 : $(element).val();
        var amm = value * $(element).data("value");
        total += amm;
    });
    var diff = 0;
    if (corteGrandTotal < 0) {
        diff = total;
    } else {
        diff = total - corteGrandTotal;
    }
    $("#amm-cash").text(numeral(total).format("$0,0.00"));
    $("#amm-diff").text(numeral(diff).format("$0,0.00"))
    corte.ammount = corteGrandTotal;
    corte.count = total;
    corte.diference = diff;
}

function setCorteListeners() {
    $(".money").on("change", function () {
        sumMoney();
    });
}

function saveCorteAndExit() {
    var exitconfirmed = confirm("Seguro que deseas realizar el corte de caja");
    if (exitconfirmed) {
        $.ajax({
            url: "requesthandler.php",
            type: "POST",
            data: {
                "function": "closeCaja",
                "corte": corte,
                "machineCounters": machineCounters
            },
            success: function () {
                window.open("corte.html");
                window.location.reload();
            }
        })
    }
}

function getPendingSell(id) {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "getPendingSell",
            "orderid": id
        },
        success: function (r) {
            if (r.success) {
                mapSell(r.payload);
                console.log(r.payload);
            }
        }
    });
}

function mapSell(data) {
    sell.orderdetails = [];
    sell.payment = [];
    sell.items = [];
    sell.orderid = data.order[0].OrderId;
    $("#orderidfolio").text("Folio : " + sell.orderid);
    console.log(Number(data.order[0].tax));
    sell.hasIva = Number(data.order[0].tax) !== 0;
    sell.isPartialPayment = data.order[0].OrderStatus !== "Finished";
    sell.order = data.order[0];
    if (data.payment.length > 0) {
        for (let i = 0; i < data.payment.length; i++) {
            sell.payment.push(data.payment[i]);
        }
    }
    for (let i = 0; i < data.orderdetails.length; i++) {
        sell.initItem(data.orderdetails[i], false);
    }
    redrawSellTable();
    sell.calculateSellTotal();
}

$("#partialpayment").on("change", function () {
    var isPartial = $(this).prop("checked")
    sell.isPartialPayment = isPartial;
    if (sell.isPartialPayment) {
        sell.order.OrderStatus = "Pendiente";
    }
    if (sell.isLocked) {
        sell.isLocked = !isPartial;
    }
});

function changeQuantity(element) {
    var current = element.data("quantity");
    var nQuntity = prompt("Cantidad", current);
    var position = element.data("position");
    sell.changeQuantity(position, nQuntity);
}

function changePrice(element) {
    var current = element.data("unitprice");
    var price = prompt("precio", current);
    var position = element.data("position");
    sell.changePrice(position, price);
    redrawSellTable();
}

function getPendingOrders() {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "getPendingOrders",
        },
        success: function (r) {
            if (r.success) {
                mapPendingOrders(r.payload);
            }
        }
    })
}

function mapPendingOrders(data) {
    for (let i = 0; i < data.length; i++) {
        var row = "<tr style='font-size: 13px;'><td>" + moment(data[i].Timestamp).format("DD-MMM HH:mm") + "</td><td><a href='javascript:getPendingSell(" + data[i].OrderId + ")'>" + data[i].OrderId + "</a></td><td>" + numeral(data[i].Total).format("$0,00.00") + "</td></tr>";
        $("#pendingOrders").append(row);
    }
}

function captureContadores() {
    $("#sharp1CounterCapture").val(machineCounters[0].currentcount);
    $("#sharp2CounterCapture").val(machineCounters[1].currentcount);
    $("#sharp3CounterCapture").val(machineCounters[2].currentcount);
    $("#sharp4CounterCapture").val(machineCounters[3].currentcount);
    $("#contadoresCapture").modal("show");
}

function reprintTicket(order) {
    if (order !== 0) {
        var printw = window.open("ticket.php?orderid=" + order, "_blank");
        printw.addEventListener("DOMContentLoaded", function () {
            printw.print();
            printw.close();
            $("#reprint-ticket").modal("hide");
        });

    }
}

function getCajaTickets() {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "getCajaTickets"
        },
        success: function (r) {
            if (r.success) {
                tickets = r.tickets;
                mapCajaTickets();
            }
        }
    })
}

function mapCajaTickets() {
    for (let i = 0; i < tickets.length; i++) {
        var rw = "<tr><td>" + tickets[i].OrderId + "</td><td>" + tickets[i].Timestamp + "</td><td><a href='javascript:reprintTicket(" + tickets[i].OrderId + ")'>" + tickets[i].Total + "</a></td>";
        $("#repbody").append(rw);
    }
}

function openReprintModal() {
    $("#reprint-ticket").modal("show");
}

function openRecentTicketsModal(){
    $("#rTicketsPrint").modal("show");
}

function reprintRecentTickets() {
    let frominput = $("#rrfrom");
    let toinput = $("#rrto");
    let from = frominput.val() !== "" ? Number(moment(frominput.val()).format("X")) : Number(moment().format("X"));
    let to = toinput.val() !== "" ? Number(moment(toinput.val()).format("X")) : Number(moment().add(1,"hours").format("X"));
    console.log(frominput.val(),toinput.val());
    console.log(from,to);
    let stringUrl = "recentTickets.php?from="+from+"&to="+to;
    let prntWindow = window.open(stringUrl,"_blank");
    prntWindow.addEventListener("DOMContentLoaded", function () {
        printw.print();
        printw.close();
        $("#rTicketsPrint").modal("hide");
    });
}