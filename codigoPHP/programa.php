<?php
session_start();
if (!isset($_SESSION['usuarioDAW218LogInLogOutTema5'])) {
    header('Location: ../login.php');
} else {
    if (isset($_POST['salir'])) {
        session_destroy(); //Destrucción de la sesión
        //Destrucción de todas las cookies (esto es para que se borre la cookie creada al iniciar sesión)
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
    } else if (isset($_REQUEST['language'])) { //Comprobamos que se cambie el idioma 
        if ($_REQUEST['language'] == 'spanish') {//Si el idioma seleccionado por el usuario es español
            setcookie("language", "spanish", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP"); //Cambiamos la cookie idioma al valor 'spanish'
        }
        if ($_REQUEST['language'] == 'portuguese') {
            setcookie("language", "portuguese", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
        }
        if ($_REQUEST['language'] == 'italian') {
            setcookie("language", "italian", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
        }
        if ($_REQUEST['language'] == 'french') {
            setcookie("language", "french", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
        }
        if ($_REQUEST['language'] == 'english') {
            setcookie("language", "english", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
        }
        header("Location: programa.php");
    }

    require_once '../config/confDB.php';
    try {

        $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
        $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
        //Creación de la consulta preparada
        $consultaUsuario = "SELECT T01_DescUsuario, T01_NumConexiones, T01_Perfil FROM T01_Usuario WHERE T01_CodUsuario = :codigo";
        //Preparación de la consulta preparada
        $buscarUsuario = $oConexionPDO->prepare($consultaUsuario);

        //Insertamos los datos en la consulta preparada
        $buscarUsuario->bindParam(':codigo', $_SESSION['usuarioDAW218LogInLogOutTema5']);

        //Se ejecuta la consulta preparada
        $buscarUsuario->execute();

        $oUsuario = $buscarUsuario->fetchObject();

        $DescripcionUsuario = $oUsuario->T01_DescUsuario;
        $NumeroConexiones = $oUsuario->T01_NumConexiones;
        $PerfilUsuario = $oUsuario->T01_Perfil;
    } catch (PDOException $excepcionPDO) {
        echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
        echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
    } finally {
        unset($oConexionPDO); //destruimos el objeto  
    }
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
            <h3>¡<?php
                if ($_COOKIE["language"] === "spanish") { //si el valor de la cookie 'spanish', se muestra el siguiente mensaje por pantalla
                    ?>Bienvenido/a  
                    <?php
                } else if ($_COOKIE["language"] === "portuguese") {
                    ?>Bem-vinda 
                    <?php
                } else if ($_COOKIE["language"] === "italian") {
                    ?>Benvenuto
                    <?php
                } else if ($_COOKIE["language"] === "french") {
                    ?>Bienvenue
                    <?php
                } else if ($_COOKIE["language"] === "english") {
                    ?>Welcome
                    <?php
                }
                ?> <span class="respuesta"><?php echo $DescripcionUsuario; ?></span>!</h3>
                <?php
                if ($PerfilUsuario === "admin") {
                    ?>
                <h3><span class="respuesta">Eres el admin</span></h3>
                <?php
            } else {
                ?>
                <h3><span class="respuesta">Eres un simple usuario</span></h3>
                <?php
            }
            if ($NumeroConexiones === '1') {
                ?>
                <h3><span class="respuesta">¡Es la primera vez que te conectas!</span></h3>
                <?php
            } else {
                ?>
                <h3>Número de veces que te has conectado:<span class="respuesta"><?php echo $NumeroConexiones; ?></span></h3>
                <h3>Última conexión: <span class="respuesta"><?php
                        $fecha = new DateTime();
                        $fecha->setTimestamp($_SESSION['FechaHoraUltimaconexionAnterior']);
                        $fechaFormateada = $fecha->format("Y-m-d H:i:s");
                        echo $fechaFormateada;
                    }
                    ?>
                </span></h3>          
            <form class="programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="submit" name="detalle" value="Detalle"/>
                <input type="submit" name="salir" value="Salir"/>
            </form>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?language=spanish"><button><img src="../webroot/css/images/españa.png" alt="Español"/></button></a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?language=portuguese"><button><img src="../webroot/css/images/portugal.png" alt="Português"/></button></a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?language=italian"><button><img src="../webroot/css/images/italia.png" alt="Italiano"/></button></a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?language=french"><button><img src="../webroot/css/images/francia.png" alt="Français"/></button></a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?language=english"><button><img src="../webroot/css/images/inglaterra.png" alt="English"/></button></a>
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