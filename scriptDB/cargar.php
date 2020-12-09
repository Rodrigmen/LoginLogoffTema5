<?php
/**
 * Script de carga
 * 
 * @version 1.0.0
 * @since 26-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
require_once "../config/confDB.php"; //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 

try {
    $miDB = new PDO(DSN, USER, PASSWORD); //Instanciamos un objeto PDO y establecemos la conexión
    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Configuramos las excepciones

    $sql = ("INSERT INTO T02_Departamento(T02_CodDepartamento,T02_DescDepartamento,T02_FechaCreacionDepartamento,T02_VolumenNegocio) VALUES
    ('INF', 'Departamento de informatica', 1574772123, 50),
    ('VEN', 'Departamento de ventas', 1574772123, 800000),
    ('CON', 'Departamento de contabilidad', 1574772123, 900000),
    ('MAT', 'Departamento de matematicas', 1574772123, 80000000),
    ('CAT', 'Departamento de gatos', 1574772123, 12584631268);");



    $miDB->exec($sql);
    
    $sql2 = ("INSERT INTO T01_Usuario(T01_CodUsuario, T01_DescUsuario, T01_Password) VALUES
    ('nereaA','NereaA',SHA2('nereaApaso',256)),
    ('miguel','Miguel',SHA2('miguelpaso',256)),
    ('bea','Bea',SHA2('beapaso',256)),
    ('nereaN','NereaN',SHA2('nereaNpaso',256)),
    ('cristinaM','CristinaM',SHA2('cristinaMpaso',256)),
    ('susana','Susana',SHA2('susanapaso',256)),
    ('sonia','Sonia',SHA2('soniapaso',256)),
    ('elena','Elena',SHA2('elenapaso',256)),
    ('nacho','Nacho',SHA2('nachopaso',256)),
    ('raul','Raul',SHA2('raulpaso',256)),
    ('luis','Luis',SHA2('luispaso',256)),
    ('arkaitz','Arkaitz',SHA2('arkaitzpaso',256)),
    ('rodrigo','Rodrigo',SHA2('rodrigopaso',256)),
    ('javier','Javier',SHA2('javierpaso',256)),
    ('cristinaN','CristinaN',SHA2('cristinaNpaso',256)),
    ('heraclio','Heraclio',SHA2('heracliopaso',256)),
    ('amor','Amor',SHA2('amorpaso',256)),
    ('antonio','Antonio',SHA2('antoniopaso',256)),
    ('leticia','Leticia',SHA2('leticiapaso',256));");



    $miDB->exec($sql2);
    
    $sql3 = ("INSERT INTO T01_Usuario(T01_CodUsuario, T01_DescUsuario, T01_Password, T01_Perfil) VALUES ('admin','admin',SHA2('adminpaso',256), 'administrador');");
    $miDB->exec($sql3);  

    echo "<h3> <span style='color: green;'>" . "Tablas cargadas correctamente</span></h3>"; //Si no se ha producido ningún error nos mostrará "Conexión establecida con éxito"
} catch (PDOException $excepcion) {//Código que se ejecutará si se produce alguna excepción
    $errorExcepcion = $excepcion->getCode(); //Almacenamos el código del error de la excepción en la variable $errorExcepcion
    $mensajeExcepcion = $excepcion->getMessage(); //Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

    echo "<span style='color: red;'>Error: </span>" . $mensajeExcepcion . "<br>"; //Mostramos el mensaje de la excepción
    echo "<span style='color: red;'>Código del error: </span>" . $errorExcepcion; //Mostramos el código de la excepción
} finally {
    unset($miDB);
}
?>