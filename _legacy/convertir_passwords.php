<?php
/**
 * Script ÚNICO para migrar contraseñas de texto plano a bcrypt.
 * Ejecutar UNA SOLA vez antes de poner el sistema en producción.
 * 
 * Uso: php convertir_passwords.php  (o desde el navegador si hay acceso)
 */

require_once "db.php";

$resultado = $conexion->query("SELECT id, password FROM usuarios");

if (!$resultado) {
    die("Error al leer usuarios: " . $conexion->error);
}

$contador = 0;

while ($u = $resultado->fetch_assoc()) {
    // Si NO empieza con $2y$ (bcrypt) entonces está en texto plano
    if (strpos($u["password"], '$2y$') !== 0) {
        $nuevo_hash = password_hash($u["password"], PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_hash, $u["id"]);
        $stmt->execute();

        $contador++;
        echo "Usuario #{$u['id']} convertido<br>";
    }
}

echo "<hr>Proceso completado. $contador contraseñas migradas.";
