<?php
session_start();
// Verifique se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../Secao_adm/Login.html');
    exit();
}
// Verifique se o ID foi passado
if (!isset($_GET['id'])) {
    echo "ID do item não fornecido.";
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

// Recuperar informações do item pelo ID
$id = $_GET['id'];
// Recuperar informações do item pelo ID
$sql = "SELECT * FROM estoque WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Verifique se o item existe
if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
} else {
    echo "Item não encontrado.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($item['nome']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
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
<body>
<div class="container">
        <!-- navegação -->
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
        
<div class="page">
        <div class="ladoe">
        <h1>Saiba mais</h1>
        <h3>BONSAI DE <?php echo mb_strtoupper(htmlspecialchars($item['nome'])); ?></h3>
        <h2><?php echo mb_strtoupper(htmlspecialchars($item['nome']));?></h2>
        <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>">
        <p>Lembre-se de que o sucesso no cultivo de um Bonsai de Acer Palmatum depende de cuidados consistentes e paciência, mas o resultado final é uma obra de arte viva que pode ser apreciada por muitos anos.</p>
        </div>
        <div class="linha-vertical"></div>
        <div class="ladod">
            <h1>Descrição:</h1>
            <ul>
                <?php echo $item['Descricao']; ?>
            </ul>
            <h1>Estilo de Cultivo:</h1>
            <ul>
                <?php echo $item['Estilo_cultivo']; ?>
            </ul>

        </div>    <!-- Formulário para editar os campos -->
</div>

<hr id="hr1">

</div>
</div>
<div class="form-container">
    <h2>Editar Informações do Item</h2>
    <form action="atualizar_item.php" method="post" class="edit-item-form">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">

        <div class="form-group left">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($item['nome']); ?>" required>
        </div>

        <div class="form-group left">
            <label for="imagem">Imagem URL:</label>
            <input type="text" id="imagem" name="imagem" value="<?php echo htmlspecialchars($item['imagem']); ?>" required>
        </div>

        <div class="form-group left">
            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco" value="<?php echo htmlspecialchars($item['valor']); ?>" required step="0.01">
        </div>

        <div class="form-group left">
            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" value="<?php echo htmlspecialchars($item['quantidade']); ?>" required>
        </div>

        <div class="form-group right">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required><?php 
            echo htmlspecialchars(preg_replace('/<\/li>\s*/', "\n", preg_replace('/<li>\s*/', "", $item['Descricao'])));
            ?></textarea>
        </div>

        <div class="form-group right">
            <label for="estilo_cultivo">Estilo de Cultivo:</label>
            <textarea id="estilo_cultivo" name="estilo_cultivo" required><?php 
                // Substitui as tags <li> e </li> por quebras de linha
                echo htmlspecialchars(preg_replace('/<\/li>\s*/', "\n", preg_replace('/<li>\s*/', "", $item['Estilo_cultivo'])));
                ?></textarea>
        </div>

        <div class="button-container">
            <button type="submit" class="submit-button">Atualizar Item</button>
        </div>
    </form>
</div>


</div>
</body>
<script>
    function autoResizeTextarea(element) {
        element.style.height = 'auto'; // Reseta a altura para calcular
        element.style.height = (element.scrollHeight) + 'px'; // Define a nova altura
    }

    // Aplica a função a todos os textareas ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        var textareas = document.querySelectorAll('textarea');
        textareas.forEach(function(textarea) {
            textarea.addEventListener('input', function() {
                autoResizeTextarea(textarea);
            });
            // Expande o textarea se já houver conteúdo
            autoResizeTextarea(textarea);
        });
    });

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

