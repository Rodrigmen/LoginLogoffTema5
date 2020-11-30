<!DOCTYPE html>
<html>
    <head>
        <title>ejercicio00 - DWES</title>
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
            .valor{
                background: white;
            }
        </style>
    </head>
    <body>
        <h2>Server</h2>
        <table>
            <?php
            /**
             * Mostrar el contenido de las variables superglobales y phpinfo().
             * 
             * @version 1.0.0
             * @since 23-11-2020
             * @author Rodrigo Robles <rodrigo.robmin@educa.jcyl.es>
             */
            foreach ($_SERVER as $apartado => $valor) {
                echo '<tr> <td class="key">' . $apartado . '</td><td class="valor">' . $valor . '</td> </tr>';
            }
            echo '</table><br>';
            if (isset($_SESSION)) {
                echo "<h2>Valor Session</h2>";
                echo '<table>';
                echo '<td>' . $_SESSION . '</td>';
                echo '</table><br>';
            }

            if (isset($_COOKIE)) {
                echo "<h2>Valor Cookie</h2>";
                print_r($_COOKIE) . "<br>";
            }

            echo "<h2>PHPINFO</h2>";
            phpinfo();
            ?>

    </body>

</html>   