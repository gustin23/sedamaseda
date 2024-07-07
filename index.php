<!--Link CSS-->
<link rel="Stylesheet" type="text/css" href="index.css">

<?php
session_start();




// Establecer la conexión a la base de datos usando MySQLi
$db = mysqli_connect("localhost", "root", "", "sedamasedabd");

if (!$db) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

$errors = array(); // Array para almacenar mensajes de error

if (isset($_POST['login_button'])) {
    $username = mysqli_real_escape_string($db, $_POST['Usuario']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $email = mysqli_real_escape_string($db, $_POST['email']);

    // Verificar si las credenciales coinciden exactamente
    if ($username === "ET24ADMINISTRADOR" && $password === "5566779000" && $email === "ET24AD@GMAIL.COM"|| $username === "usuario2" && $password === "151550505" && $email === "usuario2@gmail.com" ) {
        if ($username === "ET24ADMINISTRADOR" && $password === "5566779000" && $email === "ET24AD@GMAIL.COM") {
            $_SESSION['username'] = 'ET24ADMINISTRADOR';
            header('Location: principal.php');
            exit();
            session_destroy();
        }else{
            $_SESSION['username'] = 'usuario2';
            header('Location: cliente.php');
            exit();
            session_destroy();
        }
        exit();
    } else {
        // Credenciales incorrectas, añadir mensaje de error
        $errors[] = "El usuario no existe";
    }
}
?>
<h2>Inicio de sesión</h2>

<?php
// Mostrar errores si existen
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}
?>

<form action="" method="POST" autocomplete="off">
    <div class="form-group mb-3">
        <input type="text" name="Usuario" class="form-control" placeholder="Usuario" required>
    </div>
    <div class="form-group mb-3">
        <input type="text" name="email" class="form-control" placeholder="Correo electrónico" required>
    </div>
    <div class="form-group mb-3">
        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
    </div>
    <div class="form-group mb-3">
        <input type="submit" name="login_button" class="form-control btn btn-primary" value="Acceder">
    </div>
</form>
