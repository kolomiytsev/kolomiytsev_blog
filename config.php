<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1); 


define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "root");
define("DATABASE", "kolomiytsev2");

define("DEFAULT_PAGE", 1);
define("FIRST_PAGE", 1);
define("COUNT_POST_ON_PAGE", 3);
define("ADMIN_EMAIL", "leonid.kolomiytsev@gmail.com");

$db = new mysqli(HOST, USER, PASSWORD, DATABASE);

if (!$db) {
    die('Cannot connect to database');
}

?>
