function inputNumber(selector){
    return Number($(selector).val());
}


//Not useful beacuse jQuery is loaded
// function request(options){
//     if (!options.url) throw "url option is required";
//     let xhr = new XMLHttpRequest();
//     let data = options.data ? options.data : "";
//     xhr.open(options.type ? options.type : "GET");
//     xhr.setRequestHeader('Content-type', 'application/json; charset=UTF-8');
//     xhr.send(JSON.stringify(data));
//     xhr.onload = function(){
//
//     }
// }

function mapFormValues(jQuerySerializeArrayElements) {
    let map = {};
    $.each(jQuerySerializeArrayElements,function(index,value){
        map[value["name"]] = value["value"];
    })
    return map;
}

function getNewProviderData() {
    let fData = $("#padata");
    return mapFormValues(fData.serializeArray());
}

function displayError(message) {
    Swal.fire({
        title : "Error",
        text : message,
        icon : "error"
    });
}

function saveProvider(provider) {
    let mData = (provider ? provider : getNewProviderData());
    $.ajax({
        url : "/api/public/v0/providers",
        type : "POST",
        data : mData,
        success : function(r){
            if (r.success){
                loadProviderData();
            }else{
                displayError(r.message);
            }
        }
    })
}

function displayProvider(provider) {
    let h = Object.keys(provider);
    let bs = $("#spd");
    let rm = $("#rm");
    rm.empty();
    if (!bs.length){
        let sbtn = $("#sbutton").empty();
        bs = $(document.createElement("button")).addClass("btn btn-sm btn-info").text("Guardar");
        sbtn.append(bs);
    }
    for (const string of h) {
        let c = $(`#${string}`).val(provider[string]);
        if (c.length){
            if (!c.hasListener){
                c.change(function(e){
                    e.stopPropagation();
                    provider[string] = $(this).val();
                    console.log(provider);
                });
            }
            c.hasListener = true;
        }
    }
    if (!bs.clickListener){
        bs.click(function(){
            let isNew = $("#in").val();
            if (isNew === "true") delete provider["id"];
            saveProvider(provider);
            bs.unbind();
        })
    }
    for (const nota of provider.notas) {
        let tr = $(document.createElement("tr"));
        let h = ["created_at","billId","value"];
        let total = 0;
        for (const detail of nota.details) {
            total += (Number(detail.price) * detail.quantity);
        }
        for (const string of h) {
            let td = $(document.createElement("td"));
            td.text(nota[string]);
            if (string === "created_at"){
                td.text(moment(nota[string]).format("DD MMM YYYY HH:mm"));
            }
            if (string === "value"){
                td.text(numeral(total).format("$ 0.00"));
            }
            tr.append(td);
        }
        rm.append(tr);
    }
    bs.clickListener = true;
}

function loadProviderData(){
    $.ajax({
        url : "/api/public/v0/providers",
        success : function(r){
            console.log(r);
            let pv = $("#pv");
            pv.empty();
            if (r.payload.length){
                for (const provider of r.payload) {
                    let tr = $(document.createElement("tr"));
                    let h = ["provider_name","provider_social_name","provider_tax_id","provider_address","active"];
                    for (const string of h) {
                        let td = $(document.createElement("td"));
                        td.click(function(){
                            displayProvider(provider);
                        });
                        td.text(provider[string]);
                        if (string ==="active"){
                            td.text(provider[string] ==1 ? "SI":"NO");
                        }
                        tr.append(td);
                    }
                    pv.append(tr);
                }
            }else{

            }
        }
    })
}