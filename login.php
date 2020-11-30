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
                //Inserción del departamento y mostramos el resultado
                //Creación de la consulta preparada
                $consultaInsertar = "INSERT INTO Departamento (CodDepartamento, DescDepartamento, VolumenNegocio) VALUES (:codigo, :descripcion, :volumen)";

                //Preparación de la consulta preparada
                $insertarDepartamento = $oConexionPDO->prepare($consultaInsertar);

                //Insertamos los datos en la consulta preparada
                $insertarDepartamento->bindParam(':codigo', $codigo);
                $insertarDepartamento->bindParam(':descripcion', $_POST['descripcion']);
                $insertarDepartamento->bindParam(':volumen', $_POST['volumen']);

                //Se ejecuta la consulta preparada
                $insertarDepartamento->execute();

                header('Location: codigoPHP/programa.php'); //redireccionamiento a la página principal

                $insertarDepartamento->closeCursor();
            } else { // si el formulario no esta correctamente rellenado (campos vacios o valores introducidos incorrectos) o no se ha rellenado nunca
                //formulario
                ?>
                <form id="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset>
                        <legend>Datos necesarios:</legend>

                        <!-----------------NOMBRE----------------->
                        <div class="required">
                            <label for="nombre">Nombre: </label>
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
                            <label for="password">Contraseña: </label>
                            <input type="password" name="password" value="<?php
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
                        <input type="submit" name="enviar" value="Entrar" />
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

</html>       
