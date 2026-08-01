<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Verificar si el correo ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "<script>alert('El correo ya está registrado.'); window.location.href='index.php';</script>";
        exit;
    }

    // 2. Crear usuario en Pterodactyl mediante su API Admin
    $ptero_url = "https://panel.deltanodes.xyz/"; // URL de tu panel sin barra al final
    $ptero_api_key = "ptla_K3XcMnHU9Sya2VULrxP5RUOoTCNeBFEvyZjR3tHV1vN"; // API Key con permisos de escritura en Users

    $username_ptero = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nombre)) . rand(10,99);
    
    $data = [
        "email" => $email,
        "username" => $username_ptero,
        "first_name" => $nombre,
        "last_name" => "Client",
        "password" => $password
    ];

    $ch = curl_init($ptero_url . "/api/application/users");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $ptero_api_key,
        "Content-Type: application/json",
        "Accept: Application/vnd.pterodactyl.v1+json"
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $ptero_response = json_decode($response, true);

    if ($http_code == 201) {
        $ptero_id = $ptero_response['attributes']['id'];
        
        // 3. Guardar en la base de datos local
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, pterodactyl_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $password_hash, $ptero_id]);

        echo "<script>alert('¡Registro exitoso! Ya puedes iniciar sesión.'); window.location.href='index.php';</script>";
    } else {
        $error_msg = isset($ptero_response['errors'][0]['detail']) ? $ptero_response['errors'][0]['detail'] : 'Error desconocido al conectar con Pterodactyl';
        echo "<script>alert('Error en el registro: " . addslashes($error_msg) . "'); window.location.href='index.php';</script>";
    }
}
?>
