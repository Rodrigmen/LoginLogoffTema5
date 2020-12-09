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
        'eNombre' => null
    ];


    if (isset($_POST['cancelar'])) {
        header('Location: programa.php');
    }
    if (isset($_POST['cambiarPassword'])) {
        header('Location: cambiarPassword.php');
    }

    if (isset($_POST['eliminar'])) {
        $consultaBorrar = "DELETE FROM T01_Usuario WHERE T01_CodUsuario = :codigo";
        //Preparación de la consulta preparada
        $borrarUsuario = $oConexionPDO->prepare($consultaBorrar);


        //Insertamos los datos en la consulta preparada
        $borrarUsuario->bindParam(':codigo', $_SESSION['usuarioDAW218LogInLogOutTema5']);

        //Se ejecuta la consulta preparada
        $borrarUsuario->execute();
        header('Location: ../login.php');
    }

    //Consulta para sacar la descripcion actual del usuario
    $consultaUsuario = "SELECT T01_DescUsuario, T01_Perfil, T01_NumConexiones FROM T01_Usuario WHERE (T01_CodUsuario = :codigo)";
    //Preparación de la consulta preparada
    $buscarUsuario = $oConexionPDO->prepare($consultaUsuario);


    //Insertamos los datos en la consulta preparada
    $buscarUsuario->bindParam(':codigo', $_SESSION['usuarioDAW218LogInLogOutTema5']);

    //Se ejecuta la consulta preparada
    $buscarUsuario->execute();
    $oUsuario = $buscarUsuario->fetchObject();
    $descripcionUser = $oUsuario->T01_DescUsuario;
    $perfilUser = $oUsuario->T01_Perfil;
    $NumConexionesUser = $oUsuario->T01_NumConexiones;
    $fecha = new DateTime();
    $fecha->setTimestamp($_SESSION['FechaHoraUltimaconexionAnterior']);
    $fechaFormateada = $fecha->format("Y-m-d H:i:s");

    if (isset($_POST['enviar'])) { //si se pulsa 'enviar' (input name="enviar")
        //Validación de los campos (el resultado de la validación se mete en el array aErrores para comprobar posteriormente si da error)
        $aErrores['eNombre'] = validacionFormularios::comprobarAlfabetico($_POST['nombre'], 25, 3, REQUIRED);
        if ($_POST['nombre'] === $descripcionUser) {
            $aErrores['eNombre'] = "¡No puedes introducir el mismo!";
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
        $consultaActulizar = "UPDATE T01_Usuario SET T01_DescUsuario = :descripcionNueva WHERE T01_CodUsuario = :codigo";
        //Preparación de la consulta preparada
        $actualizarUsuario = $oConexionPDO->prepare($consultaActulizar);


        //Insertamos los datos en la consulta preparada
        $actualizarUsuario->bindParam(':descripcionNueva', $_POST['nombre']);
        $actualizarUsuario->bindParam(':codigo', $_SESSION['usuarioDAW218LogInLogOutTema5']);

        //Se ejecuta la consulta preparada
        $actualizarUsuario->execute();
        header('Location: programa.php');
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

                        <!-----------------CÓDIGO----------------->
                        <div class="required">
                            <label for="codigo">Código:</label>
                            <input type="text" name="codigo" value="<?php echo $_SESSION['usuarioDAW218LogInLogOutTema5']; ?>" readonly/>
                        </div>
                        <!-----------------NOMBRE [DESCRIPCIÓN] ACTUAL ----------------->
                        <div class="required">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre" value="<?php echo $descripcionUser; ?>"/>
                            <?php
                            //si hay error en este campo
                            if ($aErrores['eNombre'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['eNombre'] .
                                '</div>';
                            }
                            ?>
                            <!-----------------PERFIL----------------->
                            <div class="required">
                                <label for="perfil">Perfil:</label>
                                <input type="text" name="perfil" value="<?php echo $perfilUser; ?>" readonly/>
                            </div>

                            <!-----------------NÚMERO DE CONEXIONES----------------->
                            <div class="required">
                                <label for="conexiones">Número de conexiones:</label>
                                <input type="number" name="conexiones" value="<?php echo $NumConexionesUser; ?>" readonly/>
                            </div>

                            <!-----------------ÛLTIMA FECHA DE CONEXION----------------->
                            <div class="required">
                                <label for="ult">Última fecha de conexión:</label>
                                <input type="datetime" name="ult" value="<?php echo $fechaFormateada ?>" readonly/>
                            </div>

                        </div>
                        </div>
                        <input type="submit" name="enviar" value="Aceptar" />
                        <input type="submit" name="cancelar" value="Cancelar" />
                        <input type="submit" name="cambiarPassword" value="Cambiar Contraseña" />
                        <input type="submit" name="eliminar" value="Eliminar Cuenta" />
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