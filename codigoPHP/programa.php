<?php
session_start();
if (isset($_POST['salir'])) {
    require_once '../config/confDB.php';
    try {
        $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
        $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $fechaActual = new DateTime();
        $tiempo = $fechaActual->getTimestamp();
        
        $consultaActualizar = "UPDATE T01_Usuario SET T01_FechaHoraUltimaConexion = $tiempo WHERE T01_CodUsuario = :codigo";
        $actualizarFecha = $oConexionPDO->prepare($consultaActualizar);
        $actualizarFecha->bindParam(':codigo', $_SESSION['codigo']);
        $actualizarFecha->execute();
    } catch (PDOException $excepcionPDO) {
        echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
        echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
    } finally {
        unset($oConexionPDO); //destruimos el objeto  
    }
    
    session_destroy();
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 1000);
        setcookie($name, '', time() - 1000, '/');
    }
    header('Location: ../login.php');
} else if (isset($_POST['detalle'])) {
    header('Location: detalle.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Página de programa - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
        <link href="../webroot/css/stylePrograma.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="programa">
            <h2>USUARIO CORRECTO</h2>
            <h3>¡Bienvenido/a <span class="respuesta"><?php echo $_SESSION['descripcion']; ?></span>!</h3>
            <?php
            if ($_SESSION['perfil'] === "admin") {
                ?>
                <h3><span class="respuesta">Eres el admin</span></h3>
                <?php
            } else {
                ?>
                <h3><span class="respuesta">Eres un simple usuario</span></h3>
                <?php
            }
            if ($_SESSION['numconex'] === '0') {
                ?>
                <h3><span class="respuesta">¡Es la primera vez que te conectas!</span></h3>
                <?php
            } else {
                ?>
                <h3>Número de veces que te has conectado:<span class="respuesta"><?php echo $_SESSION['numconex']+1; ?></span></h3>
                <h3>Última conexión: <span class="respuesta"><?php
                        $fecha = new DateTime();
                        $fecha->setTimestamp($_SESSION['ultimaconex']);
                        $fechaFormateada = $fecha->format("Y-m-d H:i:s");
                        echo $fechaFormateada;
                    }
                    ?>

                </span></h3>          
            <form class="programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="submit" name="detalle" value="Detalle"/>
                <input type="submit" name="salir" value="Salir"/>
            </form>
        </div>
    </body>
    <footer>
        <ul>
            <li>&copy2020-2021 | Rodrigo Robles Miñambres</li>
            <li>
                <a target="_blank" href="https://github.com/Rodrigmen/LoginLogoffTema5/tree/master">
                    <img id="imggit" title="GitHub" src="../webroot/css/images/github.png"  alt="GITHUB">
                </a>
            </li>
        </ul>            
    </footer>
</html>  

