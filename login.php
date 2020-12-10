<?php
/**
 * Formulario para logearte
 * 
 * @version 2.0.0
 * @since 10-12-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once 'config/confDB.php'; //ARCHIVO DE CONFIGURACIÓN
try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); 
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    
    require_once 'core/libreriaValidacion.php'; //ARCHIVO QUE CONTIENE LAS VALIDACIONES PARA EL FORMULARIO

    $entradaOK = true; //COMPROBADOR 

    //CONSTANTES
    define('REQUIRED', 1);
    define('OPTIONAL', 0);

    //ARRAY DE POSIBLES ERRORES EN EL FORMULARIO
    $aErrores = [
        'eCodigo' => null,
        'ePassword' => null
    ];
    if (isset($_POST['registrarse'])) {
        header('Location: codigoPHP/registro.php');
    }
    if (isset($_POST['enviar'])) { //si se pulsa 'enviar' (input name="enviar")
        //Validación de los campos (el resultado de la validación se mete en el array aErrores para comprobar posteriormente si da error)
        $aErrores['eCodigo'] = validacionFormularios::comprobarAlfaNumerico($_POST['codigo'], 15, 3, REQUIRED);
        $aErrores['ePassword'] = validacionFormularios::comprobarAlfaNumerico($_POST['password'], 20, 1, REQUIRED);

        //recorremos el array de posibles errores (aErrores), si hay alguno, el campo se limpia y entradaOK es falsa (se vuelve a cargar el formulario)
        foreach ($aErrores as $campo => $validacion) {
            if ($validacion != null) {
                $entradaOK = false;
            }
        }
    } else { // sino se pulsa 'enviar'
        $entradaOK = false;
    }

    if ($entradaOK) { //si el formulario esta bien rellenado
        session_start(); // se inicia la sesión
        //Creación de la consulta preparada (solo se cogen los datos necesarios para pasarlos a la sesion y con los que haremos próximas consultas)
        $consultaUsuario = "SELECT T01_CodUsuario, T01_FechaHoraUltimaConexion FROM T01_Usuario WHERE (T01_CodUsuario = :codigo) AND  (T01_Password  = :password)";
        //Preparación de la consulta preparada
        $buscarUsuario = $oConexionPDO->prepare($consultaUsuario);

        //Creación de la contraseña mediante concatenación y el hash(codificación)
        $HASHPassword = hash('sha256', $_POST['codigo'] . $_POST['password']);

        //Insertamos los datos en la consulta preparada
        $buscarUsuario->bindParam(':codigo', $_POST['codigo']);
        $buscarUsuario->bindParam(':password', $HASHPassword);

        //Se ejecuta la consulta preparada
        $buscarUsuario->execute();

        $NumUsuarios = $buscarUsuario->rowCount(); //se suenta el número de resultados

        if ($NumUsuarios === 1) { //si existe solo un usuario con ese código y esa contraseña, es correcto
            $oUsuario = $buscarUsuario->fetchObject(); //se recorre el resultado como un objeto
            //se sacan los datos del objeto [de la base de datos] y se insertan en la sesión actual (actúa como un array asociativo)
            //NOTA: SE INSERTAN PRIMERO EN LA SESIÓN LOS DATOS DE LA BASE DE DATOS Y LUEGO SE ACTULIZAN, COGIENDO LOS DATOS ANTERIORES A LA ACTUALIZACIÓN
            $_SESSION['usuarioDAW218LogInLogOutTema5'] = $oUsuario->T01_CodUsuario;
            $_SESSION['FechaHoraUltimaconexionAnterior'] = $oUsuario->T01_FechaHoraUltimaConexion;

            //Consulta preparada -> Actualizar el número de conexiones en la BASE DE DATOS
            $consultaActualizar = "UPDATE T01_Usuario SET T01_NumConexiones = T01_NumConexiones + 1 WHERE (T01_CodUsuario = :codigo)";
            $actualizarNumConex = $oConexionPDO->prepare($consultaActualizar);
            $actualizarNumConex->bindParam(':codigo', $oUsuario->T01_CodUsuario);
            $actualizarNumConex->execute();

            //Consulta preparada -> Actualizar la fecha de la última conexion en la BASE DE DATOS
            $fechaActual = new DateTime(); //creamos una variable con la fecha actual
            $tiempo = $fechaActual->getTimestamp(); //sacamos su timestamp

            $consultaActualizar2 = "UPDATE T01_Usuario SET T01_FechaHoraUltimaConexion = $tiempo WHERE T01_CodUsuario = :codigo";
            $actualizarFecha = $oConexionPDO->prepare($consultaActualizar2);
            $actualizarFecha->bindParam(':codigo', $oUsuario->T01_CodUsuario);
            $actualizarFecha->execute();

            header('Location: codigoPHP/programa.php'); //redireccionamiento a la página principal 
        } else { //sino existe ningún usuario con esos datos, es incorrecto
            header('Location: login.php'); //redireccionamiento a la página principal
        }
        $buscarUsuario->closeCursor();
    } else { // si el formulario no esta correctamente rellenado (campos vacios o valores introducidos incorrectos) o no se ha rellenado nunca
        //FORMULARIO
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>Login - LoginLogoff</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="webroot/css/styleLoginLogoff.css" rel="stylesheet" type="text/css"/>
                <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg"/>

            </head>
            <body>
                <header>
                    <a href="../indexProyectoTema5.php">
                        <img class="imgprinc" src="webroot/css/images/flechaatras.png" alt="Atrás" title="Atrás"/>
                    </a>
                    <a href="../../../../index.html">
                        <img class="imgprinc" id="casa" src="webroot/css/images/inicio.png" alt="Página Principal" title="Página Principal"/>
                    </a>
                    <h1 id="titulo">Inicio de sesión</h1>
                </header>
                <form id="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset>

                        <!-----------------CÓDIGO----------------->
                        <div class="required">
                            <label for="codigo">Código:</label>
                            <input type="text" name="codigo"  placeholder="Código de usuario" value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['eCodigo'] == null && isset($_POST['codigo'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['codigo'];
                            }
                            ?>"/>

                            <?php
                            //si hay error en este campo
                            if ($aErrores['eCodigo'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['eCodigo'] .
                                '</div>';
                            }
                            ?>
                        </div>

                        <!-----------------PASSWORD----------------->
                        <div class="required">
                            <label for="password">Contraseña:</label>
                            <input type="password" name="password" placeholder="Contraseña del usuario" value="<?php
                            if ($aErrores['ePassword'] == null && isset($_POST['password'])) {
                                echo $_POST['password'];
                            }
                            ?>"/>

                            <?php
                            if ($aErrores['ePassword'] != NULL) {
                                echo "<div class='errores'>" .
                                $aErrores['ePassword'] .
                                '</div>';
                            }
                            ?>
                        </div>
                        <input type="submit" name="enviar" value="Iniciar Sesión" />
                        <input type="submit" name="registrarse" value="¿Eres nuevo? Registrate aquí" />
                    </fieldset>
                </form>
                <?php
            }
        } catch (PDOException $excepcionPDO) {
            echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //MENSAJE DE ERROR
            echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; //CÓDIGO DE ERROR
        } finally {
            unset($oConexionPDO); //DESTRUCCIÓN DE LA CONEXIÓN CON LA BASE DE DATOS
        }
        ?>

    </body>
    <footer>
        <ul>
            <li>&copy2020-2021 | Rodrigo Robles Miñambres</li>
            <li>
                <a target="_blank" href="https://github.com/Rodrigmen/LoginLogoffTema5/tree/master">
                    <img id="imggit" title="GitHub" src="webroot/css/images/github.png"  alt="GITHUB">
                </a>
            </li>
        </ul>            
    </footer>

</html>       