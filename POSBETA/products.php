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
    <link rel="stylesheet" href="../node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
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
<div class="container-fluid ">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <img class="img-fluid" src="img/logo.jpg"/>
                </div>
            </div>
            <div class="row">
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Productos</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table id="products" class="table table-sm table-bordered table-striped table table-hover">
                                        <thead class="thead-light">
                                        <tr>
                                            <td>Id</td>
                                            <td>Created</td>
                                            <td>Descripcion</td>
                                            <td>Precio</td>
                                            <td>Codigo</td>
                                            <td>Categoria</td>
                                        </tr>
                                        </thead>
                                        <tbody id="product-details"></tbody>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Editar producto</h5>
                                                    <button onclick="newProduct()" class="btn btn-sm btn-primary">
                                                        Nuevo Producto
                                                        <span class="fa fa-plus"></span>
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="Name">Nombre del articulo : </label>
                                                            <input type="hidden" id="ItemId">
                                                            <input type="text" class="product-data form-control form-control-sm" id="Name" name="Name" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label for="code">Codigo</label>
                                                            <input type="text" class="product-data form-control form-control-sm" id="code" name="code" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="Price">Precio</label>
                                                            <input type="number" class="product-data form-control form-control-sm" id="Price" name="Price"/>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="Status">
                                                                <label for="Status"></label>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="category">Categoria</label>
                                                            <select class="product-data form-control" id="category">
                                                                <option>Papeleria</option>
                                                                <option>Copias</option>
                                                                <option>Cyberplanet</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex align-content-end">
                                                    <button class="btn btn-sm btn-primary" onclick="updateItem()">
                                                        Guardar
                                                        <span class="fa fa-save"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Mayoreo</h5>
                                                    <button onclick="newMayPrice()" class="btn btn-primary btn-sm">
                                                        <span class="fa fa-plus"></span>
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <table id="may-data" class="table table-sm table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <td>Cantidad</td>
                                                            <td>Precio</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="mayoreo"></tbody>
                                                    </table>
                                                </div>
                                                <div class="card-footer"></div>
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
<<div id="editMayoreo" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar precio de mayoreo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 offset-3">
                <div class="wrapper">
                    <label for="itemlimit">A partir de cuantas unidades : </label>
                    <input type="number" class="form-control form-control-sm" name="itemlimit" id="itemlimit" />
                    <label for="price" class="mt-2">Precio : </label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <span class="fa fa-dollar-sign"></span>
                            </span>
                        </div>
                        <input type="number" name="price" class="form-control" id="price" />
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="delmay" type="button" class="btn btn-danger" data-dismiss="modal"">Eliminar</button>
        <button type="button" class="btn btn-primary" onclick="saveMayPrice()">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../node_modules/devbridge-autocomplete/dist/jquery.autocomplete.js"></script>
<script src="js/products.js"></script>
<script src="../node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../node_modules/numeral/numeral.js"></script>
<script src="../node_modules/moment/moment.js"></script>
<script>
    $(document).ready(function () {
        getAllProducts();
    });
</script>
</body>
</html>