<?php
session_start(); //se recupera la sesión
if (!isset($_SESSION['usuarioDAW218LogInLogOutTema5'])) { //si la sesión no tiene información (es decir, no esta creada la sesión correctamente = no te has logeado), se devuelve automáticamente a la página de login
    header('Location: ../login.php');
}
//BOTONES
if (isset($_POST['salir'])) { //si ejecutamos el boton 'salir'
    session_destroy(); //Destrucción de la sesión
    header('Location: ../login.php'); //volvemos a la página 'login.php'
}
if (isset($_POST['detalle'])) { //o si ejecutamos el boton 'detalle'
    header('Location: detalle.php'); //nos vamos a la página 'detalle.php'
}
if (isset($_POST['editar'])) { //o si ejecutamos el boton 'editar'
    header("Location: editarPerfil.php"); //nos vamos a la página 'editarPerfil.php'
}

//COOKIE
$aSaludoIdiomas = [
    "spanish" => "Bienvenido/a",
    "portuguese" => "Bem-vinda",
    "italian" => "Benvenuto",
    "french" => "Bienvenue",
    "english" => "Welcome"
];

//COMPROBAMOS SI LA COOKIE YA EXISTE 
if (isset($_COOKIE['language'])) {
    $saludo = $aSaludoIdiomas[$_COOKIE['language']];
} else { //SINO SE CREA (LA COOKIE POR DEFECTO ES EN ESPAÑOL)
    /* ----COOKIE----- */
    //creación de la cookie (su valor se pasara a 'programa.php' para identificar el idioma en el que aparecera la información)
    //setcookie(nombre, valor, expires, path, domain, secure, options, httponly);
    //name->nombre de la cookie
    //valor->el valor de la cookie
    //expires->el tiempo en que la cookie expira (0 = cuando se cierra la sesión)  [en este caso, dura un día]
    //path->la ruta dentro del servidor en la que la cookie estará disponible
    //domain->el (sub)dominio al que la cookie está disponible
    //secure->[boolean] cuando es TRUE la cookie será accesible sólo a través del protocolo HTTP
    //httponly->[boolean] cuando es TRUE la cookie será accesible sólo a través del protocolo HTTP
    //NOTA: Si quieres mantener la misma cookie por varios archivos en diferentes directorios (como 'login.php' y 'programa.php') el path (ruta) y el domain (dominio) tienen que ser el mismo
    setcookie("language", "spanish", time() + 60 * 60 * 24, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
    $saludo = $aSaludoIdiomas["spanish"];
}

if (isset($_GET['language'])) { //SI PULSAMOS CUALQUIER BOTON PARA ELEGIR EL IDIOMA
    setcookie("language", $_GET['language'], time() + 60 * 60 * 24, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
    $saludo = $aSaludoIdiomas[$_GET['language']];
}


require_once '../config/confDB.php'; //requerimos una vez el archivo de configuración
try {
    //BUSQUEDA EN LA BASE DE DATOS DE LA INFORMACIÓN QUE MOSTRAREMOS POR PANTALLA DEL USURIO LOGEADO
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

    //Variables con las que sacaremos la información del usuario logeado
    $DescripcionUsuario = $oUsuario->T01_DescUsuario;
    $NumeroConexiones = $oUsuario->T01_NumConexiones;
    $PerfilUsuario = $oUsuario->T01_Perfil;

    //POSIBLES RESPUESTAS QUE PUEDEN APARECER SEGÚN EL VALOR DE LOS CAMPOS DE LA BASE DE DATOS
    if ($PerfilUsuario === "admin") { //PERFIL DE USUARIO
        $salidaPerfil = "<h3><span class='respuesta'>Eres el admin</span></h3>";
    } else {
        $salidaPerfil = "<h3><span class='respuesta'>Eres un simple usuario</span></h3>";
    }

    if ($NumeroConexiones === '1') {
        $salidaConex = "<h3><span class='respuesta'>¡Es la primera vez que te conectas!</span></h3>";
    } else {
        $fecha = new DateTime();
        $fecha->setTimestamp($_SESSION['FechaHoraUltimaconexionAnterior']);
        $fechaFormateada = $fecha->format("Y-m-d H:i:s");
        $salidaConex = "<h3>Número de veces que te has conectado:<span class='respuesta'>$NumeroConexiones</span></h3>"
                . "<h3>Última conexión: <span class='respuesta'> $fechaFormateada </span></h3>";
    }
} catch (PDOException $excepcionPDO) {
    echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
    echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
} finally {
    unset($oConexionPDO); //destruimos el objeto  
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
            <h3>¡<?php echo $saludo ?> <span class="respuesta"><?php echo $DescripcionUsuario; ?></span>!</h3> <!-- SALUDO AL USUARIO (COOKIE Y DESCRIPCIÓN)-->
            <?php
            echo $salidaPerfil;
            echo $salidaConex;
            ?>     
            <!-- FORMULARIO CON LOS BOTONES PARA IR A DETALLE.PHP O CERRAR LA SESIÓN -->
            <form class="programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="submit" name="detalle" value="Detalle"/>
                <input type="submit" name="salir" value="Salir"/>
                <input type="submit" name="editar" value="Editar"/>
            </form>
            <!-- BOTONES PARA CAMBIAR EL IDIOMA (CAMBIA EL VALOR DE LA COOKIE) -->
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