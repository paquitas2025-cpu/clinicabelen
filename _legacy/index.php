<?php
session_start();
$mensaje = "";

if(isset($_SESSION["error"])){
    $mensaje = $_SESSION["error"];
    unset($_SESSION["error"]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - Sistema Médico</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#203a43,#203a43);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family: 'Segoe UI', sans-serif;
}

.card{
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,.25);
}

.logo{
    font-size:32px;
    font-weight:700;
    color:#203a43;
}

.btn-primary{
    border-radius:10px;
}

.alert-login{
    max-width: 260px;
    margin: 0 auto 12px auto;
    padding: 6px 10px;
    border-radius: 10px;
    font-size: 14px;
}

</style>

</head>
<body>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-4">

    <div class="card p-4">
        
        <div class="text-center mb-3">
            <div class="logo"> Sistema Médico</div>
            <small class="text-muted">Ingreso al sistema</small>
            <?php if($mensaje): ?>
    <div class="alert alert-danger text-center alert-login">
     <?= $mensaje ?>
    </div>
    <?php endif; ?>

        </div>

        <form action="login.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">
                Ingresar
            </button>
    
        </form>
    </div>
</div>
</div>
</div>

</body>
</html>
