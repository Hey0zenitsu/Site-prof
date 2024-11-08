<?php
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../Secao_adm/Login.html');
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "estoque_db";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $db_password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifique se o ID foi passado na URL, caso contrário, pegue o último item adicionado
if (!isset($_GET['id'])) {
    // Obter o último ID adicionado
    $sql_last_id = "SELECT id FROM estoque ORDER BY id DESC LIMIT 1";
    $result_last_id = $conn->query($sql_last_id);

    if ($result_last_id->num_rows > 0) {
        $row_last_id = $result_last_id->fetch_assoc();
        $id = $row_last_id['id'];
    } else {
        echo "Nenhum item encontrado.";
        exit();
    }
} else {
    $id = $_GET['id'];
}

// Recuperar informações do item pelo ID
$sql = "SELECT * FROM estoque WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se o item existe
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
    .btns a{
        float: left;              /* Faz o elemento flutuar à esquerda */
        margin: 200px 0px 0px 20px;         /* Adiciona uma margem superior */
        background-color: gray;   /* Cor de fundo */
        color: white;             /* Cor do texto */
        padding: 10px;            /* Espaçamento interno */
        font-size: 18px;          /* Tamanho da fonte */
        border: 1px solid white; 
        text-decoration: none;
        font-family: 'inter';
        color: white;
        background-color: green;
        padding: 10px 30px 10px 30px;
    }

</style>
<body>
<div class="container">

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
            <a href="../CPage/index2.php">Adicionar itens</a>
            <a href="../../../OpenToUse/home/home.html">Página Inicial</a>

        </div>
    </div>
    <div class="page">
        <div class="ladoe">
            <h1>Saiba mais</h1>
            <h3>BONSAI DE <?php echo mb_strtoupper(htmlspecialchars($item['nome'])); ?></h3>
            <h2><?php echo mb_strtoupper(htmlspecialchars($item['nome']));?></h2>
            <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>">
            <p>Lembre-se de que o sucesso no cultivo de um Bonsai depende de cuidados consistentes e paciência, mas o resultado final é uma obra de arte viva que pode ser apreciada por muitos anos.</p>
            <div class="btns">
            <a href="../edititem/editar_item.php">Voltar</a>
        </div>
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







