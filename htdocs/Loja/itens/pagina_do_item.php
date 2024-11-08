<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "estoque_db";

$conn = new mysqli($servername, $username, $db_password, $dbname);

// Verifique se o ID foi passado
if (!isset($_GET['id'])) {
    echo "ID do item não fornecido.";
    exit();
}

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Recuperar informações do item pelo ID
$id = $_GET['id'];
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
    <title>Bonsai Garden</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="navegacao">
        <div class="esquerda">
            <img src="Imagens/LogoVetorizada.png" alt="LogoMarca" width="100px">
            <h1>BONSAI GARDEN</h1>
        </div>
        <div class="direita">
            <nav>
                    <ul>
                    <a href="../../OpenToUse/Home/Home.html">HOME</a>
                    <a href="../../OpenToUse/Sobre/Sobre1.1.html">SOBRE</a>
                    <a href="../../Loja/loja/loja.php" id="home">PRODUTOS</a>
                    <a href="../../AdmUse/Secao_adm/Login.html">FUNCIONARIOS</a>
                    
                    </ul>
                </nav>
            </nav>
            <a href="carrinho.php">
                <img src="Imagens/Carrinho.png" alt="CarrinhoDeCompras" id="car" href="carrinho.php">
            </a>
        </div>
    </div>

    <div class="page">
        <div class="ladoe">
            <h1>Saiba mais</h1>
            <h3>BONSAI DE <?php echo mb_strtoupper(htmlspecialchars($item['nome'])); ?></h3>
            <h2><?php echo mb_strtoupper(htmlspecialchars($item['nome'])); ?></h2>
            <img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>">
            <p>Lembre-se de que o sucesso no cultivo de um Bonsai depende de cuidados consistentes e paciência, mas o resultado final é uma obra de arte viva que pode ser apreciada por muitos anos.</p>   
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
            <form action="adicionar_ao_carrinho.php" method="post">
                <input type="hidden" name="produto_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                <label for="quantidade">Preço R$ <?php echo number_format($item['valor'], 2, ',', '.'); ?></label>
                <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="<?php echo htmlspecialchars($item['quantidade']); ?>">
                <button type="submit">Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
