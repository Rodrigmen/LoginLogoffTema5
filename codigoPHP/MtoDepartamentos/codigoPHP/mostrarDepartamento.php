<!DOCTYPE html>
<html>
    <head>
        <title>ConsultaDepartamento - MTO</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/styleOptions.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg"/>
    </head>
    <body>
        <?php
        require_once '../config/confDBPDO.php';

        try {
            $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
            $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            ?>
            <form id="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <fieldset>
                    <legend>Consultar Departamento</legend>

                    <div class="required">
                        <label for="codigo">Código:</label>
                        <input type="text" name="codigo" value="<?php echo $_GET["codigoDep"] ?>" readonly/>
                    </div>
                    <div class="required">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" name="descripcion" value="<?php echo $descDep ?>" readonly/>
                    </div>
                    <div class="required">
                        <label for="volumen">Volumen:</label>
                        <input type="text" name="volumen"  value="<?php echo $volDep ?>" readonly/>
                    </div>

                    <div class="required">
                        <label for="nombre">Fecha de Baja:</label>
                        <input type="text" name="fecha"  value="<?php echo $fechaDep ?>" readonly/>
                    </div>
                    <?php
                } catch (PDOException $excepcionPDO) {
                    echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
                    echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
                } finally {

                    unset($oConexionPDO); //destruimos el objeto  
                }
                ?>
                <a href="MtoDepartamentos.php"> <input type="button" name="volver" value="Volver"></a>
            </fieldset>
        </form>
    </body>
</html>