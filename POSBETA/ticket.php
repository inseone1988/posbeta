<?php /** * Created by PhpStorm. * User: Javis * Date: 14/01/2019 * Time: 09:53 PM */
require_once 'requesthandler.php';
require_once 'Auth.php';
if (isset($_GET["orderid"])) {
    initSession();
    $order = loadTicketDetails($_GET["orderid"]);
    $gTotal = 0;
} else {
    http_response_code(500);
} ?>
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
<p class="folio">Folio No : <?php echo $order["order"][0]["OrderId"] ?></p>
<table>
    <thead>
    <tr>
        <th style="width: 60%;">Descripcion</th>
        <th style="width: 10%;">Cant</th>
        <th style="width: 10%;">Precio</th>
        <th style="width: 10%;">Total</th>
    </tr>
    <tbody>    <?php for ($i = 0; $i < count($order["orderdetails"]); $i++) {
        $itemTotalMoney = (floatval($order["orderdetails"][$i]["Quantity"]) * floatval($order["orderdetails"][$i]["price"]));
        $itemTotal = "$" . number_format((floatval($order["orderdetails"][$i]["Quantity"]) * floatval($order["orderdetails"][$i]["price"])), 2);
        $gTotal += $itemTotalMoney;
        echo sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $order["orderdetails"][$i]["Name"], $order["orderdetails"][$i]["Quantity"], "$" . $order["orderdetails"][$i]["price"], $itemTotal);
    } ?>    </tbody>
    <tfoot>
    <tr>
        <th></th>
        <th>IVA</th>
        <th colspan="2"><?php echo "$" . $order["payment"][0]["IVA"] ?></th>
    </tr>
    <tr>
        <th></th>
        <th>Total</th>
        <th colspan="2"> <?php echo "$" . $gTotal ?></th>
    </tr>
    <?php

    if (count($order["payment"]) > 1) {
        for ($i = 0; $i < count($order["payment"]); $i++) {
            if ($order["payment"][$i]["payment"] <= $order["payment"][$i]["Total"]){
                echo "<tr><th></th><th>Anticipo[" . ($i + 1) . "]</th><th colspan='2'> $" . $order["payment"][$i]["payment"] . "</th></tr>";
            }else{
                break;
            };

        }
        echo "<tr><th></th><th>Credito</th><th>$" . $order["payment"][(count($order["payment"]) - 1)]["Credit"] . "</th>";
    }

    ?>
    <tr>
        <th></th>
        <th>Pago</th>
        <th colspan="2"><?php echo "$" . $order["payment"][(count($order["payment"]) - 1)]["payment"] ?></th>
    </tr>
    <tr>
        <th></th>
        <th>Cambio</th>
        <th colspan="2"><?php echo "$" . $order["payment"][0]["change"] ?></th>
    </tr>
    </tfoot>
    </thead></table>
<p>Gracias por su compra</p>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/moment/min/moment-with-locales.js"></script>
<script>
    moment.locale('es');
    $("#datetime").text(displayCurrentHour());

    function displayCurrentHour() {
        return moment().format("LLLL");
    }
</script>
</body>
</html>