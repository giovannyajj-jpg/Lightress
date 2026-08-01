<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Cliente — DeltaNodes</title>
    <style>
        body { background: #07090e; color: #fff; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #121621; border: 1px solid rgba(245, 158, 11, 0.3); padding: 40px; border-radius: 15px; text-align: center; width: 100%; max-width: 400px; }
        .btn { display: block; background: #f59e0b; color: #000; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .logout { color: #ef4444; text-decoration: none; display: inline-block; margin-top: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
        <p style="color: #9ca3af; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        
        <a href="https://panel.deltanodes.xyz" target="_blank" class="btn">Ir al Panel de Servidores</a>
        
        <a href="logout.php" class="logout">Cerrar Sesión</a>
    </div>
</body>
</html>
