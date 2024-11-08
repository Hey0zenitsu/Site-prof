<?php
// Inicie a sessão
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Sistema_LR/Login/login.html');
    exit(); // Pare a execução do script
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


// Recuperar todos os itens do banco de dados
$sql = "SELECT * FROM estoque";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
        <!-- navegação -->
        <div class="navegacao">
            <div class="esquerda">
                <img src="Imagens/LogoVetorizada.png" alt="LogoMarca" width="100px">
                <h1>BONSAI GARDEN</h1>
            </div>
            <div class="direita">
                <nav>
                    <ul>
                    <a href="/OpenToUse/Home/Home.html">HOME</a>
                    <a href="/OpenToUse/Sobre/Sobre1.1.html">SOBRE</a>
                    <a href="../../Loja/loja/loja.php" id="home">PRODUTOS</a>
                    <a href="../../AdmUse/Secao_adm/Login.html">FUNCIONARIOS</a>
                    </ul>
                </nav>
                <a href="../itens/carrinho.php">
                <img src="Imagens/Carrinho.png" alt="CarrinhoDeCompras" id="car">
                </a>
            </div>
        </div>
        
<div class="page">
    <h1>Produtos</h1>
    <div class="itens">
        <div class="buttons">
            <button class="button" onclick="prevSlide()"><img src="Imagens/seta.png" alt="seta"></button>
        </div>
        <div class="slider">
        <div class="slides">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="slide">
                    <img src="<?php echo htmlspecialchars($row['imagem']); ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>" id="imgiten">
                    <h2><?php echo htmlspecialchars($row['nome']); ?></h2>
                    <p>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></p>
                    <a href="../itens/pagina_do_item.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                        SAIBA MAIS
                    </a>
                    <a href="../itens/pagina_do_item.php?id=<?php echo htmlspecialchars($row['id']); ?>">
                        <img src="Imagens/Carrinho.png" alt="CarrinhoDeCompras" id="iten" >
                    </a>
                   
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum produto disponível.</p>
        <?php endif; ?>
    </div>
</div>
    <div class="buttons">
        <button class="button" onclick="nextSlide()"><img src="Imagens/seta.png" alt="seta" id="seta"></button>
    </div>
    </div>
</div>
</div>
</div>
    <script>
        let slideIndex = 0;

        function showSlides() {
            const slides = document.querySelector('.slides');
            const totalSlides = document.querySelectorAll('.slide').length;
            if (slideIndex >= totalSlides) {
                slideIndex = 0;
            } else if (slideIndex < 0) {
                slideIndex = totalSlides - 1;
            }
            slides.style.transform = `translateX(${-slideIndex * 100}%)`;
        }

        function nextSlide() {
            slideIndex++;
            showSlides();
        }

        function prevSlide() {
            slideIndex--;
            showSlides();
        }

        showSlides(); // Show the first slide initially
    </script>


    <?php $conn->close(); ?>
</body>
</html>
