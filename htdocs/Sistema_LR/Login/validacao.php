<?php
// login.php

session_start();

// Substitua com suas credenciais do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Captura e sanitiza as entradas
$email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

$response = array('status' => 'error', 'message' => 'Credenciais inválidas');

// Verifica se os campos não estão vazios
if (!empty($email) && !empty($password)) {
    // Consulta para verificar o email e a senha
    $sql = "SELECT id, password FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verifica a senha (assumindo que está usando hashing)
        if (password_verify($password, $row['password'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $row['id'];
            $response['status'] = 'success';
            $response['message'] = 'Login bem-sucedido';
        }
    }
}

// Fecha a conexão
$conn->close();

// Retorna a resposta em formato JSON
echo json_encode($response);
