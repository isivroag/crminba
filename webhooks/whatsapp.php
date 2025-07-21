<?php
$verify_token = 'TokenDeVerificacionINBA2025.650'; // El mismo que pondrás en Meta

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? null;
    $token = $_GET['hub_verify_token'] ?? null;
    $challenge = $_GET['hub_challenge'] ?? null;

    if ($mode === 'subscribe' && $token === $verify_token) {
        header("HTTP/1.1 200 OK");
        echo $challenge;
        exit;
    } else {
        http_response_code(403);
        echo 'Token inválido';
        exit;
    }
}
?>