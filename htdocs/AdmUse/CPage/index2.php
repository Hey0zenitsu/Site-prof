<?php
// inserir_produto.php

session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../Secao_adm/Login.html');
    exit();
}

// Credenciais do banco de dados
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

// Inserir Novo Produto
if (isset($_POST['acao']) && $_POST['acao'] === 'inserir') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
    $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
    $url_imagem = isset($_POST['url_imagem']) ? trim($_POST['url_imagem']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $descricao1 = isset($_POST['1descricao']) ? trim($_POST['1descricao']) : '';
    $descricao2 = isset($_POST['2descricao']) ? trim($_POST['2descricao']) : '';
    $descricao3 = isset($_POST['3descricao']) ? trim($_POST['3descricao']) : '';
    $descricao4 = isset($_POST['4descricao']) ? trim($_POST['4descricao']) : '';
    $frase = "
        <li>$descricao</li>
        <li>$descricao1</li>
        <li>$descricao2</li>
        <li>$descricao3</li>
        <li>$descricao4</li>";

    $estilo_cultivo = isset($_POST['estilo_cultivo']) ? trim($_POST['estilo_cultivo']) : '';
    $estilo_cultivo1 = isset($_POST['1estilo_cultivo']) ? trim($_POST['1estilo_cultivo']) : '';
    $estilo_cultivo2 = isset($_POST['2estilo_cultivo']) ? trim($_POST['2estilo_cultivo']) : '';
    $estilo_cultivo3 = isset($_POST['3estilo_cultivo']) ? trim($_POST['3estilo_cultivo']) : '';
    $estilo_cultivo4 = isset($_POST['4estilo_cultivo']) ? trim($_POST['4estilo_cultivo']) : ''; 
    $lista_estilo = "
            <li>$estilo_cultivo</li>
            <li>$estilo_cultivo1</li>
            <li>$estilo_cultivo2</li>
            <li>$estilo_cultivo3</li>
            <li>$estilo_cultivo4</li>";
        

    if (!empty($nome) && $valor > 0 && $quantidade >= 0) {
        $sql = "INSERT INTO estoque (nome, valor, quantidade, imagem, descricao, estilo_cultivo) 
                VALUES ('$nome', $valor, $quantidade, '$url_imagem', '$frase', '$lista_estilo')";
        if ($conn->query($sql) === TRUE) {
            $mensagem = "Produto adicionado com sucesso!";
            header("Location: preview/preview.php");
        } else {
            $mensagem = "Erro ao adicionar o produto: " . $conn->error;
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Inserir Novo Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navegacao">
        <div class="esquerda">
            <img src="Imagens/LogoVetorizada.png" alt="LogoMarca" width="50px" id="menu-toggle" alt="Menu" style="cursor:pointer;">
            <h1>BONSAI GARDEN</h1>
        </div>
    </div>
<div class="container"> 
    <div class="menu">
        <div id="sidebar" class="sidebar">
            <h2>Ferramentas</h2>
            <a href="../cliente/Clientes.php">Clientes</a>
            <a href="../estoque/estoque.php">Estoque</a>
            <a href="../funcionarios/Funcionarios.php">Funcionarios</a>
            <a href="../CPage/index2.php">Adicionar itens</a>
            <a href="edititem/editar_item.php">Editar itens</a>
            <a href="../../OpenToUse/home/home.html">Página Inicial</a>

        </div>
    </div>
<div class="page">
<h1>Preencha as informações a baixo:</h1>
<p>Lembre-se, ao Apertar em adicionar Produto, Ele Será Publicado na Loja, tenha certeza que não há erros nas informações presentes.</p>
<?php if (!empty($mensagem)): ?>
    <p><?php echo htmlspecialchars($mensagem); ?></p>
<?php endif; ?>
<div class="table">
            <div class="section1">
            <h3>Lista de informações</h3>
            <form method="POST">
            <ul id="desc">
            <input type="text" name="descricao" placeholder="Descrição" required>
            </ul>
            <button onclick="adddesc() ">+</button>
            <button onclick="removedesc()">-</button>
            <ul id="cult">
            <input type="text" name="estilo_cultivo" placeholder="Estilo de Cultivo" required>
            </ul>
            <button onclick="addcult()">+</button>
            <button onclick="removecult()">-</button>
            </div>
            <div class="section2">
                <h3>Novo produto</h3>
                
                    <input type="text" name="nome" placeholder="Nome do Produto" required>
                    <input type="number" step="0.01" name="valor" placeholder="Valor (R$)" required>
                    <input type="number" name="quantidade" placeholder="Quantidade" required>
                    <input type="text" name="url_imagem" placeholder="URL Da Imagem">
                    <button type="submit" name="acao" value="inserir" onclick="move()">Adicionar Produto</button>
                </form>
            </div>
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
</div>
</div>
</div> 
</body>
<script>
   var value1 = 0;
var value2 = 0;

function adddesc() {
    if (value1 < 4) {
        value1 += 1;
        document.getElementById("desc").innerHTML += `<input type="text" id="desc${value1}" name="${value1}descricao" placeholder="${value1 + 1}° Descrição">`;
        
    } else {
        alert("Limite de campos: 5");
    }
}

function addcult() {
    if (value2 < 4) {
        value2 += 1;
        document.getElementById("cult").innerHTML += `<input type="text" id="cult${value2}" name="${value2}estilo_cultivo" placeholder="${value2 + 1}° Estilo de Cultivo">`;
    } else {
        alert("Limite de campos: 5");
    }
}

function removedesc() {
    if (value1 > 0) {
        var inputToRemove = document.getElementById(`desc${value1}`);
        inputToRemove.parentNode.removeChild(inputToRemove);
        value1 -= 1;
    } else {
        alert("Nenhum campo para remover");
    }
}

function removecult() {
    if (value2 > 0) {
        var inputToRemove = document.getElementById(`cult${value2}`);
        inputToRemove.parentNode.removeChild(inputToRemove);
        value2 -= 1;
    } else {
        alert("Nenhum campo para remover");
    }
}
function move(){
    window.locate(preview/preview.php)
}

</script>
</html>

<?php
$conn->close();
?>
