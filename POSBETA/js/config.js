var users;
var counters;
var sExistences;

function getUsers() {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "getConfigData"
        },
        success: function (r) {
            if (r.success) {
                users = r.payload;
                counters = r.counters;
                sExistences = r.sExistences;
                mapUsers();
                mapCurrentCounters();
                sheetChart(sExistences);
            }
        }
    });
}

function mapUsers() {
    for (let i = 0; i < users.length; i++) {
        var row = "<tr><td>" + users[i].id + "</td><td>" + users[i].email + "</td><td>" + users[i].username + "</td><td><button onclick='deleteUser(" + users[i].id + "," + i + ")' class='btn btn-sm btn-info'><span class='fa fa-trash'></span></button></td>";
        $("#users-table").append(row);
    }
}

function addUser() {

}

function updateCounter(counter) {
    $.ajax({
        url: "requesthandler.php",
        type: "POST",
        dataType: "JSON",
        data: {
            "function": "updatecounter",
            "data": counter
        },
        success: function (r) {
            if (r.success) {
                alert("Actualizado");
                location.reload();
            }
        }
    });
}

$(".counter").on("change", function (event) {
    var val = $(this).val();
    var posiiton = $(this).data("position");
    var counter = counters[posiiton];
    counter.currentcount = val;
    updateCounter(counter);
});

function deleteUser(uid, position) {
    var conf = confirm("Eliminar usuario");
    if (conf) {
        $.ajax({
            url: "requesthandler.php",
            type: "POST",
            dataType: "JSON",
            data: {
                "function": "deleteUser",
                "id": uid
            },
            success: function (r) {
                window.location.reload();
            }
        })
    }
}

function newUser() {
    if (validateNoEmptyUserInputs()) {
        var data = getUserData();
        $.ajax({
            url: "requesthandler.php",
            type: "POST",
            dataType: "JSON",
            data: data,
            success: function (r) {
                if (r.success) {
                    window.location.reload();
                }
            }
        })
    }
}

function validateNoEmptyUserInputs() {
    if ($("#username").val !== "" && $("#mail").val !== "" && $("#password").val !== "") {
        return true;
    } else {
        alert("Los campos no pueden estar vacios");
        return false;
    }
}

function mapCurrentCounters() {
    for (let i = 0; i < counters.length; i++) {
        $("#Sharp" + (i + 1)).val(Number(counters[i].currentcount));
        $("#Sharp" + (i + 1)).data("position", i);
    }
}

function getUserData() {
    return {
        "function": "newUser",
        "username": $("#username").val(),
        "mail": $("#mail").val(),
        "password": $("#password").val(),
        "user_role": $("#userrole").val(),
        "user_branch": $("#user_branch").val()
    }
}

function adjustPaperModal() {
    $("#adjustPaperModal").modal("show");
}

$(".countersum").on("change", function () {
    var type = $(this).data("type");
    switch (type) {
        case "carta":
            var boxes = inputNumber("#cartabox");
            var packs = inputNumber("#cartapack");
            var units = inputNumber("#cartasheet");
            var total = ((boxes * 5000) + (packs * 500) + units);
            sExistences[0].ammount = total;
            $("#countercarta").text(total);
            break;
        case "oficio":
            var boxes = inputNumber("#oficiobox");
            var packs = inputNumber("#oficiopack");
            var units = inputNumber("#oficiosheet");
            var total = ((boxes * 5000) + (packs * 500) + units);
            sExistences[1].ammount = total;
            $("#counteroficio").text(total);
            break;
        case "dblcarta":
            var boxes = inputNumber("#dblcartabox");
            var packs = inputNumber("#dblcartapack");
            var units = inputNumber("#dblcartasheet");
            var total = ((boxes * 5000) + (packs * 500) + units);
            sExistences[2].ammount = total;
            $("#counterdblcarta").text(total);
            break;
    }
});

function saveSheetExistences() {
    var conf = confirm("Actualizar existencias");
    if (conf) {
        $.ajax({
            url: "requesthandler.php",
            type: "POST",
            dataType: "JSON",
            data: {
                "function": "updateSheetExistences",
                "data": sExistences
            },
            success: function (r) {
                window.location.reload();
            }
        });
    }
}