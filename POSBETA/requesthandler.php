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
    echo json_encode(["suggestions" => $result, "error" => $db->error]);
}

function newOrder()
{
    initSession();
    $now = date("Y-m-d H:i:s");
    $db = db();
    $result = $db->insert("orders", ["Timestamp" => $now, "branch" => $_SESSION["user_branch"], "cajaid" => $_SESSION["caja"]["idcaja"], "OrderStatus" => "Pendiente"]);    // var_dump($db->error());
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

function getitemByCode()
{
    $itemcode = $_POST["itemid"];
    $db = db();
    $result = $db->select("products", "*", ["code" => $itemcode]);
    $result[0]["mayoreo"] = getItemMayoreo($result[0]["ItemId"]);
    echo json_encode(["success" => true, "payload" => $result]);
}

function returnItem($itemid)
{
    $db = db();
    $result = $db->select("products", "*", ["ItemId" => $itemid]);
    $result[0]["mayoreo"] = getItemMayoreo($itemid);
    count($result[0]["mayoreo"]) < 1 ? $result[0]["hasmayoreo"] = false : $result[0]["hasmayoreo"] = true;
    return $result[0];
}

function getAllItems()
{
    $resultPayload = [];
    $db = db();
    $items = $db->select("products", ["ItemId"]);
    for ($i = 0; $i < count($items); $i++) {
        array_push($resultPayload, returnItem($items[$i]["ItemId"]));
    }
    echo json_encode([
        "success" => true,
        "payload" => $resultPayload
    ]);
}

function updateItem()
{
    $db = db();
    $data = $_POST["data"];
    unset($data["mayoreo"]);
    $result = $db->update("products", $data, ["ItemId" => $_POST["data"]["ItemId"]]);
    if ($result->rowCount() != 0) {
        echo json_encode([
            "success" => true
        ]);
    }
}

function getItemMayoreo($itemid)
{
    $db = db();
    return $db->select("mayoreo", ["idmayoreo", "productid", "itemlimit", "price"], ["productid" => $itemid]);
}

function saveItems()
{
    initSession();
    $db = db();
    $data = $_POST["orderdetails"];
    //separate updates and inserts
    $inserts = [];
    $updates = [];
    for ($i = 0; $i < count($data); $i++) {
        if (isset($data[$i]["orderdetailsid"])) {
            array_push($updates, $data[$i]);
        } else {
            $itemcurrent = $data[$i];
            $itemcurrent["cajaid"] = $_SESSION["caja"]["idcaja"];
            array_push($inserts, $itemcurrent);
        }
    }
    if (count($inserts) > 0) {
        insertitems($inserts);
    }
    if (count($updates) > 0) {
        updateItems($updates);
    }
}

function insertitems($data)
{
    $db = db();
    $resultitems = $db->insert("orderdetails", $data);
}

function updateItems($data)
{
    $db = db();
    for ($i = 0; $i < count($data); $i++) {
        $resulupdates = $db->update("orderdetails", $data[$i], ["orderdetailsid" => $data[$i]["orderdetailsid"]]);
    }

}

function savePayment()
{
    initSession();
    $db = db();
    $payment = $_POST["payment"];
    for ($i = 0; $i < count($payment); $i++) {
        $cpayment = $payment[$i];
        if (!isset($cpayment["idPayment"])) {
            $cpayment["cajaid"] = $_SESSION["caja"]["idcaja"];
            $resultpayment = $db->insert("payment", $cpayment);
        } else {
            $cpayment["cajacount"] = 0;
            $resultpayment = $db->update("payment", $cpayment, ["idPayment" => $cpayment["idPayment"]]);
        }
    }

}

function saveOrder()
{
    initSession();
    $db = db();
    $data = $_POST["orderdata"];
    $orderid = $_POST["orderdata"]["OrderId"];
    unset($_POST["orderdata"]["OrderId"]);
    $data["EmployeeId"] = $_SESSION["auth_username"];
    $resultorder = $db->update("orders", $data, ["OrderId" => $orderid]);
    saveItems();
    savePayment();
    updateCounters();
    echo json_encode(["success" => true]);
}

function updateCounters()
{
    $db = db();
    $data = $_POST["sheetCounters"];
    for ($i = 0; $i < count($data); $i++) {
        $db->update("sheetcounter", $data[$i], ["idsheetcounter" => $data[$i]["idsheetcounter"]]);
        //var_dump($db->error());
    };
}

function getOrder($orderid)
{
    $db = db();
    return $db->select("orders", "*", ["OrderId" => $orderid]);
}

function getOrderDetails($orderid)
{
    $db = db();
    $reslt = $db->select("orderdetails", ["[>]products" => ["ProductId" => "ItemId"]], ["ProductId", "Name", "Quantity", "orderdetails.price",], ["OrderId" => $orderid, "orderdetails.Status[!]" => 0]);
    return $reslt;
}

function getOrderPayments($orderId)
{
    $db = db();
    return $db->select("payment", [
        "idPayment[Int]",
        "Timestamp[String]",
        "OrderId[Int]",
        "PaymentDescription[String]",
        "PaymentMethod[Int]",
        "Ammount[Number]",
        "discount[Number]",
        "payment[Number]",
        "Total[Number]",
        "Credit[Number]",
        "change[Number]",
        "IVA[Number]",
        "cajaid",
        "cajacount"
    ], ["OrderId" => $orderId]);
}

function loadTicketDetails($orderid)
{
    return ["order" => getOrder($orderid), "orderdetails" => getOrderDetails($orderid), "payment" => getOrderPayments($orderid)];
}

function getFullOrderDetails($orderid)
{
    $db = db();
    $result = $db->select("orderdetails", ["[>]products" => ["ProductId" => "ItemId"]], ["orderdetailsid", "orderdetails.Status", "OrderId", "ProductId", "Name", "Quantity", "ProductId(ItemId)", "orderdetails.price(Price)",], ["OrderId" => $orderid]);
    return $result;
}

function getSell($orderid)
{
    return ["order" => getOrder($orderid), "orderdetails" => getFullOrderDetails($orderid), "payment" => getOrderPayments($orderid)];
}

function saveDotacion()
{
    initSession();
    $db = db();
    $result = $db->update("caja", ["dotacion" => $_POST["ammount"]], ["idcaja" => $_SESSION["caja"]["idcaja"]]);
    $data = [
        "caja" => $_SESSION["caja"]["idcaja"],
        "timestamp" => date("Y-m-d H:i:s"),
        "description" => "DOTACION",
        "ammount" => $_POST["ammount"],
        "user" => $_SESSION["auth_username"],
        "type" => "entrada"
    ];
    if (saveRetiro($data)) {
        if ($result->rowCount()) {
            echo json_encode(["success" => true]);
        }
    }
}

function getCajaReport()
{
    initSession();
    $quey = sprintf("Select orderdetails.cajaid, products.category, Sum(orderdetails.Quantity) As total, Sum(orderdetails.Quantity * orderdetails.price) As payment, Sum(If(orders.tax,(((orderdetails.Quantity * orderdetails.price) * 16) / 100), 0)) As IVA From orderdetails Left Join products On products.ItemId = orderdetails.ProductId Left Join payment On payment.OrderId = orderdetails.OrderId Left Join orders On orders.OrderId = orderdetails.OrderId Where orders.cajaid =%s And orderdetails.Status != 0 Group By products.category", $_SESSION["caja"]["idcaja"]);
    $db = db();
    $result = $db->query($quey)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "payload" => $result, "anticipos" => getAnticipos(), "totals" => getSellTotals(), "cashMovements" => getRetirosTotals()]);
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
    $queery = sprintf("Select Sum(orderdetails.Quantity) As totalcantidad, Sum(orderdetails.Quantity * orderdetails.price) As payment From orderdetails Left Join orders On orders.OrderId = orderdetails.OrderId Inner Join payment On payment.OrderId = orders.OrderId Where orders.cajaid = %s And orderdetails.Status != 0", $_SESSION["caja"]["idcaja"]);
    $db = db();
    $result = $db->query($queery)->fetchAll(PDO::FETCH_ASSOC);
    return $result[0];
}

function getAnticipos()
{
    initSession();
    $db = db();
    $query = sprintf("SELECT Sum(payment.payment) AS anticipo FROM payment WHERE payment.cajaid = %s AND payment.PaymentDescription = 'Pago parcial / Anticipo'", $_SESSION["caja"]["idcaja"]);
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result[0];
}

function getRetirosTotals()
{
    initSession();
    $query = sprintf("SELECT type, SUM(ammount) AS cajamvmts FROM retiros WHERE caja = %s GROUP BY type", $_SESSION["caja"]["idcaja"]);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($db->error());
    //var_dump($db->last());
    return $result;
}

function saveCorte()
{
    initSession();
    $data = $_POST["corte"];
    $data["timestamp"] = date("Y-m-d H:i:s");
    $data["caja"] = $_SESSION["caja"]["idcaja"];
    $data["type"] = "DAY";
    $data["user"] = $_SESSION["auth_username"];
    //var_dump($data);
    $db = db();
    $result = $db->insert("corte", $data);
    return $db->id();
}

function closeCaja()
{
    initSession();
    $now = date("Y-m-d H:i:s");
    $caja = $_SESSION["caja"]["idcaja"];
    $db = db();
    $result = $db->update("caja", ["Cierre" => $now, "Status" => "CLOSED"],["idcaja"=>$caja]);
    updateMachineCounters();
    return $result->rowCount();
}

function updateMachineCounters()
{
    $db = db();
    $data = $_POST["machineCounters"];
    for ($i = 0; $i < count($data[0]); $i++) {
        $db->update("contadores", $data[$i], ["idcontadores" => $data[$i]["idcontadores"]]);
        //var_dump($db->error());
    }
}

function saveRetiro($data)
{
    initSession();
    if (count($data) == 0) {
        $data = $_POST["data"];
        $data["caja"] = $_SESSION["caja"]["idcaja"];
        $data["user"] = $_SESSION["auth_username"];
    }
    $db = db();
    $result = $db->insert("retiros", $data);
    return $db->id();
}

function saveCorteAndExit()
{
    initSession();
    if (saveCorte() != 0) {
        if (closeCaja() != 0) {
            logout();
        }
    }
}

function finishOrder($orderid)
{
    $db = db();
    $result = $db->update("orders", ["OrderStatus" => "Finished"], ["OrderID" => $orderid]);
    return $result->rowCount();
}

function getPendingSell($orderid)
{
    return loadTicketDetails($orderid);
}

function getPendingSellFunction()
{
    $data = getSell($_POST["orderid"]);
    if ($data != null) {
        echo json_encode([
            "success" => true,
            "payload" => $data
        ]);
    }
}

function getPendingOrders()
{
    $db = db();
    $result = $db->select("orders",["[>]payment"=>"OrderId"] ,["OrderId","orders.Timestamp","payment.Total"], ["OrderStatus" => "Pendiente","ORDER"=>["orders.OrderId"=>"DESC"],"GROUP"=>"OrderId"]);
    echo json_encode([
        "success" => true,
        "payload" => $result
    ]);
}


function newProduct()
{
    $db = db();
    $now = date("Y-m-d H:i:s");
    $result = $db->insert("products", ["Created" => $now]);
    if ($db->id() !== 0) {
        echo json_encode([
            "success" => true,
            "id" => $db->id()
        ]);
    }
}

function newMayPrice($data)
{
    $db = db();
    $result = $db->insert("mayoreo", $data);
    return $db->id();
}

function updateMayPrice($data)
{
    $db = db();
    $count = 0;
    if (count($data) > 0) {
        for ($i = 0; $i < count($data); $i++) {
            $current = $data[$i];
            $db->update("mayoreo", $current, ["idmayoreo" => $current["idmayoreo"]]);
        }
    }

}

function saveItemMayoreos()
{
    $datas = $_POST["data"];
    $inserts = [];
    $updates = [];
    for ($i = 0; $i < count($datas); $i++) {
        $current = $datas[$i];
        isset($current["idmayoreo"]) ? array_push($updates, $current) : array_push($inserts, $current);
    }
    newMayPrice($inserts);
    updateMayPrice($updates);
    echo json_encode([
        "success" => true
    ]);

}

function getMachineCounters()
{
    $db = db();
    return $db->select("contadores", "*");
}

function getSheetCounters()
{
    $db = db();
    initSession();
    $result = $db->select("sheetcounter", "*", ["cajaid" => $_SESSION["caja"]["idcaja"]]);
    if (count($result) == 0) {
        initSheetCounters();
        getSheetCounters();
    } else {
        return $result;
    }
}

function getAllCounters()
{
    echo json_encode([
        "success" => true,
        "machineCounters" => getMachineCounters(),
        "sheetCounters" => getSheetCounters()
    ]);
}

function initSheetCounters()
{
    initSession();
    $cajaid = $_SESSION["caja"]["idcaja"];
    $db = db();
    $initValues = [
        [
            "lastval" => 0,
            "currval" => 0,
            "name" => "carta",
            "cajaid" => $cajaid
        ], [
            "lastval" => 0,
            "currval" => 0,
            "name" => "oficio",
            "cajaid" => $cajaid
        ], [
            "lastval" => 0,
            "currval" => 0,
            "name" => "doblecarta",
            "cajaid" => $cajaid
        ]

    ];
    $result = $db->insert("sheetcounter", $initValues);
    return true;
}


function getUsers()
{
    $db = db();
    $result = $db->select("users", ["id", "email", "username"]);
    echo json_encode([
        "success" => true,
        "payload" => $result,
        "counters" => getCounters(),
        "sExistences" => getSheetExistences()
    ]);
}

function updateCunter()
{
    $db = db();
    $result = $db->update("contadores", $_POST["data"], ["idcontadores" => $_POST["data"]["idcontadores"]]);
    if ($result->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "last" => $db->last()
        ]);
    }
}

function getCounters()
{
    $db = db();
    $result = $db->select("contadores", "*");
    return $result;
}

function deleteUser()
{
    try {
        $auth = new Delight\Auth\Auth(connection());
        $auth->admin()->deleteUserById($_POST['id']);
    } catch (\Delight\Auth\UnknownIdException $e) {
        die('Unknown ID');
    }
}

function getCajasFromDay()
{
    $db = db();
    $query = sprintf("select idcaja from caja where Apertura between '%s' and '%s'", $_POST["from"], $_POST["to"]);
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getOrdersFromDay()
{

}

function getSheetExistences()
{
    $db = db();
    $result = $db->select("inventarios", ["idinventarios", "ammount"], ["OR" => ["pruductid" => [5, 6, 10]]]);
    return $result;
}

function updateSheetExistences()
{
    $db = db();
    for ($i = 0; $i < count($_POST["data"]); $i++) {
        $result = $db->update("inventarios", ["ammount" => $_POST["data"][$i]["ammount"]], ["idinventarios" => $_POST["data"][$i]["idinventarios"]]);
    }
    echo json_encode(["success" => true]);
}

function getCajaReportFull()
{

}

function getOrdersFromCaja()
{
    $db = db();
    initSession();
    $result = $db->select("orders", ["OrderId"], ["cajaid" => $_SESSION["caja"]["idcaja"]]);
    return $result;
}

function getOrderMergeDetails()
{
    $orders = getOrdersFromCaja();
    for ($i = 0; $i < count($orders); $i++) {
        $current = $orders[$i]["OrderId"];
        $current["OrderDetails"] = getOrderDetails($current);
    }
}

function getCajaCorte()
{
    $db = db();
    initSession();
    $result = $db->select("orders", [
        "[>]orderdetails" => "OrderId",
        "[>]payment" => "OrderId",
        "[>]products" => ["orderdetails.ProductId" => "ItemId"]
    ], [
        "branch[Int]",
        "OrderId[Int]",
        "orders.cajaid[Int]",
        "orders.Timestamp",
        "DatetoFinish",
        "OrderStatus",
        "prioridad",
        "tax[Bool]",
        "Products" => [
            "orderdetails.fecha",
            "orderdetails.ProductId",
            "orderdetails.Quantity",
            "products.Name",
            "orderdetails.Status[Bool]",
            "orderdetails.price[Number]"
        ],
        "pagos" => [
            "payment.Timestamp",
            "payment.OrderId[Int]",
            "payment.PaymentDescription",
            "payment.payment[Number]",
            "payment.Total[Number]",
            "payment.credit[Number]",
            "payment.change[Number]",
            "payment.IVA[Number]",
            "payment.cajaid[Int]"
        ]
    ], [
        "orders.cajaid" => 33
    ]);
    //var_dump($db->error());
    echo json_encode($result);
}

function deleteMayoreo()
{
    $db = db();
    $result = $db->delete("mayoreo", ["idmayoreo" => $_POST["data"]]);
    echo json_encode([
        "success" => $result->rowCount() > 0
    ]);
}

function getLastCashMovement()
{
    $db = db();
    $query = "SELECT * FROM retiros ORDER BY idretiros DESC LIMIT 1";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCajasFrom($from, $to)
{
    $db = db();
    $result = $db->select("caja", ["idcaja[Int]"], ["Cierre[<>]" => [date($from), date($to)]]);
    return $result;
}

function getCajaOrders($cajaid)
{
    $db = db();
    $result = $db->select("orders", ["OrderId[Int]"], ["cajaid" => $cajaid]);
    return $result;
}

function getOrderFR($oid)
{
    $db = db();
    $result = $db->select("orders", ["Timestamp", "OrderStatus", "cajaid[Int]"], ["OrderId" => $oid]);
    return $result[0];
}

function getOrderDetailsFR($oid)
{
    $db = db();
    $result = $db->select("orderdetails", ["[>]products" => ["ProductId" => "ItemId"]], ["fecha", "ProductId", "Name", "category", "Quantity[Int]", "orderdetails.Status[Bool]", "orderdetails.price[Number]"], ["OrderId" => $oid]);
    //var_dump($db->error());
    if (count($result) > 0) {
        return $result;
    } else {
        return [];
    }
}

function getOrderPaymentsFR($oid, $cajaid)
{
    $db = db();
    $result = $db->select("payment", ["Timestamp", "Ammount[Number]", "discount[Number]", "payment[Number]", "Total[Number]", "Credit[Number]", "Change[Number]", "IVA[Number]"], ["OrderId" => $oid, "cajaid" => $cajaid]);
    if (count($result) > 0) {
        return $result;
    } else {
        return [];
    }
}

function getSystemCorteCount($cajas)
{
    $nCajas = filtercajas($cajas);
    $stringCajas = implode(",", $nCajas);
    $query = sprintf("SELECT SUM(ammount) as ammount, SUM(corte.count) AS count,SUM(diference) AS diference FROM corte WHERE  caja IN(%s)", $stringCajas);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result[0];
}

function stringJoinCajas($cajas)
{
    $nCajas = filtercajas($cajas);
    return implode(",", $nCajas);
}

function filtercajas($cajas)
{
    $mCajas = [];
    foreach ($cajas as $caja) {
        array_push($mCajas, $caja["idcaja"]);
    }
    return $mCajas;
}

function getCorteMainSummary($cajas)
{
    $query = sprintf("SELECT orderdetails.ProductId, Sum(orderdetails.Quantity) AS quantity, products.category, SUM(orderdetails.Quantity * orderdetails.price) as total FROM orderdetails INNER JOIN products ON products.ItemId = orderdetails.ProductId WHERE orderdetails.cajaid IN(%s) AND orderdetails.Status = 1 GROUP BY products.category", $cajas);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCortecopiesDetails($cajas)
{
    $query = sprintf("SELECT orderdetails.ProductId, products.Name, Sum(orderdetails.Quantity) AS quantity, orderdetails.price, Sum(orderdetails.Quantity * orderdetails.price) as total FROM orderdetails INNER JOIN products ON products.ItemId = orderdetails.ProductId WHERE products.category = 'copias' AND orderdetails.cajaid IN(%s) AND orderdetails.Status = 1 GROUP BY products.ItemId, orderdetails.price ORDER BY products.Name ASC", $cajas);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($db->error());
    return $result;
}

function getPapeleriaDetails($cajas)
{
    $query = sprintf("SELECT orderdetails.ProductId, products.Name, Sum(orderdetails.Quantity) AS quantity, orderdetails.price, Sum(orderdetails.Quantity * orderdetails.price) as total FROM orderdetails INNER JOIN products ON products.ItemId = orderdetails.ProductId WHERE products.category = 'papeleria' AND orderdetails.cajaid IN(%s) AND orderdetails.Status = 1 GROUP BY products.ItemId, orderdetails.price ORDER BY products.Name ASC", $cajas);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCyberplanetDetails($cajas)
{
    $query = sprintf("SELECT orderdetails.ProductId, products.Name, Sum(orderdetails.Quantity) AS quantity, orderdetails.price, Sum(orderdetails.Quantity * orderdetails.price) as total FROM orderdetails INNER JOIN products ON products.ItemId = orderdetails.ProductId WHERE products.category = 'cyberplanet' AND orderdetails.cajaid IN(%s) AND orderdetails.Status = 1 GROUP BY products.ItemId, orderdetails.price ORDER BY products.Name ASC", $cajas);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getRetiros($cajas)
{
    $query = sprintf("SELECT * FROM retiros WHERE caja in(%s)", $cajas);
    $db = db();
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getFullReport()
{
    //var_dump($_POST);
    $cajas = getCajasFrom($_POST["data"]["from"], $_POST["data"]["to"]);
    $sCajas = stringJoinCajas($cajas);
    if (count($cajas) > 0) {
        $cajaDetails = [];
        $response = [];
        for ($i = 0; $i < count($cajas); $i++) {
            array_push($cajaDetails, getCajaOrders($cajas[$i]["idcaja"]));
        }
        for ($i = 0; $i < count($cajaDetails); $i++) {
            $orders = $cajaDetails[$i];
            for ($j = 0; $j < count($orders); $j++) {
                $oid = $orders[$j]["OrderId"];
                $mData = getOrderFR($oid);
                $nArray = array_merge($orders[$j], $mData);
                $nArray["details"] = getOrderDetailsFR($oid);
                $nArray["payments"] = getOrderPaymentsFR($oid, $nArray["cajaid"]);
                array_push($response, $nArray);
            }
        }
        $response = ["success" => true,
            "systemCorte" => getSystemCorteCount($cajas),
            "payload" => $response,
            "details" => [
                "summary" => getCorteMainSummary($sCajas),
                "copias" => getCortecopiesDetails($sCajas),
                "papeleria" => getPapeleriaDetails($sCajas),
                "cyberplanet" => getCyberplanetDetails($sCajas),
                "cash" => getRetiros($sCajas)
            ]
        ];
        //var_dump($response);
        echo json_encode($response);
    }
}

function getCajaTicketsforReprint()
{
    initSession();
    $db = db();
    $result = $db->select("orders", ["[>]payment" => ["OrderId" => "OrderId"]], ["orders.OrderId", "orders.Timestamp", "payment.Total"], ["orders.cajaid" => $_SESSION["caja"]["idcaja"]]);
    //var_dump($db->error());
    echo json_encode([
        "success"=>true,
        "tickets"=>$result
    ]);
}

function saveJobAnticipo(){
    initSession();
    $db = db();
    $result = $db->insert("payment",[
        "payment"=>$_POST["qty"],
        "OrderId"=>$_POST["orderid"],
        "cajaid"=>$_SESSION["caja"]["idcaja"],
        "Timestamp"=> date("Y-m-d H:i:s"),
        "PaymentDescription"=>"Pago parcial / Anticipo",
        "PaymentMethod"=>"Efectivo",
        "Ammount"=>0,
        "discount"=>0,
        "Total"=>$_POST["qty"],
        "Credit"=> -$_POST["qty"],
        "change"=>0,
        "IVA"=>0
    ]);
    if ($db->id() != 0){
        echo json_encode([
            "success"=>true
        ]);
    }else{
        echo json_encode([
            "success"=>false,
            "error"=>$db->error()
        ]);
    }
}

function getRecentTickets(){
    $db = db();
    $from = date("Y-m-d H:i:s",$_GET["from"]);
    $to = date("Y-m-d H:i:s",$_GET["to"]);
    //var_dump($from,$to);
    $sql = sprintf("Select OrderId, Timestamp, SUM(IF(payment < Total,payment,Total)) AS payment From vialogik_casaensuenos.payment WHERE Timestamp BETWEEN '%s' AND '%s 'GROUP BY OrderId",$from,$to);
    $resutl = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //$resutl = $db->select("payment",["Orderid","Timestamp","payment"],["Timestamp[<>]"=>[$from,$to]]);
   //var_dump($db->last());
    return $resutl;
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
        case "getCajaReport":
            getCajaReport();
            break;
        case "closeCaja":
            saveCorteAndExit();
            break;
        case "saveDotacion":
            saveDotacion();
            break;
        case "saveRetiro":
            saveRetiro([]);
            break;
        case "getPendingSell":
            getPendingSellFunction();
            break;
        case "getPendingOrders":
            getPendingOrders();
            break;
        case "getAllProducts":
            getAllItems();
            break;
        case "updateProduct":
            updateItem();
            break;
        case "newProduct":
            newProduct();
            break;
        case "saveMay":
            saveItemMayoreos();
            break;
        case "getCounters":
            getAllCounters();
            break;
        case "getConfigData":
            getUsers();
            break;
        case "deleteUser":
            deleteUser();
            break;
        case "newUser":
            newUser();
            break;
        case "updatecounter":
            updateCunter();
            break;
        case "sheetExistences":
            getSheetExistences();
            break;
        case "updateSheetExistences":
            updateSheetExistences();
            break;
        case "getFullCorte":
            getCajaCorte();
            break;
        case "getItemByCode":
            getitemByCode();
            break;
        case "deleteMayoreo":
            deleteMayoreo();
            break;
        case "getFullReport":
            getFullReport();
            break;
        case "getCajaTickets":
            getCajaTicketsforReprint();
            break;
        case "saveJobAnticipo":
            saveJobAnticipo();
            break;
        default:
            break;
    }
}
?>