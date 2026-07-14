<?php
session_start();
require_once "db.php";

// Verificar sesión
if (!isset($_SESSION["user_id"], $_SESSION["rol"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["rol"] !== "archivos") {
    die("Acceso denegado: solo el encargado de archivos puede subir documentos.");
}

// Solo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: panel_archivos.php");
    exit();
}

$trabajador_id = (int)($_POST["trabajador_id"] ?? 0);
if ($trabajador_id <= 0) {
    die("Error: debe seleccionar un trabajador.");
}

// Obtener empresa del trabajador
$stmt = $conexion->prepare("SELECT empresa_id FROM trabajadores WHERE id = ?");
$stmt->bind_param("i", $trabajador_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if (!$data) {
    die("Error: trabajador no encontrado.");
}

$empresa_id = (int)$data["empresa_id"];

// Tipos de documento permitidos (name del input => código en BD)
$tipos_documento = [
    "CAMO"         => "CAMO",
    "EMO"          => "EMO",
    "LABORATORIO"  => "LABORATORIO",
    "OFTALMOLOGIA" => "OFTALMOLOGIA",
    "RESUMEN"      => "RESUMEN",
    "OBSERVACIONES" => "OBSERVACIONES",
    "CONCLUSIONES" => "CONCLUSIONES",
];

$usuario_id = (int)$_SESSION["user_id"];
$subidos = 0;
$errores = [];

foreach ($tipos_documento as $campo => $codigo) {
    // Si no subieron este archivo, lo saltamos
    if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] !== UPLOAD_ERR_OK) {
        continue;
    }

    $archivo = $_FILES[$campo];

    // Validar PDF
    if ($archivo["type"] !== "application/pdf") {
        $errores[] = "$codigo: solo se permiten archivos PDF";
        continue;
    }

    // Validar tamaño (ej: 10 MB max)
    $max_size = 10 * 1024 * 1024;
    if ($archivo["size"] > $max_size) {
        $errores[] = "$codigo: el archivo excede 10 MB";
        continue;
    }

    // Obtener ID del tipo de documento
    $q2 = $conexion->prepare("SELECT id FROM tipos_documento WHERE codigo = ?");
    $q2->bind_param("s", $codigo);
    $q2->execute();
    $tipo_row = $q2->get_result()->fetch_assoc();

    if (!$tipo_row) {
        $errores[] = "$codigo: tipo de documento no registrado en BD";
        continue;
    }

    $tipo_id = (int)$tipo_row["id"];

    // Crear ruta: uploads/empresa_{id}/trabajador_{id}/{codigo}/
    $base = "uploads" . DIRECTORY_SEPARATOR
          . "empresa_{$empresa_id}" . DIRECTORY_SEPARATOR
          . "trabajador_{$trabajador_id}" . DIRECTORY_SEPARATOR
          . $codigo . DIRECTORY_SEPARATOR;

    if (!is_dir($base)) {
        mkdir($base, 0777, true);
    }

    $nombre_seguro = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($archivo["name"]));
    $ruta_final = $base . $nombre_seguro;

    if (!move_uploaded_file($archivo["tmp_name"], $ruta_final)) {
        $errores[] = "$codigo: error al guardar el archivo";
        continue;
    }

    // Guardar en BD
    $insert = $conexion->prepare("
        INSERT INTO archivos_medicos
            (trabajador_id, tipo_documento_id, nombre_archivo, ruta_archivo, usuario_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iissi", $trabajador_id, $tipo_id, $nombre_seguro, $ruta_final, $usuario_id);
    $insert->execute();
    $subidos++;
}

// Redirigir con resultado
$params = [];
if ($subidos > 0) {
    $params[] = "ok=" . $subidos;
}
if (!empty($errores)) {
    $_SESSION["error_upload"] = implode(" | ", $errores);
}

$query = !empty($params) ? "?" . implode("&", $params) : "";
header("Location: panel_archivos.php{$query}");
exit();
