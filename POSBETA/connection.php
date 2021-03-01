<?php
/**
 * Created by PhpStorm.
 * User: Javier Ramirez
 * Date: 11/01/2019
 * Time: 12:38 PM
 */

require $_SERVER["DOCUMENT_ROOT"] .  '/vendor/autoload.php';

function db(){
    $db = new Medoo([
        'database_type' => 'mysql',
        'database_name' => 'vialogik_casaensuenos',
        'server' => 'localhost',
        'username' => 'vialogik_ensueno',
        'password' => '2*hqx26L',
    ]);
    return $db;
}
?>