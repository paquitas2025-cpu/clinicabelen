
<?php
session_start();
if($_SESSION["rol_id"] != 2) die("Acceso denegado");
echo "Bienvenido médico ocupacional";
