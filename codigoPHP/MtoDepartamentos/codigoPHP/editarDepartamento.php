<?php
require_once '../config/confDBPDO.php';

try {

    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    require_once '../core/201020libreriaValidacion.php';

    /* SACAR A TRAVÉS DE UNA CONSULTA */
    $consultaBuscar = "SELECT * FROM Departamento WHERE CodDepartamento LIKE :codigo";

    $buscarDepartamento = $oConexionPDO->prepare($consultaBuscar);

    //Inserción de datos en al consulta
    $buscarDepartamento->bindParam(':codigo', $_GET["codigoDep"]);

    //Ejecución
    $buscarDepartamento->execute();

    while ($departamento = $buscarDepartamento->fetch(PDO::FETCH_OBJ)) {
        $descDep = $departamento->DescDepartamento;
        $volDep = $departamento->VolumenNegocio;
        $fechaDep = $departamento->FechaBaja;
    }
    //Creamos una variable boleana para definir cuando esta bien o mal rellenado el formulario
    $entradaOK = true;

    //Creamos dos constantes: 'REQUIRED' indica si un campo es obligatorio (tiene que tener algun valor); 'OPTIONAL' indica que un campo no es obligatorio
    define('REQUIRED', 1);
    define('OPTIONAL', 0);

    //Array que contiene los posibles errores de los campos del formulario
    $aErrores = [
        'eDescripcion' => null,
        'eVolumen' => null
    ];
    if (isset($_POST['editar'])) {

        //DESCRIPCIÓN (input type="text") [OBLIGATORIO {texto alfabetico}] 
        $aErrores['eDescripcion'] = validacionFormularios::comprobarAlfaNumerico($_POST['descripcion'], 35, 1, REQUIRED);
        //VOLUMEN DE NEGOCIO (input type="number") [OBLIGATORIO {número entero}] 
        $aErrores['eVolumen'] = validacionFormularios::comprobarEntero($_POST['volumen'], PHP_INT_MAX, 1, OPTIONAL);

        //recorremos el array de posibles errores (aErrores), si hay alguno, el campo se limpia y entradaOK es falsa (se vuelve a cargar el formulario)
        foreach ($aErrores as $campo => $validacion) {
            if ($validacion != null) {
                $entradaOK = false;
            }
        }
    } else {
        $entradaOK = false;
    }
    if ($entradaOK) {

        $consultaActualizar = "UPDATE Departamento SET DescDepartamento = :descripcion, VolumenNegocio = :volumen WHERE CodDepartamento LIKE :codigo";

        $actualizarDepartamento = $oConexionPDO->prepare($consultaActualizar);
        //Inserción de datos en al consulta
        $actualizarDepartamento->bindParam(':descripcion', $_POST["descripcion"]);
        $actualizarDepartamento->bindParam(':volumen', $_POST["volumen"]);
        $actualizarDepartamento->bindParam(':codigo', $_GET['codigoDep']);

        //Ejecución
        $actualizarDepartamento->execute();

        header('Location: MtoDepartamentos.php'); //redireccionamiento a la página principal
    } else {
        //A TRAVÉS DEL ACTION SE VUELVE A PASAR EL GET CON EL CODIGO, HACIENDO LA CONSULTA DE NUEVO
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <title>EditarDepartamento - MTO</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="../webroot/css/styleOptions.css" rel="stylesheet" type="text/css"/>
                <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg"/>
            </head>
            <body>
                <form id="formulario" action="<?php echo $_SERVER['PHP_SELF'] . '?codigoDep=' . $_GET['codigoDep']; ?>" method="post"> 
                    <fieldset>
                        <legend>Editar Departamento</legend>

                        <div class="required">
                            <label for="codigo">Código:</label>
                            <input type="text" name="codigo" value="<?php
                            echo $_GET["codigoDep"];
                            ?>" readonly/>
                        </div>
                        <!-----------------DESCRIPCIÓN----------------->
                        <div class="optional">
                            <label for="codigo">Descripción: </label>
                            <input type="text" name="descripcion" placeholder="Departamento de..." value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['eDescripcion'] == null && isset($_POST['descripcion'])) {

                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['descripcion'];
                            } else {
                                echo $descDep;
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
                        <div class="optional">
                            <label for="volumen">Volumen:</label>
                            <input type="number" name="volumen"  value="<?php
                            //si no hay error y se ha insertado un valor en el campo con anterioridad
                            if ($aErrores['eVolumen'] == null && isset($_POST['volumen'])) {
                                //se muestra dicho valor (el campo no aparece vacío si se relleno correctamente 
                                //[en el caso de que haya que se recarge el formulario por un campo mal rellenado, asi no hay que rellenarlo desde 0])
                                echo $_POST['volumen'];
                            } else {
                                echo $volDep;
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

                        <div class="required">
                            <label for="nombre">Fecha de Baja:</label>
                            <input type="text" name="fecha"  value="<?php
                            echo $fechaDep;
                            ?>" readonly/>
                        </div>
                        <?php
                    }
                } catch (PDOException $excepcionPDO) {
                    echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
                    echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
                } finally {

                    unset($oConexionPDO); //destruimos el objeto  
                }
                ?>
                <input type="submit" name="editar" value="Actualizar"/>
                <a class="botones" href="MtoDepartamentos.php"> <input type="button" name="cancelar" value="Cancelar"></a>           
            </fieldset>
        </form>
    </body>
</html>