<?php
/**
 * Created by PhpStorm.
 * User: Javier Ramirez
 * Date: 11/01/2019
 * Time: 09:08 AM
 */
http_response_code(500);
require 'Auth.php';
if (!userLoggedIn()){
    redirectTologinPage();
}
//var_dump($_SESSION);
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
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
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
        </ul>
    </div>
</nav>
<div class="container-fluid d-print-none">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <img class="img-fluid" src="img/logo.jpg" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group">
                        <li class="list-group-item active"><?php echo $_SESSION["auth_username"];?></li>
                        <li class="list-group-item">Inicio</li>
                        <li class="list-group-item">
                            <a href="inventories.php">Almacen</a>
                        </li>
                        <?php if (isAdmin(intval($_SESSION["auth_user_id"]))){
                            echo "
                                <li class='list-group-item'>
                                    <a href='products.php'>Productos</a>
                                </li>
                                <li class='list-group-item'>
                                    <a href='config.php'>Configuracion</a>
                                </li>
                            ";
                        } ?>
                        <li class="list-group-item">
                            <a href="javascript:openRecentTicketsModal()">Suma tickets</a>
                        </li>
                        <li class="list-group-item">
                            <a href="javascript:openReprintModal()">Reimprimir ticket</a>
                        </li>
                        <li class="list-group-item">
                            <a href="login.php?logout=true">Salir
                            <span class="fa fa-sign-out-alt"></span></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Tickets Pendientes
                            </div>
                            <div class="card-body">
                                <div class="table-wrapper">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <td>Fecha</td>
                                            <td>Folio</td>
                                            <td>Monto</td>
                                        </tr>
                                        </thead>
                                        <tbody id="pendingOrders">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a href="#existences" data-toggle="tab" role="tab" id="existences-tab" class="nav-link active">Existencias</a>
                </li>
                <li class="nav-item">
                    <a href="#qpc" data-toggle="tab" role="tab" id="qpc-tab" class="nav-link">Recepcion de mercancias</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="existences">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Almacen</h4>
                                </div>
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>Id</td>
                                                        <td>Codigo</td>
                                                        <td>SKU</td>
                                                        <td>Descripcion</td>
                                                        <td>Existencias</td>
                                                        <td>Accion</td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="qpc">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Ingreso rapido a almacen</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="btn-toolbar m-3">
                                        <div class="input-group">
                                            <select class="form-control form-control-sm" name="providerselect" id="ps">
                                                <option selected disabled>Selecciona proveedor</option>
                                            </select>
                                        </div>
                                        <div class="input-group ml-1">
                                            <input type="text" placeholder="No. de nota" class="form-control form-control-sm" id="billid" name="billid">
                                        </div>
                                        <div class="input-group">
                                            <select class="form-control form-control-sm" name="providerselect" id="pts">
                                                <option selected disabled>Tipo de nota</option>
                                                <option value="contado">Contado</option>
                                                <option value="credito">Credito</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="btn-toolbar m-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </div>
                                            </div>
                                            <input id="ccap" class="form-control-sm form-control" type="text" placeholder="Capture codigo"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Codigo</th>
                                                <th>Descripcion</th>
                                                <th>Existencia</th>
                                                <th>Ultimo costo</th>
                                                <th>Surtimiento</th>
                                                <th>Costo actual</th>
                                                <th>% Cambio</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dr">
                                            <tr>
                                                <td colspan="7" class="no-data-text">Capture un codigo de producto</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7">
                                                    <div class="w-100 d-flex justify-content-end">
                                                        <button class="btn btn-sm btn-info" id="sn">Ingresar nota a almacen</button>
                                                    </div>

                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
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

<!-- Modal -->
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../node_modules/numeral/numeral.js"></script>
<script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="js/inventory.js"></script>
</body>
</html>