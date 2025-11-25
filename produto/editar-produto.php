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

if(!isset($_GET['id'])){
    header('Location: ../admin.php');
    exit;
}

$id = (int)$_GET['id'];
$produto = $repo->buscarPorId($id);
if(!$produto) header('Location: ../admin.php');

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY categoria")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $efeito = trim($_POST['efeito']);
    $preco = isset($_POST['preco']) ? floatval(trim($_POST['preco'])) : 0;
    $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;

    $imagemNome = $produto->getImagem();

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK){
        $pastaUploads = 'uploads/';
        if(!is_dir($pastaUploads)) mkdir($pastaUploads,0777,true);

        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagemNome = uniqid('img_', true).".".$ext;

        move_uploaded_file($_FILES['imagem']['tmp_name'], $pastaUploads.$imagemNome);
    }

    if($nome && $descricao && $efeito && $preco && $categoria_id){
        $produtoAtualizado = new Produto(
            (int)$id,
            $nome,
            $descricao,
            $efeito,
            $preco,
            $categoria_id,
            $imagemNome
        );
        $repo->atualizar($produtoAtualizado);

        header('Location: ../admin.php');
        exit;
    } else {
        $erro = "Todos os campos são obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Produto</title>
<link rel="stylesheet" href="../css/reset.css">
<link rel="stylesheet" href="../css/form.css">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<header class="header-admin">
    <a href="../admin.php"><button class="botao-menu">☰ Menu</button></a>
    <h1>Editar Produto</h1>
</header>

<section class="container-form">

    <?php if($erro): ?>
        <p class="mensagem-erro"><?= $erro ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <label for="nome">Nome do Produto:</label>
        <input type="text" id="nome" name="nome" 
               value="<?= htmlspecialchars($produto->getNome()) ?>" required>

        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" 
               value="<?= htmlspecialchars($produto->getDescricao()) ?>" required>

        <label for="efeito">Efeito:</label>
        <input type="text" id="efeito" name="efeito" 
               value="<?= htmlspecialchars($produto->getEfeito()) ?>" required>

        <label for="preco">Preço:</label>
        <input type="text" id="preco" name="preco" 
               value="<?= htmlspecialchars($produto->getPreco()) ?>" required>

        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria_id" required>
            <option value="">Selecione a categoria</option>
        <?php foreach($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" 
                <?= $produto->getCategoriaId() == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['categoria']) ?>
            </option>
        <?php endforeach; ?>
        </select>

        <?php if($produto->getImagem()): ?>
            <p>Imagem atual:</p>
            <img src="uploads/<?= htmlspecialchars($produto->getImagem()) ?>" 
                 alt="Imagem do produto" width="120">
        <?php endif; ?>

        <label for="imagem">Nova Imagem (opcional):</label>
        <input type="file" id="imagem" name="imagem" accept="image/*">

        <button type="submit" class="botao-cadastrar">Atualizar Produto</button>

    </form>

</section>

</body>
</html>
