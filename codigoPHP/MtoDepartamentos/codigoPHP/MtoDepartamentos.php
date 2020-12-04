<?php
session_start(); //se recupera la sesión
if (!isset($_SESSION['usuarioDAW218LogInLogOutTema5'])) { //si la sesión no tiene información (es decir, no esta creada la sesión correctamente = no te has logeado), se devuelve automáticamente a la página de login
    header('Location: ../../../login.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MtoDepartamentosTema5 - DWES</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/styleMtoDepartamentosTema4.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg"/>
    </head>
    <body>
        <header>
            <a href="../../../login.php.php">
                <img class="imgprinc" src="../webroot/css/images/flechaatras.png" alt="Atrás" title="Atrás"/>
            </a>
            <h1 id="titulo">Mantenimiento de Departamentos</h1>
        </header>
        <?php
        /**
         * Página principal de Mantenimiento de Departamentos tema 5
         * 
         * @version 1.0.0
         * @since 04-12-2020
         * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
         */
        require_once '../config/confDBPDO.php';
        try {
            $oConexionPDO = new PDO(DSN, USER, PASSWORD, CHARSET); //creo el objeto PDO con las constantes iniciadas en el archivo datosBD.php
            $oConexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //le damos este atributo a la conexión (la configuramos) para poder utilizar las excepciones
            //Requerimos una vez la libreria de validaciones
            require_once '../core/201020libreriaValidacion.php';
            ?>
            <form id="formulario" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <fieldset>
                    <!-----------------DESCRIPCIÓN----------------->
                    <div class="required">
                        <label for="codigo">Descripción: </label>
                        <input type="search" name="descripcion" placeholder="Departamento de..." value="<?php
                        if (isset($_POST['descripcion'])) {
                            echo $_POST['descripcion'];
                        }
                        ?>"/>
                        <input type="submit" name="buscar" value="Buscar" />
                    </div>     
                    <a href="altaDepartamento.php"> <input type="button" name="Insertar Departemento" value="Insertar Departemento"></a>
                    <a href="mostrarCodigo.php"> <input type="button" name="Ver Código" value="Ver código"></a>
                    <label for="exportar">Exportar:</label>
                    <select name="exportar"  onchange="location = this.value;">
                        <option value="#">Elige una opción:</option>
                        <option value="exportarDepartamentosXML.php">XML</option>
                        <option value="exportarDepartamentosJSON.php">JSON</option>
                        <option value="exportarDepartamentosCSV.php">CSV</option>
                    </select>

                    <label for="importar">Importar:</label>
                    <select name="importar"  onchange="location = this.value;">
                        <option value=#>Elige una opción:</option>
                        <option value="importarDepartamentosXML.php">XML</option>
                        <option value="importarDepartamentosJSON.php">JSON</option>
                        <option value="importarDepartamentosCSV.php">CSV</option>
                    </select>
                </fieldset>
            </form>
            <?php
            $descripcionBuscada = "";
            if (isset($_POST['buscar'])) {
                $descripcionBuscada = $_POST['descripcion'];
            }
            //Consulta preparada a la base de datos
            $consultaBuscar = "SELECT * FROM T02_Departamento WHERE T02_DescDepartamento LIKE CONCAT('%', :descripcion, '%')";
            $buscarDepartamento = $oConexionPDO->prepare($consultaBuscar);
            //Inserción de datos en al consulta
            $buscarDepartamento->bindParam(':descripcion', $descripcionBuscada);
            //Ejecución
            $buscarDepartamento->execute();
            $numeroDepartamentos = $buscarDepartamento->rowCount(); //número de departamentos encontrados
            if ($numeroDepartamentos !== 0) { //si si se encuentran departamentos, se muestran
                echo "<table>"
                . "<thead>"
                . "<tr>"
                . "<th>Código</th>"
                . "<th> </th>"
                . "<th>Descripción</th>"
                . "<th> </th>"
                . "<th>Volumen de negocio</th>"
                . "<th> </th>"
                . "<th>Fecha de baja</th>"
                . "<th> </th>"
                . "<th>Opciones</th>"
                . "</tr>"
                . "</thead>"
                . "<tbody>";
                while ($departamento = $buscarDepartamento->fetch(PDO::FETCH_OBJ)) {
                    $habilitado = true;
                    $codigoDep = $departamento->T02_CodDepartamento;
                    $descDep = $departamento->T02_DescDepartamento;
                    $volDep = $departamento->T02_VolumenNegocio;
                    $fechaDep = $departamento->T02_FechaBajaDepartamento;
                    if (!is_null($fechaDep)) {
                        $habilitado = false;
                    }
                    if ($habilitado === false) {
                        echo "<tr style='background-color:#FA6D5C;'>";
                    } else {
                        echo "<tr>";
                    }
                    echo "<td>$codigoDep</td>"
                    . "<td>$descDep</td>"
                    . "<td>$volDep</td>"
                    . "<td>";
                    if (is_null($fechaDep)) {
                        echo " ";
                    } else {
                        echo $fechaDep;
                    }
                    echo "</td>";
                    //OPCIONES POSIBLES CON EL DEPARTAMENTO
                    ?>
                <td>
                    <a href="editarDepartamento.php?codigoDep=<?php echo $codigoDep ?>">
                        <img class="imgejer" src="../webroot/css/images/editar.png"  alt="Editar" title="Editar"/>
                    </a>
                </td>
                <td>
                    <a href="mostrarDepartamento.php?codigoDep=<?php echo $codigoDep ?>">
                        <img class="imgejer" src="../webroot/css/images/analitica.png"alt="Ver datos" title="Ver datos"/>
                    </a>
                </td>
                <td>
                    <a href="bajaDepartamento.php?codigoDep=<?php echo $codigoDep ?>">
                        <img class="imgejer" src="../webroot/css/images/eliminar.png"alt="Eliminar" title="Eliminar"/>
                    </a>
                </td>
                <?php
                echo '<td>';
                //OPCIOÓN DE HABILITACIÓN O INHABILITACION DEL DEPARTAMENTO (SEGÚN SI ESTA HABILITADO O NO)
                if ($habilitado) {
                    ?>
                    <a href="bajaLogicaDepartamento.php?codigoDep=<?php echo $codigoDep ?>">
                        <img class="imgejer" src="../webroot/css/images/habilitar.png"  alt="Inhabilitar" title="Inhabilitar"/>
                    </a>
                    <?php
                } else {
                    ?>
                    <a href="rehabilitarDepartamento.php?codigoDep=<?php echo $codigoDep ?>">
                        <img class="imgejer" src="../webroot/css/images/inhabilitar.png"  alt="HAbilitar" title="Habilitar"/>
                    </a>
                    <?php
                }
                echo '</td>';
                echo "</tr>";
            }
            echo "</tbody>"
            . "</table>";
        } else {  //si no encontramos ningún departamento, lo notificamos al usuario
            echo "<h4>¡No hay ningún departamento con esa descripción!</h4>";
        }
    } catch (PDOException $excepcionPDO) {
        echo "<p style='color:red;'>Mensaje de error: " . $excepcionPDO->getMessage() . "</p>"; //Muestra el mesaje de error
        echo "<p style='color:red;'>Código de error: " . $excepcionPDO->getCode() . "</p>"; // Muestra el codigo del error
    } finally {
        unset($oConexionPDO); //destruimos el objeto  
    }
    ?>
</body>
<footer>
    <ul>
        <li>&copy2020-2021 | Rodrigo Robles Miñambres</li>
        <li>
            <a target="_blank" href="https://github.com/Rodrigmen/MtoDepartamentosTema4/tree/developer">
                <img class="imgprinc" title="GitHub" src="../webroot/css/images/github.png"  alt="GITHUB">
            </a>
        </li>
    </ul>            
</footer>
</html>