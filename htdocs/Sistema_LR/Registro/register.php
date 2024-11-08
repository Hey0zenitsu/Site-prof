<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Define o cabeçalho para JSON
header('Content-Type: application/json');

// Verifica se o método é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $date = $_POST['date'];

    // Hash da senha
    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

    // Verifica se o email já existe
    $checkEmailSql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Este email já está registrado.']);
        } else {
            // Prepara a consulta SQL para inserir novo usuário
            $sql = "INSERT INTO usuarios (username, email, password, date) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssss", $user, $email, $hashed_pass, $date);

                // Executa a consulta
                if ($stmt->execute()) {
                    // Retorna sucesso
                    echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao inserir dados.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a declaração de inserção.']);
            }
        }

        // Fecha a declaração de verificação de email
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a declaração de verificação de email.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método não suportado']);
}

// Fecha a conexão
$conn->close();
?>
