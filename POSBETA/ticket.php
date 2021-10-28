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
    <div>Hello</div>

</body>

<script>
    var order = <?php echo json_encode($order)?> ;
    window.open("vialogikprint:" + btoa(JSON.stringify(order)),"_blank");
</script>

</html>