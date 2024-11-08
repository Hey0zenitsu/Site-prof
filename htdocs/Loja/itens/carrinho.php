<?php
session_start();

// Verifique se o cliente está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../Sistema_LR/Login/login.html'); // Redireciona para a página de login se o cliente não estiver logado
    exit();
}

$cliente_id = $_SESSION['user_id'];

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

// Limpar o carrinho
if (isset($_POST['limpar_carrinho'])) {
    $sql_limpar = "DELETE FROM carrinho WHERE cliente_id = ?";
    $stmt_limpar = $conn->prepare($sql_limpar);
    $stmt_limpar->bind_param("i", $cliente_id);
    $stmt_limpar->execute();
    $stmt_limpar->close();
    header("Location: carrinho.php"); // Redireciona para a página do carrinho após limpar
    exit();
}

// Buscar itens do carrinho para o cliente logado
$sql = "SELECT c.quantidade, e.nome, e.valor, e.imagem 
        FROM carrinho c 
        JOIN estoque e ON c.produto_id = e.id 
        WHERE c.cliente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$total_carrinho = 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .page{
            display: flex;
            flex-direction: column;
        }
        .page h1{
            font-family: 'roboto';
            font-size: 60px;
            margin: 30px;
            color: #1E3706;
        }
        .button {
            display: flex;
            justify-content: center;
            width: 70%; /* Ajuste para o container dos botões */
            margin: 30px 0px 0px 15%;
        }
        .button button{
            font-size: 30px;
        }

        .btnrow {
            display: flex;
            justify-content: space-between;
            width: 100%; /* Largura total do container dos botões */
            gap: 15px;
        }
        .btn2 button {
            background-color: black; /* Cor de fundo preta */
            color: white; /* Cor do texto */
            border: none; /* Remover bordas */
            padding: 15px 30px; /* Aumentar o espaçamento interno para torná-lo maior */
            font-size: 22px; /* Aumentar o tamanho da fonte */
            cursor: pointer; /* Cursor como mão ao passar o mouse */
            transition: background-color 0.3s; /* Transição suave para mudanças de cor */
        }

        .btn2 button:hover {
            background-color: #333; /* Cor de fundo ao passar o mouse (um tom de preto mais claro) */
        }
        .btn1 button {
            background-color: #4CAF50; /* Cor de fundo verde */
            color: white; /* Cor do texto */
            border: none; /* Remover bordas */
            padding: 15px 30px; /* Aumentar o espaçamento interno para torná-lo maior */
            font-size: 22px; /* Tamanho da fonte */
            cursor: pointer; /* Cursor como mão ao passar o mouse */
            transition: background-color 0.3s; /* Transição suave para mudanças de cor */
        }

        .btn1 button:hover {
            background-color: #45a049; /* Cor de fundo ao passar o mouse (um tom de verde mais claro) */
        }
        #row{
            border: none; /* Remove a borda padrão */
            height: 2px; /* Espessura da linha */
            background-color: #4CAF50; /* Cor da linha */
            width: 100%; /* Largura da linha */
            border-radius: 5px; /* Deixa as pontas arredondadas */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Sombra para um efeito tridimensional */
        }
        table {
            width: 70%; /* A tabela ocupa 70% da largura do container */
            border-collapse: separate; /* Espaçamento entre as bordas */
            border-spacing: 0; /* Remover o espaçamento entre bordas */
        }

        th {
            background-color: #4CAF50; /* Cor de fundo verde */
            padding: 10px; /* Espaçamento interno dos cabeçalhos */
            text-align: center; /* Alinhar texto à esquerda */
            font-family: 'inter'; /* Corrigindo a fonte */
            font-size: 30px;
            font-weight: 300;
            border: 1px solid black; /* Adicionando borda preta */
        } 

        td {
            background-color: white; /* Cor de fundo branca para as células */
            padding: 10px; /* Espaçamento interno das células */
            border: 1px solid black; /* Borda preta e reta */
            font-family: 'inter'; /* Corrigindo a fonte */
            font-size: 20px;
            text-align: center;
            font-weight: 200;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Cor de fundo para linhas pares (opcional) */
        }

        tr:hover {
            background-color: #e0e0e0; /* Cor ao passar o mouse sobre a linha */
        }
        .table{
            display: flex;
            justify-content: center;
            margin: 50px 0px 10px 0px;
        }
        .table2{
            display: flex;
            justify-content: right;
            width: 85%;
        }
        .table2 table{
            width: 20%;
        }
        .table2 td{
            background-color: white; /* Cor de fundo branca para as células */
            padding: 20px; /* Espaçamento interno das células */
            border: 1.5px solid black; /* Borda preta e reta */
            font-family: 'inter'; /* Corrigindo a fonte */
            font-size: 20px;
            text-align: center;
            font-weight: 200;
        }
        #value{
            color: red;
            font-size: 15px;
        }
        #iimg{
            border-radius: 5px;
        }
        p{
            border: 1px solid gray; /* Borda cinza */
            background-color: #f0f0f0; /* Cor de fundo mais clara */
            padding: 15px; /* Espaçamento interno */
            border-radius: 8px; /* Bordas arredondadas */
            font-size: 25px; /* Tamanho da fonte */
            color: #333; /* Cor do texto */
            margin: 20px; /* Margem externa */
            font-family: 'inter';
        }
        </style>
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
                    <a href="../Home/Home.html">HOME</a>
                    <a href="../Sobre/Sobre1.1.html">SOBRE</a>
                    <a href="../../Loja/loja/loja.php">PRODUTOS</a>
                    <a href="../../AdmUse/Secao_adm/Login.html">FUNCIONARIOS</a>
                </ul>
            </nav>
            <a href="carrinho.php">
                <img src="Imagens/Carrinho.png" alt="CarrinhoDeCompras" id="car" id="home">
            </a>
        </div>
    </div>

    <div class="page">
        <h1>Meu carrinho</h1>
        <div class="table">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Produto</th>
                    <th>Imagem</th>
                    <th>Quantidade</th>
                    <th>Unidade</th>
                    <th>Total</th>
                </tr>

                <?php while ($item = $result->fetch_assoc()): 
                    $subtotal = $item['valor'] * $item['quantidade'];
                    $total_carrinho += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" width="50" id="iimg"></td>
                    <td><?php echo htmlspecialchars($item['quantidade']); ?></td>
                    <td>R$ <?php echo number_format($item['valor'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
                </table>
            </div>
            <div class="table2">
            <table>
                <tr>
                    <td colspan="4">Total:</td>
                    <td id="value">R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></td>
                </tr>
                </table>
                </div>
            <div class="button">
                <div class="btnrow">
            <div class="btn1">
                <button type="button" onclick="window.location.href='../loja/loja.php'">Continuar Comprando</button>
            </div>
            <div class="btn2">
            <button type="button" onclick="finalizarCompra()">Finalizar Compra</button>
            </div>
            <div class="btn2">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="limpar_carrinho">Limpar Carrinho</button>
                </form>
            </div>
            </div>
            </div>
        <?php else: ?>
            <p>Seu carrinho está vazio.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
?>
</body>
<script>
function finalizarCompra() {
    // Itens do carrinho
    const itens = <?php
        $result->data_seek(0); // Volta o ponteiro para o início do resultado
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    ?>;
    
    let mensagem = "Olá, gostaria de finalizar minha compra:\n\n";
    let total = 0;

    itens.forEach(item => {
        const subtotal = (item.valor * item.quantidade).toFixed(2);
        total += parseFloat(subtotal);
        mensagem += `Produto: ${item.nome}\nQuantidade: ${item.quantidade}\nValor Unitário: R$ ${parseFloat(item.valor).toFixed(2)}\nSubtotal: R$ ${subtotal}\n\n`;
    });

    mensagem += `Total da Compra: R$ ${total.toFixed(2)}`;

    // Codifica a mensagem para uso na URL
    mensagem = encodeURIComponent(mensagem);
    
    // Número do WhatsApp (substitua pelo número desejado)
    const numeroWhatsApp = "5541999700128"; // Exemplo: 55 + DDD + número
    const linkWhatsApp = `https://wa.me/${numeroWhatsApp}?text=${mensagem}`;

    // Redireciona para o link do WhatsApp
    window.open(linkWhatsApp, '_blank');
}
</script>
</html>
