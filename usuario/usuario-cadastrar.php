<?php
// Carrega a classe antes do session_start
require_once __DIR__ . '/../src/Modelo/Usuario.php';
require_once __DIR__ . '/../src/Repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../src/conexao-bd.php';

session_start();

// Limpa sessão quebrada
if (isset($_SESSION['usuario']) && !($_SESSION['usuario'] instanceof Usuario)) {
    session_unset();
    session_destroy();
    session_start();
}

$erro = '';
$sucesso = '';

// Se o usuário já estiver logado, redireciona para a tela dele
if (isset($_SESSION['usuario']) && $_SESSION['usuario'] instanceof Usuario) {
    $usuarioLogado = $_SESSION['usuario'];
    if ($usuarioLogado->getPerfil() === 'Admin') {
        header('Location: ../admin.php');
        exit;
    } else {
        header('Location: usuario-tela.php');
        exit;
    }
}

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
            $sucesso = 'Cadastro realizado com sucesso! <a href="../login.php">Entrar</a>';
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
</head>
<body>
<main>
<section class="container-form">
    <div class="form-wrapper">  
        <h2>Cadastro de Usuário</h2>

        <?php if ($erro): ?>
            <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($sucesso): ?>
            <p class="mensagem-sucesso"><?= $sucesso ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <button type="submit" class="botao-cadastrar">Cadastrar</button>
        </form>

        <p style="text-align:center; margin-top: 10px;">
            Já tem conta? <a href="../login.php">Entrar</a>
        </p>
    </div>
</section>
</main>
</body>
</html>
