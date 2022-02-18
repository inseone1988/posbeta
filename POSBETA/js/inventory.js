var cst = $("#ccap");
var dr = $("#dr");
var nota = {};

function displayError(message, callback) {
    Swal.fire({
        icon: "error",
        title: "Error",
        text: message
    });
    if (callback) callback();
}

function mapFormValues(jQuerySerializeArrayElements) {
    let map = {};
    $.each(jQuerySerializeArrayElements, function (index, value) {
        map[value["name"]] = value["value"];
    })
    return map;
}

function populateProviderDropdown(providers) {
    let e = $("#ps");
    e.change(function () {
        nota.provid = $(this).val();
    });
    for (const provider of providers) {
        let option = $(document.createElement("option"));
        option.text(provider.provider_name);
        option.attr("value", provider.id);
        e.append(option);
    }
}

function getProviders() {
    $.ajax({
        url: "/api/public/v0/providers",
        data: "JSON",
        success: data => {
            console.log(data);
            if (data.success) {
                populateProviderDropdown(data.payload);
            }
        }
    })
}

function calculateValues(cp) {
    let c = $("#cost");
    let pvin = $("#pv");
    let gpin = $("#gp");
    let gnomin = $("#gn");
    let cval = Number(c.val());
    let pvinval = Number(pvin.val());
    let gpinval = Number(gpin.val());
    let gn = (cval * (gpinval / 100))
    let pv = cval + gn;
    if (cp) {
        let perc = ((pvinval * 100) / cval);
        gpin.val(perc);
    }
    pvin.val(pv);
    gnomin.val(gn);

}

function updateTotalRow() {
    let total = 0;
    let fr = $("#fr");
    fr.remove();
    fr = $(document.createElement("tr")).attr("id","fr");
    dr.append(fr);
    for(let product of nota.products){
        total += Number(product.surtimiento.cost) * Number(product.surtimiento.quantity);
    }
    let th = $(document.createElement("td"));
    th.attr("colspan",7);
    th.append($(document.createElement("div")).addClass("w-100 d-flex justify-content-end").append("Total : " + numeral(total).format("$0.00")));
    fr.append(th);
}

function addProductToTable(payload) {
    console.log(payload);
    let headers = ["codigo", "descripcion", "existencia", "uc", "surtimiento", "costoactual", "cambio"];
    let tr = $(document.createElement("tr"));
    for (const header of headers) {
        let td = $(document.createElement("td"));
        switch (header) {
            case "codigo":
                td.text(payload.code ? payload.code : payload.sku);
                break;
            case "descripcion":
                td.text(payload.Name);
                break;
            case "existencia" :
                td.text(payload.inventory.ammount)
                break;
            case "uc" :
                td.text(payload.lim ? payload.lim.price : 0);
                break;
            case "surtimiento" :
                td.text(payload.surtimiento.quantity);
                break;
            case "costoactual":
                td.text(payload.surtimiento.cost);
                break;
            case "cambio":

                break;
        }
        tr.append(td);
    }
    dr.append(tr);
    updateTotalRow();
    cst.val("");
    cst.focus();
}

function showCodeResult(payload) {
    let html = `<div class="row w-100">
                   <div class="col-md-12">
                        <form id="surt">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="quantity">Cantidad</label>
                                    <input type="text" class="form-control" name="quantity" id="quantity">   
                                </div>
                                <div class="col-sm-6">
                                    <label for="cost">Costo</label>
                                    <input onchange="calculateValues()" type="text" class="form-control" name="cost" id="cost">   
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-title">
                                            Precio base
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="pv">Precio venta</label>
                                                    <input onchange="calculateValues(true)" type="number" class="form-control" name="pv" id="pv">
                                                </div>
                                                <div class="col-4">
                                                    <label for="gp">% Ganancia</label>
                                                    <input onchange="calculateValues()" type="number" class="form-control" name="gp" value="${payload.gp ? payload.gp : 40}" id="gp">
                                                </div>
                                                <div class="col-4">
                                                    <label for="gn">Ganacia por pieza</label>
                                                    <input onchange="calculateValues()" type="number" class="form-control" name="gn" id="gn">
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>  
                        </form>
                   </div>`;
    Swal.fire({
        title: `<h2>${payload.Name}`,
        html: html,
        showDenyButton: true,
        confirmButtonText: "Guardar",
        denyButtonText: "Cancelar",
    }).then(function (result) {
        console.log(result);
        if (result.isConfirmed) {
            let surt = mapFormValues($("#surt").serializeArray());
            if (surt.quantity === "" || surt.cost === "" || surt.pv === "") {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Debes agregar cantidad, costo y precio de venta. Intenta nuevamente"
                });
                return;
            }
            payload.surtimiento = surt;
            if (!nota.products) nota.products = [];
            nota.products.push(payload);
            addProductToTable(payload);
        }
    });
}

function validateInputs() {
    if (!nota.provid){
        Swal.fire({
            icon : "info",
            title : "Sin proveedor",
            text : "Elige un proveedor o crea uno generico"
        })
        return false;
    }
    if (!nota.products || !nota.products.length) {
        Swal.fire({
            icon: "info",
            title: "Nada que agrgar",
            text: "Debes agregar al menos un articulo a la nota"
        });
        return false;
    }
    if (!nota.btype){
        Swal.fire({
            icon : "info",
            title : "Falta tipo de nota",
            text : "Debes seleccionar si la nota es pago de contado o credito"
        })
        return false;
    }
    return true;
}

function setElementListeners() {
    cst.change(function (e) {
        if (cst.val() !== "") {
            $.ajax({
                url: "/api/public/v0/products/" + cst.val(),
                dataType: "JSON",
                success: data => {
                    if (data.success) {
                        if (!dr.editing) {
                            dr.editing = true;
                            dr.empty();
                        }
                        showCodeResult(data.payload);
                    } else {
                        displayError(data.message, function () {
                            cst.val("");
                            cst.focus();
                        });
                    }
                }
            })
        }
    });
    $("#pts").click(function(){
        nota.btype = $(this).val();
    })
    $("#sn").click(function () {
        if (validateInputs()) {
            console.log(nota);
            $.ajax({
                url : "/api/public/v0/bills",
                type : "POST",
                dataType : "JSON",
                data : nota,
                success : (r)=>{
                    if (r.success){
                        Swal.fire({
                            icon : "success",
                            title : "Se ha guardado la nota con el numero de movimiento :",
                            html : "<h3 class='text-info'>" + r.payload.billId
                        }).then(function(result){
                            window.location.reload();
                        });
                    }
                }
            })
        }
    })
    $("#billid").change(function(event){
        nota.billid = $(this).val();
    })
}

$(document).ready(function () {
    getProviders();
    setElementListeners();
})