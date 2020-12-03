<?php
/**
 * Mostrar el contenido de las variables superglobales y phpinfo().
 * 
 * @version 1.0.0
 * @since 30-11-2020
 * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
 */
session_start();
if (!isset($_SESSION['usuarioDAW218LogInLogOutTema5'])) {
    header('Location: ../login.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Página de detalle - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="../webroot/css/images/favicon.jpg" /> 
        <style>
            body{
                background-color: #A9C6FF;
            }
            h2{
                text-align: center;
            }

            table{
                margin: auto;    
            }
            .key{
                text-align: center;
                background: black;
                color:white;
            }
            .imgprinc{
                width:2.5%;
                height:2.5%;
                transition: 0.5s;
                float:left;

            }

            .imgprinc:hover{
                filter: drop-shadow(0 2px 5px white);

            }
            .valor{
                background: white;
            }
        </style>
    </head>
    <body>
        <header>
            <a href="programa.php">
                <img class="imgprinc" src="../webroot/css/images/flechaatras.png" alt="Atrás" title="Atrás"/>
            </a>

        </header>
        <h2>$_SERVER</h2>
        <table>
            <?php
            foreach ($_SERVER as $apartado => $valor) {
                echo '<tr> <td class="key">' . $apartado . '</td><td class="valor">' . $valor . '</td> </tr>';
            }
            echo '</table><br>';
            if (isset($_SESSION)) {
                echo "<h2>$" . "_SESSION</h2>";
                echo '<table>';
                foreach ($_SESSION as $apartado2 => $valor2) {
                    echo '<tr> <td class="key">' . $apartado2 . '</td><td class="valor">' . $valor2 . '</td> </tr>';
                }
                echo '</table><br>';
            }

            if (isset($_COOKIE)) {
                echo "<h2>$" . "_COOKIE</h2>";
                echo '<table>';
                foreach ($_COOKIE as $apartado3 => $valor3) {
                    echo '<tr> <td class="key">' . $apartado3 . '</td><td class="valor">' . $valor3 . '</td> </tr>';
                }
                echo '</table><br>';
            }

            echo "<h2>PHPINFO</h2>";
            phpinfo();
            ?>
    </body>

</html>   