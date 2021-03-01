<?php
/**
 * Created by PhpStorm.
 * User: Javis
 * Date: 15/01/2019
 * Time: 10:01 PM
 */

require_once 'Utils.php';
initSession();
getActiveCaja();
if (!userLoggedIn()){
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
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.css"/>
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.css">

    <title>Casa de la copia - Caja</title>
</head>
<body>
<div class="container-fluid">
    <div class="row" style="margin-top: 100px;">
        <div class="col-md-4 offset-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Apertura de caja</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <img class="img-fluid" src="img/logo.jpg">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <?php
                            if ($_SESSION["caja"]["init_type"] == "resume") {
                                echo "
                                        <div class='alert alert-danger mt-2'>
                                        Se ha encontrado una caja actualmente activa, se continuara con la misma.
                                        </div>
                                        <button class='btn btn-primary btn-lg' onclick='continueCaja()'>OK</button>
                                    ";
                            } else {
                                echo "
                                        <label for='dotacion'>Dotacion</label>
                                        <div class='input-group input-group-lg'>
                                            <div class='input-group-prepend'>
                                                <span class='input-group-text'>
                                                    <span class='fa fa-dollar-sign'></span>
                                                </span>
                                            </div>
                                            <input id='dt-ammount' type='number' step='0.50' class='form-control' />
                                        </div>
                                        <button class='btn btn-primary btn-lg mt-3' onclick='saveDotacion()'>Guardar <span class='fa fa-save'></span></button>
                                    ";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
<script>
    function continueCaja() {
        window.open("index.php", "_self");
    }

    function saveDotacion(){
        var ammount = $("#dt-ammount").val();
        $.ajax({
            url:"requesthandler.php",
            type : "POST",
            dataType : "JSON",
            data : {
                "function" : "saveDotacion",
                "ammount" : ammount
            },
            success : function(r){
                if (r.success){
                    window.open("index.php","_self");
                }
            }
        });
    }
</script>
</body>
</html>
