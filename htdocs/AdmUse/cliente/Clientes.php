<?php
// usuarios.php

session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: ../Secao_adm/Login.html');
    exit();
}

// Substitua com suas credenciais do banco de dados
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "usuarios";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $db_password, $dbname);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Variável para armazenar a mensagem de erro ou sucesso
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : 0;
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';
    $novo_username = isset($_POST['novo_username']) ? trim($_POST['novo_username']) : '';
    $novo_email = isset($_POST['novo_email']) ? trim($_POST['novo_email']) : '';
    $nova_senha = isset($_POST['nova_senha']) ? trim($_POST['nova_senha']) : '';
    $nova_data = isset($_POST['nova_data']) ? trim($_POST['nova_data']) : '';

    if ($usuario_id > 0) {
        if ($acao === 'alterar_dados') {
            // Atualiza username, email, senha e data se fornecido
            $sql_parts = [];
            if (!empty($novo_username)) {
                $sql_parts[] = "username = '$novo_username'";
            }
            if (!empty($novo_email)) {
                $sql_parts[] = "email = '$novo_email'";
            }
            if (!empty($nova_senha)) {
                $hashed_senha = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql_parts[] = "password = '$hashed_senha'";
            }
            if (!empty($nova_data)) {
                $sql_parts[] = "date = '$nova_data'";
            }
            if (!empty($sql_parts)) {
                $sql = "UPDATE usuarios SET " . implode(', ', $sql_parts) . " WHERE id = $usuario_id";
                if ($conn->query($sql) === TRUE) {
                    $mensagem = "Dados do usuário alterados com sucesso!";
                } else {
                    $mensagem = "Erro ao alterar os dados: " . $conn->error;
                }
            } else {
                $mensagem = "Nenhum dado para atualizar.";
            }
        } elseif ($acao === 'deletar') {
            // Deletar o usuário do banco de dados
            $sql = "DELETE FROM usuarios WHERE id = $usuario_id";
            if ($conn->query($sql) === TRUE) {
                $mensagem = "Usuário deletado com sucesso!";
            } else {
                $mensagem = "Erro ao deletar o usuário: " . $conn->error;
            }
        } else {
            $mensagem = "Ação não reconhecida ou parâmetros inválidos.";
        }
    } elseif (!empty($novo_username) && !empty($novo_email) && !empty($nova_senha)) {
        $hashed_senha = password_hash($nova_senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (username, email, password, date) VALUES ('$novo_username', '$novo_email', '$hashed_senha', NOW())";
        if ($conn->query($sql) === TRUE) {
            $mensagem = "Usuário adicionado com sucesso!";
        } else {
            $mensagem = "Erro ao adicionar o novo usuário: " . $conn->error;
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    }
}

// Exibir Usuários Atuais
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="styles.css">
</head>
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
            <a href="../CPage/edititem/editar_item.php">Editar item</a>
            <a href="../../OpenToUse/home/home.html">Página Inicial</a>
        </div>
    </div>
    <div class="page">
        <h1>Gerenciamento de Usuários</h1>

        <?php if (!empty($mensagem)): ?>
            <p><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        <!-- Seção de Usuários Atuais -->
        <div class="table">
            <div class="section1">
                <h2>Usuários Atuais</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Senha</th>
                        <th>Data de Registro</th>
                    </tr>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars('******'); // Não exibe a senha diretamente ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nenhum usuário registrado.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Seção de Ações -->
            <div class="section2">
                <h2>Ações de Usuário</h2>
                
                <!-- Adicionar Novo Usuário -->
                <h3>Adicionar Novo Usuário</h3>
                <form method="POST">
                    <input type="text" name="novo_username" placeholder="Username" required>
                    <input type="email" name="novo_email" placeholder="Email" required>
                    <input type="password" name="nova_senha" placeholder="Senha" required>
                    <button type="submit">Adicionar Usuário</button>
                </form>
                <hr>

                <!-- Deletar Usuário -->
                <h3>Deletar Usuário</h3>
                <form method="POST">
                    <input type="number" name="usuario_id" placeholder="ID do Usuário" required>
                    <button type="submit" name="acao" value="deletar">Deletar</button>
                </form>
                <hr>

                <!-- Alterar Dados do Usuário -->
                <h3>Alterar Dados do Usuário</h3>
                <form method="POST">
                    <input type="number" name="usuario_id" placeholder="ID do Usuário" required>
                    <input type="text" name="novo_username" placeholder="Novo Username">
                    <input type="email" name="novo_email" placeholder="Novo Email">
                    <input type="password" name="nova_senha" placeholder="Nova Senha">
                    <input type="date" name="nova_data" placeholder="Nova Data">
                    <button type="submit" name="acao" value="alterar_dados">Alterar Dados</button>
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
    console.log("Menu toggle clicked");
    if (sidebar.style.left === '0px') {
        sidebar.style.left = '-250px';
        menuToggle.classList.remove('active');
    } else {
        sidebar.style.left = '0px';
        menuToggle.classList.add('active');
    }
});
</script>
</html>

<?php
$conn->close();
?>
