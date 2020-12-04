<?php
session_start(); //se recupera la sesión
if (!isset($_SESSION['usuarioDAW218LogInLogOutTema5'])) { //si la sesión no tiene información (es decir, no esta creada la sesión correctamente = no te has logeado), se devuelve automáticamente a la página de login
    header('Location: ../login.php');
}
require_once '../config/confDB.php';
try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
    //Requerimos una vez la libreria de validaciones
    require_once '../core/libreriaValidacion.php';

    //Creamos una variable boleana para definir cuando esta bien o mal rellenado el formulario
    $entradaOK = true;

    //Creamos dos constantes: 'REQUIRED' indica si un campo es obligatorio (tiene que tener algun valor); 'OPTIONAL' indica que un campo no es obligatorio
    define('REQUIRED', 1);
    define('OPTIONAL', 0);

    //Array que contiene los posibles errores de los campos del formulario
    $aErrores = [
        'ePasswordA' => null,
        'ePasswordN' => null,
        'ePasswordC' => null
    ];


    if (isset($_POST['cancelar'])) {
        header('Location: editarPerfil.php');
    }

    //Consulta para sacar la descripcion actual del usuario
    $consultaUsuario = "SELECT T01_Password FROM T01_Usuario WHERE (T01_CodUsuario = :codigo)";
    //Preparación de la consulta preparada
    $buscarUsuario = $oConexionPDO->prepare($consultaUsuario);


    //Insertamos los datos en la consulta preparada
    $buscarUsuario->bindParam(':codigo', $_SESSION['usuarioDAW218LogInLogOutTema5']);

    //Se ejecuta la consulta preparada
    $buscarUsuario->execute();
    $oUsuario = $buscarUsuario->fetchObject();
    $passwordActual = $oUsuario->T01_Password;


    if (isset($_POST['enviar'])) { //si se pulsa 'enviar' (input name="enviar")
        //Validación de los campos (el resultado de la validación se mete en el array aErrores para comprobar posteriormente si da error)
        $aErrores['ePasswordA'] = validacionFormularios::comprobarAlfaNumerico($_POST['passwordA'], 20, 1, REQUIRED);
        $aErrores['ePasswordN'] = validacionFormularios::comprobarAlfaNumerico($_POST['passwordN'], 20, 1, REQUIRED);
        $aErrores['ePasswordC'] = validacionFormularios::comprobarAlfaNumerico($_POST['passwordC'], 20, 1, REQUIRED);

        $HASHPassword = hash('sha256', $_SESSION['usuarioDAW218LogInLogOutTema5'] . $_POST['passwordA']);
        if ($HASHPassword !== $passwordActual) {
            $aErrores['ePasswordA'] = "¡Contraseña incorrecta!";
        }

        if ($_POST['passwordN'] !== $_POST['passwordC']) {
            $aErrores['ePasswordC'] = "¡No has introducido lo mismo!";
        }

        //recorremos el array de posibles errores (aErrores), si hay alguno, el campo se limpia y entradaOK es falsa (se vuelve a cargar el formulario)
        foreach ($aErrores as $campo => $validacion) {
            if ($validacion != null) {
                $entradaOK = false;
            }
        }
    } else { // sino se pulsa 'enviar'
        $entradaOK = false;
    }

    if ($entradaOK) {
        $consultaActulizar = "UPDATE T01_Usuario SET T01_Password = :passwordNueva WHERE T01_CodUsuario = :codigo";
        //Preparación de la consulta preparada
        $actualizarUsuario = $oConexionPDO->prepare($consultaActulizar);


        $HASHNUEVAPassword = hash('sha256', $_SESSION['usuarioDAW218LogInLogOutTema5'] . $_POST['passwordN']);
        //Insertamos los datos en la consulta preparada
        $actualizarUsuario->bindParam(':passwordNueva', $HASHNUEVAPassword);
        $actualizarUsuario->bindParam(':codigo', $_SESSION['usuarioDAW218LogInLogOutTema5']);

        //Se ejecuta la consulta preparada
        $actualizarUsuario->execute();
        header('Location: editarPerfil.php');
    } else {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>Editar - Login</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
                <link href="../webroot/css/styleLoginLogoff.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>   
                <header>
                    <h1 id="titulo">Editar Usuario</h1>
                </header>
                <form id="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset>

                        </div>
                        <!-----------------PASSWORD ACTUAL----------------->
                        <div class="required">
                            <label for="passwordA">Contraseña Actual:</label>
                            <input type="password" name="passwordA" placeholder="Contraseña actual del usuario" value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['ePasswordA'] == null && isset($_POST['passwordA'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['passwordA'];
                            }
                            ?>"/>

                            <?php
                            //si hay error en este campo
                            if ($aErrores['ePasswordA'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['ePasswordA'] .
                                '</div>';
                            }
                            ?>
                            <!-----------------PASSWORD NUEVA----------------->
                            <div class="required">
                                <label for="passwordN">Contraseña Nueva:</label>
                                <input type="password" name="passwordN" placeholder="Contraseña nueva del usuario" value="<?php
                                //si no hay error y se ha insertado un valor en el campo con anterioridad
                                if ($aErrores['ePasswordN'] == null && isset($_POST['passwordN'])) {

                                    //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                    //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                    echo $_POST['passwordN'];
                                }
                                ?>"/>

                                <?php
                                //si hay error en este campo
                                if ($aErrores['ePasswordN'] != NULL) {
                                    echo "<div class='errores'>" .
                                    //se muestra dicho error
                                    $aErrores['ePasswordN'] .
                                    '</div>';
                                }
                                ?>
                                <!-----------------PASSWORD NUEVA CONFIRMACIÓN----------------->
                                <div class="required">
                                    <label for="passwordC">Repite la contraseña nueva:</label>
                                    <input type="password" name="passwordC" placeholder="Contraseña nueva del usuario" value="<?php
                                    //si no hay error y se ha insertado un valor en el campo con anterioridad
                                    if ($aErrores['ePasswordC'] == null && isset($_POST['passwordC'])) {

                                        //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                        //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                        echo $_POST['passwordC'];
                                    }
                                    ?>"/>

                                    <?php
                                    //si hay error en este campo
                                    if ($aErrores['ePasswordC'] != NULL) {
                                        echo "<div class='errores'>" .
                                        //se muestra dicho error
                                        $aErrores['ePasswordC'] .
                                        '</div>';
                                    }
                                    ?>

                                </div>
                            </div>
                            <input type="submit" name="enviar" value="Aceptar" />
                            <input type="submit" name="cancelar" value="Cancelar" />
                    </fieldset>
                </form>
            </body>
            <?php
        }
    } catch (PDOException $excepcionPDO) {
        echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
        echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
    } finally {
        unset($oConexionPDO); //destruimos el objeto  
    }
    ?>
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