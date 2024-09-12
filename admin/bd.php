<?php
session_start();

// Incluir el archivo de configuración de la base de datos
require_once 'C:\xampp\htdocs\Proyectoseminario\admin\bd.php';

// Función para registrar un nuevo usuario
function registrarUsuario($nombre, $apellidos, $edad, $dni, $contrasena, $tipo_usuario) {
    global $pdo;
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuario (nombre_us, apellidos_us, edad, dni_us, contraseña_us, us_tipo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nombre, $apellidos, $edad, $dni, $contrasena_hash, $tipo_usuario]);
}

// Función para iniciar sesión
function iniciarSesion($dni, $contrasena) {
    global $pdo;
    $sql = "SELECT * FROM usuario WHERE dni_us = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dni]);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($contrasena, $usuario['contraseña_us'])) {
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['nombre_usuario'] = $usuario['nombre_us'] . ' ' . $usuario['apellidos_us'];
        $_SESSION['tipo_usuario'] = $usuario['us_tipo'];
        return true;
    }
    return false;
}

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registro'])) {
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $edad = $_POST['edad'];
        $dni = $_POST['dni'];
        $contrasena = $_POST['contrasena'];
        $tipo_usuario = $_POST['tipo_usuario'];

        if (registrarUsuario($nombre, $apellidos, $edad, $dni, $contrasena, $tipo_usuario)) {
            echo "Usuario registrado con éxito";
        } else {
            echo "Error al registrar el usuario";
        }
    } elseif (isset($_POST['login'])) {
        $dni = $_POST['dni'];
        $contrasena = $_POST['contrasena'];

        if (iniciarSesion($dni, $contrasena)) {
            echo "Inicio de sesión exitoso. Bienvenido, " . $_SESSION['nombre_usuario'];
        } else {
            echo "DNI o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Farmacia - Inicio de Sesión y Registro</title>
    <style>
        /* ... (el estilo CSS se mantiene igual) ... */
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title">Iniciar sesión</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="dni">DNI</label>
                    <input id="dni" name="dni" type="text" placeholder="Ingresa tu DNI" required>
                </div>
                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <input id="contrasena" name="contrasena" type="password" placeholder="Ingresa tu contraseña" required>
                </div>
                <button type="submit" name="login" class="button">Iniciar sesión</button>
            </form>
        </div>
        <div class="card">
            <h2 class="card-title">Registrarse</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" placeholder="Ingresa tu nombre" required>
                </div>
                <div class="input-group">
                    <label for="apellidos">Apellidos</label>
                    <input id="apellidos" name="apellidos" type="text" placeholder="Ingresa tus apellidos" required>
                </div>
                <div class="input-group">
                    <label for="edad">Edad</label>
                    <input id="edad" name="edad" type="text" placeholder="Ingresa tu edad" required>
                </div>
                <div class="input-group">
                    <label for="dni">DNI</label>
                    <input id="dni" name="dni" type="text" placeholder="Ingresa tu DNI" required>
                </div>
                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <input id="contrasena" name="contrasena" type="password" placeholder="Ingresa tu contraseña" required>
                </div>
                <div class="input-group">
                    <label for="tipo_usuario">Tipo de Usuario</label>
                    <select id="tipo_usuario" name="tipo_usuario" required>
                        <?php
                        // Obtener tipos de usuario de la base de datos
                        $sql = "SELECT * FROM tipo_us";
                        $stmt = $pdo->query($sql);
                        while ($row = $stmt->fetch()) {
                            echo "<option value='" . $row['id_tipo_us'] . "'>" . $row['nombre_tipo'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="registro" class="button">Registrarse</button>
            </form>
        </div>
    </div>
</body>
</html>