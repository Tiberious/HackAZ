<?php 

    $db = new PDO(
        "mysql:host=localhost;dbname=nest",
        "root",
        "gusadelic",
	array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    );



?>