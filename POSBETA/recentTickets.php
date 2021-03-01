<?php /** * Created by PhpStorm. * User: Javis * Date: 14/01/2019 * Time: 09:53 PM */
require_once 'requesthandler.php';
require_once 'Auth.php';
if (isset($_GET["from"]) && isset($_GET["to"])) {
    $tickets = getRecentTickets();
    //var_dump($tickets);
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
<p id="datetime"></p>
<table>
    <thead>
    <tr>
        <th style="width: 10%;">Ticket</th>
        <th style="width: 60%;">Fecha</th>
        <th style="width: 30%;">Total</th>
    </tr>
    <tbody>
    <?php
    $total = 0;
    for ($i = 0; $i < count($tickets); $i++) {
        $ct = $tickets[$i];
        $total += floatval($ct["payment"]);
        echo sprintf("<tr><td>%s</td><td>%s</td><td>$ %s</td>", $ct["OrderId"], $ct["Timestamp"], $ct["payment"]);
    }
    echo sprintf("<tr><td class='text-right total' colspan='2'>Total</td><td class='total'>$ %s</td>",$total);
    ?>
    </tbody>
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