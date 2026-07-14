<?php
session_start();
require "db.php";

// si no hay login, redirigir
if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

$usuario_id = (int)$_SESSION["user_id"];

// obtener lista de trabajadores
$trabajadores = $conexion->query("SELECT id, nombres, apellidos FROM trabajadores 
ORDER BY apellidos ASC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Encargado de Archivos</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #f4f6f9;
}

header {
    background: #203a43;
    color: white;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,.12);
}

h2 {
    color: #203a43;
    margin-top: 0;
}

select, input, button {
    width: 100%;
    padding: 12px;
    margin-top: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

button {
    background: #203a43;
    color: white;
    border: none;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    opacity: 0.9;
}

.alert {
    padding: 12px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.logout {
    background: #c0392b;
    padding: 10px 15px;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
</style>
</head>

<body>

<header>
    <h2>Panel – Encargado de Archivos</h2>
    <a class="logout" href="logout.php">Cerrar sesión</a>
</header>

<div class="container">

<?php if (isset($_GET["ok"])): ?>
    <div class="alert alert-success">
        ✅ <?= (int)$_GET["ok"] ?> archivo(s) subido(s) correctamente.
    </div>
<?php endif; ?>

<?php if (isset($_SESSION["error_upload"])): ?>
    <div class="alert alert-danger">
        ⚠️ <?= htmlspecialchars($_SESSION["error_upload"]) ?>
    </div>
    <?php unset($_SESSION["error_upload"]); ?>
<?php endif; ?>

<div class="card">

<h2>Subir archivo médico</h2>

    

    <form action="subir_archivos.php" method="POST" enctype="multipart/form-data">

        <label class="label">Seleccionar Trabajador</label>
        <select name="trabajador_id" class="form-control select" required>
            <option value="">-- Seleccione --</option>
            <?php while($t = $trabajadores->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>">
                    <?= $t['apellidos']." ".$t['nombres'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <div class="section-box">
            <label class="label">CAMO</label>
            <input type="file" name="CAMO" accept="application/pdf" class="form-control">

            <label class="label">EMO</label>
            <input type="file" name="EMO" accept="application/pdf" class="form-control">

            <label class="label">LABORATORIO</label>
            <input type="file" name="LABORATORIO" accept="application/pdf" class="form-control">

            <label class="label">OFTALMOLOGÍA</label>
            <input type="file" name="OFTALMOLOGIA" accept="application/pdf" class="form-control">

            <label class="label">RESUMEN</label>
            <input type="file" name="RESUMEN" accept="application/pdf" class="form-control">

            <label class="label">OBSERVACIONES</label>
            <input type="file" name="OBSERVACIONES" accept="application/pdf" class="form-control">

            <label class="label">CONCLUSIONES</label>
            <input type="file" name="CONCLUSIONES" accept="application/pdf" class="form-control">
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-main">Guardar Resultados</button>
        </div>

    </form>

</div>

</body>
</html>
