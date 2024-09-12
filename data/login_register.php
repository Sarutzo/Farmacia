<?php
session_start();

// Conexión a la base de datos
$host = 'localhost';
$db   = 'sistema_login_registro';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Función para registrar un nuevo usuario
function registrarUsuario($nombre_usuario, $correo, $contrasena) {
    global $pdo;
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre_usuario, correo, contrasena) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nombre_usuario, $correo, $contrasena_hash]);
}

// Función para iniciar sesión
function iniciarSesion($nombre_usuario, $contrasena) {
    global $pdo;
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre_usuario]);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        return true;
    }
    return false;
}

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registro'])) {
        $nombre_usuario = $_POST['register-username'];
        $correo = $_POST['register-email'];
        $contrasena = $_POST['register-password'];
        $confirmar_contrasena = $_POST['register-confirm-password'];

        if ($contrasena === $confirmar_contrasena) {
            if (registrarUsuario($nombre_usuario, $correo, $contrasena)) {
                echo "Usuario registrado con éxito";
            } else {
                echo "Error al registrar el usuario";
            }
        } else {
            echo "Las contraseñas no coinciden";
        }
    } elseif (isset($_POST['login'])) {
        $nombre_usuario = $_POST['username'];
        $contrasena = $_POST['password'];

        if (iniciarSesion($nombre_usuario, $contrasena)) {
            echo "Inicio de sesión exitoso";
        } else {
            echo "Nombre de usuario o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- ... (el resto del head se mantiene igual) ... -->
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Iniciar sesión</h2>
                <p class="card-description">Ingresa tu nombre de usuario y contraseña para acceder a tu cuenta.</p>
            </div>
            <form method="POST" action="">
                <div class="card-content">
                    <div class="input-group">
                        <label for="username">Nombre de usuario</label>
                        <input id="username" name="username" type="text" placeholder="Ingresa tu nombre de usuario" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="link">¿Olvidaste tu contraseña?</a>
                    <button type="submit" name="login" class="button">Iniciar sesión</button>
                </div>
            </form>
        </div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Registrarse</h2>
                <p class="card-description">Crea una nueva cuenta para acceder a nuestros servicios.</p>
            </div>
            <form method="POST" action="">
                <div class="card-content">
                    <div class="input-group">
                        <label for="register-username">Nombre de usuario</label>
                        <input id="register-username" name="register-username" type="text" placeholder="Ingresa tu nombre de usuario" required>
                    </div>
                    <div class="input-group">
                        <label for="register-email">Correo electrónico</label>
                        <input id="register-email" name="register-email" type="email" placeholder="Ingresa tu correo electrónico" required>
                    </div>
                    <div class="input-group">
                        <label for="register-password">Contraseña</label>
                        <input id="register-password" name="register-password" type="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                    <div class="input-group">
                        <label for="register-confirm-password">Confirmar contraseña</label>
                        <input id="register-confirm-password" name="register-confirm-password" type="password" placeholder="Confirma tu contraseña" required>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="link">¿Ya tienes una cuenta? Inicia sesión</a>
                    <button type="submit" name="registro" class="button">Registrarse</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>