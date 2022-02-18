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
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Edicion de proveedores</h2>
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Razon social</th>
                                        <th>RFC</th>
                                        <th>Direccion</th>
                                        <th>Activo</th>
                                    </tr>
                                </thead>
                                <tbody id="pv">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    Edicion de proveedor
                                     <button class="btn btn-sm btn-light" title="Nuevo" id="crprv">
                                         <i class="fas fa-plus"></i>
                                     </button>
                                </div>
                                <div class="card-content m-2">
                                    <form id="padata">
                                        <input type="hidden" value="false" id="in"/>
                                        <div class="form-group">
                                            <label for="provider_name">Nombre o representante</label>
                                            <input type="text" class="form-control" name="provider_name" id="provider_name">
                                        </div>
                                        <div class="form-group">
                                            <label for="provider_social_name">Razon social</label>
                                            <input type="text" class="form-control" name="provider_social_name" id="provider_social_name">
                                        </div>
                                        <div class="form-group">
                                            <label for="provider_tax_id">RFC</label>
                                            <input type="text" class="form-control" name="provider_tax_id" id="provider_tax_id">
                                        </div>
                                        <div class="form-group">
                                            <label for="provider_address">Direccion</label>
                                            <textarea class="form-control" name="provider_address" id="provider_address"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="active">Activo</label>
                                            <input type="text" class="form-control" name="active" id="active">
                                        </div>
                                        <div id="sbutton">
                                            <button type="submit" class="btn btn-sm btn-info" id="spd">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    Movimientos recientes
                                </div>
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Folio</th>
                                                        <th>Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="rm">

                                                </tbody>
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
    </div>
</div>
<!--MODALS-->

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.js"></script>
<script src="../node_modules/numeral/numeral.js"></script>
<script src="../node_modules/moment/min/moment-with-locales.min.js"></script>
<script src="js/chrtdata.js"></script>
<script src="js/config.js"></script>
<script src="js/utils.js"></script>
<script src="js/reports.js"></script>
<script src="../node_modules/sweetalert2/dist/sweetalert2.all.js"></script>
<script>
    var pdata = $("#padata");
    window.addEventListener("DOMContentLoaded",function(event){
        $("form").submit(function (e){
            e.preventDefault();
        });
        loadProviderData();

        $("#crprv").click(function(){
            $("#in").val("true");
            pdata.trigger("reset");
            let btn = $(document.createElement("button")).addClass("btn btn-sm btn-info").text("Guardar");
            btn.click(function(){
                saveProvider();
            });
            let sbtncontainer = $("#sbutton");
            sbtncontainer.empty();
            sbtncontainer.append(btn);
        });
    })
</script>
</body>
</html>