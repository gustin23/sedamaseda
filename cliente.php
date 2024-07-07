<!--Link CSS-->
<link rel="Stylesheet" type="text/css" href="cliente.css">


<?php


session_start(); // Asegúrate de iniciar la sesión

// Verifica si 'username' está definido y es igual a 'ET24ADMINISTRADOR'
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'usuario2') {
    // Si no ha iniciado sesión o no es el usuario correcto, redirigir a la página de inicio de sesión
    header('location: /index.php');
    exit();
}




// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sedamasedabd");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error en la conexión a la base de datos: " . $conexion->connect_error);
}

// Obtener las remeras desde la base de datos
$consulta = "SELECT  t.NumeroTalle, e.nombreEquipo, r.Imagen
             FROM remeras r
             JOIN talles t ON r.IdTalle = t.IdTalle
             JOIN equipos e ON r.IdEquipo = e.IdEquipo";

$resultado = $conexion->query($consulta);

// Verificar si hay resultados
if ($resultado->num_rows > 0) {
    echo '<h2>Remeras en la Base de Datos</h2>';
    echo '<table border="1">';
    echo '<tr><th>Talle</th><th>Nombre del Equipo</th><th>Imagen</th></tr>';

    // Mostrar cada remera en la tabla
    while ($remera = $resultado->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $remera['NumeroTalle'] . '</td>';
        echo '<td>' . $remera['nombreEquipo'] . '</td>';
        echo '<td>';
            
        // Verificar si la imagen existe antes de mostrarla
        if ($remera['Imagen'] && file_exists($remera['Imagen'])) {
            echo '<img src="' . $remera['Imagen'] . '" alt="Imagen de la Remera" style="max-width: 100px; max-height: 100px;">';
        } else {
            echo 'No hay imagen disponible';
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';


    echo '<a id="volver_boton" href="javascript:void(0);" onclick="cerrarSesion()" style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: #FFFFFF; text-decoration: none; border-radius: 5px; margin-top: 10px;">Volver a la Página Principal</a>';

} else {
    echo "No hay remeras en la base de datos.";
}

// Cerrar la conexión
$conexion->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Remeras</title>
    <!-- Link CSS -->
    <link rel="Stylesheet" type="text/css" href="principal.css">
    <script>
        function cerrarSesion() {
            // Envía una solicitud AJAX para cerrar la sesión en el servidor
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'cerrar_sesion.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Redirige a la página principal después de cerrar la sesión
                    window.location.href = 'http://localhost/index.php';
                }
            };
            xhr.send();
        }
    </script>
</head>
</html>
