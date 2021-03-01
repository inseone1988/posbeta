<?php
require __DIR__ . '/vendor/autoload.php';
use Medoo\Medoo;

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

function connection (){
    return new PDO('mysql:dbname=vialogik_casaensuenos;host=localhost;charset=utf8', 'vialogik_ensueno', '2*hqx26L');
}

 ?>
