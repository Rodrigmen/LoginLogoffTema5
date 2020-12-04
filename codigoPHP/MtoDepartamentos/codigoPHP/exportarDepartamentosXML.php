<?php

/**
 *  Página web que toma datos (código y descripción) de la tabla Departamento y guarda en un fichero departamento.xml. 
 * (COPIA DE SEGURIDAD / EXPORTAR). El fichero exportado se encuentra en el directorio .../tmp/ del servidor [PDO]
 * sudo chmod -R 2775 /var/www/html -> comando necesario para que el servidor tenga permisos de creación de documentos
 * @version 1.0.0
 * @since 03-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once '../config/confDBPDO.php';


try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo confDBPDO.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones

    $docXML = new DOMDocument("1.0", "utf-8"); //creamos el documento
    $docXML->formatOutput = true; //Da formato a la salida con identación y espacios extra.


    $xml_Raiz = $docXML->createElement("DAW218DBDepartamentos"); //Creamos la raiz (NOMBRE DE LA BASE DE DATOS)
    $docXML->appendChild($xml_Raiz); //lo añadimos como "hijo" del documento
    //Sacamos las tablas de la base de datos
    $consultaMostrarTablas = "SHOW TABLES";
    $sacarTablas = $oConexionPDO->prepare($consultaMostrarTablas);
    $sacarTablas->execute();

    $contadorTabla = 0; //este contador nos ayudara a numerar las diferentes tablas
    $contador = 1; //este contador nos ayudara a numerar los diferentes departamentos
    while ($tabla = $sacarTablas->fetch()) { //se recorren todas las tablas
        $xml_Tabla = $docXML->createElement($tabla[$contadorTabla]); //se crea un elemento por cada tabla de la base de datos
        $xml_Raiz->appendChild($xml_Tabla); //se añaden al elemento raiz

        $consultarTablas = "SELECT * FROM " . $tabla[$contadorTabla]; //sacamos todos los registros de la tabla
        $sacarDatosTablas = $oConexionPDO->prepare($consultarTablas);
        $sacarDatosTablas->execute();
        while ($registroTabla = $sacarDatosTablas->fetch(PDO::FETCH_ASSOC)) { //recorremos cada registro
            $xml_Datos = $docXML->createElement("DatosDepartamento$contador"); //creamos un elemento para diferenciar cada registro (cada departamento)
            $xml_Tabla->appendChild($xml_Datos); //se añaden al elemento de la tabla
            $contador++;
            foreach ($registroTabla as $key => $value) { //recorremos todos los datos de cada registro (de cada departamento)
                $xml_Key = $docXML->createElement($key); //creamos un elemento por cada dato
                $xml_Datos->appendChild($xml_Key); //se lo añadimos al elemento de los registros

                $xml_Value = $docXML->createTextNode($value); //para sacar el contenido, creamos un nodo de texto
                $xml_Key->appendChild($xml_Value); // lo añadimos al elemento del dato en concreto
            }
        }
        $contadorTabla++;
    }

    $sacarTablas->closeCursor();

    $nombreArchivo = "../../tmp/departamentos.xml"; //decidimos el directorio y el nombre del archivo
    $guardado = $docXML->save($nombreArchivo); //guardamos el archivo

    if (file_exists($nombreArchivo)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($nombreArchivo) . '"');
        header('Content-Length: ' . filesize($nombreArchivo));
        readfile($nombreArchivo);
        exit;
    } else {
        echo "<p style='color:red;'>Error al obtener el archivo XML</p>"; //Muestra el mesaje de error
    }
} catch (PDOException $errorConexion) {
    echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
    echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
} finally {
    unset($oConexionPDO); //destruimos el objeto
}
?>