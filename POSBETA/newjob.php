<?php
/**
 * Created by PhpStorm.
 * User: Javis
 * Date: 14/01/2019
 * Time: 09:53 PM
 */

require_once 'Auth.php';
if (isset($_GET["orderid"])){
    initSession();
    $orderid = $_GET["orderid"];
    $anticipo = $_GET["ant"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Casa de la copia</title>
    <link href="css/print.css" rel="stylesheet"/>
</head>
<body>
<div class="text-center">
    <img class="img-logo" src="img/logo.jpg">
</div>

<h3>Casa de la copia</h3>
<div class="text-center"><?php echo $_SESSION["branch_data"]["branch_address"];?></div>
<p><?php echo $_SESSION["branch_data"]["branch_phone"];?></p>
<p><?php echo "RFC : " . $_SESSION["branch_data"]["branch_RFC"]; ?></p>
<p class="job-number"><?php echo $orderid?></p>
<p>Anticipo : </p>
<p class="job-anticipo"><?php echo $anticipo?></p>

<p>Gracias por su compra</p>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script>
</script>
</body>
</html>
