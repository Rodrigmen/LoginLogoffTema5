<?php
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
        'eCodigo' => null,
        'eNombre' => null,
        'ePassword' => null,
        'eRPassword' => null
    ];


    if (isset($_POST['cancelar'])) {
        header('Location: ../login.php');
    }

    if (isset($_POST['enviar'])) { //si se pulsa 'enviar' (input name="enviar")
        //Validación de los campos (el resultado de la validación se mete en el array aErrores para comprobar posteriormente si da error)
        $aErrores['eCodigo'] = validacionFormularios::comprobarAlfaNumerico($_POST['codigo'], 15, 3, REQUIRED);

        $consultaUsuario = "SELECT T01_CodUsuario FROM T01_Usuario WHERE (T01_CodUsuario = :codigo)";
        //Preparación de la consulta preparada
        $buscarUsuario = $oConexionPDO->prepare($consultaUsuario);


        //Insertamos los datos en la consulta preparada
        $buscarUsuario->bindParam(':codigo', $_POST['codigo']);

        //Se ejecuta la consulta preparada
        $buscarUsuario->execute();

        $NumUsuarios = $buscarUsuario->rowCount();
        if ($NumUsuarios === 1) {
            $aErrores['eCodigo'] = "¡Código ya EXISTENTE!";
        }

        $aErrores['eNombre'] = validacionFormularios::comprobarAlfabetico($_POST['nombre'], 25, 3, REQUIRED);
        $aErrores['ePassword'] = validacionFormularios::comprobarAlfaNumerico($_POST['password'], 20, 1, REQUIRED);
        $aErrores['eRPassword'] = validacionFormularios::comprobarAlfaNumerico($_POST['Rpassword'], 20, 1, REQUIRED);

        if ($_POST['password'] !== $_POST['Rpassword']) {
            $aErrores['eRPassword'] = "No es la misma contraseña";
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

        //Creación de la consulta preparada (solo se cogen los datos necesarios para pasarlos a la sesion y con los que haremos próximas consultas)
        $consultaCrear = "INSERT INTO T01_Usuario(T01_CodUsuario, T01_DescUsuario, T01_Password, T01_FechaHoraUltimaConexion, T01_NumConexiones) "
                . "VALUES (:codigo, :descripcion, :password, :fecha, :numconex)";

        $HASHPassword = hash('sha256', $_POST['codigo'] . $_POST['password']);
        $fechaActual = new DateTime(); //creamos una variable con la fecha actual
        $tiempo = $fechaActual->getTimestamp(); //sacamos su timestamp
        $primeraConex = 1;
        //Preparación de la consulta preparada
        $crearUsuario = $oConexionPDO->prepare($consultaCrear);

        //Insertamos los datos en la consulta preparada
        $crearUsuario->bindParam(':codigo', $_POST['codigo']);
        $crearUsuario->bindParam(':descripcion', $_POST['nombre']);
        $crearUsuario->bindParam(':password', $HASHPassword);
        $crearUsuario->bindParam(':fecha', $tiempo);
        $crearUsuario->bindParam(':numconex', $primeraConex);

        //Se ejecuta la consulta preparada
        $crearUsuario->execute();

        session_start(); // se inicia la sesión
        $_SESSION['usuarioDAW218LogInLogOutTema5'] = $_POST['codigo'];
        $_SESSION['FechaHoraUltimaconexionAnterior'] = $tiempo;
        setcookie("language", "spanish", 0, "/proyectoDWES/proyectoTema5/LoginLogoffTema5/codigoPHP");
        header('Location: programa.php');
    } else {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>Registro - Login</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
                <link href="../webroot/css/styleLoginLogoff.css" rel="stylesheet" type="text/css"/>
            </head>
            <body>   
                <header>
                    <h1 id="titulo">Nuevo Usuario</h1>
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
                        <!-----------------NOMBRE [DESCRIPCIÓN]----------------->
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

                                <!-----------------REPETIR PASSWORD----------------->
                                <div class="required">
                                    <label for="Rpassword">Repetir contraseña:</label>
                                    <input type="password" name="Rpassword" placeholder="Repite la contraseña introducida anteriormente" value="<?php
                                    //si no hay error y se ha insertado un valor en el campo con anterioridad
                                    if ($aErrores['eRPassword'] == null && isset($_POST['Rpassword'])) {

                                        //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                        //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                        echo $_POST['Rpassword'];
                                    }
                                    ?>"/>

                                    <?php
                                    //si hay error en este campo
                                    if ($aErrores['eRPassword'] != NULL) {
                                        echo "<div class='errores'>" .
                                        //se muestra dicho error
                                        $aErrores['eRPassword'] .
                                        '</div>';
                                    }
                                    ?>

                                </div>
                            </div>
                            <input type="submit" name="enviar" value="Registrarse" />
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