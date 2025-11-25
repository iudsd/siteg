<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once 'src/conexao-bd.php';
require_once 'src/Repositorio/ProdutoRepositorio.php';
require_once 'src/Repositorio/UsuarioRepositorio.php';
require_once 'src/Modelo/Produto.php';
require_once 'src/Modelo/Usuario.php';

$repoProduto = new ProdutoRepositorio($pdo);
$repoUsuario = new UsuarioRepositorio($pdo);

// Ações via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['excluir_produto_id'])) {
        $idProduto = (int) $_POST['excluir_produto_id'];
        $repoProduto->remover($idProduto);
        header('Location: admin.php?aba=produtos');
        exit;
    }

    if (isset($_POST['excluir_usuario_id'])) {
        $idUsuario = (int) $_POST['excluir_usuario_id'];
        $repoUsuario->excluir($idUsuario);
        header('Location: admin.php?aba=usuarios');
        exit;
    }

    if (isset($_POST['promover_usuario_id'])) {
        $idUsuario = (int) $_POST['promover_usuario_id'];
        $repoUsuario->tornarAdmin($idUsuario);
        header('Location: admin.php?aba=usuarios');
        exit;
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

$aba = $_GET['aba'] ?? 'produtos';

// PAGINAÇÃO DE PRODUTOS
$itens_por_pagina = filter_input(INPUT_GET, 'itens_por_pagina', FILTER_VALIDATE_INT) ?: 5;
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// total e páginas (ProdutoRepositorio deve ter contarTotal())
$total_produtos = method_exists($repoProduto, 'contarTotal') ? $repoProduto->contarTotal() : count($repoProduto->buscarTodos());
$total_paginas = $itens_por_pagina > 0 ? (int)ceil($total_produtos / $itens_por_pagina) : 1;

$ordem = filter_input(INPUT_GET, 'ordem') ?: null;
$direcao = filter_input(INPUT_GET, 'direcao') ?: 'ASC';

// Busca produtos (se o repositório tem buscarPaginado usa ele, senão buscaTodos)
if (method_exists($repoProduto, 'buscarPaginado')) {
    $produtos = $repoProduto->buscarPaginado($itens_por_pagina, $offset, $ordem, $direcao);
} else {
    $produtos = $repoProduto->buscarTodos();
}

// Funções utilitárias
function gerarUrlOrdenacao($campo, $paginaAtual, $ordemAtual, $direcaoAtual, $itensPorPagina) {
    $novaDirecao = ($ordemAtual === $campo && $direcaoAtual === 'ASC') ? 'DESC' : 'ASC';
    return "?pagina={$paginaAtual}&ordem={$campo}&direcao={$novaDirecao}&itens_por_pagina={$itensPorPagina}";
}

function mostrarIconeOrdenacao($campo, $ordemAtual, $direcaoAtual) {
    if ($ordemAtual !== $campo) return '&#8597;';
    return $direcaoAtual === 'ASC' ? '↑' : '↓';
}

$usuarioLogado = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/admin.css">
<title>Painel Administrativo</title>
</head>
<body>
<header class="header-admin">
    <h1>Painel Administrativo</h1>
    <form method="post" style="display:inline;">
        <button type="submit" name="logout" class="botao-home">Sair</button>
    </form>
</header>

<nav class="menu-admin">
    <a href="?aba=produtos" class="<?= $aba==='produtos' ? 'ativo' : '' ?>">Produtos</a>
    <a href="?aba=usuarios" class="<?= $aba==='usuarios' ? 'ativo' : '' ?>">Usuários</a>
</nav>

<section class="container-table">

<?php if ($aba === 'produtos'): ?>
    <form class="pesquisa-container" method="get">
        <input type="hidden" name="aba" value="produtos">
        <input type="text" name="pesquisa" placeholder="Pesquisar produto..." value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
        <button type="submit">🔍</button>
    </form>

    <form class="form-paginacao" method="GET" action="">
        <label for="itens_por_pagina">Itens por página:</label>
        <select name="itens_por_pagina" id="itens_por_pagina" onchange="this.form.submit()">
            <option value="5" <?= $itens_por_pagina == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $itens_por_pagina == 10 ? 'selected' : '' ?>>10</option>
            <option value="20" <?= $itens_por_pagina == 20 ? 'selected' : '' ?>>20</option>
        </select>
        <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
        <input type="hidden" name="direcao" value="<?= htmlspecialchars($direcao) ?>">
    </form>

    <table>
        <thead>
    <tr>
        <th>Nome</th>
        <th>
            <a href="<?= gerarUrlOrdenacao('descricao', $pagina_atual, $ordem, $direcao, $itens_por_pagina) ?>">
                Descrição <?= mostrarIconeOrdenacao('descricao', $ordem, $direcao) ?>
            </a>
        </th>
        <th>
            <a href="<?= gerarUrlOrdenacao('preco', $pagina_atual, $ordem, $direcao, $itens_por_pagina) ?>">
                Preço <?= mostrarIconeOrdenacao('preco', $ordem, $direcao) ?>
            </a>
        </th>
        <th>
            <a href="<?= gerarUrlOrdenacao('categoria_id', $pagina_atual, $ordem, $direcao, $itens_por_pagina) ?>">
                Categoria <?= mostrarIconeOrdenacao('categoria_id', $ordem, $direcao) ?>
            </a>
        </th>
        <th colspan="2">Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($produtos as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p->getNome()) ?></td>
            <td><?= htmlspecialchars($p->getDescricao()) ?></td>
            <td><?= htmlspecialchars($p->getPrecoFormatado()) ?></td>
            <td><?= htmlspecialchars($p->getCategoriaNome() ?? 'Sem categoria') ?></td>

            <td><a class="botao-editar" href="/siteg/produto/editar-produto.php?id=<?= $p->getId() ?>">Editar</a></td>

            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="excluir_produto_id" value="<?= $p->getId() ?>">
                    <input type="submit" class="botao-excluir" value="Excluir">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>

    <div class="paginacao">
        <?php if ($total_paginas > 1): ?>
            <?php if ($pagina_atual > 1): ?>
                <a href="?pagina=<?= $pagina_atual - 1 ?>&itens_por_pagina=<?= $itens_por_pagina ?>&ordem=<?= urlencode($ordem) ?>&direcao=<?= urlencode($direcao) ?>">Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if ($i == $pagina_atual): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="?pagina=<?= $i ?>&itens_por_pagina=<?= $itens_por_pagina ?>&ordem=<?= urlencode($ordem) ?>&direcao=<?= urlencode($direcao) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_atual + 1 ?>&itens_por_pagina=<?= $itens_por_pagina ?>&ordem=<?= urlencode($ordem) ?>&direcao=<?= urlencode($direcao) ?>">Próximo</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <a href="/siteg/produto/cadastrar-produto.php" class="botao-cadastrar">+ Adicionar Produto</a>
    <form action="/siteg/produto/gerador-pdf.php" method="post" style="display:inline;">
        <input type="submit" class="botao-cadastrar" value="Baixar Relatório">
    </form>

<?php elseif ($aba === 'usuarios'): ?>

    <form class="pesquisa-container" method="get">
        <input type="hidden" name="aba" value="usuarios">
        <input type="text" name="pesquisa_usuario" placeholder="Pesquisar usuário..." value="<?= htmlspecialchars($_GET['pesquisa_usuario'] ?? '') ?>">
        <button type="submit">🔍</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th colspan="2">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($repoUsuario->listar() as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u->getNome()) ?></td>
                <td><?= htmlspecialchars($u->getEmail()) ?></td>
                <td><?= htmlspecialchars($u->getPerfil()) ?></td>
                <td>
                    <?php if ($u->getPerfil() !== 'Admin'): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="promover_usuario_id" value="<?= $u->getId() ?>">
                            <input type="submit" class="botao-editar" value="Tornar Admin">
                        </form>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="excluir_usuario_id" value="<?= $u->getId() ?>">
                        <input type="submit" class="botao-excluir" value="Excluir">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="/siteg/usuario/usuario-cadastrar-admin.php" class="botao-cadastrar">+ Adicionar Usuário</a>

<?php endif; ?>

</section>
</body>
</html>
