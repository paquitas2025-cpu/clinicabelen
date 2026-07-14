<?php
$host = "localhost";
$usuario = "root";
$clave = "root";
$base_datos = "sistema_medico";

$conexion = new mysqli($host, $usuario, $clave, $base_datos);

if($conexion->connect_errno){
    die("Error en la conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
