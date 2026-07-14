<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$usuario   = trim($_POST["usuario"] ?? "");
$password  = trim($_POST["password"] ?? "");

// Buscar usuario
$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$user = $resultado->fetch_assoc();

if (!$user) {
    $_SESSION["error"] = "Usuario no encontrado";
    header("Location: index.php");
    exit();
}

if ($user["estado"] != 1) {
    $_SESSION["error"] = "Usuario inactivo";
    header("Location: index.php");
    exit();
}

if (!password_verify($password, $user["password"])) {
    $_SESSION["error"] = "Contraseña incorrecta";
    header("Location: index.php");
    exit();
}

// LOGIN CORRECTO
$_SESSION["user_id"]  = (int)$user["id"];
$_SESSION["usuario"]  = $user["usuario"];
$_SESSION["rol_id"]   = (int)$user["rol_id"];
$_SESSION["rol"]      = $user["rol"];

$rutas = [
    1 => "panel_administrativo.php",
    2 => "panel_medico.php",
    3 => "panel_archivos.php",
];

$destino = $rutas[$user["rol_id"]] ?? "index.php";
header("Location: $destino");
exit();
