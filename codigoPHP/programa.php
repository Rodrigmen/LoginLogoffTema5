<?php
if (isset($_POST['salir'])) {
    session_destroy();
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 1000);
        setcookie($name, '', time() - 1000, '/');
    }
    header('Location: ../../indexProyectoTema5.php');
} else if (isset($_POST['detalle'])) {
    header('Location: detalle.php');
} else {
    session_start();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Página de programa - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
        <link href="../../webroot/css/styleDWESTema5.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="programa">
            <h2>Usuario correcto</h2>
            <h3>Nombre: <?php echo $_SESSION['codigo']; ?></h3>
            <h3>Descripción: <?php echo $_SESSION['descripcion']; ?></h3>
            <h3>Perfil: <?php echo $_SESSION['perfil']; ?></h3>
            <form class="programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="submit" name="detalle" value="Detalle"/>
                <input type="submit" name="salir" value="Salir"/>
            </form>
        </div>
    </body>
</html>  

