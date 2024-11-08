<?php
// atualizar_item.php

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "estoque_db";

$conn = new mysqli($servername, $username, $db_password, $dbname);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifique se os dados foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nome = trim($_POST['nome']);
    $imagem = trim($_POST['imagem']);
    $preco = isset($_POST['preco']) ? floatval($_POST['preco']) : 0.0; // Corrigido de 'valor' para 'preco'
    $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
    $estilo_cultivo = isset($_POST['estilo_cultivo']) ? $_POST['estilo_cultivo'] : '';

    // Adicione as tags <li> de volta
    $descricao = "<li>" . implode("</li><li>", array_map('trim', explode("\n", $descricao))) . "</li>";
    $estilo_cultivo = "<li>" . implode("</li><li>", array_map('trim', explode("\n", $estilo_cultivo))) . "</li>";

    // Prepare a declaração SQL para evitar injeções
    $sql = "UPDATE estoque SET nome=?, imagem=?, valor=?, quantidade=?, Descricao=?, Estilo_cultivo=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    // Bind dos parâmetros
    $stmt->bind_param("ssddssi", $nome, $imagem, $preco, $quantidade, $descricao, $estilo_cultivo, $id);

    // Executa a consulta
    if ($stmt->execute()) {
        // Redirecionar ou informar sucesso
        header("Location: pagina_do_item.php?id=" . $id); // Redireciona para a página do item
        exit();
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }
}

// Fechar a conexão
$conn->close();
?>
