<?php
if (isset($_POST['salir'])) {
    header('Location: ../../indexProyectoTema5.php');
} else if (isset($_POST['detalle'])) {
    header('Location: ../detalle.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ejercicio01 - DWES</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
        <link href="../../webroot/css/styleDWESTema5.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="programa">
            <h2>Usuario correcto</h2>
            <form class="programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="submit" name="detalle" value="Detalle"/>
                <input type="submit" name="salir" value="Salir"/>
            </form>
        </div>
    </body>
</html>  

