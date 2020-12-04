
<?php
/**
 * Formulario para añadir un departamento en la tabla Departamento con validación de entrada y control de errores. [PDO]
 * 
 * @version 1.0.0
 * @since 26-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once '../config/confDBPDO.php';
try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
    //Requerimos una vez la libreria de validaciones
    require_once '../core/201020libreriaValidacion.php';

    //Creamos una variable boleana para definir cuando esta bien o mal rellenado el formulario
    $entradaOK = true;

    //Creamos dos constantes: 'REQUIRED' indica si un campo es obligatorio (tiene que tener algun valor); 'OPTIONAL' indica que un campo no es obligatorio
    define('REQUIRED', 1);
    define('OPTIONAL', 0);

    //Array que contiene los posibles errores de los campos del formulario
    $aErrores = [
        'eCodigo' => null,
        'eDescripcion' => null,
        'eVolumen' => null
    ];

    //Array que contiene los valores correctos de los campos del formulario
    $aFormulario = [
        'fCodigo' => null,
        'fDescripcion' => null,
        'fVolumen' => null
    ];

    if (isset($_POST['enviar'])) { //si se pulsa 'enviar' (input name="enviar")
        //Validación de los campos (el resultado de la validación se mete en el array aErrores para comprobar posteriormente si da error)
        //CÓDIGO (input type="text") [OBLIGATORIO {texto alfabetico}] 
        $aErrores['eCodigo'] = validacionFormularios::comprobarAlfabetico($_POST['codigo'], 3, 3, REQUIRED);

        //El código es primary key, por lo que no se puede repetir. Entonces validamos la entrada para que no se intente crear un departamento con un código ya insertado
        //Se sacan mediante una consulta a la base de datos todos los códigos

        $consultaCodigos = "SELECT * FROM Departamento WHERE CodDepartamento = :codigo";
        $codigosExistentes = $oConexionPDO->prepare($consultaCodigos);
        $codigo = strtoupper($_POST['codigo']); //METEMOS EN UNA VARIABLE EL CODIGO INTRODUCIDO PASADO A MAYUSCULAS, DICHA VARIABLE SE USA PARA LA BUSQUEDA E INSERCION DEL CÓDIGO
        $codigosExistentes->bindParam(':codigo', $codigo);
        $codigosExistentes->execute();

        $numeroDepartamentos3 = $codigosExistentes->rowCount();

        if ($numeroDepartamentos3 > 0) {
            $aErrores['eCodigo'] = "¡Código ya EXISTENTE!";
        }

        //DESCRIPCIÓN (input type="text") [OBLIGATORIO {texto alfabetico}] 
        $aErrores['eDescripcion'] = validacionFormularios::comprobarAlfaNumerico($_POST['descripcion'], 35, 1, REQUIRED);
        //VOLUMEN DE NEGOCIO (input type="number") [OBLIGATORIO {número entero}] 
        $aErrores['eVolumen'] = validacionFormularios::comprobarEntero($_POST['volumen'], PHP_INT_MAX, 1, REQUIRED);

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

        header('Location: MtoDepartamentos.php'); //redireccionamiento a la página principal

        $insertarDepartamento->closeCursor();
    } else { // si el formulario no esta correctamente rellenado (campos vacios o valores introducidos incorrectos) o no se ha rellenado nunca
        //formulario
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>insertarDepartamento - MTO</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="../webroot/css/styleOptions.css" rel="stylesheet" type="text/css"/>
                <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg"/>
            </head>
            <body>
                <form id="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset>
                        <legend>Insertar Departamento</legend>

                        <!-----------------CÓDIGO----------------->
                        <div class="required">
                            <label for="codigo">Código: </label>
                            <input type="text" name="codigo"  placeholder="3 letras..." value="<?php
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

                        <!-----------------DESCRIPCIÓN----------------->
                        <div class="required">
                            <label for="codigo">Descripción: </label>
                            <input type="text" name="descripcion" placeholder="Departamento de..." value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['eDescripcion'] == null && isset($_POST['descripcion'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['descripcion'];
                            }
                            ?>"/>

                            <?php
                            //si hay error en este campo
                            if ($aErrores['eDescripcion'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['eDescripcion'] .
                                '</div>';
                            }
                            ?>
                        </div>

                        <!-----------------VOLUMEN DE NEGOCIO----------------->
                        <div class="required">
                            <label for="codigo">Volumen de negocio: </label>
                            <input type="number" name="volumen" placeholder="Más de 1" value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['eVolumen'] == null && isset($_POST['volumen'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['volumen'];
                            }
                            ?>"/>

                            <?php
                            //si hay error en este campo
                            if ($aErrores['eVolumen'] != NULL) {
                                echo "<div class='errores'>" .
                                //se muestra dicho error
                                $aErrores['eVolumen'] .
                                '</div>';
                            }
                            ?>
                        </div>
                        <input type="submit" name="enviar" value="Insertar" />
                        <a href="MtoDepartamentos.php"> <input type="button" name="cancelar" value="Cancelar"></a>  
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