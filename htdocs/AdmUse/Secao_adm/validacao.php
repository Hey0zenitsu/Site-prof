<?php
// login_admin.php

session_start();

// Substitua com suas credenciais do banco de dados
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "funcionarios";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $db_password, $dbname);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Captura e sanitiza as entradas
$email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
$user_password = isset($_POST['password']) ? trim($_POST['password']) : '';

$response = array('status' => 'error', 'message' => 'Credenciais inválidas');

// Verifica se os campos não estão vazios
if (!empty($email) && !empty($user_password)) {
    // Consulta para verificar o email e a senha na tabela admin
    $sql = "SELECT id, senha FROM admin WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verifica a senha sem usar hashing
        if ($user_password === $row['senha']) {
            // Login bem-sucedido
            $_SESSION['admin_id'] = $row['id'];
            $response['status'] = 'success';
            $response['message'] = 'Login bem-sucedido';
        }
    }
}

// Retorna a resposta como JSON
echo json_encode($response);

// Fecha a conexão com o banco de dados
$conn->close();
?>
