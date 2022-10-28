<?php
$host = "localhost";
$username = "root";
$password = "root";
$database = "test";

$connection = new mysqli($host, $username, $password, $database);
if(!$connection){
    die("Nie można połączyć się z bazą danych!");
}
?>