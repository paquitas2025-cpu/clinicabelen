<?php
session_start();
if($_SESSION["rol_id"] != 1) die("Acceso denegado");
echo "Bienvenido área administrativa";
