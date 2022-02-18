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
    <link rel="stylesheet" href="css/jqueryautocomplete.css">
    <link rel="stylesheet" href="/node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css">
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
            <div class="row mt-2">
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-primary text-white card-counter-header">
                            Carta
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="cartacounter">0</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-primary text-white card-counter-header">
                            Oficio
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="oficiocounter">0</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header bg-primary text-white card-counter-header">
                            Doble carta
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="doblecartacounter">0</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header card-counter-header">
                            Sharp 1
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="sharp1">0</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header card-counter-header">
                            Sharp 2
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="sharp2">0</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header card-counter-header">
                            Sharp 3
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="sharp3">0</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header card-counter-header">
                            Sharp 4
                        </div>
                        <div class="card-body card-counter-body">
                            <p id="sharp4">0</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 bg-light">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="lcd-display">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="lcd-money-sign">$</div>
                                    </div>
                                    <div class="col-md-9 display-center">
                                        <div class="lcd-money-ammount" id="lcd-money-ammount">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2 id="orderidfolio">Folio:</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form id="search-form">
                                        <input id="item-search" class="form-control form-control-lg"
                                               placeholder="Ingrese articulo" type="text"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <table class="table table-sm table-bordered table-hover table-striped">
                                <thead class="text-center">
                                <tr>
                                    <th style="width: 10%;">Id <button class="btn btn-sm" onclick="redrawSellTable()"><span class="fa fa-sync"></span></button></th>
                                    <th style="width: 45%;">Descripcion</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 15%;">Precio</th>
                                    <th style="width: 15%;">Total</th>
                                </tr>
                                </thead>
                                <tbody id="sell-details"></tbody>
                                <tfoot id="misc" class="hide">
                                <tr id='taxrow'>
                                    <td colspan='3'></td>
                                    <td>IVA</td>
                                    <td id="ivatotal"></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="buttons-wrapper">
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(5)">Carta normal
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(7)">Carta duplex
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(6)">Oficio normal
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(8)">Oficio duplex
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(10)">Doble carta
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(11)">Dbl-carta
                                    DPLX
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="searchByButton('eng')">
                                    Engargolado
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(9)">Cyberplanet
                                </button>
                            </div>
                            <div class="col-md-4 d-none">
                                <button class="btn btn-light btn-lg" type="button" disabled>Papeleria
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="newWork()" style="width: 100%">
                                    Trabajo
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="goSearch(331)" style="width: 100%">
                                    Imp. Cyberpl.
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-lg btn-info" type="button" onclick="sell.toggleTax()"
                                        style="width: 100%">iva
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <button class="btn btn-light btn-lg" type="button" onclick="makeWithdraw()" style="width: 100%">
                                    Retiro/Entrada
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-light btn-lg" type="button" onclick="displayCorteModal()" style="width: 100%">
                                    Corte
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button id="delete-item" data-position="" class="btn btn-light btn-lg" type="button" onclick="removeItem($(this))" style="width: 100%">
                                    Quitar
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-light btn-lg" type="button" onclick="" style="width: 100%">
                                    Reiniciar
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-danger btn-lg" type="button" onclick="" style="width: 100%">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button class="btn btn-primary btn-lg" type="button" onclick="cobrar($(this))"
                                        style="width: 100%">Cobrar .-[F2]
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODALS-->
<div id="cobrar-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="folio-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                    <label>Pago : </label>
                                    <input style="height: 2.2rem;font-size: 2rem;" id="payment" type="number" step="0.50" class="form-control" value="0" autofocus>
                                <div class="text-danger mt-2 pagoerrortext" id="pagoerrortext"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="partialpayment">
                                    <label class="form-check-label">
                                        Pago parcial / Anticipo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 payment-total-text">
                                        Total
                                    </div>
                                    <div id="cobrar-total" class="col-md-8 text-left payment-total">
                                        $0.00
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 payment-total-text">Pago</div>
                                    <div id="cobrar-pago" class="col-md-8 text-left payment-total">$0.00</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 payment-total-text">Cambio</div>
                                    <div id="cobrar-cambio" class="col-md-8 text-left payment-total">$0.00</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button id="candp" type="button" class="btn btn-primary" onclick="confirmSellAndPrint()">Guardar e imprimir .-[ENTER][ENTER]</button>
            </div>
        </div>
    </div>
</div>
<div id="retiro-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Retiro / Entrada de efectivo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-md-12">

              </div>
          </div>
        <div class="row">
            <div class="col-md-6 offset-3">
                <label style="font-size: 1.5rem;">Cantidad a <strong id="optype"></strong> : </label>
                <input id="withdraw-amm" value="0" type="number" class="form-control form-control-lg" />
                <div class="form-group">
                    <label for="withdraw-desc" style="font-size: 1rem">Motivo / Descripcion</label>
                    <textarea rows="3" class="form-control" id="withdraw-desc"></textarea>
                </div>
            </div>
        </div>
          <div class="row mt-3">
              <div class="col-md-12 text-center">
                  <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="customSwitch1"/>
                      <label class="custom-control-label" for="customSwitch1" >Retiro / Entrada</label>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveretiro()">Guardar e imprimir [Enter] <span class="fa fa-save"></span></button>
      </div>
    </div>
  </div>
</div>
<div id="corte-modal" class="modal d-print-flex" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Corte de caja :  <?php echo $_SESSION["caja"]["idcaja"]?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header font-weight-bold card-counter-header">
                                System
                            </div>
                            <div class="card-body">
                                <div id="amm-system" class="corte-money-ammount"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header font-weight-bold card-counter-header">
                                Diferencia
                            </div>
                            <div class="card-body">
                                <div id="amm-diff" class="corte-money-ammount">$0.00</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header font-weight-bold card-counter-header">
                                Corte
                            </div>
                            <div class="card-body">
                                <div id="amm-cash" class="corte-money-ammount">$0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th colspan="5" class="text-center">Resumen de caja</th>
                                        </tr>
                                        <tr>
                                            <th>Caja</th>
                                            <th style="width: 40%">Categoria</th>
                                            <th>Cant. Vendida</th>
                                            <th>Total Efectivo</th>
                                            <th>IVA</th>
                                        </tr>
                                    </thead>
                                    <tbody id="caja-details"></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-sm btn-primary" onclick="captureContadores();">Contadores</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6>Dinero</h6>
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input data-value="0.50" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            0.50
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="1" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            1
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="2" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            2
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="5" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            5
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="10" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            10
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="20" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            20
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="50" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            50
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="100" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            100
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="200" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            200
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="500" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            500
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input data-value="1000" type="number" class="form-control money">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="width: 70px;">
                                            <span class="fa fa-dollar-sign"></span>
                                            1000
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveCorteAndExit()">Realizar corte</button>
      </div>
    </div>
  </div>
</div>
<div id="contadoresCapture" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contadores</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Sharp 1
                        </span>
                    </div>
                    <input type="number" class="form-control" id="sharp1CounterCapture">
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Sharp 2
                        </span>
                    </div>
                    <input type="number" class="form-control" id="sharp2CounterCapture">
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Sharp 3
                        </span>
                    </div>
                    <input type="number" class="form-control" id="sharp3CounterCapture">
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Sharp 4
                        </span>
                    </div>
                    <input type="number" class="form-control" id="sharp4CounterCapture">
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="updateMachineCounters();">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!--START MODAL -->
<div id="reprint-ticket" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reimprimir ticket</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" style="height: 300px;overflow: auto;">
                        <table class="table table-sm table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Hora</th>
                                <th>Monto</th>
                            </tr>
                            </thead>
                            <tbody id="repbody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="rTicketsPrint" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tickets recientes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 offset-1">
                        <input id="rrfrom" type="text" class="form-control form-control-sm" placeholder="De">
                    </div>
                    <div class="col-md-1 text-center">A</div>
                    <div class="col-md-4">
                        <input id="rrto" type="text{
format:"Y-m-d H:i class="form-control form-control-sm" placeholder="De">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-sm btn-primary" onclick="reprintRecentTickets()">
                            <span class="fa fa-search"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../node_modules/devbridge-autocomplete/dist/jquery.autocomplete.js"></script>
<script src="../node_modules/numeral/numeral.js"></script>
<script src="../node_modules/moment/moment.js"></script>
<script src="../node_modules/countup.js/dist/countUp.js"></script>
<script src="/node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
<script src="js/shortcutHandler.js"></script>
<script src="js/sell.js"></script>
<script src="js/counters.js"></script>
<script>
    $(document).ready(function(){
       setItemSearchListener();
       setCorteListeners();
       switchOpTypeText();
       getPendingOrders();
       getCounters(function () {
        getCajaTickets();
       });
       $("#rrfrom").datetimepicker({
           format:"Y-m-d H:i"
       });
       $("#rrto").datetimepicker({
           format:"Y-m-d H:i"
       });
    });
    $("#item-search").autocomplete({
        serviceUrl: 'requesthandler.php',
        minChars: 3,
        type: "POST",
        onSelect: function (suggestion) {
            goSearch(suggestion.data);
        },
        params: {
            "function": "autocomplete",
            "table": "products",
            "data": "Name",
            "value": "ItemId",
            "field": "Name"
        }
    });

    window.onbeforeunload = function (event) {
        event.preventDefault();
        if (sell.orderid !== 0) {
            if (!printed) {
                return "Venta pendiente";
            }
        }
    }
</script>
</body>
</html>