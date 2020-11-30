<?php
/**
 * Control de acceso mediante la función header.
 * 
 * @version 1.0.0
 * @since 23-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != 'paso') {
    header('WWW-Authenticate: Basic realm="Mi dominio"');
    header('HTTP/1.0 401 Unauthorized');
    echo "¡Usuario no valido!";
    exit;
} else {
    echo "<p>Hola {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>Introdujo {$_SERVER['PHP_AUTH_PW']} como su contraseña.</p>";
    header('Location: programa.php');
}
?> 
<!DOCTYPE html>
<html>
    <head>
        <title>ejercicio01 - DWES</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
        <link href="../webroot/css/styleDWESTema5.css" rel="stylesheet" type="text/css"/>
    </head>
</html>  