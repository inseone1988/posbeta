<?php /** * Created by PhpStorm. * User: Javis * Date: 14/01/2019 * Time: 09:53 PM */
require_once 'requesthandler.php';
require_once 'Auth.php';
initSession();
$data = getLastCashMovement();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Casa de la copia</title>
    <link href="css/print.css" rel="stylesheet"/>
</head>
<body>
<div class="text-center"><img class="img-logo" src="img/logo.jpg"></div>
<h3>Casa de la copia</h3>
<div class="text-center" style="font-size: 0.8rem;"><?php echo $_SESSION["branch_data"]["branch_address"]; ?></div>
<p style="font-size: 0.7rem;"> TELEFONO : <?php echo $_SESSION["branch_data"]["branch_phone"]; ?></p>
<p style="font-size: 0.7rem;"><?php echo "RFC : " . $_SESSION["branch_data"]["branch_RFC"]; ?></p>
<p id="datetime"></p>
<p class="folio">Folio No : <?php echo $data[0]["idretiros"];?></p>
<p class="retiro"><?php echo $data[0]["type"]?></p>
<p class="retiro-amm">Cantidad : <?php echo $data[0]["ammount"]?></p>
<p class="retiro-desc"><?php echo $data[0]["description"] ?></p>
<p>Gracias por su compra</p>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/moment/min/moment-with-locales.js"></script>
<script>
    moment.locale('es');
    $("#datetime").text(displayCurrentHour());
    function displayCurrentHour(){
        return moment().format("LLLL");
    }
</script>
</body>
</html>