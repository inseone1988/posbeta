<?php /** * Created by PhpStorm. * User: Javier Ramirez * Date: 11/01/2019 * Time: 12:33 PM */
date_default_timezone_set("America/Mexico_City");
require_once $_SERVER["DOCUMENT_ROOT"] . '/connection.php';
require_once 'Auth.php';
function autocomplete()
{
    $db = db();
    $table = $_POST["table"];
    $data = $_POST["data"];
    $value = $_POST["value"];
    $field = $_POST["field"];
    $term = $_POST["query"];
    $query = sprintf("SELECT %s AS value,%s AS data FROM %s WHERE %s REGEXP '%s'", $data, $value, $table, $field, $term);
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["suggestions" => $result, "error" => $db->error()]);
}

function newOrder()
{
    initSession();
    $now = date("Y-m-d H:i:s");
    $db = db();
    $result = $db->insert("orders", ["Timestamp" => $now, "branch" => $_SESSION["user_branch"], "cajaid" => $_SESSION["caja"]["idcaja"]]);    // var_dump($db->error());
    echo json_encode(["success" => true, "orderid" => $db->id()]);
}

function getItem()
{
    $itemid = $_POST["itemid"];
    $db = db();
    $result = $db->select("products", "*", ["ItemId" => $itemid]);
    $result[0]["mayoreo"] = getItemMayoreo($itemid);
    echo json_encode(["success" => true, "payload" => $result]);
}

function getItemMayoreo($itemid)
{
    $db = db();
    return $db->select("mayoreo", ["itemlimit", "price"], ["productid" => $itemid]);
}

function saveItems()
{
    $db = db();
    $data = $_POST["orderdetails"];
    //separate updates and inserts
    $inserts = [];
    $updates = [];
    for ($i = 0;$i < count($data);$i++){
        if (isset($data[$i]["orderdetailsid"])){
            array_push($updates,$data[$i]);
        }else{
            array_push($inserts,$data[$i]);
        }
    }
    if (count($inserts) > 0){
       insertitems($inserts);
    }
    if (count($updates) > 0){
        updateItems($updates);
    }
}

function insertitems($data){
    $db = db();
    $resultitems = $db->insert("orderdetails",$data);
}

function updateItems($data){
    $db = db();
    for ($i = 0; $i < count($data);$i++){
        $resulupdates = $db->update("orderdetails",$data[$i],["orderdetailsid"=>$data[$i]["orderdetailsid"]]);
    }

}

function savePayment()
{
    $db = db();
    $resultpayment = $db->insert("payment", $_POST["payment"]);
}

function saveOrder()
{
    initSession();
    $db = db();
    $data = $_POST["orderdata"];
    $orderid = $_POST["orderdata"]["OrderId"] ;
    unset($_POST["orderdata"]["OrderId"]);
    $data["EmployeeId"] = $_SESSION["auth_username"];
    $resultorder = $db->update("orders",$data ,["OrderId"=>$orderid]);
    saveItems();
    savePayment();
    echo json_encode(["success" => true]);
}

function getOrder($orderid)
{
    $db = db();
    return $db->select("orders", "*", ["OrderId" => $orderid]);
}

function getOrderDetails($orderid)
{
    $db = db();
    $reslt = $db->select("orderdetails", ["[>]products" => ["ProductId" => "ItemId"]], ["ProductId", "Name", "Quantity", "orderdetails.price",], ["OrderId" => $orderid]);
    return $reslt;
}

function getOrderPayments($orderId)
{
    $db = db();
    return $db->select("payment", "*", ["OrderId" => $orderId]);
}

function loadTicketDetails($orderid)
{
    return ["order" => getOrder($orderid), "orderdetails" => getOrderDetails($orderid), "payment" => getOrderPayments($orderid)];
}

function getFullOrderDetails($orderid){
    $db = db();
    $result = $db->select("orderdetails",["[>]products" => ["ProductId" => "ItemId"]], ["orderdetailsid","orderdetails.Status","OrderId","ProductId", "Name", "Quantity", "ProductId(ItemId)", "orderdetails.price(Price)",], ["OrderId" => $orderid]);
    return $result;
}

function getSell($orderid){
    return ["order" => getOrder($orderid), "orderdetails" => getFullOrderDetails($orderid), "payment" => getOrderPayments($orderid)];
}

function saveDotacion()
{
    initSession();
    $db = db();
    $result = $db->update("caja", ["dotacion" => $_POST["ammount"]], ["idcaja" => $_SESSION["caja"]["idcaja"]]);
    $data = [
        "caja"=>$_SESSION["caja"]["idcaja"],
        "timestamp"=>date("Y-m-d H:i:s"),
        "description"=>"DOTACION",
        "ammount"=>$_POST["ammount"],
        "user"=>$_SESSION["auth_username"],
        "type"=>"entrada"
    ];
    if (saveRetiro($data)){
        if ($result->rowCount()) {
            echo json_encode(["success" => true]);
        }
    }
}

function getCajaReport()
{
    initSession();
    $quey = sprintf("SELECT orders.cajaid, products.category, Sum(orderdetails.Quantity) AS total, Round(Sum(orderdetails.Quantity * orderdetails.price), 2) AS payment, Sum(payment.IVA) AS IVA FROM orderdetails LEFT JOIN orders ON orders.OrderId = orderdetails.OrderId LEFT JOIN products ON products.ItemId = orderdetails.ProductId LEFT JOIN payment ON payment.OrderId = orderdetails.OrderId WHERE orders.cajaid = %s GROUP BY products.category", $_SESSION["caja"]["idcaja"]);
    $db = db();
    $result = $db->query($quey)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "payload" => $result, "totals" => getSellTotals(),"cashMovements"=>getRetirosTotals()]);
}

function getCajaRetiros()
{
    initSession();
    $db = db();
    $result = $db->select("retiros", "*", ["caja" => $_SESSION["caja"]["idcaja"]]);
    return $result[0];
}

function getSellTotals()
{
    initSession();
    $queery = sprintf("SELECT SUM(Quantity) AS totalcantidad,(SElECT SUM(payment.Total) FROM payment WHERE payment.OrderId IN(SELECT orders.OrderId FROM orders WHERE orders.cajaid = %s)) AS payment FROM orderdetails LEFT JOIN orders ON orders.OrderId = orderdetails.OrderId INNER JOIN payment ON payment.OrderId = orders.OrderId WHERE orders.cajaid = %s", $_SESSION["caja"]["idcaja"], $_SESSION["caja"]["idcaja"]);
    $db = db();
    $result = $db->query($queery)->fetchAll(PDO::FETCH_ASSOC);
    return $result[0];
}

function getRetirosTotals()
{
    initSession();
    $query = sprintf("SELECT type, SUM(ammount) AS cajamvmts FROM retiros WHERE caja = %s GROUP BY type", $_SESSION["caja"]["idcaja"]);
    $db  = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function saveCorte(){
    initSession();
    $data = $_POST["corte"];
    $data["timestamp"] = date("Y-m-d H:i:s");
    $data["caja"] = $_SESSION["caja"]["idcaja"];
    $data["type"] = "DAY";
    $data["user"] = $_SESSION["auth_username"];
    //var_dump($data);
    $db = db();
    $result = $db->insert("corte",$data);
    return $db->id();
}

function closeCaja(){
    initSession();
    $now = date("Y-m-d H:i:s");
    $db = db();
    $result = $db->update("caja",["Cierre"=>$now,"Status"=>"CLOSED"]);
    return $result->rowCount();
}

function saveRetiro($data){
    initSession();
    if (count($data) == 0){
        $data = $_POST["data"];
        $data["caja"] = $_SESSION["caja"]["idcaja"];
        $data["user"] = $_SESSION["auth_username"];
    }
    $db = db();
    $result = $db->insert("retiros",$data);
    return $db->id();
}

function saveCorteAndExit(){
    initSession();
    if (saveCorte() != 0){
        if (closeCaja() != 0){
            logout();
        }
    }
}

function finishOrder($orderid){
    $db = db();
    $result = $db->update("orders",["OrderStatus"=>"Finished"],["OrderID"=>$orderid]);
    return $result->rowCount();
}

function getPendingSell($orderid){
    return loadTicketDetails($orderid);
}

function getPendingSellFunction(){
    $data = getSell($_POST["orderid"]);
    if ($data != null){
        echo json_encode([
            "success"=>true,
            "payload"=>$data
        ]);
    }
}

function getPendingOrders(){
    $db = db();
    $result = $db->select("orders",["OrderId"],["OrderStatus"=>"Pendiente"]);
    echo json_encode([
        "success"=>true,
        "payload"=>$result
    ]);
}

if (isset($_POST["function"])) {
    switch ($_POST["function"]) {
        case "autocomplete":
            autocomplete();
            break;
        case "newOrder":
            newOrder();
            break;
        case "getItem":
            getItem();
            break;
        case "saveOrder":
            saveOrder();
            break;
        case "getCajaRepor