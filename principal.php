<!--Link CSS-->
<link rel="Stylesheet" type="text/css" href="principal.css">


<?php


session_start(); // Asegúrate de iniciar la sesión

// Verifica si 'username' está definido y es igual a 'ET24ADMINISTRADOR'
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'ET24ADMINISTRADOR') {
    // Si no ha iniciado sesión o no es el usuario correcto, redirigir a la página de inicio de sesión
    header('location: /index.php');
    exit();
}




// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sedamasedabd");

echo '<a id="volver_href" href="javascript:void(0);" onclick="cerrarSesion()" style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: #FFFFFF; text-decoration: none; border-radius: 5px; margin-top: 10px;">Volver a la Página Principal</a>';

?>
<?php

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error en la conexión a la base de datos: " . $conexion->connect_error);
}

// Procesar la eliminación cuando se hace clic en el botón
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_remera'])) {
    $idRemeraEliminar = $_POST['id_remera_eliminar'];

    // Eliminar la remera de la base de datos
    $consultaEliminar = "DELETE FROM remeras WHERE IdRemera = '$idRemeraEliminar'";
    
    if ($conexion->query($consultaEliminar) === TRUE) {
        
    } else {
        echo "Error al eliminar la remera: " . $conexion->error;
    }
}

// Procesar el formulario cuando se envía para agregar nuevas remeras
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_remera'])) {
    $idTalle = $_POST['talle'];
    $nombreEquipo = $_POST['equipo'];

    // Verificar si se ha enviado un archivo
    if(isset($_FILES['imagen'])) {
        $imagenNombre = $_FILES['imagen']['name'];
        $imagenTemp = $_FILES['imagen']['tmp_name'];

        // Establecer la ruta de destino para guardar la imagen
        $rutaImagen = 'uploads/' . $imagenNombre;

        // Mover la imagen al directorio de destino
        move_uploaded_file($imagenTemp, $rutaImagen);
    } else {
        // En caso de que no se haya enviado una imagen, establecer $rutaImagen como nulo o como una ruta predeterminada.
        $rutaImagen = null; // o $rutaImagen = 'ruta_predeterminada.jpg';
    }

    // Verificar si el equipo ya existe
    $idEquipo = obtenerIdEquipo($conexion, $nombreEquipo);

    // Si no existe, agregar el nuevo equipo
    if (!$idEquipo) {
        $idEquipo = agregarNuevoEquipo($conexion, $nombreEquipo);
    }

    // Insertar nueva remera en la base de datos
    $consultaAgregar = "INSERT INTO remeras (IdTalle, IdEquipo, Imagen) VALUES ('$idTalle', '$idEquipo', '$rutaImagen')";

    if ($conexion->query($consultaAgregar) === TRUE) {

    } else {
        echo "Error al agregar la remera: " . $conexion->error;
    }
}



// Obtener las remeras desde la base de datos
$consulta = "SELECT r.IdRemera, t.NumeroTalle, e.nombreEquipo, r.Imagen
             FROM remeras r
             JOIN talles t ON r.IdTalle = t.IdTalle
             JOIN equipos e ON r.IdEquipo = e.IdEquipo";

$resultado = $conexion->query($consulta);

// Obtener la lista de talles desde la base de datos
$talles = $conexion->query("SELECT * FROM talles");
?>

<!DOCTYPE html>
<html lang="en">
<head>

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


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Remeras</title>
</head>
<body>


    <h2 id="administrar-remeras-heading">Administrar Remeras</h2>

    <form id="form_mostrar"action="" method="POST">
    <input id="mostrar_remeras" type="submit" name="mostrar_remeras" value="Mostrar Remeras">
</form>
<form id="form_ordenar_nombre"action="" method="POST">
    <input id="ordenar_nombre_button" type="submit" name="ordenar_por_nombre" value="Ordenar por Nombre del Equipo" onclick="botonClickeado()">
</form>
<form id="form_ordenar_talle"action="" method="POST">
    <input id="ordenar_talle" type="submit" name="ordenar_por_talles" value="Ordenar por Talles de Menor a Mayor" onclick="botonClickeado()">
</form>
<form id="form_Ordenar_Inverso" action="" method="POST">
    <input id="ordenar_inverso" type="submit" name="ordenar_por_talles_inverso" value="Ordenar por Talles de Mayor a Menor" onclick="botonClickeado()">
</form>



    <!-- Formulario para agregar nuevas remeras -->
    <form id="form_agregar"action="" method="POST" enctype="multipart/form-data">
        <label  for="talle">Talle:</label>
        <select name="talle" id="talle" required>
            <?php while ($talle = $talles->fetch_assoc()) : ?>
                <option value="<?php echo $talle['IdTalle']; ?>"><?php echo $talle['NumeroTalle']; ?></option>
            <?php endwhile; ?>
        </select><br>

        <label for="equipo">Nombre del Equipo:</label>
        <input type="text" name="equipo" id="equipo" required><br>

        <label for="imagen">Imagen:</label>
        <input type="file" name="imagen" id="imagen" accept="image/*" required>
        
        <input type="submit" name="agregar_remera" value="Agregar Remera">
    </form>

    <!-- Mostrar las remeras en la tabla -->
    <?php
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mostrar_remeras'])) {
    
        $consulta = "SELECT r.IdRemera, t.NumeroTalle, e.nombreEquipo, r.Imagen
             FROM remeras r
             JOIN talles t ON r.IdTalle = t.IdTalle
             JOIN equipos e ON r.IdEquipo = e.IdEquipo";
    
        $resultado = $conexion->query($consulta);
    
        if ($resultado->num_rows > 0) {
            echo '<div class="table-container">';
            echo '<table id="table_original"border="1">';
            echo '<tr><th>Talle</th><th>Nombre del Equipo</th><th>Imagen</th><th>Acciones</th></tr>';
    
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
                echo '<td>';
                echo '<form id="form_eliminar_2"action="" method="POST">';
                echo '<input type="hidden" name="id_remera_eliminar" value="' . $remera['IdRemera'] . '">';
                echo '<input type="submit" name="eliminar_remera" value="Eliminar">';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
    
            echo '</table>';
            echo '</div>';
            } else {
            echo "No hay remeras en la base de datos.";
        }
    }



    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ordenar_por_nombre'])) {
        $consulta = "SELECT r.IdRemera, t.NumeroTalle, e.nombreEquipo, r.Imagen
                     FROM remeras r
                     JOIN talles t ON r.IdTalle = t.IdTalle
                     JOIN equipos e ON r.IdEquipo = e.IdEquipo
                     ORDER BY e.nombreEquipo ASC";
    
        $resultado = $conexion->query($consulta);
    
            if ($resultado->num_rows > 0) {
                echo '<div class="table-container">';
                echo '<table id="table_2" border="1">';
                echo '<tr><th>Talle</th><th>Nombre del Equipo</th><th>Imagen</th><th>Acciones</th></tr>';
        
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
                    echo '<td>';
                    echo '<form id="form_eliminar_2"action="" method="POST">';
                    echo '<input type="hidden" name="id_remera_eliminar" value="' . $remera['IdRemera'] . '">';
                    echo '<input type="submit" name="eliminar_remera" value="Eliminar">';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
        
                echo '</table>';
                echo '</div>';
    
            } else {
                echo "No hay remeras en la base de datos.";
            }
        }
        
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ordenar_por_talles'])) {
        // Creamos un arreglo para establecer el orden de los talles
        $ordenTalles = ['S', 'M', 'L', 'XL', 'XXL'];
    
        // Convertimos el arreglo en una cadena con comas para usar en la cláusula ORDER BY
        $ordenTallesString = "'" . implode("','", $ordenTalles) . "'";
    
        $consulta = "SELECT r.IdRemera, t.NumeroTalle, e.nombreEquipo, r.Imagen
                     FROM remeras r
                     JOIN talles t ON r.IdTalle = t.IdTalle
                     JOIN equipos e ON r.IdEquipo = e.IdEquipo
                     ORDER BY FIELD(t.NumeroTalle, $ordenTallesString)";
    
        $resultado = $conexion->query($consulta);
    
        if ($resultado->num_rows > 0) {
            echo '<div class="table-container">';
            echo '<table id="table_2" border="1">';
            echo '<tr><th>Talle</th><th>Nombre del Equipo</th><th>Imagen</th><th>Acciones</th></tr>';
    
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
                echo '<td>';
                echo '<form id="form_eliminar_2"action="" method="POST">';
                echo '<input type="hidden" name="id_remera_eliminar" value="' . $remera['IdRemera'] . '">';
                echo '<input type="submit" name="eliminar_remera" value="Eliminar">';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
    
            echo '</table>';
            echo '</div>';
        }else {
            echo "No hay remeras en la base de datos.";
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ordenar_por_talles_inverso'])) {
        // Creamos un arreglo con el orden de los talles, y luego lo invertimos
        $ordenTalles = ['S', 'M', 'L', 'XL', 'XXL'];
        $ordenTallesInverso = array_reverse($ordenTalles);
    
        // Convertimos el arreglo en una cadena con comas para usar en la cláusula ORDER BY
        $ordenTallesStringInverso = "'" . implode("','", $ordenTallesInverso) . "'";
    
        $consulta = "SELECT r.IdRemera, t.NumeroTalle, e.nombreEquipo, r.Imagen
                     FROM remeras r
                     JOIN talles t ON r.IdTalle = t.IdTalle
                     JOIN equipos e ON r.IdEquipo = e.IdEquipo
                     ORDER BY FIELD(t.NumeroTalle, $ordenTallesStringInverso)";
    
        $resultado = $conexion->query($consulta);
    
        if ($resultado->num_rows > 0) {
            echo  '<div class="table-container">';
            echo '<table id="table_2"border="1">';
            echo '<tr><th>Talle</th><th>Nombre del Equipo</th><th>Imagen</th><th>Acciones</th></tr>';
    
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
                echo '<td>';
                echo '<form id="form_eliminar_2"action="" method="POST">';
                echo '<input type="hidden" name="id_remera_eliminar" value="' . $remera['IdRemera'] . '">';
                echo '<input type="submit" name="eliminar_remera" value="Eliminar">';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
    
            echo '</table>';
            echo '</div>';
            } else {
            echo "No hay remeras en la base de datos.";
        }
    }

    
    ?>



</body>
</html>

<?php
// Cerrar la conexión
$conexion->close();

// Obtener el ID de un equipo por su nombre
function obtenerIdEquipo($conexion, $nombreEquipo) {
    $resultado = $conexion->query("SELECT IdEquipo FROM equipos WHERE nombreEquipo = '$nombreEquipo' LIMIT 1");

    if ($resultado->num_rows > 0) {
        $equipo = $resultado->fetch_assoc();
        return $equipo['IdEquipo'];
    }

    return false;
}

// Agregar un nuevo equipo y devolver su ID
function agregarNuevoEquipo($conexion, $nombreEquipo) {
    $consulta = "INSERT INTO equipos (nombreEquipo) VALUES ('$nombreEquipo')";
    $conexion->query($consulta);

    return $conexion->insert_id;
}




?>

