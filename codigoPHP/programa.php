<?php
session_start();
if (!isset($_SESSION['codigo'])) {
    header('Location: ../login.php');
} else {
    if (isset($_POST['salir'])) {
        session_destroy(); //Destrucción de la sesión
        //Destrucción de las cookies
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
    } else if (isset($_POST['recargar'])) {
        header('Location: programa.php');
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
                switch ($_COOKIE["language"]) {

                    case "spanish":
                        ?>
                        Bienvenido/a  
                        <?php
                        break;
                    case "portuguese":
                        ?>
                        Bem-vinda 
                        <?php
                        break;
                }
                ?> <span class="respuesta"><?php echo $_SESSION['descripcion']; ?></span>!</h3>
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
                <h3>Número de veces que te has conectado:<span class="respuesta"><?php echo $_SESSION['numconex'] + 1; ?></span></h3>
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
                <input type="submit" name="recargar" value="Recargar"/><br>

                <label for="idioma">Selecciona el idioma:</label>
                <select name="idioma" id="idioma">
                    <option value="opciones" >Idiomas</option>
                    <option value="<?php setcookie("language", "spanish", time() + 3600); ?>" >Español</option>
                    <option value="<?php setcookie("language", "portuguese", time() + 3600); ?>">Portugés</option>                   
                </select>
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

