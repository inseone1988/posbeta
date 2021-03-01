<?php
require 'Auth.php';

if (isset($_GET["logout"])){
    initSession();
    logout();
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

    <title>Bienvenido Casa de la copia</title>
</head>
<body>
<div class="container-fluid" style="margin-top: 100px;">
    <div class="row mt-3">
        <div class="col-md-4 offset-md-4">
            <div class="card">
                <form action="Auth.php" method="post">
                <div class="card-counter-body">
                    <div class="row">
                        <input type="hidden" name="function" value="login" />
                        <div class="col-md-12 text-center">
                            <img class="img-fluid mt-2" src="img/logo.jpg" style="width: 250px;"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <label for="username">Username : </label>
                            <input name="username" id="username" type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6 offset-md-3">
                            <label for="password">Password : </label>
                            <input name="password" id="password" type="password" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="card-footer mt-2 text-center">
                    <button class="btn btn-primary">Ingresar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src=""></script>
</body>
</html>