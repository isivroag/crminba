<?php
$servername = "tecniem.com";  // Nombre del servidor MySQL
$username = "tecniemc_srmcheca";      // Nombre de usuario de MySQL
$password = "SrmCheca.2024";   // Contraseña de MySQL
$database = "tecniemc_srmcheca"; // Nombre de la base de datos
date_default_timezone_set('America/Mexico_City');

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$token = $_GET['token'];
$username = $_POST['username'];
$password = md5($_POST['password']);
$actual= date("Y-m-d H:i:s");


$sql = "SELECT * FROM usuariotoken WHERE token = ? AND token_expiry > ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $token, $actual);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $id_prov = $row['id_prov'];
   

    $sql = "SELECT * FROM proveedor WHERE id_prov <> ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_prov, $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "El nombre de usuario que has ingresado ya está en uso. Por favor, elige otro nombre de usuario.";
    }else {

        $sql = "UPDATE usuariotoken SET username = ?, password = ?, token = NULL, token_expiry = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $password, $user_id);

        if ($stmt->execute()) {

            $sql = "UPDATE proveedor SET username = ?, password = ? WHERE id_prov = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $password, $id_prov);
            $stmt->execute();



            $sql = "INSERT INTO w_usuarioprov (id_prov, username, password)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
            username = VALUES(username),
            password = VALUES(password)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id_prov, $username, $password);
            $stmt->execute();

            echo "Registro completado con éxito.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }



} else {
    echo "El enlace de registro no es válido o ha expirado.". date("Y-m-d H:i:s") ." ".$token. " ".$id_prov ;
}

$conn->close();
?>
