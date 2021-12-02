<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "name_of_database";

$conn = new mysqli($host,$user,$password,$database);

$conn->set_charset("utf-8");
