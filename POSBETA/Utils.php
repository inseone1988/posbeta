<?php /** * Created by PhpStorm. * User: Javier Ramirez * Date: 15/01/2019 * Time: 03:45 PM */
require_once $_SERVER["DOCUMENT_ROOT"] . '/connection.php';
require_once "Auth.php";
function userBranch()
{
    $db = db();
    $result = $db->select("users", ["branch"], ["id" => $_SESSION["auth_user_id"]]);
    $_SESSION["user_branch"] = $result[0]["branch"];
    getBranch();
}

function getBranch()
{
    $db = db();
    $result = $db->select("branches", "*", ["branchid" => $_SESSION["user_branch"]]);
    $_SESSION["branch_data"] = $result[0];
}

function newCaja()
{
    initSession();
    $db = db();
    $now = date("Y-m-d H:i:s");
    $result = $db->insert("caja", ["Apertura" => $now, "user" => $_SESSION["auth_username"], "Status" => "ACTIVE", "branch" => $_SESSION["user_branch"]]);
    if ($db->id() != 0) {
        getActiveCaja();
        $_SESSION["caja"]["init_type"] = "new";
    }
}

function getActiveCaja()
{
    initSession();
    $db = db();
    $result = $db->select("caja", "*", ["Status" => "ACTIVE"]);
    if (count($result) != 0) {
        $_SESSION["caja"] = $result[0];
        $_SESSION["caja"]["init_type"] = "resume";
    } else {
        newCaja();
    }
} ?>