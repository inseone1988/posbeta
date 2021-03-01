var products;
var pointer;
var mayPointer;
function getAllProducts(){
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType : "JSON",
        data : {
            "function": "getAllProducts"
        },
        success:function(r){
            if (r.success){
                products = r.payload;
                mapProducts(r.payload);
            }
        }
    });
}

function mapProducts(products){
    for (let i = 0; i < products.length; i++) {
        var current = products[i];
        row = "<tr data-position='"+i+"' onclick='editProduct("+i+")'><td>"+current.ItemId+"</td><td></td><td>"+current.Name+"</td><td>"+current.Price+"</td><td>"+current.code+"</td><td>"+current.category+"</td>";
        $("#product-details").append(row);

    }
    $("#products").DataTable({
        languaje : dtLanguaje
    });
}

function editProduct(mIndex){
    pointer = mIndex;
    var product = products[mIndex];
    $.each(product,function(index,value){
        $("#"+index).val(value);
    });
    mapMayoreo(product["mayoreo"]);
}

function mapValues(){
    var product = products[pointer];
    $.each(product,function(index,value){
        var product = products[pointer];
        product[index] = $("#"+index).val();
    });
}

function updateItem(){
    if (pointer != undefined){
        mapValues();
        var data = products[pointer];
        $.ajax({
            url: "requesthandler.php",
            type : "POST",
            dataType : "JSON",
            data : {
                "function":"updateProduct",
                "data" : data
            },
            success : function(r){
                if (r.success){
                    //TODO handle item on saved
                    window.location.reload();
                }
            }
        })
    }
}

function newProduct(){
    $.ajax({
        url: "requesthandler.php",
        type : "POST",
        dataType : "JSON",
        data : {
            "function":"newProduct"
        },
        success : function(r){
            if (r.success){
                var id = r.id;
                addNewProductToDatabase(id);
            }
        }
    })
}

function addNewProductToDatabase(id){
    products.push({
        ItemId : id,
        Name : "",
        Price : 1.00,
        code : "",
        status : "true",
        mayoreo : [],
        category : "Papeleria"
    });
    pointer = products.length -1;
    editProduct(pointer);
}

function mapMayoreo(mayoreo){
    $("#mayoreo").empty();
    if (mayoreo.length > 0){
        $.each(mayoreo,function(index,value){
            var row = "<tr onclick='showMayoreoModal("+index+")'><td>"+value.itemlimit+"</td><td>"+value.price+"</td></tr>";
            $("#mayoreo").append(row);
        });
    }
}

function showMayoreoModal(mIndex){
    if (pointer != undefined){
        mayPointer = mIndex;
        var product = products[pointer];
        var mayOb = product["mayoreo"][mIndex];
        $("#itemlimit").val(mayOb["itemlimit"]);
        $("#price").val(mayOb["price"]);
        $("#editMayoreo").modal("show");
        $("#delmay").click(function () {
           confirmDeleteMayoreo(Number(mayOb.idmayoreo));
        });
    }

}

function newMayPrice(){
    if (pointer != undefined){
        var mayCont = products[pointer]["mayoreo"];
        var mayoreo = {
            productid : products[pointer]["ItemId"],
            alias : "Desde",
            itemlimit : 0,
            price : products[pointer]["Price"]
        };
        mayCont.push(mayoreo);
        mapMayoreo(mayCont);
    }

}

function saveMayPrice(){
    var may = products[pointer]["mayoreo"][mayPointer];
    may.itemlimit = $("#itemlimit").val();
    may.price = $("#price").val();
    var data = products[pointer]["mayoreo"];
    $.ajax({
        url: "requesthandler.php",
        type : "POST",
        dataType : "JSON",
        data : {
            "function": "saveMay",
            "data": data
        },
        success : function(r){
            window.location.reload();
        }
    })
}

var dtLanguaje = {
    "sProcessing":     "Procesando...",
    "sLengthMenu":     "Mostrar _MENU_ registros",
    "sZeroRecords":    "No se encontraron resultados",
    "sEmptyTable":     "Ningún dato disponible en esta tabla",
    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix":    "",
    "sSearch":         "Buscar:",
    "sUrl":            "",
    "sInfoThousands":  ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst":    "Primero",
        "sLast":     "Último",
        "sNext":     "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    }
};

function confirmDeleteMayoreo(mid){
    var con = confirm("Desea eliminar precio de mayoreo");
    if (con){
        deleteMayoreo(mid);
    }
}

function deleteMayoreo(mayoreoid){
    $.ajax({
                url: "requesthandler.php",
                type: "POST",
                dataType : "JSON",
                data : {
                    "function": "deleteMayoreo",
                    "data" : mayoreoid
                },
                success : function(r){
                    if (r.success){
                        location.reload();
                    }
                }
            });
}