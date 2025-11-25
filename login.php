<?php
// Carrega a classe antes do session_start
require_once 'src/Modelo/Usuario.php';
require_once 'src/conexao-bd.php';
require_once 'src/Repositorio/UsuarioRepositorio.php';

session_start();

// Limpa sessão caso esteja quebrada
if (isset($_SESSION['usuario']) && !($_SESSION['usuario'] instanceof Usuario)) {
    session_unset();
    session_destroy();
    session_start();
}

$repoUsuario = new UsuarioRepositorio($pdo);
$erro = $_GET['erro'] ?? '';

// Redireciona se já estiver logado
if (isset($_SESSION['usuario']) && $_SESSION['usuario'] instanceof Usuario) {
    $usuarioLogado = $_SESSION['usuario'];
    if ($usuarioLogado->getPerfil() === 'Admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: usuario/usuario-tela.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/admin.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<title>Login - Otimizador de Builds</title>
</head>
<body>

<main>
    <section class="container-admin-banner">
        <img src="img/login-logo.jpg" alt="Banner" class="logo-banner">
        <h1 class="banner-texto">Otimizador de Builds</h1>
    </section>

    <section class="container-form">
        <div class="form-wrapper">
            <?php if ($erro === 'credenciais'): ?>
                <p class="mensagem-erro">Usuário ou senha incorretos.</p>
            <?php elseif ($erro === 'campos'): ?>
                <p class="mensagem-erro">Preencha e-mail e senha.</p>
            <?php endif; ?>

            <form action="autenticar.php" method="post">
                <label for="email">E-mail: </label>
                <input type="email" id="email" name="email" placeholder="Digite o seu e-mail" required>

                <label for="senha">Senha: </label>
                <input type="password" id="senha" name="senha" placeholder="Digite a sua senha" required>

                <input type="submit" class="botao-cadastrar" value="Entrar">
            </form>

            <p style="text-align:center; margin-top: 10px;">
                Não tem conta? <a href="usuario/usuario-cadastrar.php">Cadastre-se</a>
            </p>
        </div>
    </section>
</main>

<script>
window.addEventListener('DOMContentLoaded', function(){
    const msg = document.querySelector('.mensagem-erro');
    if(msg){
        setTimeout(() => msg.classList.add('oculto'), 5000);
    }
});
</script>

</body>
</html>
