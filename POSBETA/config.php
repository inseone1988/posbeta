<?php
/**
 * Created by PhpStorm.
 * User: Javier Ramirez
 * Date: 11/01/2019
 * Time: 09:08 AM
 */
require 'Auth.php';
if (!userLoggedIn()) {
    redirectTologinPage();
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.css">
    <link rel="stylesheet" href="css/jqueryautocomplete.css">
    <link rel="stylesheet" href="../node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css"/>
    <title>Bienvenidos Casa de la copia</title>
</head>
<body>
<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Casa de la copia</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor03"
            aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor03">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <?php echo "Caja No. " . $_SESSION["caja"]["idcaja"]; ?>
            </li>
            <li class="navbar-item ml-2">
                <?php echo "Hora de apertura : " . $_SESSION["caja"]["Apertura"] ?>
            </li>
            <li class="navbar-item ml-2 text-info font-weight-bold">
                <?php echo "Sucursal : " . $_SESSION["branch_data"]["branch_name"] . " " . $_SESSION["branch_data"]["branch_address"]; ?>
            </li>
        </ul>
    </div>
</nav>
<div class="container-fluid ">
    <div class="row">
        <div class="col-sm-4 col-md-2">
            <div class="card">
                <div class="card-body">
                    <img class="img-fluid" src="img/logo.jpg"/>
                </div>
            </div>
            <div class="row d-print-none">
                <div class="col-md-12">
                    <ul class="list-group">
                        <li class="list-group-item active"><?php echo $_SESSION["auth_username"]; ?></li>
                        <li class="list-group-item">
                            <a href="index.php">Inicio</a>
                        </li>
                        <?php if (isAdmin(intval($_SESSION["auth_user_id"]))) {
                            echo "
                                <li class='list-group-item'>
                                    <a href='products.php'>Productos</a>
                                </li>
                                <li class='list-group-item'>
                                    <a href='config.php'>Configuracion</a>
                                </li>
                                <li class='list-group-item'>
                                <a href='providers.php'>Proveedores</a>
</li>
                            ";
                        } ?>
                        <li class="list-group-item">
                            <a href="login.php?logout=true">Salir
                                <span class="fa fa-sign-out-alt"></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-10 mt-3">
            <div class="tab-wrapper">
                <ul class="nav nav-tabs  d-print-none" id="mTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="" data-toggle="tab" href="#home"
                                            role="tab">Home</a></li>
                    <li class="nav-item"><a class="nav-link" id="" data-toggle="tab" href="#users"
                                            role="tab">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" id="" data-toggle="tab" href="#counters" role="tab">Contadores</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" id="" data-toggle="tab" href="#reports"
                                            role="tab">Reportes</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Dashboard</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Existencias en papel
                                                    <small class="ml-3"><a href="javascript:adjustPaperModal()">Ajustar
                                                            <span
                                                                    class="fa fa-external-link-alt"></span></a></small>
                                                </h3>
                                                <div class="row mt-2">
                                                    <div class="col-md-4">
                                                        <div class="card">
                                                            <div class="card-header bg-primary text-white card-counter-header">
                                                                Carta
                                                            </div>
                                                            <div class="card-body card-counter-body">
                                                                <p id="csCounter">15</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card">
                                                            <div class="card-header bg-primary text-white card-counter-header">
                                                                Oficio
                                                            </div>
                                                            <div class="card-body card-counter-body">
                                                                <p id="osCounter">15</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card">
                                                            <div class="card-header bg-primary text-white card-counter-header">
                                                                Dbl Carta
                                                            </div>
                                                            <div class="card-body card-counter-body">
                                                                <p id="dcsCounter">15</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th colspan="2">Ultimo surtimiento</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>Proveedor :</td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Cajas :</td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Valor de remision /factura :</td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Saldo / Credito :</td>
                                                                <td></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <canvas id="sheetCounter"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="users" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mt-3">
                                <table class="table table-sm table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th colspan="4">
                                            Usuarios
                                            <button class="btn btn-sm btn-primary ml-3">
                                                <span class="fa fa-plus"></span>
                                            </button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>id</th>
                                        <th>Mail</th>
                                        <th>Nombre de usuario</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="users-table">

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Nuevo usuario</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control form-control-sm" id="mail"
                                                       name="mail" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="mt-2 form-control form-control-sm"
                                                       id="username" name="username" placeholder="Nombre de usuario">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="password" class="mt-2 form-control form-control-sm"
                                                       id="password" name="password" placeholder="Contraseña">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <select id="userrole" class="form-control form-control-sm">
                                                    <option value="2">Empleado</option>
                                                    <option value="1">Administrador</option>
                                                </select>
                                                <input type="hidden" id="user_branch"
                                                       value="<?php echo $_SESSION["user_branch"]; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button onclick="newUser()" class="btn btn-sm btn-primary">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="counters" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Contadores: maquinas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="input-group input-group-sm mt-2">
                                            <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Sharp 1
                                    </span>
                                            </div>
                                            <input name="" id="Sharp1" class="counter form-control" type="number">
                                        </div>
                                        <div class="input-group input-group-sm mt-2">
                                            <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Sharp 2
                                    </span>
                                            </div>
                                            <input name="" id="Sharp2" class="counter form-control" type="number">
                                        </div>
                                        <div class="input-group input-group-sm mt-2">
                                            <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Sharp 3
                                    </span>
                                            </div>
                                            <input name="" id="Sharp3" class="counter form-control" type="number">
                                        </div>
                                        <div class="input-group input-group-sm mt-2">
                                            <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Sharp 4
                                    </span>
                                            </div>
                                            <input name="" id="Sharp4" class="counter form-control" type="number">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="reports" role="tabpanel">
                        <div class="row">
                            <div class="col-md-3 d-print-none">
                                <div id="calendar"></div>
                            </div>
                            <div class="col-md-9 mt-3">
                                <div class="card d-print-block" id="day-report">
                                    <div class="card-header d-print-block">
                                        <h4>Reporte de caja dia : <small style="text-transform: capitalize" id="report-date"></small></h4>
                                        <div class="row">
                                            <div class="col-md-12 d-flex align-items-end justify-content-end d-print-none">
                                                <button class="btn btn-sm btn-primary" onclick="printReport()">Imprimir</button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white card-counter-header">
                                                        Total venta
                                                    </div>
                                                    <div class="card-body card-counter-body">
                                                        <p id="report-total">$ 0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white card-counter-header">
                                                        Diferencia
                                                    </div>
                                                    <div class="card-body card-counter-body">
                                                        <p id="report-diference">$ 0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-white card-counter-header">
                                                        Cuenta de efectivo
                                                    </div>
                                                    <div class="card-body card-counter-body">
                                                        <p id="report-cash">$ 0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODALS-->
<div id="adjustPaperModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar existencias de papel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th style="width: 20%">Tipo</th>
                                <th style="width: 20%">Cajas</th>
                                <th style="width: 20%">Paquetes</th>
                                <th style="width: 20%">Hojas</th>
                                <th style="width: 20%">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Carta</td>
                                <td><input id="cartabox" value="0" data-type="carta" type="number"
                                           class="countersum form-control form-control-sm"></td>
                                <td><input id="cartapack" value="0" data-type="carta" type="number"
                                           class="countersum form-control form-control-sm"></td>
                                <td><input id="cartasheet" value="0" data-type="carta" type="number"
                                           class="countersum form-control form-control-sm"/></td>
                                <td id="countercarta"></td>
                            </tr>
                            <tr>
                                <td>Oficio</td>
                                <td><input id="oficiobox" data-type="oficio" value="0" type="number"
                                           class="countersum form-control form-control-sm"></td>
                                <td><input id="oficiopack" data-type="oficio" value="0" type="number"
                                           class="countersum form-control form-control-sm"></td>
                                <td><input id="oficiosheet" data-type="oficio" value="0" type="number"
                                           class="countersum form-control form-control-sm"/></td>
                                <td id="counteroficio"></td>
                            </tr>
                            <tr>
                                <td>Doble carta</td>
                                <td><input id="dblcartabox" data-type="dblcarta" value="0" type="number"
                                           class="countersum form-control form-control-sm"></td>
                                <td><input id="dblcartapack" data-type="dblcarta" value="0" type="number"
                                           class="countersum form-control form-control-sm"></td>
                                <td><input id="dblcartasheet" data-type="dblcarta" value="0" type="number"
                                           class="countersum form-control form-control-sm"/></td>
                                <td id="counterdblcarta"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveSheetExistences()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.js"></script>
<script src="../node_modules/numeral/numeral.js"></script>
<script src="../node_modules/moment/min/moment-with-locales.min.js"></script>
<script src="../node_modules/chart.js/dist/Chart.min.js"></script>
<script src="js/chrtdata.js"></script>
<script src="js/config.js"></script>
<script src="js/utils.js"></script>
<script src="js/reports.js"></script>
<script>
    var calendar;
    moment.locale('es');
    $(document).ready(function () {
        getUsers();
        calendar = $("#calendar").datetimepicker({
            inline: true,
            format: "Y-m-d",
            timepicker: false,
            onSelectDate: function (ct, i) {
                var from = moment(ct).format("Y-MM-DD 00:00:00");
                var to = moment(ct).format("Y-MM-DD 23:59:59");
                getReport(from,to);
            }
        })
    })
</script>
</body>
</html>