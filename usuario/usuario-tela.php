<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../src/conexao-bd.php';
require_once __DIR__ . '/../src/Repositorio/ProdutoRepositorio.php';
require_once __DIR__ . '/../src/Modelo/Produto.php';

$repoProduto = new ProdutoRepositorio($pdo);
$produtos = $repoProduto->buscarTodos();

if (!is_array($produtos)) {
    $produtos = [];
}

$termoProduto = $_GET['pesquisa'] ?? '';
if ($termoProduto !== '') {
    $produtos = array_filter($produtos, function($p) use ($termoProduto){
        return stripos($p->getNome(), $termoProduto) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/reset.css">
<link rel="stylesheet" href="../css/usuario-tela.css">
<title>Usuário - Produtos</title>
</head>
<body>

<header class="header-usuario">
    <h1>Produtos</h1>

    <form method="post" action="../logout.php">
        <button type="submit" class="botao-home">Sair</button>
    </form>
</header>

<form class="pesquisa-container" method="get">
    <input type="text" name="pesquisa" placeholder="Pesquisar produto..."
           value="<?php echo htmlspecialchars($termoProduto); ?>">
    <button type="submit">🔍</button>
</form>

<section class="container-table">
<table>
    <thead>
        <tr>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Efeito</th>
            <th>Preço</th>
            <th>Comprar</th>
        </tr>
    </thead>

    <tbody>
    <?php if (empty($produtos)): ?>
        <tr>
            <td colspan="6" style="text-align:center; padding: 20px;">
                Nenhum produto encontrado.
            </td>
        </tr>

    <?php else: ?>
        <?php foreach ($produtos as $p): ?>
        <tr>
            <td>
                <?php if ($p->getImagem()): ?>
                    <img 
                        src="../produto/uploads/<?php echo $p->getImagem(); ?>"
                        alt="<?php echo htmlspecialchars($p->getNome()); ?>" 
                        class="produto-img"
                    >
                <?php else: ?>
                    <div class="sem-imagem">Sem imagem</div>
                <?php endif; ?>
            </td>

            <td><?php echo htmlspecialchars($p->getNome()); ?></td>
            <td><?php echo htmlspecialchars($p->getDescricao()); ?></td>
            <td><?php echo htmlspecialchars($p->getEfeito()); ?></td>
            <td><?php echo htmlspecialchars($p->getPreco()); ?></td>

            <td>
                <button class="botao-comprar">Comprar</button>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</section>

</body>
</html>
