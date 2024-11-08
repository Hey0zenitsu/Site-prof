<?php
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
$dbname = "estoque_db";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Exibir Itens
$sql = "SELECT id, nome, imagem FROM estoque"; // Altere 'imagem' para o nome da coluna correta
$result = $conn->query($sql);

// Adicione um tratamento de erro para a consulta
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Itens</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .lista h1 {
            text-align: center;
            color: #333;
        }
        .lista table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .lista th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .lista th {
            background-color: #f2f2f2;
        }
        .item-link {
            text-decoration: none;
            color: #333;
        }
        .lista img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 5px;
        }
        .lista p{
            text-align: center;
        }
        /* menu */

.sidebar {
    width: 250px;
    height: 100%;
    background-color: #353535;
    position: fixed;
    left: -250px; /* Escondido inicialmente */
    transition: left 0.3s;
    font-family: 'inter';
}

.sidebar h2 {
    color: white;
    padding: 15px;
    border-bottom: 2px solid #2d6a4f; /* Linha verde embaixo dos títulos */
    font-weight: 200;
}

.sidebar a {
    color: white;
    padding: 15px;
    text-decoration: none;
    display: block;
}

.sidebar a:hover {
    background-color: #575757;
}
.active {
    transform: scale(1.1); /* Aumenta a imagem em 10% */
    transition: transform 0.3s;
}

    </style>
</head>
<body>
<div class="navegacao">
            <div class="esquerda">
            <img src="../Imagens/LogoVetorizada.png" alt="LogoMarca" width="50px" id="menu-toggle" alt="Menu" style="cursor:pointer;">
            <h1>BONSAI GARDEN</h1>
            </div>
            
            </div>
            <div class="menu">
        <div id="sidebar" class="sidebar">
            <h2>Ferramentas</h2>
            <a href="../../cliente/Clientes.php">Clientes</a>
            <a href="../../estoque/estoque.php">Estoque</a>
            <a href="../../funcionarios/Funcionarios.php">Funcionarios</a>
            <a href="../index2.php">Adicionar itens</a>
            <a href="editar_item.php">Editar itens</a>
            <a href="../../../OpenToUse/home/home.html">Página Inicial</a>

        </div>
    </div>
<div class="container">
<div class="lista">
    <h1>Lista de Itens</h1>
        <p>Clique no nome do item para modificar-lo</p>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Imagem</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                <td>
                <span class="item-link" onclick="copyToClipboard('<?php echo $row['id']; ?>')">
                    <?php echo htmlspecialchars($row['id']); ?>
                </span>
                </td>
                    <td>
                        <a class="item-link" href="pagina_do_item.php?id=<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['nome']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="pagina_do_item.php?id=<?php echo $row['id']; ?>">
                            <img src="<?php echo htmlspecialchars($row['imagem']); ?>" alt="Imagem do Item">
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Nenhum item registrado.</td>
            </tr>
        <?php endif; ?>
    </table>

    </div>
    <script>
function copyToClipboard(id) {
        // Cria um elemento de input temporário
        const input = document.createElement('input');
        input.value = id; // Define o valor como o ID
        document.body.appendChild(input); // Adiciona o input ao DOM
        input.select(); // Seleciona o conteúdo do input
        document.execCommand('copy'); // Executa o comando de copiar
        document.body.removeChild(input); // Remove o input após a cópia

        // Opcional: Alerta ou mensagem para o usuário
        alert(`ID ${id} copiado para a área de transferência!`);
         }
    </script>
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
