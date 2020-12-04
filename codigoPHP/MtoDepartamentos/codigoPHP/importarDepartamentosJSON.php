<?php

/**
 * Página web que toma datos (código y descripción) de un fichero json y los añade a la tabla 
 * Departamento de nuestra base de datos. (IMPORTAR) [PDO]
 * 
 * @version 1.0.0
 * @since 09-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once '../config/confDBPDO.php';
try {
    $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo confDBPDO.php
    $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones


    $nombreArchivo = "../../tmp/departamentosJSON.json";
    $doc_JSON = file_get_contents($nombreArchivo);
    if ($doc_JSON) {
        $oConexionPDO->beginTransaction(); //empezamos la transacción
        $aJSON = json_decode($doc_JSON);
        //PASO PREVIO: BORRAR LA TABLA DEPARTAMENTOS (esto para que se realice siempre la importación)
        $consultaBorrarDepartamento = "DROP TABLE IF EXISTS Departamento";
        $borrarDepartamento = $oConexionPDO->prepare($consultaBorrarDepartamento);
        $borrarDepartamento->execute();
        $borrarDepartamento->closeCursor();


        $consultaCrearTabla = "CREATE TABLE IF NOT EXISTS Departamento (
                                                CodDepartamento CHAR(3) PRIMARY KEY,
                                                DescDepartamento VARCHAR(255) NOT NULL,
                                                VolumenNegocio FLOAT NOT NULL,
                                                FechaBaja DATE 
                                            )  ENGINE=INNODB;";
        $crearTabla = $oConexionPDO->prepare($consultaCrearTabla);
        $crearTabla->execute(); //creamos la tabla de la copia de seguridad
        $crearTabla->closeCursor();



        $consultaInsertar = "INSERT INTO Departamento VALUES (:codigo, :descripcion, :volumen, :fecha)";
        $insertarDepartamento = $oConexionPDO->prepare($consultaInsertar);
        for ($nDepartamento = 0; $nDepartamento < count($aJSON); $nDepartamento++) { //recorremos los elementos
            $insertarDepartamento->bindParam('codigo', $aJSON[$nDepartamento]->CodDepartamento);
            $insertarDepartamento->bindParam(':descripcion', $aJSON[$nDepartamento]->DescDepartamento);
            $insertarDepartamento->bindParam(':volumen', $aJSON[$nDepartamento]->VolumenNegocio);
            if (empty($aJSON[$nDepartamento]->FechaBaja)) {
                $aJSON[$nDepartamento]->FechaBaja = null;
            }
            $insertarDepartamento->bindParam(':fecha', $aJSON[$nDepartamento]->FechaBaja);

            $insertarDepartamento->execute();
        }
        $insertarDepartamento->closeCursor();

        $oConexionPDO->commit(); //se ejecuta la transacción
        header('Location: MtoDepartamentos.php'); //redireccionamiento a la página principal
    } else {
        exit('Error abriendo departamentosJSON.json.');
    }
} catch (PDOException $excepcionPDO) {
    $oConexionPDO->rollBack();
    echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
    echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
} finally {
    unset($oConexionPDO); //destruimos el objeto
}
?>