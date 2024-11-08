<?php
session_start();

// Verifique se o cliente está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Sistema_LR/Login/login.html');
    exit();
}

$cliente_id = $_SESSION['user_id'];

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os dados do formulário
    $produto_id = $_POST['produto_id'];
    $quantidade = (int)$_POST['quantidade'];

    // Verifique se a quantidade é válida
    if ($quantidade <= 0) {
        echo "Quantidade inválida.";
        exit();
    }

    // Conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $db_password = "";
    $dbname = "estoque_db";

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Verifique se o produto já está no carrinho
    $sql = "SELECT * FROM carrinho WHERE cliente_id = ? AND produto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cliente_id, $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Se o produto já estiver no carrinho, atualize a quantidade
        $sql = "UPDATE carrinho SET quantidade = quantidade + ? WHERE cliente_id = ? AND produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantidade, $cliente_id, $produto_id);
    } else {
        // Se o produto não estiver no carrinho, adicione-o
        $sql = "INSERT INTO carrinho (cliente_id, produto_id, quantidade) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $cliente_id, $produto_id, $quantidade);
    }

    if ($stmt->execute()) {
        // Redirecionar para a página do carrinho após adicionar
        header('Location: carrinho.php');
        exit();
    } else {
        echo "Erro ao adicionar o item ao carrinho: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
