<!DOCTYPE html>
<html>
    <head>
        <title>mostrarCodigo - MTO</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
        <style>
            .imgprinc{
                width:40px;
                height:40px;
                transition: 0.5s;

            }
        </style>
    </head>
    <body>
        <a href="MtoDepartamentos.php">
            <img class="imgprinc" src="../webroot/css/images/flechaatras.png" alt="Atrás" title="Atrás"/>
        </a>
        <?php
        echo '<h1>MtoDepartamentos [Página Principal]</h1>';
        $filename = "MtoDepartamentos.php";
        highlight_file($filename);

        echo '<h1>altaDepartamento [Inserción de un departamento]</h1>';
        $filename2 = "altaDepartamento.php";
        highlight_file($filename2);

        echo '<h1>bajaDepartamento [Eliminación de un departamento]</h1>';
        $filename3 = "bajaDepartamento.php";
        highlight_file($filename3);

        echo '<h1>editarDepartamento [Edición de un departamento]</h1>';
        $filename4 = "editarDepartamento.php";
        highlight_file($filename4);

        echo '<h1>mostrarDepartamento [Consulta de un departamento]</h1>';
        $filename5 = "mostrarDepartamento.php";
        highlight_file($filename5);

        echo '<h1>bajaLogicaDepartamento [Desactiva un departamento a una determinada fecha]</h1>';
        $filename6 = "bajaLogicaDepartamento.php";
        highlight_file($filename6);

        echo '<h1>rehabilitarDepartamento [Reactiva un departamento]</h1>';
        $filename7 = "rehabilitarDepartamento.php";
        highlight_file($filename7);

        echo '<h1>exportarDepartamentosXML [Exportas los datos de la tabla Departamento a local en formato XML]</h1>';
        $filename8 = "exportarDepartamentosXML.php";
        highlight_file($filename8);

        echo '<h1>importarDepartamentosXML [Importas los datos de la tabla Departamento desde el directorio /tmp en formato XML]</h1>';
        $filename9 = "importarDepartamentosXML.php";
        highlight_file($filename9);

        echo '<h1>exportarDepartamentosJSON [Exportas los datos de la tabla Departamento a local en formato JSON]</h1>';
        $filename10 = "exportarDepartamentosJSON.php";
        highlight_file($filename10);

        echo '<h1>importarDepartamentosJSON [Importas los datos de la tabla Departamento desde el directorio /tmp en formato JSON]</h1>';
        $filename11 = "importarDepartamentosJSON.php";
        highlight_file($filename11);

        echo '<h1>Configuración</h1>';
        $filename12 = "../config/confDBPDO.php";
        highlight_file($filename12);

        echo '<h1>SCRIPT DE CREACIÓN</h1>';
        $filename13 = "../scriptDB/CreaDAW218DBDepartamentos.sql";
        highlight_file($filename13);

        echo '<h1>SCRIPT DE CARGA INICIAL</h1>';
        $filename14 = "../scriptDB/CargaInicialDAW218DBDepartamentos.sql";
        highlight_file($filename14);

        echo '<h1>SCRIPT DE BORRADO</h1>';
        $filename15 = "../scriptDB/BorraDAW218DBDepartamentos.sql";
        highlight_file($filename15);
        ?>

    </body>

</html>