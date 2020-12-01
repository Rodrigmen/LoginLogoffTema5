<?php
/**
 * Formulario para logearte
 * 
 * @version 1.0.0
 * @since 30-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once 'config/confDB.php';
try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
    //Requerimos una vez la libreria de validaciones
    require_once 'core/libreriaValidacion.php';
    session_start();

    //Creamos una variable boleana para definir cuando esta bien o mal rellenado el formulario
    $entradaOK = true;

    //Creamos dos constantes: 'REQUIRED' indica si un campo es obligatorio (tiene que tener algun valor); 'OPTIONAL' indica que un campo no es obligatorio
    define('REQUIRED', 1);
    define('OPTIONAL', 0);

    //Array que contiene los posibles errores de los campos del formulario
    $aErrores = [
        'eNombre' => null,
        'ePassword' => null
    ];

    //Array que contiene los valores correctos de los campos del formulario
    $aFormulario = [
        'eNombre' => null,
        'ePassword' => null
    ];

    if (isset($_POST['enviar'])) { //si se pulsa 'enviar' (input name="enviar")
        //Validación de los campos (el resultado de la validación se mete en el array aErrores para comprobar posteriormente si da error)
        $aErrores['eNombre'] = validacionFormularios::comprobarAlfabetico($_POST['nombre'], 20, 3, REQUIRED);
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
        //Creación de la consulta preparada
        $consultaUsuario = "SELECT * FROM T01_Usuario WHERE (T01_CodUsuario = :codigo) AND  (T01_Password  = :password)";
        //Preparación de la consulta preparada
        $buscarUsuario = $oConexionPDO->prepare($consultaUsuario);

        //Insertamos los datos en la consulta preparada
        $buscarUsuario->bindParam(':codigo', $_POST['nombre']);
        $HASHPassword = hash('sha256', $_POST['nombre'] . $_POST['password']);
        $buscarUsuario->bindParam(':password', $HASHPassword);

        //Se ejecuta la consulta preparada
        $buscarUsuario->execute();

        $NumUsuarios = $buscarUsuario->rowCount(); //se suenta el número de resultados

        if ($NumUsuarios === 1) { //si existe solo un usuario con ese código y esa contraseña, es correcto
            $oUsuario = $buscarUsuario->fetchObject(); //se recorre el resultado como un objeto
            //se sacan los datos del objeto [de la base de datos] y se insertan en la sesión actual (actúa como un array asociativo)
            //NOTA: SE INSERTAN PRIMERO EN LA SESIÓN LOS DATOS DE LA BASE DE DATOS Y LUEGO SE ACTULIZAN, COGIENDO LOS DATOS ANTERIORES A LA ACTUALIZACIÓN
            $_SESSION['codigo'] = $oUsuario->T01_CodUsuario;
            $_SESSION['descripcion'] = $oUsuario->T01_DescUsuario;
            $_SESSION['perfil'] = $oUsuario->T01_Perfil;
            $_SESSION['numconex'] = $oUsuario->T01_NumConexiones;
            $_SESSION['ultimaconex'] = $oUsuario->T01_FechaHoraUltimaConexion;

            //Actualizar el número de conexiones en la BASE DE DATOS
            $consultaActualizar = "UPDATE T01_Usuario SET T01_NumConexiones = T01_NumConexiones + 1 WHERE (T01_CodUsuario = :codigo)";
            $actualizarNumConex = $oConexionPDO->prepare($consultaActualizar);
            $actualizarNumConex->bindParam(':codigo', $oUsuario->T01_CodUsuario);
            $actualizarNumConex->execute();

            //Actualizar la fecha de la última conexion en la BASE DE DATOS
            $fechaActual = new DateTime();
            $tiempo = $fechaActual->getTimestamp();

            $consultaActualizar2 = "UPDATE T01_Usuario SET T01_FechaHoraUltimaConexion = $tiempo WHERE T01_CodUsuario = :codigo";
            $actualizarFecha = $oConexionPDO->prepare($consultaActualizar2);
            $actualizarFecha->bindParam(':codigo', $_SESSION['codigo']);
            $actualizarFecha->execute();

            setcookie("language", "spanish");
            header('Location: codigoPHP/programa.php'); //redireccionamiento a la página principal 
        } else { //sino existe ningún usuario con esos datos, es incorrecto
            header('Location: login.php'); //redireccionamiento a la página principal
        }
        $buscarUsuario->closeCursor();
    } else { // si el formulario no esta correctamente rellenado (campos vacios o valores introducidos incorrectos) o no se ha rellenado nunca
        //formulario
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

                        <!-----------------NOMBRE----------------->
                        <div class="required">
                            <label for="nombre">Nombre:</label>
                            <input type="text" name="nombre"  placeholder="Nombre de usuario" value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['eNombre'] == null && isset($_POST['nombre'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['nombre'];
                            }
                            ?>"/>

                            <?php
                            //si hay error en este campo
                            if ($aErrores['eNombre'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['eNombre'] .
                                '</div>';
                            }
                            ?>
                        </div>

                        <!-----------------PASSWORD----------------->
                        <div class="required">
                            <label for="password">Contraseña:</label>
                            <input type="password" name="password" placeholder="Contraseña del usuario" value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['ePassword'] == null && isset($_POST['password'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['password'];
                            }
                            ?>"/>

                            <?php
                            //si hay error en este campo
                            if ($aErrores['ePassword'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['ePassword'] .
                                '</div>';
                            }
                            ?>
                        </div>
                        <input type="submit" name="enviar" value="Siguiente" />
                    </fieldset>
                </form>
                <?php
            }
        } catch (PDOException $excepcionPDO) {
            echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
            echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
        } finally {
            unset($oConexionPDO); //destruimos el objeto  
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
