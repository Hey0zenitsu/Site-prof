<?php
session_start();

// Substitua com suas credenciais do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Captura e sanitiza as entradas
$email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
$senha_atual = isset($_POST['senha_atual']) ? trim($_POST['senha_atual']) : '';
$nova_senha = isset($_POST['nova_senha']) ? trim($_POST['nova_senha']) : '';

$response = array('status' => 'error', 'message' => 'Erro ao alterar a senha');

// Verifica se os campos não estão vazios
if (!empty($email) && !empty($senha_atual) && !empty($nova_senha)) {
    // Consulta para verificar o email e a senha atual
    $sql = "SELECT id, password FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica a senha atual
        if (password_verify($senha_atual, $row['password'])) {
            // Gera o hash da nova senha
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            // Atualiza a senha no banco de dados
            $sql_update = "UPDATE usuarios SET password = '$nova_senha_hash' WHERE id = " . $row['id'];

            if ($conn->query($sql_update) === TRUE) {
                $response['status'] = 'success';
                $response['message'] = 'Senha alterada com sucesso';
            } else {
                $response['message'] = 'Erro ao atualizar a senha';
            }
        } else {
            header('Location: incorrect_pass.html');
        }
    } else {
        $response['message'] = 'Email não encontrado';
    }
}

// Fecha a conexão
$conn->close();

// Retorna a resposta em formato JSON
echo json_encode($response);
?>
