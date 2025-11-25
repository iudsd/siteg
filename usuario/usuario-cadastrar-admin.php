<?php
require_once __DIR__ . '/../src/Modelo/Usuario.php';
require_once __DIR__ . '/../src/Repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../src/conexao-bd.php';

session_start();

// Só ADMIN pode cadastrar usuários
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SESSION['usuario']->getPerfil() !== 'Admin') {
    header('Location: ../usuario/usuario-tela.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senhaDigitada = trim($_POST['senha'] ?? '');

    if (!$nome || !$email || !$senhaDigitada) {
        $erro = 'Preencha todos os campos.';
    } else {

        $repo = new UsuarioRepositorio($pdo);

        if ($repo->buscarPorEmail($email)) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $senha = password_hash($senhaDigitada, PASSWORD_DEFAULT);
            $usuario = new Usuario(0, $nome, $email, $senha, 'User');
            $repo->salvar($usuario);
            $sucesso = 'Usuário cadastrado com sucesso!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Usuário</title>
<link rel="stylesheet" href="../css/reset.css">
<link rel="stylesheet" href="../css/login.css">
<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/cadastro-usuario.css">
</head>
<body>
<main>
<section class="container-form">
    <div class="banner-wrapper">
        <span class="banner-text">Otimizador de Builds</span>
    </div>
    <div class="form-wrapper">
        <h2>Cadastro de Usuário</h2>

        <?php if ($erro): ?>
            <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($sucesso): ?>
            <p class="mensagem-sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite o nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite o e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite a senha" required>

            <button type="submit" class="botao-cadastrar">Cadastrar</button>
        </form>

        <p style="text-align:center; margin-top: 10px;">
            <a href="../admin.php">Voltar</a>
        </p>
    </div>
</section>
</main>
</body>
</html>
