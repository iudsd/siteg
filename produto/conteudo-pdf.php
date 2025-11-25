<?php
require "../src/conexao-bd.php";
require "../src/Modelo/Produto.php";
require "../src/Repositorio/ProdutoRepositorio.php";

date_default_timezone_set('America/Sao_Paulo');
$rodapeDataHora = date('d/m/Y H:i');

$produtoRepositorio = new ProdutoRepositorio($pdo);
$produtos = $produtoRepositorio->buscarTodos();

// Caminho da imagem do logo
$imagePath = '../img/cadastro-logo.jpg';
$imageData = base64_encode(file_get_contents($imagePath));
$imageSrc = 'data:image/jpeg;base64,' . $imageData;
?>
<head>
<meta charset="UTF-8">
<style>
body, table, th, td, h3 { font-family: Arial, Helvetica, sans-serif; }
table { width: 90%; margin: auto 0; border-collapse: collapse; }
th, td { border: 1px solid #000; padding: 8px; font-size: 12px; }
h3 { text-align: center; margin: 0.5rem 0 1rem; }
.pdf-footer { position: fixed; bottom: 0; left: 0; right: 0; height: 30px; text-align: center; font-size: 12px; color: #444; border-top: 1px solid #ddd; padding-top: 6px; }
body { margin-bottom: 50px; margin-top: 0; }
.pdf-img { width: 100px; }
</style>
</head>

<img src="<?= $imageSrc ?>" class="pdf-img" alt="logo">

<h3>Listagem de produtos</h3>

<table>
<thead>
<tr>
<th>Produto</th>
<th>Descrição</th>
<th>Efeito</th>
<th>Valor</th>
</tr>
</thead>
<tbody>
<?php foreach ($produtos as $produto): ?>
<tr>
<td><?= htmlspecialchars($produto->getNome()) ?></td>
<td><?= htmlspecialchars($produto->getDescricao()) ?></td>
<td><?= htmlspecialchars($produto->getEfeito()) ?></td>
<td><?= htmlspecialchars($produto->getPreco()) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="pdf-footer">
Gerado em: <?= htmlspecialchars($rodapeDataHora) ?>
</div>
