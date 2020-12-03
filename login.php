<?php
/**
 * Formulario para logearte
 * 
 * @version 1.0.0
 * @since 02-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once 'config/confDB.php'; //requerimos una vez el archivo de configuración donde tenemos los datos necesarios para establecer la conexión con la base de datos
try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
    //Requerimos una vez la libreria de validaciones
    require_once 'core/libreriaValidacion.php';

    //Creamos una variable boleana para definir cuando esta bien o mal rellenado el formulario
    $entradaOK = true;

    //Creamos dos constantes: 'REQUIRED' indica si un campo es obligatorio (tiene que tener algun valor); 'OPTIONAL' indica que un campo no es obligatorio
    define('REQUIRED', 1);
    define('OPTIONAL', 0);

    //Array que contiene los posibles errores de los campos del formulario
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

            /* ----COOKIE----- */
            //creación de la cookie (su valor se pasara a 'programa.php' para identificar el idioma en el que aparecera la información)
            //setcookie(nombre, valor, expires, path, domain, secure, options, httponly);
            //name->nombre de la cookie
            //valor->el valor de la cookie
            //expires->el tiempo en que la cookie expira (0 = cuando se cierra la sesión)
            //path->la ruta dentro del servidor en la que la cookie estará disponible
            //domain->el (sub)dominio al que la cookie está disponible
            //secure->[boolean] cuando es TRUE la cookie será accesible sólo a través del protocolo HTTP
            //httponly->[boolean] cuando es TRUE la cookie será accesible sólo a través del protocolo HTTP
            //NOTA: Si quieres mantener la misma cookie por varios archivos en diferentes directorios (como 'login.php' y 'programa.php') el path (ruta) y el domain (dominio) tienen que ser el mismo
            setcookie("language", "spanish", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");

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
                        <input type="submit" name="enviar" value="Iniciar Sesión" />
                        <input type="submit" name="registrarse" value="¿Eres nuevo? Registrate aquí" />
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
