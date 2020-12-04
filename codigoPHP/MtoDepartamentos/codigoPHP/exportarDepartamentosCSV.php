<?php

/**
 *  Página web que toma datos (código y descripción) de la tabla Departamento y guarda en un fichero departamento.csv. 
 * (COPIA DE SEGURIDAD / EXPORTAR). El fichero exportado se encuentra en el directorio .../tmp/ del servidor [PDO]
 * @version 1.0.0
 * @since 10-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once '../config/confDBPDO.php';


try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo confDBPDO.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
    //Sacamos las tablas de la base de datos
    $consultaMostrarTablas = "SHOW TABLES";
    $sacarTablas = $oConexionPDO->prepare($consultaMostrarTablas);
    $sacarTablas->execute();

    $aDatos = [];
    $archivoCSV = fopen('../../tmp/departamentosCSV.csv', 'w');
    $contador = 0; //este contador nos ayudara a numerar los diferentes departamentos
    while ($tabla = $sacarTablas->fetch()) { //se recorren todas las tablas
        $consultarTablas = "SELECT * FROM " . $tabla[$contador]; //sacamos todos los registros de la tabla
        $sacarDatosTablas = $oConexionPDO->prepare($consultarTablas);
        $sacarDatosTablas->execute();

        while ($registroTabla = $sacarDatosTablas->fetch(PDO::FETCH_ASSOC)) { //recorremos cada registro
            array_push($aDatos, $registroTabla);
        }

        $contador++;
    }
    foreach ($aDatos as $campos) {
        fputcsv($archivoCSV, $campos);
    }

    $sacarTablas->closeCursor();
    fclose($archivoCSV);

    $fichero = '../../tmp/departamentosCSV.csv';
    if (file_exists($fichero)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fichero) . '"');
        header('Content-Length: ' . filesize($fichero));
        readfile($fichero);
        exit;
    } else {
        echo "<p style='color:red;'>Error al obtener el archivo CSV</p>"; //Muestra el mesaje de error
    }
} catch (PDOException $errorConexion) {
    echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
    echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
} finally {
    unset($oConexionPDO); //destruimos el objeto
}
?>