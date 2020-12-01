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
    } else if (isset($_POST['recargar'])) { //Comprobamos que el usuario haya enviado el formulario (se recarga la página)
        if ($_POST['language'] == 'spanish') {//Si el idioma seleccionado por el usuario es español
            setcookie("language", 'spanish'); //Creamos o cambiamos la cookie idioma al valor 'spanish'
        }
        if ($_POST['language'] == 'portuguese') {
            setcookie("language", 'portuguese'); 
        }
        if ($_POST['language'] == 'italian') {
            setcookie("language", 'italian'); 
        }
        if ($_POST['language'] == 'french') {
            setcookie("language", 'french'); 
        }
        if ($_POST['language'] == 'english') {
            setcookie("language", 'english'); 
        }

        header('location: programa.php'); //Volvemos a cargar el ejercicio01.php para que se recargue el valor de las cookies
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

                <label for="language">Selecciona el idioma:</label>
                <select name="language" id="language">
                    <option value="spanish" <?php
                    if (isset($_COOKIE['language'])) {//si existe la cookie 'language'
                        if ($_COOKIE['language'] === "spanish") {//Si el idioma almacenado es español
                            echo 'selected'; //Será el valor seleccionado en nuestra lista una vez recargada la página
                        }
                    }
                    ?>>Español</option>
                    <option value="portuguese" <?php
                    if (isset($_COOKIE['language'])) {
                        if ($_COOKIE['language'] === "portuguese") {
                            echo 'selected'; 
                        }
                    }
                    ?>>Portugés</option>
                    <option value="italian" <?php
                    if (isset($_COOKIE['language'])) {
                        if ($_COOKIE['language'] === "italian") {
                            echo 'selected'; 
                        }
                    }
                    ?>>Italiano</option>
                    <option value="french" <?php
                    if (isset($_COOKIE['language'])) {
                        if ($_COOKIE['language'] === "french") {
                            echo 'selected'; 
                        }
                    }
                    ?>>Francés</option>
                    <option value="english" <?php
                    if (isset($_COOKIE['language'])) {
                        if ($_COOKIE['language'] === "english") {
                            echo 'selected';
                        }
                    }
                    ?>>Inglés</option>  
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