<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: ../login.php');
    exit;
}

require_once '../src/conexao-bd.php';
require_once '../src/Repositorio/ProdutoRepositorio.php';
require_once '../src/Modelo/Produto.php';

$repo = new ProdutoRepositorio($pdo);
$erro = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $efeito = trim($_POST['efeito']);
    $preco = trim($_POST['preco']);
    $categoria_id = (int) $_POST['categoria_id'];
    $imagemNome = null;

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error']===UPLOAD_ERR_OK){
        $pastaUploads = 'uploads/';
        if(!is_dir($pastaUploads)) mkdir($pastaUploads,0777,true);
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagemNome = uniqid('img_',true).".".$ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'],$pastaUploads.$imagemNome);
    }

    if($nome && $descricao && $efeito && $preco && $categoria_id){
        $produto = new Produto(
            0,
            $nome,
            $descricao,
            $efeito,
            (float)$preco,
            $categoria_id,
            $imagemNome
        );
        $repo->salvar($produto);
        header('Location: ../admin.php');
        exit;
    } else $erro = "Todos os campos são obrigatórios.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Produto</title>
<link rel="stylesheet" href="../css/form.css">
<link rel="stylesheet" href="../css/cadastrar-produto.css">
</head>
<body>
<header class="header-admin">
    <a href="../admin.php"><button class="botao-menu">☰ Menu</button></a>
    <h1>Cadastrar Produto</h1>
</header>
<section class="container-form">
    <div class="banner-wrapper">
        <span class="banner-text">Otimizador de Builds</span>
    </div>
    <?php if($erro) echo "<p class='mensagem-erro'>$erro</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome do Produto" required>
        <input type="text" name="descricao" placeholder="Descrição" required>
        <input type="text" name="efeito" placeholder="Efeito" required>
        <input type="text" name="preco" placeholder="Preço" required>

        <select name="categoria_id" required>
            <option value="">Selecione a categoria</option>
            <option value="1">Categoria 1</option>
            <option value="2">Categoria 2</option>
            <option value="3">Categoria 3</option>
        </select>

        <input type="file" name="imagem" accept="image/*" required>
        <button type="submit" class="botao-cadastrar">Cadastrar Produto</button>
    </form>
</section>
</body>
</html>
