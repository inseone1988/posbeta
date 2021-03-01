<?php /** * Created by PhpStorm. * User: Javier Ramirez * Date: 15/01/2019 * Time: 12:18 PM */
require_once $_SERVER["DOCUMENT_ROOT"] . '/connection.php';
require_once 'Utils.php';
function initSession()
{
    if (!isset($_SESSION)) {
        session_start();
    }
}

function userLoggedIn()
{
    initSession();
    if (!count($_SESSION) == 0) {
        if ($_SESSION["auth_logged_in"]) {
            return true;
        }
    }
    return false;
}

function redirectTologinPage()
{
    $ref = $_SERVER["PHP_SELF"];
    header("Location:login.php?referer=" . $ref);
}

function saveUserBranch($userid)
{
    $db = db();
    $db->update("users", ["branch" => $_POST["user_branch"]], ["id" => $userid]);
}

function newUser()
{
    $auth = new \Delight\Auth\Auth(connection());
    $response = [];
    $userId = $auth->register($_POST["mail"], $_POST["password"], $_POST["username"]);
    switch ($_POST["user_role"]) {
        case 1:
            try {
                $auth->admin()->addRoleForUserById($userId, \Delight\Auth\Role::ADMIN);
            } catch (\Delight\Auth\UnknownIdException $e) {
                die('Unknown user ID');
            }
            break;
        case 2:
            try {
                $auth->admin()->addRoleForUserById($userId, \Delight\Auth\Role::EMPLOYEE);
            } catch (\Delight\Auth\UnknownIdException $e) {
                die('Unknown user ID');
            }
            break;
        default:
            try {
                $auth->admin()->addRoleForUserById($userId, \Delight\Auth\Role::EMPLOYEE);
            } catch (\Delight\Auth\UnknownIdException $e) {
                die('Unknown user ID');
            }
            break;
    }
    saveUserBranch($userId);
    echo json_encode(["success" => true, "userId" => $userId]);
}

function isAdmin($userid)
{
    $admins = [1,2,4,5,6];
    if (in_array($userid, $admins)) {
        return true;
    }
    return false;
}

function login()
{
    $auth = new \Delight\Auth\Auth(connection());
    try {
        if (isset($_GET["referer"])) {
            $referer = $_GET["referer"];
        } else {
            $referer = "caja.php";
        }
        $auth->loginWithUsername($_POST['username'], $_POST['password']);
        userBranch();
        header("Location:" . $referer);
    } catch (\Delight\Auth\InvalidEmailException $e) {
        die('Wrong email address');
    } catch (\Delight\Auth\InvalidPasswordException $e) {
        die('Wrong password');
    } catch (\Delight\Auth\EmailNotVerifiedException $e) {
        die('Email not verified');
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        die('Too many requests');
    }
}

function logout()
{
    $auth = new \Delight\Auth\Auth(connection());
    try {
        $auth->logOut();
        header("Location:login.php");
    } catch (\Delight\Auth\NotLoggedInException $e) {
        die('Not logged in');
    }
}

if (isset($_POST["function"])) {
    switch ($_POST["function"]) {
        case "login":
            login();
            break;
        case "newUser":
            newUser();
            break;
        case "logout":
            logout();
            break;
    }
}