<?php

try {
    $host = 'localhost';
    $db   = 'prices-off';
    $user = 'postgres'; 
    $pass = 'postgres';
    $port = '5432'; 

    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    
    $db = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
