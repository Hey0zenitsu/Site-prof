<?php
// estoque.php

session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../Secao_adm/Login.html');
    exit();
}

// Substitua com suas credenciais do banco de dados
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "estoque_db";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $db_password, $dbname);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Variável para armazenar a mensagem de erro ou sucesso
$mensagem = "";

// Deletar Produto
if (isset($_POST['acao']) && $_POST['acao'] === 'deletar') {
    $produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
    if ($produto_id > 0) {
        $sql = "DELETE FROM estoque WHERE id = $produto_id";
        if ($conn->query($sql) === TRUE) {
            $mensagem = "Produto deletado com sucesso!";
        } else {
            $mensagem = "Erro ao deletar o produto: " . $conn->error;
        }
    } else {
        $mensagem = "ID do produto inválido.";
    }
}

// Alterar Dados do Produto
if (isset($_POST['acao']) && $_POST['acao'] === 'alterar') {
    $produto_id = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
    $novo_nome = isset($_POST['novo_nome']) ? trim($_POST['novo_nome']) : '';
    $novo_valor = isset($_POST['novo_valor']) ? floatval($_POST['novo_valor']) : 0;
    $nova_quantidade = isset($_POST['nova_quantidade']) ? intval($_POST['nova_quantidade']) : 0;
    $url_imagem = isset($_POST['url_imagem']) ? trim($_POST['url_imagem']) : '';

    if ($produto_id > 0) {
        $sql_parts = [];
        if (!empty($novo_nome)) {
            $sql_parts[] = "nome = '$novo_nome'";
        }
        if ($novo_valor > 0) {
            $sql_parts[] = "valor = $novo_valor";
        }
        if ($nova_quantidade >= 0) {
            $sql_parts[] = "quantidade = $nova_quantidade";
        }
        if (!empty($url_imagem)) {
            $sql_parts[] = "imagem = '$url_imagem'";
        }
        if (!empty($sql_parts)) {
            $sql = "UPDATE estoque SET " . implode(', ', $sql_parts) . " WHERE id = $produto_id";
            if ($conn->query($sql) === TRUE) {
                $mensagem = "Dados do produto alterados com sucesso!";
            } else {
                $mensagem = "Erro ao alterar os dados: " . $conn->error;
            }
        } else {
            $mensagem = "Nenhum dado para atualizar.";
        }
    } else {
        $mensagem = "ID do produto inválido.";
    }
}

// Exibir Estoque Atual
$sql = "SELECT * FROM estoque";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .imagem-produto {
        border-radius: 10px; /* Ajuste o valor conforme desejado */
    }
</style>
<body>
<div class="container">
    <div class="navegacao">
        <div class="esquerda">
        <img src="Imagens/LogoVetorizada.png" alt="LogoMarca" width="50px" id="menu-toggle" alt="Menu" style="cursor:pointer;">
        <h1>BONSAI GARDEN</h1>
        </div>
    
    </div>
    <div class="menu">
        <div id="sidebar" class="sidebar">
            <h2>Ferramentas</h2>
            <a href="../cliente/Clientes.php">Clientes</a>
            <a href="../estoque/estoque.php">Estoque</a>
            <a href="../funcionarios/Funcionarios.php">Funcionarios</a>
            <a href="../CPage/index2.php">Adicionar itens</a>
            <a href="../CPage/edititem/editar_item.php">Editar itens</a>
            <a href="../../OpenToUse/home/home.html">Página Inicial</a>
        </div>
    </div>
    <div class="page">
        <h1>Gerenciamento de Estoque</h1>

        <?php if (!empty($mensagem)): ?>
            <p><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        <!-- Seção de Estoque Atual -->
        <div class="table">
            <div class="section1">
                <h2>Estoque Atual</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Produto</th>
                        <th>Valor (R$)</th>
                        <th>Quantidade</th>
                        <th>Imagem</th>
                    </tr>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td><?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($row['quantidade']); ?></td>
                                <td><img class="imagem-produto" src="<?php echo htmlspecialchars($row['imagem']); ?>" alt="Imagem do Produto" width="100"></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nenhum produto no estoque.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Formulários de Ação -->
            <div class="section2">
                <h2>Gerenciamento de Estoque</h2>

                <!-- Deletar Itens -->
                <h3>Deletar Itens</h3>
                <form method="POST">
                    <input type="number" name="produto_id" placeholder="ID do Produto" required>
                    <button type="submit" name="acao" value="deletar">Deletar</button>
                </form>
                <hr>

                <!-- Alterar Dados do Produto -->
                <h3>Alterar Dados do Produto</h3>
                <form method="POST">
                    <input type="number" name="produto_id" placeholder="ID do Produto" required>
                    <input type="text" name="novo_nome" placeholder="Novo Nome">
                    <input type="number" step="0.01" name="novo_valor" placeholder="Novo Valor">
                    <input type="number" name="nova_quantidade" placeholder="Nova Quantidade">
                    <input type="text" name="url_imagem" placeholder="URL Da Imagem">
                    <button type="submit" name="acao" value="alterar">Alterar Dados</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');

menuToggle.addEventListener('click', () => {
    if (sidebar.style.left === '0px') {
        sidebar.style.left = '-250px';
        menuToggle.classList.remove('active'); // Remove a classe quando o menu fecha
    } else {
        sidebar.style.left = '0px';
        menuToggle.classList.add('active'); // Adiciona a classe quando o menu abre
    }
});
</script>
</html>

<?php
$conn->close();
?>
